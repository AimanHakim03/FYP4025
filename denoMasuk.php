<?php

include ("header.php");
include ("forms.php");
session_start();

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

// FIX FOR UNDEFINED VARIABLES - GET ALL POST/GET DATA
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$pk = isset($_GET['pk']) ? $_GET['pk'] : (isset($_POST['pk']) ? $_POST['pk'] : '');
$strURL = isset($_POST['strURL']) ? $_POST['strURL'] : (isset($_GET['strURL']) ? $_GET['strURL'] : '');
$SubmitForm = isset($_POST['SubmitForm']) ? $_POST['SubmitForm'] : '';

// Form fields
$bankInDate = isset($_POST['bankInDate']) ? $_POST['bankInDate'] : '';
$actualReplenishDate = isset($_POST['actualReplenishDate']) ? $_POST['actualReplenishDate'] : '';
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
$replenishPic = isset($_POST['replenishPic']) ? $_POST['replenishPic'] : '';
$replenishPic1 = isset($_FILES['replenishPic1']['name']) ? $_FILES['replenishPic1']['name'] : '';

$sFileName		= "denoMasuk.php";
$sActionFileName= "deno.php";
$Title 			= "PENGISIAN SEMULA MESIN ATM";

$path = dirname(__FILE__); 

$delDoc = isset($_POST['delDoc']) ? $_POST['delDoc'] : '';
$delID = isset($_POST['delID']) ? $_POST['delID'] : '';

$strErrMsg = Array();

$r100 = isset($_POST['r100']) ? $_POST['r100'] : 0;
$vr100 = isset($_POST['vr100']) ? $_POST['vr100'] : 0;
$r50 = isset($_POST['r50']) ? $_POST['r50'] : 0;
$vr50 = isset($_POST['vr50']) ? $_POST['vr50'] : 0;
$r20 = isset($_POST['r20']) ? $_POST['r20'] : 0;
$vr20 = isset($_POST['vr20']) ? $_POST['vr20'] : 0;
$r10 = isset($_POST['r10']) ? $_POST['r10'] : 0;
$vr10 = isset($_POST['vr10']) ? $_POST['vr10'] : 0;

$u100 = isset($_POST['u100']) ? $_POST['u100'] : 0;
$vu100 = isset($_POST['vu100']) ? $_POST['vu100'] : 0;
$u50 = isset($_POST['u50']) ? $_POST['u50'] : 0;
$vu50 = isset($_POST['vu50']) ? $_POST['vu50'] : 0;
$u20 = isset($_POST['u20']) ? $_POST['u20'] : 0;
$vu20 = isset($_POST['vu20']) ? $_POST['vu20'] : 0;
$u10 = isset($_POST['u10']) ? $_POST['u10'] : 0;
$vu10 = isset($_POST['vu10']) ? $_POST['vu10'] : 0;

$hu100 = isset($_POST['hu100']) ? $_POST['hu100'] : 0;
$hvu100 = isset($_POST['hvu100']) ? $_POST['hvu100'] : 0;
$hu50 = isset($_POST['hu50']) ? $_POST['hu50'] : 0;
$hvu50 = isset($_POST['hvu50']) ? $_POST['hvu50'] : 0;
$hu20 = isset($_POST['hu20']) ? $_POST['hu20'] : 0;
$hvu20 = isset($_POST['hvu20']) ? $_POST['hvu20'] : 0;
$hu10 = isset($_POST['hu10']) ? $_POST['hu10'] : 0;
$hvu10 = isset($_POST['hvu10']) ? $_POST['hvu10'] : 0;

$rvalue = isset($_POST['rvalue']) ? $_POST['rvalue'] : 0;
$uvalue = isset($_POST['uvalue']) ? $_POST['uvalue'] : 0;
$huvalue = isset($_POST['huvalue']) ? $_POST['huvalue'] : 0;
$vValue = isset($_POST['vValue']) ? $_POST['vValue'] : 0;
$vDesc = isset($_POST['vDesc']) ? $_POST['vDesc'] : '';
$vValue1 = isset($_POST['vValue1']) ? $_POST['vValue1'] : 0;
$vDesc1 = isset($_POST['vDesc1']) ? $_POST['vDesc1'] : '';
$vValue2 = isset($_POST['vValue2']) ? $_POST['vValue2'] : 0;

$err2 = '';
$bankInDate1 = '';
$actualReplenishDate1 = '';

