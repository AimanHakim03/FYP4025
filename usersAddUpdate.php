<?php

include ("common.php");
include ("setupinfo.php");

include ("forms.php");
//$conn->debug=true;

// Initialize ALL request variables
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$SubmitForm = $_REQUEST['SubmitForm'] ?? '';
$groupID = $_REQUEST['groupID'] ?? '';
$userName = $_REQUEST['userName'] ?? '';
$password = $_REQUEST['password'] ?? '';
$fullName = $_REQUEST['fullName'] ?? '';
$email = $_REQUEST['email'] ?? '';
$position = $_REQUEST['position'] ?? '';
$contactNo = $_REQUEST['contactNo'] ?? '';
$mobileNo = $_REQUEST['mobileNo'] ?? '';
$gender = $_REQUEST['gender'] ?? '';
$icNo = $_REQUEST['icNo'] ?? '';
$branchID = $_REQUEST['branchID'] ?? '';
$isActive = $_REQUEST['isActive'] ?? 0;
$oldLogin = $_REQUEST['oldLogin'] ?? '';
$pwd = $_REQUEST['pwd'] ?? '';
$createdDate = $_REQUEST['createdDate'] ?? '';
$createdBy = $_REQUEST['createdBy'] ?? '';
$updatedDate = $_REQUEST['updatedDate'] ?? '';
$updatedBy = $_REQUEST['updatedBy'] ?? '';

// Cache session variables - THIS IS THE IMPORTANT PART!
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';

if ($_SESSION['Session.groupID'] <> "3" AND $_SESSION['Session.groupID'] <> "4" AND $_SESSION['Session.groupID'] <> "9")	{
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

//if (!isset($isActive))		$isActive= 0;

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta name="keywords" content="'.$drowyeKatem.'">
<meta name="description" content="'.$cseDatem.'">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	<LINK rel="stylesheet" href="images/zssb.css" >	
</head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

$sFileName		= "usersAddUpdate.php";
$sActionFileName= "users.php";
$Title 			= "Pengguna";

//--- BEGIN : PREPARE COMPANY LISTING ------------------------------------------------------------------------------------------
$branchList = array();
$branchVal = array();
$sSQL = " SELECT vendorID, vendorCode, vendorName FROM vendor WHERE isDeleted = '0' ORDER BY vendorName ";
$GetList = $conn->Execute($sSQL); // Removed & reference operator
if ($GetList->RowCount() <> 0) {
	while (!$GetList->EOF) {
		array_push($branchList, $GetList->fields('vendorName') . ' (' . $GetList->fields('vendorCode') . ')');
		array_push($branchVal, $GetList->fields('vendorID'));
		$GetList->MoveNext();
	}
}
//--- END   : PREPARE COMPANY LISTING ------------------------------------------------------------------------------------------

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

$i=1;
$FormLabel[$i]   	= "Profil";
$FormElement[$i] 	= "groupID";
if ($action == 'add') $FormType[$i]	  	= "selectAuto"; else $FormType[$i]	  	= "select";
$FormData[$i]    	= $groupList;
$FormDataValue[$i]	= $groupVal;
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "1";
$FormLength[$i]  	= "1";

$i++;
$FormLabel[$i]   	= "ID Pengguna";
$FormElement[$i] 	= "userName";
if ($action == 'add') $FormType[$i]	  	= "text"; else $FormType[$i]	  	= "hidden";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "25";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]   	= "Kata laluan";
$FormElement[$i] 	= "password";
$FormType[$i]	  	= "password";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "25";
$FormLength[$i]  	= "10";

$i++;
$FormLabel[$i]  	= "Nama";
$FormElement[$i] 	= "fullName";
$FormType[$i]	  	= "textUcase";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Email";
$FormElement[$i] 	= "email";
$FormType[$i]	  	= "text";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank", "CheckEmail");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Jawatan";
$FormElement[$i] 	= "position";
$FormType[$i]	  	= "textUcase";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]  	= "Tel No";
$FormElement[$i] 	= "contactNo";
$FormType[$i]	  	= "text";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "30";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]  	= "Mobile No";
$FormElement[$i] 	= "mobileNo";
$FormType[$i]	  	= "text";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "30";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Jantina";
$FormElement[$i] 	= "gender";
$FormType[$i]	  	= "radio";
$FormData[$i]    	= array('Lelaki','Perempuan');
$FormDataValue[$i]	= array(0,1);
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "1";
$FormLength[$i]  	= "1";

