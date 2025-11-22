<?php

// used by	:	users.php , 
//				groups.php (check the user before delete the group)
function ctUserGroup($id, $groupid) {
	global $conn;
	
	// Validate inputs
	if ($id === null || $groupid === null) {
		return false;
	}
	
	$sSQL = "";
	$sWhere = "";		
	
	if ($id == "ALL") {
		$sWhere .= "groupID = " . tosql($groupid, "Number");	
		$sWhere = " WHERE (" . $sWhere . ")";
	} else {
		$sWhere .= "userID = " . tosql($id, "Number") . " and ";
		$sWhere .= "groupID = " . tosql($groupid, "Number");
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	
	$sSQL = "SELECT userID, userName, fullName FROM users";
	$sSQL = $sSQL . $sWhere . ' ORDER BY userName';
	
	$rs = $conn->Execute($sSQL);
	
	// Error handling
	if ($rs === false) {
		error_log("ctUserGroup SQL Error: " . $conn->ErrorMsg());
		return false;
	}
	
	return $rs;
}

// used by : users.php
function ctUser($id) {
	global $conn;
	
	// Validate input
	if ($id === null) {
		return false;
	}
	
	$sSQL = "";
	$sWhere = "";		
	
	if ($id == "ALL") {
		$sWhere .= "isActive = 1";
		$sWhere = " WHERE (" . $sWhere . ")";
	} else {
		$sWhere .= "userID = " . tosql($id, "Number") . " and ";
		$sWhere .= "isActive = 1 ";
		$sWhere = " WHERE (" . $sWhere . ")";
	}
	
	$sSQL = "SELECT userID FROM users";
	$sSQL = $sSQL . $sWhere . ' ORDER BY userName';
	
	$rs = $conn->Execute($sSQL);
	
	// Error handling
	if ($rs === false) {
		error_log("ctUser SQL Error: " . $conn->ErrorMsg());
		return false;
	}
	
	return $rs;
}

// used by : users.php & profile.php
function ctVerifyUser($login, $pwd) {
	global $conn;
	
	// Validate inputs
	if ($login === null || $pwd === null || $login === '' || $pwd === '') {
		return false;
	}
	
	$sSQL = "";
	$sSQL = "	SELECT	userID, userName, fullName, email, groupID, hqID, branchID, access " .
			"	FROM 	users " .
			"	WHERE	userName = " . tosql($login, "Text") .
			" 	AND 	password = " . tosql($pwd, "Text");
	
	$rs = $conn->Execute($sSQL);
	
	// Error handling
	if ($rs === false) {
		error_log("ctVerifyUser SQL Error: " . $conn->ErrorMsg());
		return false;
	}
	
	return $rs;
}

// used by : logs.php
function ctLog($q = '', $group = '') {
	global $conn;
	
	$sSQL = "";
	$sWhere = "";		
	
	// Build WHERE clause with proper logic
	$conditions = array();
	
	if ($q !== null && $q !== "") {
		$conditions[] = "eventText like " . tosql("%" . $q . "%", "Text");
	}	
	
	if ($group !== null && $group !== "") {
		$conditions[] = "groupName = " . tosql($group, "Text");
	}	
	
	if (count($conditions) > 0) {
		$sWhere = " WHERE " . implode(" AND ", $conditions);
	}

	$sSQL = "SELECT * FROM logs ";
	$sSQL = $sSQL . $sWhere . ' ORDER BY dateEvent DESC';
	
	$rs = $conn->Execute($sSQL);
	
	// Error handling
	if ($rs === false) {
		error_log("ctLog SQL Error: " . $conn->ErrorMsg());
		return false;
	}
	
	return $rs;
}

?>