if ($SubmitForm <> "") {

	if (count($strErrMsg) == "0") {
		if ($bankInDate <>'') $bankInDate1 	= substr($bankInDate,6,4).'-'.substr($bankInDate,3,2).'-'.substr($bankInDate,0,2);
		if ($actualReplenishDate <>'') $actualReplenishDate1 = substr($actualReplenishDate,6,4).'-'.substr($actualReplenishDate,3,2).'-'.substr($actualReplenishDate,0,2);

		$sSQL = "";
		$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));
		
		switch(strtolower($SubmitForm)) {
			case "simpan":
				$rValue = $r100*100 + $r50*50 + $r20*20 + $r10*10;
				$uValue = $u100*100 + $u50*50 + $u20*20 + $u10*10;
				$huValue = $hu100*100 + $hu50*50 + $hu20*20 + $hu10*10;
			    $sWhere = "instructID	=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	instruct SET " .
						  " status			= '2' " .
						  ",replenish100	=" . tosql($r100, "Text") .
						  ",replenish50		=" . tosql($r50, "Text") .
						  ",replenish20		=" . tosql($r20, "Text") .
						  ",replenish10		=" . tosql($r10, "Text") .
						  ",replenishValue	=" . tosql(number_format($rValue,2,'.',''), "Text") .
						  ",unload100		=" . tosql($u100, "Text") .
						  ",unload50		=" . tosql($u50, "Text") .
						  ",unload20		=" . tosql($u20, "Text") .
						  ",unload10		=" . tosql($u10, "Text") .
						  ",unloadValue		=" . tosql(number_format($uValue,2,'.',''), "Text") .
						  ",host100			=" . tosql($hu100, "Text") .
						  ",host50			=" . tosql($hu50, "Text") .
						  ",host20			=" . tosql($hu20, "Text") .
						  ",host10			=" . tosql($hu10, "Text") .
						  ",hostValue		=" . tosql(number_format($huValue,2,'.',''), "Text") .
						  ",bankInDate		=" . tosql($bankInDate1, "Text") .
						  ",actualReplenishDate	=" . tosql($actualReplenishDate1, "Text") .
						  ",remarks			=" . tosql($remarks, "Text") .
						  ",replenishPic	=" . tosql($replenishPic, "Text") .
						  ",unloadDate		=" . tosql(date("Y-m-d H:i:s"), "Text") .
						  ",replenishDate	=" . tosql(date("Y-m-d H:i:s"), "Text") .
						  ",replenishBy		=" . tosql($_SESSION['Session.userName'], "Text") ;

						  $sSQL .= " WHERE " . $sWhere;
				$rs = $conn->Execute($sSQL);
				$msg = "Rekod Replenish/Unload ".$docNo." telah dikemaskini";
			break;
			case "selesai":
			    $sWhere = "instructID	=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	instruct SET " .
						  " status			= '3' " .
						  ",replenishDate	=" . tosql(date("Y-m-d H:i:s"), "Text") .
				          ",replenishBy		=" . tosql($_SESSION['Session.userName'], "Text") ;
				$sSQL .= " WHERE " . $sWhere;
				$rs = $conn->Execute($sSQL);
				$msg = "Rekod Replenish/Unload ".$docNo." telah selesai";
			break;
			case "hantar":
			    $sWhere = "instructID	=" . tosql($pk, "Text");
	        	$sSQL	= "UPDATE	instruct SET " .
						  " status			= '4' " .
						  ",approvedDate	=" . tosql(date("Y-m-d H:i:s"), "Text") .
				          ",approvedBy		=" . tosql($_SESSION['Session.userName'], "Text") ;
				$sSQL .= " WHERE " . $sWhere;
				$rs = $conn->Execute($sSQL);
				$msg = "Rekod Replenish/Unload ".$docNo." telah diluluskan dan dihantar ke Pusat ATM";
			break;
		}

		if ($rs === false) { 
			print '<p align="center"><hr size="1" color="#999999">
			<b class="textFont">Rekod '.$Title.' : Daftar/Kemaskini - ralat : '.$conn->ErrorMsg().'<br>'.$sSQL.'<br>- Sila hubungi Pihak Pentadbir Sistem -<br></b>
			<hr size="1" color="#999999"></p>';
			exit; 
		}

		$user  = $_SESSION['Session.userName'];
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $msg, $type);

		print '<script>
					alert ("'.$msg.'");
					window.location.href="'. $sActionFileName.'?'.$strURL.'";
				</script>';
	}
}			

//--- BEGIN	:	HANDLING FAIL ATTACHMENT
if ($delDoc <> '') {
	unlink($path."\\scanned\\".$delDoc);
	$delDoc='';
}
if ($replenishPic1 <> '') {
	$allowedExtension = array("pdf","PDF","jpg","JPG"); 
	
	$fileName = $_FILES['replenishPic1']['name'];
   	$tmpName  = $_FILES['replenishPic1']['tmp_name'];
   	$fileSize = $_FILES['replenishPic1']['size'];
   	$fileType = $_FILES['replenishPic1']['type'];

	$fileExt = explode('.', $_FILES['replenishPic1']['name']);
	if (!in_array($fileExt[1], $allowedExtension)) {
		$err2 = '<br><span class="error-text">* Filename not allowed to upload ('.$fileName.') only pdf and jpg.</span>';
	} elseif ($fileSize > 30000000) {
		$err2 = '<br><span class="error-text">* Document size ('.$fileName.') must be less than 3000KB.</span>';
	} else {
		$fp = @fopen($tmpName,"r");
 		$buffer = "";
		if(!$fp){
			$err2 = '<br><span class="error-text">* ERROR opening file on server.</span>';
		} else {
			while(!feof($fp)) {
   	   			$buffer .= fgets($fp,4096);
			}
			$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));
			$fileExt = pathinfo($_FILES['replenishPic1']['name'],PATHINFO_EXTENSION);	
			$fileName = $docNo.".".$fileExt;

			file_put_contents($path."\\scanned\\".$fileName,$buffer);
			$replenishPic = $fileName;
		}
	}
}

