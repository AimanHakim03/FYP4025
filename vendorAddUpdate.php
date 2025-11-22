<?php

include ("header.php");
include ("forms.php");
session_start();
//$conn->debug=true;

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.close;</script>';
}

// FIX FOR UNDEFINED VARIABLES - GET ALL POST/GET DATA
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$pk = isset($_GET['pk']) ? $_GET['pk'] : (isset($_POST['pk']) ? $_POST['pk'] : '');
$strURL = isset($_POST['strURL']) ? $_POST['strURL'] : (isset($_GET['strURL']) ? $_GET['strURL'] : '');
$SubmitForm = isset($_POST['SubmitForm']) ? $_POST['SubmitForm'] : '';
$newItem1 = isset($_POST['newItem1']) ? $_POST['newItem1'] : '';

// Form field variables
$vendorCode = isset($_POST['vendorCode']) ? $_POST['vendorCode'] : '';
$vendorName = isset($_POST['vendorName']) ? $_POST['vendorName'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$telNo = isset($_POST['telNo']) ? $_POST['telNo'] : '';
$faxNo = isset($_POST['faxNo']) ? $_POST['faxNo'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$contactPerson = isset($_POST['contactPerson']) ? $_POST['contactPerson'] : '';
$bankName = isset($_POST['bankName']) ? $_POST['bankName'] : '';
$accNo = isset($_POST['accNo']) ? $_POST['accNo'] : '';
$isUD = isset($_POST['isUD']) ? $_POST['isUD'] : 0;

// Array variables - IMPORTANT!
$picNameArr = isset($_POST['picNameArr']) ? $_POST['picNameArr'] : array();
$picICArr = isset($_POST['picICArr']) ? $_POST['picICArr'] : array();
$bilArr = isset($_POST['bilArr']) ? $_POST['bilArr'] : array();
$picName = '';
$picIC = '';

if (!isset($isActive))	$isActive= 1;
$access1 = isset($_POST['access1']) ? $_POST['access1'] : 0;
$delItem1 = isset($_POST['delItem1']) ? $_POST['delItem1'] : '';
$itemCount = isset($_POST['itemCount']) ? $_POST['itemCount'] : 1;

$sFileName		= "vendorAddUpdate.php";
$sActionFileName= "vendor.php";
$Title 			= "Syarikat";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = Array();

$i = 1;
$FormLabel[$i]   	= "* Kod";
$FormElement[$i] 	= "vendorCode";
$FormType[$i]	= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "20";
$FormLength[$i]  	= "20";

$i++;
$FormLabel[$i]   	= "* Nama";
$FormElement[$i] 	= "vendorName";
$FormType[$i]	= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckBlank");
$FormSize[$i]    	= "70";
$FormLength[$i]  	= "100";

$i++;
$FormLabel[$i]   	= "Alamat";
$FormElement[$i] 	= "address";
$FormType[$i]		= "textarea255";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "60";
$FormLength[$i]  	= "4";

$i++;
$FormLabel[$i]   	= "No Telefon";
$FormElement[$i] 	= "telNo";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "30";
$FormLength[$i]  	= "25";

$i++;
$FormLabel[$i]   	= "No Faksimili";
$FormElement[$i] 	= "faxNo";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "30";
$FormLength[$i]  	= "25";

$i++;
$FormLabel[$i]   	= "Email";
$FormElement[$i] 	= "email";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array("CheckEmailAddress");
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Orang Dihubungi";
$FormElement[$i] 	= "contactPerson";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "50";

$i++;
$FormLabel[$i]   	= "Nama Bank";
$FormElement[$i] 	= "bankName";
$FormType[$i]		= "textUcase";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "50";
$FormLength[$i]  	= "100";

$i++;
$FormLabel[$i]   	= "No Akaun";
$FormElement[$i] 	= "accNo";
$FormType[$i]		= "text";
$FormData[$i]   	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "20";
$FormLength[$i]  	= "20";

$i++;
$FormLabel[$i]   	= "Upload/Download Fail";
$FormElement[$i] 	= "isUD";
$FormType[$i]	  	= "checkbox";
$FormData[$i]    	= "";
$FormDataValue[$i]	= "";
$FormCheck[$i]   	= array();
$FormSize[$i]    	= "1";
$FormLength[$i]  	= "1";

if ($action == "update") {
	$i++;
	$FormLabel[$i]  	= "Tarikh Daftar";
	$FormElement[$i] 	= "createdDate";
	$FormType[$i]	  	= "hiddenDateTime";
	$FormData[$i]    	= "";
	$FormDataValue[$i]	= "";
	$FormCheck[$i]   	= array();
	$FormSize[$i]    	= "1";
	$FormLength[$i]  	= "1";

	$i++;
	$FormLabel[$i]  	= "Daftar Oleh";
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
	$FormLabel[$i]  	= "Kemaskini Oleh";
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
		for($j=0 ; $j < count($FormCheck[$i]); $j++) {
			FormValidation ($FormLabel[$i], 
							$FormElement[$i], 
							${$FormElement[$i]},  // FIX: Added curly braces
							$FormCheck[$i][$j],
							$i);
		}
	}	

/*	if ($oldBranch <> $branchName) {
		if (strtoupper(trim(dlookup("branch", "branchName", "branchName=" . tosql($branchName, "Text")))) == strtoupper($branchName)) {
			array_push ($strErrMsg, "branchName");
			print '- <font class=redText>Branch Name <b>( '.$branchName.' )</b> already exists. Please use another Branch Name.</font><br>';
		}
	} */

	//--- End   : Call function FormValidation ---  
	if (count($strErrMsg) == "0") {
//		$conn->debug=true;
		if (count($picNameArr) <> 0) 	$picName = implode('|', $picNameArr);
		if (count($picICArr) <> 0) 		$picIC = implode('|', $picICArr);
		$createdBy 	= $_SESSION['Session.userName'];
		$createdDate = date("Y-m-d H:i:s");             
		$updatedBy 	= $_SESSION['Session.userName'];
		$updatedDate = date("Y-m-d H:i:s");    
		$sSQL = "";
		switch(strtolower($SubmitForm)) 
		{
			case "daftar":
				$sSQL	= "INSERT INTO vendor (" . 
				          "vendorCode," . 
						  "vendorName,".			
						  "address,".		
						  "telNo,".	
						  "faxNo,".			
						  "email,".			
						  "contactPerson,".			
						  "bankName,".			
						  "accNo,".			
						  "picName,".			
						  "picIC,".			
						  "isUD,".			
				          "createdDate," . 
				          "createdBy," . 
				          "updatedDate," . 
				          "updatedBy)" . 
				          " VALUES (" . 
				          tosql($vendorCode, "Text") . "," .
				          tosql($vendorName, "Text") . ",".
				          tosql($address, "Text") . ",".
				          tosql($telNo, "Text") . ",".
				          tosql($faxNo, "Text") . ",".
				          tosql($email, "Text") . ",".
				          tosql($contactPerson, "Text") . ",".
						  tosql($bankName, "Text") . ",".
						  tosql($accNo, "Text") . ",".
				          tosql($picName, "Text") . ",".
				          tosql($picIC, "Text") . ",".
						  $isUD . "," .
						  tosql($createdDate, "Text") . "," .
				          tosql($createdBy, "Text") . ",".
						  tosql($updatedDate, "Text") . "," .
				          tosql($updatedBy, "Text") . ")";
				$msg = "Rekod telah didaftarkan ke dalam sistem";
			break;
			case "kemaskini":
			    $sWhere = "vendorID=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	vendor SET " .
				          "vendorCode		=" . tosql($vendorCode, "Text") .
				          ",vendorName	=" . tosql($vendorName, "Text").
						  ",address		=" . tosql($address, "Text").
						  ",telNo		=" . tosql($telNo, "Text").
						  ",faxNo		=" . tosql($faxNo, "Text").
						  ",email		=" . tosql($email, "Text").
						  ",contactPerson=" . tosql($contactPerson, "Text").
						  ",bankName	=" . tosql($bankName, "Text").
						  ",accNo		=" . tosql($accNo, "Text").
						  ",picName		=" . tosql($picName, "Text").
						  ",picIC		=" . tosql($picIC, "Text").
						  ",isUD		=" . $isUD .
						  ",updatedDate	=" . tosql($updatedDate, "Text") .
				          ",updatedBy	=" . tosql($updatedBy, "Text") ;
				$sSQL .= " where " . $sWhere;
				$msg = "Rekod dikemaskinikan";
			break;
		}
		$rs = $conn->Execute($sSQL);

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		if (strtolower($SubmitForm) == "daftar")
			$event = "Syarikat telah didaftarkan - ".$vendorName;
		else
			$event = "Syarikat telah dikemaskinikan - ".$vendorName;
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 				
		
		print '	<script>
					alert ("'.$msg.'");
					window.location.href = "'.$sActionFileName.'?'.$strURL.'";;
				</script>';
	}
}			
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

if ($action == "update") {
	if ($pk <> "") {
		//--- Begin : query database for information ---------------------------------------------
		$sSQL = "	SELECT 	* FROM  vendor WHERE 	vendorID = " . tosql($pk ,"Text");
		$rs = $conn->Execute($sSQL);
		if ($access1 == '0') { 
			$picNameArr = explode('|',$rs->fields('picName'));
			$picICArr 	= explode('|',$rs->fields('picIC'));
			$bilArr = Array();
			if (count($picNameArr) <> 0) {
				for ($a = 0; $a < count($picNameArr); $a++) {
					array_push ($bilArr, count($bilArr)+1);
				}
			}
		}
		//--- End   : query database for information ---------------------------------------------
	}
}

if (count($picNameArr) == 0) {
	$bilArr		= array();
	$picNameArr = array();
	$picICArr	= array();
	$newItem1 = 'add';
}
$access1++;
if ($newItem1 <> '') {
	$delItem1 = ''; 
	for ($a = 0; $a < $itemCount; $a++) {
		array_push ($bilArr, count($bilArr)+1);
		array_push ($picNameArr, '');
		array_push ($picICArr, '');
	}
}

if ($action == "add") {
	print '<form name="MyDHSB" action='.$sFileName.'?action='.$action.' method=post>';
} else {
	print '<form name="MyDHSB" action='.$sFileName.'?action='.$action.'&pk='.$pk.' method=post>';
}
print '
<input type="hidden" name="access1" value="'.$access1.'">
<input type="hidden" name="delItem1" value="'.$delItem1.'">
<input type="hidden" name="newItem1" value="">
<input type="hidden" name="strURL" value="'.$strURL.'">
<table border=0 cellpadding=3 cellspacing=1 width="800" align="center" class="lineBG">
	<tr class=Header>
		<td colspan="2" height="30" valign="middle"><b>';
		
if ($action == "add") print 'Daftar '.$Title; else print 'Kemaskini '.$Title.' : '.$rs->fields('vendorName');
print '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	if ($i ==11) print '<tr class="Header"><td colspan=2>Audit Informasi:</td></tr>';
	print '<tr valign=top><td class="Label" align=right width="200">'.$FormLabel[$i].' :</td>';
	if (in_array($FormElement[$i], $strErrMsg))
	  print '<td class=errdata>';
	else
	  print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($action == "update") { 
		$strFormValue = tohtml($rs->fields($FormElement[$i])); 
	} else {
		$strFormValue = ${$FormElement[$i]};  // FIX: Added curly braces
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
print '<tr class=Header>
			<td colspan=2>
				Nama Wakil: <input type="Text" name="itemCount" value="'.$itemCount.'" size="2" maxlength="2">
				<input type="button" value="Tambah Senarai Wakil" class="but" style="width:150px;" onclick="newLineList1();">
			</td>
		</tr>
		<tr class="Data">
			<td colspan="2">
			<table border="0" cellspacing="1" cellpadding="1" width="600" align="center" class="lineBG">
				<tr class="Label">
					<td width="1%">Bil</td>
					<td height="30">Nama Wakil</td>
					<td align="left">No KP</td>
				</tr>';
if (count($picNameArr) <> 0) {
	$bil=0; 
	for ($a = 0; $a < count($picNameArr); $a++) {
		if ($delItem1 <> $bilArr[$a]) {
			$bil++;
			print '	<input type="hidden" name="bilArr[]" value="'.$bil.'">
					<tr class="Data1">
						<td align="right">'.$bil.'.&nbsp;<img src="images/delete.gif" border="0" alt="Delete item" onclick=DeleteItem1("'.$bil.'");></td>
						<td>';
			print FormEntry("","picNameArr[]","textUcase",$picNameArr[$a],"","","50","80");		
			print '		</td>
						<td>';
			print FormEntry("","picICArr[]","textUcase",$picICArr[$a],"","","20","40");		
			print '		</td>	
				</tr>';
		}
	}
} 
print '		</table>
		</td>
	</tr>';
	
print '<tr><td colspan=2 align=center class=Data>';
if ($action == "add") {
	print '
	<input type=hidden name=branchID>
	<input type="hidden" name="oldBranch" value="">
	<input type=Submit name=SubmitForm class="but" value="Daftar">';
} else { 
    print '
	<input type=hidden name=branchID class="textFont" value='.$pk.'>
	<input type=hidden name=oldBranch value='.$rs->fields('vendorName').'>
	<input type=Submit name=SubmitForm class="but" value="Kemaskini">';
}
if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1")	{
	print '	<input type="Button" value="Cancel" class="but" onclick="window.location.href=\'index.php\';">';
} else {
	print '	<input type="Button" value="Kembali" class="but" onclick="window.location.href=\''.$sActionFileName.'\';">';
}
print '	</td>
		</tr>
</table>
</form>
<script>
    function newLineList1() {
		e = document.MyDHSB;
        e.newItem1.value = "add";
		e.submit();
	}	
    function DeleteItem1(id) {
		e = document.MyDHSB;
        if (confirm(\'Hapuskan rekod ini?\')) {
			e.delItem1.value = id;
	        e.submit();
        }
	}			
</script>';
include("footer.php");	
?>