$i++;
$FormLabel[$i]  	= "ID No";
$FormElement[$i] 	= "icNo";
$FormType[$i]	  	= "text";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]  	= "Syarikat";
$FormElement[$i] 	= "branchID";
if ($groupID <> "3" && $groupID <> "9")	{
	$FormType[$i]	  	= "select";
} else {
	$FormType[$i]	  	= "hidden";
}
$FormData[$i]    	= $branchList;
$FormDataValue[$i]	= $branchVal;
if ($groupID == "0" && $groupID == "1")	{
	$FormCheck[$i]   	= array("CheckBlank");
} else {
	$FormCheck[$i]   	= array();
}
$FormSize[$i]    	= "1";
$FormLength[$i]  	= "1";

$i++;
$FormLabel[$i]   	= "Aktif";
$FormElement[$i] 	= "isActive";
$FormType[$i]	  	= "checkbox";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "1";
$FormLength[$i]  	= "1";

if ($action == "update") {
	$i++;
	$FormLabel[$i]  	= "Tarikh Rekod";
	$FormElement[$i] 	= "createdDate";
	$FormType[$i]	  	= "hiddenDateTime";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";

	$i++;
	$FormLabel[$i]  	= "Direkod Oleh";
	$FormElement[$i] 	= "createdBy";
	$FormType[$i]	  	= "hidden";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
	
	$i++;
	$FormLabel[$i]  	= "Tarikh Kemaskini";
	$FormElement[$i] 	= "updatedDate";
	$FormType[$i]	  	= "hiddenDateTime";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
	
	$i++;
	$FormLabel[$i]  	= "Dikemaskini Oleh";
	$FormElement[$i] 	= "updatedBy";
	$FormType[$i]	  	= "hidden";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";
}
//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->

if ($SubmitForm <> "") {
	//	$conn->debug=true;
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			$fieldName = $FormElement[$i];
			$fieldValue = isset($$fieldName) ? $$fieldName : '';

			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$fieldValue,  
				$FormCheck[$i][$j],
				$i
			);
		}
	}	
	if ($oldLogin <> $userName) {
		if (strtoupper(trim(dlookup("users", "userName", "userName=" . tosql($userName, "Text")))) == strtoupper($userName)) {
			array_push ($strErrMsg, "userName");
			print '- <font class=redText>ID Pengguna <b>( '.$userName.' )</b> telah wujud. Sila guna ID Pengguna yang lain.</font><br>';
		}
	}
	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