// Initialize display variables
$instructID = '';
$instructDate = '';
$docNo = '';
$atmID = '';
$atmNo = '';
$vendorID = '';
$vendorName = '';
$qty100 = 0; $value100 = 0;
$qty50 = 0; $value50 = 0;
$qty20 = 0; $value20 = 0;
$qty10 = 0; $value10 = 0;
$jumlah = 0;
$status = '';
$trvalue = 0;
$tuvalue = 0;
$htuvalue = 0;
$fullName = '';
$contactNo = '';
$email = '';
$position = '';
$replenishBy = '';
$replenishDate = '';
$createdBy = '';
$createdDate = '';
$updatedBy = '';
$updatedDate = '';
$verifiedBy = '';
$verifiedDate = '';
$acceptedBy = '';
$acceptedDate = '';
$approvedBy = '';
$approvedDate = '';
$err5 = '';
$err6 = '';
$err7 = '';

if ($action == "update"){
	if ($pk <> "") {
		$sSQL = "	SELECT * FROM instruct WHERE instructID = " . tosql($pk ,"Text");
		$rs = $conn->Execute($sSQL);
		
		$instructID 	= $rs->fields('instructID');
		$instructDate  	= todate('d/m/Y',$rs->fields('instructDate'));
		$docNo			= $rs->fields('docNo');
		$atmID	 		= $rs->fields('atmID');
		$atmNo		 	= dlookup("atm", "atmNo", "atmID=" .tosql($rs->fields('atmID'), "Text")) . " - ".
						  dlookup("atm", "atmLoc", "atmID=" .tosql($rs->fields('atmID'), "Text"));
		$vendorID 		= $rs->fields('vendorID');
		$vendorName 	= dlookup("vendor", "vendorName", "vendorID=" .tosql($rs->fields('vendorID'), "Text"));
		$qty100 		= $rs->fields('qty100');
		$value100 		= $rs->fields('qty100')*100;
		$qty50	 		= $rs->fields('qty50');
		$value50 		= $rs->fields('qty50')*50;
		$qty20 			= $rs->fields('qty20');
		$value20 		= $rs->fields('qty20')*20;
		$qty10	 		= $rs->fields('qty10');
		$value10 		= $rs->fields('qty10')*10;
		$jumlah			= $rs->fields('totalValue');
		$status	 		= $rs->fields('status');
		
		$r100			= $rs->fields('replenish100');
		$r50			= $rs->fields('replenish50');
		$r20			= $rs->fields('replenish20');
		$r10			= $rs->fields('replenish10');
		$vr100 			= $rs->fields('replenish100')*100;
		$vr50 			= $rs->fields('replenish50')*50;
		$vr20 			= $rs->fields('replenish20')*20;
		$vr10 			= $rs->fields('replenish10')*10;
		$trvalue		= $rs->fields('replenishValue');
		
		$u100			= $rs->fields('unload100');
		$u50			= $rs->fields('unload50');
		$u20			= $rs->fields('unload20');
		$u10			= $rs->fields('unload10');
		$vu100 			= $rs->fields('unload100')*100;
		$vu50 			= $rs->fields('unload50')*50;
		$vu20 			= $rs->fields('unload20')*20;
		$vu10 			= $rs->fields('unload10')*10;
		$tuvalue		= $rs->fields('unloadValue');
		
		$hu100			= $rs->fields('host100');
		$hu50			= $rs->fields('host50');
		$hu20			= $rs->fields('host20');
		$hu10			= $rs->fields('host10');
		$hvu100 		= $rs->fields('host100')*100;
		$hvu50 			= $rs->fields('host50')*50;
		$hvu20 			= $rs->fields('host20')*20;
		$hvu10 			= $rs->fields('host10')*10;
		$htuvalue		= $rs->fields('hostValue');
		
		$vValue			= $rs->fields('unloadValue') - $rs->fields('hostValue');
		if ($vValue <> '0') {
			if ($vValue > 0) {
				$vDesc = 'excess';
			} else {
				$vDesc = 'short';
			}
		}
		$vValue1		= $rs->fields('totalValue') - $rs->fields('replenishValue');
		$vValue2		= $tuvalue;
		
		if ($vValue1 <> '0') {
			if ($vValue1 > 0) {
				$vDesc1 = 'excess';
				$vValue2 = $tuvalue + $vValue1;
			} else {
				$vDesc1 = 'short';
			}
		} else {
			$vDesc1 = '';
		}
		
		$bankInDate  	= todate('d/m/Y',$rs->fields('bankInDate'));
		$actualReplenishDate  	= todate('d/m/Y',$rs->fields('actualReplenishDate'));
		$remarks  	= $rs->fields('remarks');
		$sSQL1 = "	SELECT fullName, position, contactNo, email, hqID FROM users  
					WHERE userName = " . tosql($rs->fields('createdBy') ,"Text");
		$rs1 = $conn->Execute($sSQL1);
		
		$fullName		= $rs1->fields('fullName');
		$contactNo		= $rs1->fields('contactNo');
		$email			= $rs1->fields('email');
		$position		= $rs1->fields('position');

		$replenishBy	= $rs->fields('replenishBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('replenishBy'), "Text"));
		$replenishDate 	= toDate("d/m/Y H:i:s A",$rs->fields('replenishDate'));		
		$createdBy		= $rs->fields('createdBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('createdBy'), "Text"));
		$createdDate 	= toDate("d/m/Y H:i:s A",$rs->fields('createdDate'));		
		$updatedBy		= $rs->fields('updatedBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('updatedBy'), "Text"));
		$updatedDate 	= toDate("d/m/Y H:i:s A",$rs->fields('updatedDate'));		
		$verifiedBy		= $rs->fields('verifiedBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('verifiedBy'), "Text"));
		$verifiedDate 	= toDate("d/m/Y H:i:s A",$rs->fields('verifiedDate'));		
		$acceptedBy		= $rs->fields('acceptedBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('acceptedBy'), "Text"));
		$acceptedDate 	= toDate("d/m/Y H:i:s A",$rs->fields('acceptedDate'));		
		$approvedBy		= $rs->fields('approvedBy').'-'.dlookup("users", "fullName", "userName=" .tosql($rs->fields('approvedBy'), "Text"));
		$approvedDate 	= toDate("d/m/Y H:i:s A",$rs->fields('approvedDate'));		

		if ($replenishPic =='') $replenishPic	= $rs->fields('replenishPic');
	}
}

if (($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") AND $status=='2')	{
	$inputText = "text";
	$inputDate = "date";
	$inputTextarea100 = "textarea100";
	$inputFile = "file";
	$inputSelect = "select";
	$inputRadio = "radio";
	$readonly = "";
} else {
	$inputText = "hidden";
	$inputDate = "hidden";
	$inputTextarea100 = "hidden";
	$inputFile = "hidden";
	$inputSelect = "hidden";
	$inputRadio = "hidden";
	$readonly = "readonly";	
}

?>

<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

.modern-container {
	max-width: 1400px;
	margin: 0 auto;
	padding: 24px;
	background: #f5f7fa;
	min-height: 100vh;
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.page-header {
	background: white;
	border-radius: 16px;
	padding: 24px 32px;
	margin-bottom: 24px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.page-title {
	font-size: 28px;
	font-weight: 700;
	color: #1a1a1a;
	margin-bottom: 8px;
}

.page-subtitle {
	font-size: 16px;
	color: #64748b;
	font-weight: 500;
}

.info-grid {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 16px;
	background: white;
	border-radius: 16px;
	padding: 24px;
	margin-bottom: 24px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.info-item {
	display: flex;
	flex-direction: column;
	gap: 6px;
}

.info-label {
	font-size: 13px;
	color: #64748b;
	font-weight: 500;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.info-value {
	font-size: 15px;
	color: #1a1a1a;
	font-weight: 600;
}

.denomination-grid {
	background: white;
	border-radius: 16px;
	padding: 24px;
	margin-bottom: 24px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
	overflow-x: auto;
}

.denom-table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0;
}

.denom-table thead th {
	padding: 12px 16px;
	text-align: center;
	font-size: 12px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	border-bottom: 2px solid #e2e8f0;
	position: sticky;
	top: 0;
	background: white;
}

.header-arahan { color: #475569; }
.header-replenish { color: #16a34a; background: #f0fdf4; }
.header-unload { color: #dc2626; background: #fef2f2; }

.denom-table tbody td {
	padding: 16px;
	text-align: center;
	border-bottom: 1px solid #f1f5f9;
}

.denom-label {
	font-weight: 700;
	color: #1a1a1a;
	font-size: 15px;
	text-align: right;
	padding-right: 24px !important;
}

.denom-value {
	font-weight: 600;
	color: #475569;
	font-size: 14px;
}

.denom-input {
	width: 100%;
	padding: 10px 12px;
	border: 2px solid #e2e8f0;
	border-radius: 8px;
	font-size: 14px;
	font-weight: 600;
	text-align: right;
	transition: all 0.2s;
	background: white;
}

.denom-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.denom-input:disabled, .denom-input[readonly] {
	background: #f8fafc;
	color: #475569;
	border-color: #f1f5f9;
	cursor: not-allowed;
}

.total-row td {
	font-weight: 700;
	font-size: 15px;
	padding: 20px 16px !important;
	border-top: 2px solid #e2e8f0;
	background: #f8fafc;
}

.variant-badge {
	display: inline-block;
	padding: 4px 12px;
	border-radius: 6px;
	font-size: 12px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.variant-excess {
	background: #fef2f2;
	color: #dc2626;
}

.variant-short {
	background: #fff7ed;
	color: #ea580c;
}

.additional-info {
	background: white;
	border-radius: 16px;
	padding: 24px;
	margin-bottom: 24px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.form-row {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 20px;
	margin-bottom: 20px;
}

.form-group {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.form-label {
	font-size: 13px;
	color: #475569;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.form-input, .form-textarea {
	padding: 12px 16px;
	border: 2px solid #e2e8f0;
	border-radius: 8px;
	font-size: 14px;
	font-weight: 500;
	transition: all 0.2s;
	font-family: inherit;
}

.form-input:focus, .form-textarea:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
	resize: vertical;
	min-height: 100px;
}

.file-upload-area {
	border: 2px dashed #e2e8f0;
	border-radius: 12px;
	padding: 24px;
	text-align: center;
	background: #f8fafc;
	transition: all 0.2s;
}

.file-upload-area:hover {
	border-color: #3b82f6;
	background: #eff6ff;
}

.file-link {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 600;
	font-size: 14px;
}

.file-link:hover {
	text-decoration: underline;
}

.error-text {
	color: #dc2626;
	font-size: 13px;
	font-weight: 600;
	margin-top: 8px;
	display: block;
}

.audit-section {
	background: white;
	border-radius: 16px;
	padding: 24px;
	margin-bottom: 24px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.audit-title {
	font-size: 18px;
	font-weight: 700;
	color: #1a1a1a;
	margin-bottom: 20px;
	padding-bottom: 16px;
	border-bottom: 2px solid #f1f5f9;
}

.audit-grid {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 16px;
}

.audit-item {
	display: flex;
	flex-direction: column;
	gap: 6px;
	padding: 12px;
	background: #f8fafc;
	border-radius: 8px;
}

.audit-label {
	font-size: 12px;
	color: #64748b;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.audit-value {
	font-size: 14px;
	color: #1a1a1a;
	font-weight: 600;
}

.action-buttons {
	display: flex;
	justify-content: center;
	gap: 12px;
	padding: 24px;
	background: white;
	border-radius: 16px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.btn {
	padding: 12px 32px;
	border: none;
	border-radius: 10px;
	font-size: 14px;
	font-weight: 700;
	cursor: pointer;
	transition: all 0.2s;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:active {
	transform: translateY(0);
}

.btn-back {
	background: #64748b;
	color: white;
}

.btn-save {
	background: #3b82f6;
	color: white;
}

.btn-complete {
	background: #16a34a;
	color: white;
}

.btn-submit {
	background: #8b5cf6;
	color: white;
}

@media (max-width: 768px) {
	.info-grid, .form-row, .audit-grid {
		grid-template-columns: 1fr;
	}
	
	.action-buttons {
		flex-direction: column;
	}
	
	.btn {
		width: 100%;
	}
}
</style>

<form name="MyDHSB" action="<?php echo $sFileName; ?>?action=<?php echo $action; ?>&pk=<?php echo $pk; ?>" method="post" enctype="multipart/form-data" onSubmit="submitonce(this)"> 

<input type="hidden" name="strURL" value="<?php echo $strURL; ?>">
<input type="hidden" name="delDoc" value="<?php echo $delDoc; ?>">
<input type="hidden" name="delID" value="<?php echo $delID; ?>">
<input type="hidden" name="docNo" value="<?php echo $docNo; ?>">

<div class="modern-container">
	
	<!-- Page Header -->
	<div class="page-header">
		<h1 class="page-title"><?php echo $Title; ?></h1>
		<p class="page-subtitle"><?php echo $atmNo; ?></p>
	</div>

	<!-- Document & Officer Info -->
	<div class="info-grid">
		<div class="info-item">
			<span class="info-label">No Dokumen</span>
			<span class="info-value"><?php echo $docNo; ?></span>
		</div>
		<div class="info-item">
			<span class="info-label">Tarikh Pengisian</span>
			<span class="info-value"><?php echo $instructDate; ?></span>
		</div>
		<div class="info-item">
			<span class="info-label">Nama Pegawai</span>
			<span class="info-value"><?php echo $createdBy; ?></span>
		</div>
		<div class="info-item">
			<span class="info-label">Tarikh Arahan Dikeluarkan</span>
			<span class="info-value"><?php echo $createdDate; ?></span>
		</div>
		<div class="info-item">
			<span class="info-label">Email</span>
			<span class="info-value"><?php echo $email; ?></span>
		</div>
		<div class="info-item">
			<span class="info-label">No Dihubungi</span>
			<span class="info-value"><?php echo $contactNo; ?></span>
		</div>
	</div>

	<!-- Denomination Table -->
	<div class="denomination-grid">
		<table class="denom-table">
			<thead>
				<tr>
					<th rowspan="2" style="width: 150px;">DENOMINASI</th>
					<th colspan="2" class="header-arahan">ARAHAN</th>
					<th colspan="2" class="header-replenish">REPLENISH</th>
					<th colspan="2" class="header-unload">UNLOAD (FIZIKAL)</th>
					<th colspan="2" class="header-unload">UNLOAD (LOCAL)</th>
				</tr>
				<tr>
					<th class="header-arahan">KEPING</th>
					<th class="header-arahan">JUMLAH (RM)</th>
					<th class="header-replenish">KEPING</th>
					<th class="header-replenish">JUMLAH (RM)</th>
					<th class="header-unload">KEPING</th>
					<th class="header-unload">JUMLAH (RM)</th>
					<th class="header-unload">KEPING</th>
					<th class="header-unload">JUMLAH (RM)</th>
				</tr>
			</thead>
			<tbody>
				<!-- RM 100 -->
				<tr>
					<td class="denom-label">RM 100</td>
					<td class="denom-value"><?php echo $qty100; ?></td>
					<td class="denom-value"><?php echo number_format($value100,2); ?></td>
					<td>
						<input type="text" name="r100" value="<?php echo $r100; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="1">
					</td>
					<td>
						<input type="text" name="vr100" value="<?php echo number_format($vr100,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="u100" value="<?php echo $u100; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="5">
					</td>
					<td>
						<input type="text" name="vu100" value="<?php echo number_format($vu100,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="hu100" value="<?php echo $hu100; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="9">
					</td>
					<td>
						<input type="text" name="hvu100" value="<?php echo number_format($hvu100,2); ?>" class="denom-input" readonly>
					</td>
				</tr>

				<!-- RM 50 -->
				<tr>
					<td class="denom-label">RM 50</td>
					<td class="denom-value"><?php echo $qty50; ?></td>
					<td class="denom-value"><?php echo number_format($value50,2); ?></td>
					<td>
						<input type="text" name="r50" value="<?php echo $r50; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="2">
					</td>
					<td>
						<input type="text" name="vr50" value="<?php echo number_format($vr50,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="u50" value="<?php echo $u50; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="6">
					</td>
					<td>
						<input type="text" name="vu50" value="<?php echo number_format($vu50,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="hu50" value="<?php echo $hu50; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="10">
					</td>
					<td>
						<input type="text" name="hvu50" value="<?php echo number_format($hvu50,2); ?>" class="denom-input" readonly>
					</td>
				</tr>

				<!-- RM 20 -->
				<tr>
					<td class="denom-label">RM 20</td>
					<td class="denom-value"><?php echo $qty20; ?></td>
					<td class="denom-value"><?php echo number_format($value20,2); ?></td>
					<td>
						<input type="text" name="r20" value="<?php echo $r20; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="3">
					</td>
					<td>
						<input type="text" name="vr20" value="<?php echo number_format($vr20,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="u20" value="<?php echo $u20; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="7">
					</td>
					<td>
						<input type="text" name="vu20" value="<?php echo number_format($vu20,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="hu20" value="<?php echo $hu20; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="11">
					</td>
					<td>
						<input type="text" name="hvu20" value="<?php echo number_format($hvu20,2); ?>" class="denom-input" readonly>
					</td>
				</tr>

				<!-- RM 10 -->
				<tr>
					<td class="denom-label">RM 10</td>
					<td class="denom-value"><?php echo $qty10; ?></td>
					<td class="denom-value"><?php echo number_format($value10,2); ?></td>
					<td>
						<input type="text" name="r10" value="<?php echo $r10; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="4">
					</td>
					<td>
						<input type="text" name="vr10" value="<?php echo number_format($vr10,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="u10" value="<?php echo $u10; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="8">
					</td>
					<td>
						<input type="text" name="vu10" value="<?php echo number_format($vu10,2); ?>" class="denom-input" readonly>
					</td>
					<td>
						<input type="text" name="hu10" value="<?php echo $hu10; ?>" class="denom-input" maxlength="10" onchange="autoCalc();" <?php echo $readonly; ?> tabindex="12">
					</td>
					<td>
						<input type="text" name="hvu10" value="<?php echo number_format($hvu10,2); ?>" class="denom-input" readonly>
					</td>
				</tr>

				<!-- TOTAL ROW -->
				<tr class="total-row">
					<td class="denom-label">JUMLAH (RM)</td>
					<td></td>
					<td>
						<input type="text" name="jumlah" value="<?php echo number_format($jumlah,2); ?>" class="denom-input" readonly>
					</td>
					<td></td>
					<td>
						<input type="text" name="trvalue" value="<?php echo number_format($trvalue,2); ?>" class="denom-input" readonly>
					</td>
					<td></td>
					<td>
						<input type="text" name="tuvalue" value="<?php echo number_format($tuvalue,2); ?>" class="denom-input" readonly>
					</td>
					<td></td>
					<td>
						<input type="text" name="htuvalue" value="<?php echo number_format($htuvalue,2); ?>" class="denom-input" readonly>
					</td>
				</tr>

				<!-- VARIANCE ROW -->
				<tr>
					<td class="denom-label">VARIAN</td>
					<td colspan="2"></td>
					<td>
						<?php if($vDesc1) { ?>
							<span class="variant-badge variant-<?php echo $vDesc1; ?>"><?php echo strtoupper($vDesc1); ?></span>
						<?php } ?>
					</td>
					<td>
						<input type="text" name="vValue1" value="<?php echo number_format($vValue1,2); ?>" class="denom-input" readonly>
						<input type="hidden" name="vDesc1" value="<?php echo $vDesc1; ?>">
					</td>
					<td colspan="2" style="text-align: right; padding-right: 24px; font-weight: 700; color: #64748b;">
						<?php if($vDesc) { ?>
							<span class="variant-badge variant-<?php echo $vDesc; ?>"><?php echo strtoupper($vDesc); ?></span>
						<?php } ?>
					</td>
					<td></td>
					<td>
						<input type="text" name="vValue" value="<?php echo number_format($vValue,2); ?>" class="denom-input" readonly>
						<input type="hidden" name="vDesc" value="<?php echo $vDesc; ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- Additional Information -->
	<div class="additional-info">
		<div class="form-row">
			<div class="form-group">
				<label class="form-label">Tarikh Sebenar Replenish</label>
				<?php echo FormEntry("","actualReplenishDate","date",$actualReplenishDate,"","","12","10").$err5; ?>
			</div>
			<div class="form-group">
				<label class="form-label">Tarikh Bank In</label>
				<?php echo FormEntry("","bankInDate","date",$bankInDate,"","","12","10").$err7; ?>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group">
				<label class="form-label">Remarks</label>
				<?php echo FormEntry("","remarks","textarea",$remarks,"","","60","4").$err6; ?>
			</div>
			<div class="form-group">
				<label class="form-label">Jumlah Bank-in</label>
				<input type="text" name="vValue2" value="<?php echo number_format($vValue2,2); ?>" class="form-input" readonly>
			</div>
		</div>

		<div class="form-group">
			<label class="form-label">Slip Keluaran/Masukan</label>
			<div class="file-upload-area">
				<?php 
				$filename = $path.'\\scanned\\'.$replenishPic;
				if (file_exists($filename)) {
					if ($replenishPic <> '') {
						if (($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") AND $status=='2') {		
							echo '<img src="images/delete.gif" border="0" onclick="DeleteDoc(\''.$replenishPic.'\');" title="Delete File" style="cursor: pointer; margin-right: 12px;">';
						}
						echo '<a href="scanned/'.$replenishPic.'" target="new" class="file-link">📄 '.$replenishPic.'</a>';
						echo '<input type="hidden" name="replenishPic" value="'.$replenishPic.'">';
					} else {
						echo '<p style="color: #64748b;">No File Attached</p>'; 
					}
				} else {
					echo '<p style="color: #64748b;">No File Attached</p>'; 
				}
				?>
				<br><br>
				<input type="<?php echo $inputFile; ?>" name="replenishPic1" onchange="document.MyDHSB.submit();" style="margin-top: 12px;">
				<?php echo $err2; ?>
			</div>
		</div>
	</div>

	<!-- Audit Section -->
	<div class="audit-section">
		<h2 class="audit-title">AUDIT INFORMASI</h2>
		<div class="audit-grid">
			<div class="audit-item">
				<span class="audit-label">Status</span>
				<span class="audit-value"><?php echo $statusList[array_search($status, $statusVal)]; ?></span>
			</div>
			<div class="audit-item">
				<span class="audit-label">Diterima Oleh</span>
				<span class="audit-value"><?php echo $acceptedBy; ?><br><small style="color: #64748b;"><?php echo $acceptedDate; ?></small></span>
			</div>
			<div class="audit-item">
				<span class="audit-label">Arahan Oleh</span>
				<span class="audit-value"><?php echo $createdBy; ?><br><small style="color: #64748b;"><?php echo $createdDate; ?></small></span>
			</div>
			<div class="audit-item">
				<span class="audit-label">Direkod Oleh</span>
				<span class="audit-value"><?php echo $replenishBy; ?><br><small style="color: #64748b;"><?php echo $replenishDate; ?></small></span>
			</div>
			<div class="audit-item">
				<span class="audit-label">Disahkan Oleh</span>
				<span class="audit-value"><?php echo $verifiedBy; ?><br><small style="color: #64748b;"><?php echo $verifiedDate; ?></small></span>
			</div>
			<div class="audit-item">
				<span class="audit-label">Diluluskan Oleh</span>
				<span class="audit-value"><?php echo $approvedBy; ?><br><small style="color: #64748b;"><?php echo $approvedDate; ?></small></span>
			</div>
		</div>
	</div>

	<!-- Action Buttons -->
	<div class="action-buttons">
		<?php
		if ($action == "update") {
			echo '<input type="hidden" name="instructID" value="'.$pk.'">';
			echo '<input type="button" class="btn btn-back" value="Kembali" onclick="window.location.href=\''.$sActionFileName.'?'.$strURL.'\';">';

			if (($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") AND $status=='2') {
				echo '<input type="submit" name="SubmitForm" class="btn btn-save" value="Simpan">';
				echo '<input type="submit" name="SubmitForm" class="btn btn-complete" value="Selesai">';
			}
			if ($_SESSION['Session.groupID'] == "1" AND $status == '3') { 
				echo '<input type="submit" name="SubmitForm" class="btn btn-submit" value="Hantar">';
			}
		}
		?>
	</div>

</div>

</form>

<script>	
	function DeleteDoc(id) {
		e = document.MyDHSB;
        if (confirm('Delete file ' + id +'?')) {
			e.delDoc.value = id;
		    e.submit();
        }
	}	

	function autoCalc() {
		e = document.MyDHSB;	
		TVR=0;
		TVU=0;
		THVU=0;
		for(c=0; c<e.elements.length; c++) {
			if (e.elements[c].name=="r100") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vr100.value = (eval(e.elements[c].value) * 100).toFixed(2);
				TVR += eval(e.vr100.value);
		    }

			if (e.elements[c].name=="r50") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vr50.value = (eval(e.elements[c].value) * 50).toFixed(2);
				TVR += eval(e.vr50.value);
		    }
			
			if (e.elements[c].name=="r20") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vr20.value = (eval(e.elements[c].value) * 20).toFixed(2);
				TVR += eval(e.vr20.value);
		    }
			
			if (e.elements[c].name=="r10") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vr10.value = (eval(e.elements[c].value) * 10).toFixed(2);
				TVR += eval(e.vr10.value);
		    }

			if (e.elements[c].name=="u100") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vu100.value = (eval(e.elements[c].value) * 100).toFixed(2);
				TVU += eval(e.vu100.value);
		    }

			if (e.elements[c].name=="u50") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vu50.value = (eval(e.elements[c].value) * 50).toFixed(2);
				TVU += eval(e.vu50.value);
		    }

			if (e.elements[c].name=="u20") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vu20.value = (eval(e.elements[c].value) * 20).toFixed(2);
				TVU += eval(e.vu20.value);
		    }
			if (e.elements[c].name=="u10") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.vu10.value = (eval(e.elements[c].value) * 10).toFixed(2);
				TVU += eval(e.vu10.value);
		    }
			
			if (e.elements[c].name=="hu100") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.hvu100.value = (eval(e.elements[c].value) * 100).toFixed(2);
				THVU += eval(e.hvu100.value);
		    }

			if (e.elements[c].name=="hu50") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.hvu50.value = (eval(e.elements[c].value) * 50).toFixed(2);
				THVU += eval(e.hvu50.value);
		    }

			if (e.elements[c].name=="hu20") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.hvu20.value = (eval(e.elements[c].value) * 20).toFixed(2);
				THVU += eval(e.hvu20.value);
		    }
			if (e.elements[c].name=="hu10") {
				if (isNaN(e.elements[c].value) || e.elements[c].value == "" || e.elements[c].value == " ") {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
				}
				e.hvu10.value = (eval(e.elements[c].value) * 10).toFixed(2);
				THVU += eval(e.hvu10.value);
		    }
		}
		e.trvalue.value = TVR.toFixed(2);
		e.tuvalue.value = TVU.toFixed(2);
		e.htuvalue.value = THVU.toFixed(2);

		e.vValue.value = (e.tuvalue.value - e.htuvalue.value).toFixed(2);	
		if (e.vValue.value != 0) {
			if (e.vValue.value > 0) { 
				e.vDesc.value = "excess";
			} else {
				e.vDesc.value = "short";
			}
		} else {
			e.vDesc.value = "";
		}
		
		e.vValue1.value = (e.jumlah.value.replace(/,/g, '') - e.trvalue.value).toFixed(2);
		e.vValue2.value = parseFloat(e.tuvalue.value).toFixed(2);
		
		if (e.vValue1.value != 0) {
			if (e.vValue1.value > 0) { 
				e.vDesc1.value = "excess";
				e.vValue2.value = (parseFloat(e.tuvalue.value) + parseFloat(e.vValue1.value)).toFixed(2);
			} else {
				e.vDesc1.value = "short";	
			}
		} else {
			e.vDesc1.value = "";
		}	
	}

</script>

<?php
include('footer.php');
?>