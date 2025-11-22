<?php

include("common.php");	
// Session is already started in common.php!
include("setupinfo.php");	
include("dhsbQuery.php");	

// Initialize all request variables
$continue = $_REQUEST['continue'] ?? '';
$action = $_REQUEST['action'] ?? '';
$username = $_REQUEST['username'] ?? '';
$password = $_REQUEST['password'] ?? '';
$msg = '';

if ($action) {
	if ($username <> "" && $password <> "") {
	
		// Cache session variable with default
		$sessionAd = $_SESSION['Session.ad'] ?? '';
		
		if ($sessionAd == '1' || $sessionAd == '') {	
			//--- Login as different user id
			// Add your logic here if needed
			
		} elseif ($sessionAd == '2') {		
			//--- Login as administrator
			$username = strtoupper($username);
			$encryptpwd = strtoupper(md5($password.$username));
			$GetUser = ctVerifyUser($username, $encryptpwd);
			
			if ($GetUser && $GetUser->RowCount() == 1) {
				if ($msg == '') {
					$_SESSION['Session.userID'] = $GetUser->fields('userID');		
					$_SESSION['Session.userName'] = $GetUser->fields('userName');
					$_SESSION['Session.fullName'] = $GetUser->fields('fullName');
					$_SESSION['Session.email'] = $GetUser->fields('email');
					$_SESSION['Session.groupID'] = $GetUser->fields('groupID');	
					
					// Initialize groupList and groupVal if not set
					if (!isset($groupList)) $groupList = array();
					if (!isset($groupVal)) $groupVal = array();
					
					if ($_SESSION['Session.groupID'] == "9") {
						array_push($groupList, "PENYELIA SISTEM");
						array_push($groupVal, "9");
					} 
					
					if ($_SESSION['Session.groupID'] == "0" || $_SESSION['Session.groupID'] == "1") {
						$_SESSION['Session.branchID'] = $GetUser->fields('branchID');	
						$_SESSION['Session.branchName'] = dlookup("vendor", "vendorName", "vendorID=" . tosql($GetUser->fields('branchID'), "Text"));	
						$_SESSION['Session.branchCode'] = dlookup("vendor", "vendorCode", "vendorID=" . tosql($GetUser->fields('branchID'), "Text"));	
						$_SESSION['Session.isUD'] = dlookup("vendor", "isUD", "vendorID=" . tosql($GetUser->fields('branchID'), "Text"));	
					} else {
						// FIXED: Changed == to = for assignment
						$_SESSION['Session.branchID'] = '99999';	
						$_SESSION['Session.branchName'] = 'IBU PEJABAT';	
						$_SESSION['Session.isUD'] = '1';	
					}	
					
					// Make sure we have the group arrays populated
					if (count($groupList) > 0 && count($groupVal) > 0) {
						$groupIndex = array_search($GetUser->fields('groupID'), $groupVal);
						if ($groupIndex !== false) {
							$_SESSION['Session.groupName'] = $groupList[$groupIndex];		
						} else {
							$_SESSION['Session.groupName'] = 'Unknown';
						}
					} else {
						// Fallback if arrays aren't populated
						$_SESSION['Session.groupName'] = 'User';
					}
					
					//--- Begin : Create log 
					$user = $_SESSION['Session.userName'];
					$event = "Log Masuk";
					$type = $_SESSION['Session.groupName'];
					createLog($user, $event, $type);
					//--- End   : Create log 
				}
			} else {
				$msg = 'ID Penguna atau Kata laluan tidak sah...!';
			}
		
		}
	} else {
		$msg = 'ID Pengguna/Kata Laluan mesti diisi!';
	}
	
	print '<script>';
	if ($msg <> "") {
		// Escape the message for JavaScript to prevent XSS
		$escapedMsg = addslashes($msg);
		print '
			alert("'.$escapedMsg.'");
			window.location = "index.php";';
	} else {
		if ($continue <> "") {
			// Sanitize the continue URL
			$safeContinue = htmlspecialchars($continue, ENT_QUOTES, 'UTF-8');
			print 'window.location = "'.$safeContinue.'";';
		} else {
			print 'window.location = "index.php";';
		}
	}	
	print '</script>';
}
?>