//		$isActive = get_checkbox_value(get_param("isActive"), "1", "0", "Number");
		$createdBy 	= $_SESSION['Session.userName'];
		$createdDate = date("Y-m-d H:i:s");             
		$updatedBy 	= $_SESSION['Session.userName'];
		$updatedDate = date("Y-m-d H:i:s");               
		$sSQL = "";
		switch(strtolower($SubmitForm)) 
		{
			case "daftar":
			    $un = strtoupper($userName);
				$userID = strtoupper(uniqid(rand(),1)); 
				if ($password <> "*****") {
					$password = strtoupper(md5($password.$un));
				}
				$sSQL	= "INSERT INTO users (" . 
				          "userID," . 				
				          "userName," . 
						  "password," . 
						  "fullName," . 
						  "email," . 
						  "position," . 
						  "contactNo," . 
						  "mobileNo," . 
						  "icNo," . 
						  "gender," . 
						  "isActive," . 
						  "isDeleted," . 
						  "branchID," . 
						  "groupID," . 
				          "createdDate," . 
				          "createdBy," . 
				          "updatedDate," . 
				          "updatedBy)" . 
				          " VALUES (" . 
				          tosql($userID, "Text") . "," .
				          tosql($userName, "Text") . "," .
						  tosql($password, "Text") . "," .
						  tosql($fullName, "Text") . "," .
						  tosql($email, "Text") . "," .
						  tosql($position, "Text") . "," .
						  tosql($contactNo, "Text") . "," .
						  tosql($mobileNo, "Text") . "," .						  
						  tosql($icNo, "Text") . "," .
						  tosql($gender, "Number") . "," .
						  $isActive . "," .
						  tosql("0", "Number") . "," .
						  tosql($branchID, "Number") . "," .
						  tosql($groupID, "Number") . "," .
						  tosql($createdDate, "Text") . "," .
				          tosql($createdBy, "Text") . ",".
						  tosql($updatedDate, "Text") . "," .
				          tosql($updatedBy, "Text") . ")";
				$msg = "Rekod telah didaftarkan";
			break;
			case "kemaskini":
			    $un = strtoupper($userName);
				if ($password <> $pwd) $password = strtoupper(md5($password.$un));
			    $sWhere = "userID=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	users SET " .
				          "userName		=" . tosql($userName, "Text") .
						  ",password	=" . tosql($password, "Text") .
						  ",fullName	=" . tosql($fullName, "Text") .
  						  ",email		=" . tosql($email, "Text") .
  						  ",position	=" . tosql($position, "Text") .
  						  ",contactNo	=" . tosql($contactNo, "Text") .
  						  ",mobileNo	=" . tosql($mobileNo, "Text") .
						  ",icNo		=" . tosql($icNo, "Text") .
						  ",gender		=" . tosql($gender, "gender") .
  				          ",isActive	=" . $isActive .
						  ",branchID	=" . tosql($branchID, "Number") .
						  ",groupID		=" . tosql($groupID, "Number") .
						  ",updatedDate	=" . tosql($updatedDate, "Text") .
				          ",updatedBy	=" . tosql($updatedBy, "Text") ;
				$sSQL .= " WHERE " . $sWhere;
				$msg = "Rekod telah dikemaskinikan";
			break;
		}
		$rs = $conn->Execute($sSQL);

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		if (strtolower($SubmitForm) == "daftar")
			$event = "ID Pengguna telah didaftarkan - ".$userName;
		else
			$event = "ID Pengguna telah dikemaskinikan  - ".$userName;
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 				
		
		print '<script>
					alert ("'.$msg.'");
					opener.document.location = "' . $sActionFileName . '";
					window.close();
				</script>';
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($action == "update") {
	if ($pk <> "") {
		//--- Begin : query database for information ---------------------------------------------
		$sSQL = "SELECT * FROM users WHERE userID = '".$pk."' ";
		$rs = $conn->Execute($sSQL);
		//--- End   : query database for information ---------------------------------------------
	}
}

if ($action == "add") {
	print '<form name="MyDHSB" action="'.$sFileName.'?action='.$action.'" method="post">';
} else {
	print '<form name="MyDHSB" action="'.$sFileName.'?action='.$action.'&pk='.$pk.'" method="post">';
}
print '
<table border=0 cellpadding=3 cellspacing=1 width=100% align="center" class="lineBG">
	<tr class=Header>
		<td colspan="2" height="30" valign="middle">';
		
if ($action == "add") print 'Register '.$Title; else print 'Update '.$Title.' : '.tohtml($rs->fields('userName'));
print '</td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i == 13) print '<tr class=Header><td colspan=2>Audit Informasi:</td></tr>';
	print '<tr valign=top><td class=Label align=right>'.$FormLabel[$i].' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($action == "update") { 
		$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	} else {
				$fieldName = $FormElement[$i];
		$strFormValue = isset($$fieldName) ? $$fieldName : '';
	}
	FormEntry($FormLabel[$i], 
			  $FormElement[$i], 
			  $FormType[$i],
			  $strFormValue,
			  $FormData[$i],
			  $FormDataValue[$i],
			  $FormSize[$i],
			  $FormLength[$i]);
	//--- End   : Call function FormEntry ---------------------------------------------------------  
    print '&nbsp;</td></tr>';
}

print '<tr><td colspan=2 align=center class=Data>';
if ($action == "add") {
	print '
	<input type=hidden name=userID>
	<input type="hidden" name="oldName" value="">
	<input type=Submit name=SubmitForm class="but" value=Daftar>';
} else { 
    print '
	<input type=hidden name=userID class="textFont" value='.$pk.'>
	<input type="hidden" name="pwd" value="'.tohtml($rs->fields('password')).'">
	<input type=hidden name=oldLogin value='.$rs->fields('userName').'>
	<input type=Submit name=SubmitForm class="but" value=Kemaskini>';
}
print '		</td>
		</tr>
</table>
</form>';

?>

