<?php

include("common.php");
session_start();
include("setupinfo.php");

//$conn->debug=true;

// FIX FOR UNDEFINED VARIABLES - GET ALL POST/GET DATA
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$vid = isset($_POST['vid']) ? $_POST['vid'] : (isset($_GET['vid']) ? $_GET['vid'] : '');
$dt = isset($_POST['dt']) ? $_POST['dt'] : (isset($_GET['dt']) ? $_GET['dt'] : '');
$dt1 = isset($_POST['dt1']) ? $_POST['dt1'] : (isset($_GET['dt1']) ? $_GET['dt1'] : '');
$msg = '';

$path = dirname(__FILE__); 
$ListThisDir = dirname(__FILE__).'\\files\\'; 
//print $ListThisDir;

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}
if ($vid == "" OR $dt == "" OR $dt1 == "") {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

if ($action <> "") {
	$fileName = isset($_FILES['uploadFile']['name']) ? $_FILES['uploadFile']['name'] : '';
   	$tmpName = isset($_FILES['uploadFile']['tmp_name']) ? $_FILES['uploadFile']['tmp_name'] : '';
   	$fileSize = isset($_FILES['uploadFile']['size']) ? $_FILES['uploadFile']['size'] : 0;
   	$fileType = isset($_FILES['uploadFile']['type']) ? $_FILES['uploadFile']['type'] : '';
	
	if ($fileName != '') {
		$fileExt = explode('.', $fileName);
		$toName = '2-'.DATE('Ymdhms').'.'.$fileExt[1]; 
		
		$uploadFile =  $tmpName;
	//	$toFile = $ListThisDir.'\\'.$fileName;  
		$toFile = $ListThisDir.'\\'.$toName;   
	//	print $uploadFile.'-'.$toFile.'-'.$fileExt[1];
		if ($uploadFile <> ""){
			if ($fileExt[1] <> "jpg" AND $fileExt[1] <> "JPG" AND $fileExt[1] <> "pdf" AND $fileExt[1] <> "PDF") {
				$msg = 'Only JPEG/PDF file can be uploaded...!';
			}
			if ($fileSize > 1000000) {
				$msg = 'File size ('.$fileName.') must be less than 1M.';
			} 
			if ($msg == '' && !copy( $uploadFile, $toFile ) ) {
				$msg =  'File could not be uploaded';
			}
		} else {
			$msg = 'File cannot be empty';
		}
		
		if ($msg =='') {
			$sSQL = " INSERT INTO slip (createdDate, instructDate, vendorID, slipAttach, uploadBy, uploadDate) VALUES 
				  ('".$dt."','".$dt1."','".$vid."','".$toName."','".$_SESSION['Session.userName']."','".date("Y-m-d H:i:s")."')";
			$rsI = &$conn->Execute($sSQL);
			if ($rsI === false) { 
				$sSQL = "	UPDATE slip SET 
							slipAttach = '".$toName."', uploadBy = '".$_SESSION['Session.userName']."', uploadDate = '".date("Y-m-d H:i:s")."' 
							WHERE	vendorID = '".$vid."' AND createdDate = '".$dt."' AND instructDate = '".$dt1."' ";
				$rsU = &$conn->Execute($sSQL);
			}
	//		print $sSQL;
			//--- Begin : Create log 
			$user  = $_SESSION['Session.userName'];
			$event = "Host Receipt: ".$toName." (".$fileName.") - Upload";
			$type  = $_SESSION['Session.groupName'];
			createLog($user , $event, $type);
			//--- End   : Create log 	
		}
	} else {
		$msg = 'Please select a file to upload';
	}
	
	print '<script>';
	if ($msg <> "") {
		print 'alert("'.$msg.'");';
	} else {
		print ' opener.document.location = "home.php?yy='.todate('Y',$dt).'&mm='.todate('m',$dt).'&dd='.todate('d',$dt).'";
				window.close();';
	}
	print '</script>';
}

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>STARGATE - UPLOAD FILE</title>
<style>
  body, .button{background: buttonface; font-family:"MS Sans Serif";font-size:9pt;margin:5px;}
  .text {font-family:"MS Sans Serif";font-size:9pt;padding:5px 0px 5px 0px;}
  input{font-family:"MS Sans Serif";font-size:9pt;margin:0px;}
  .controls{width:100%;text-align:right;margin-top:5px;margin-right:3px}
</style>
</head>

<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" scroll="no" onload="self.focus();">

<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="vid" value="'.$vid.'">
<input type="hidden" name="dt" value="'.$dt.'">
<input type="hidden" name="dt1" value="'.$dt1.'">
<fieldset title="File Upload"><legend>File Upload</legend>
<table>
	<tr>
	  <td class="text">File:</td>
	  <td class="inputs"><input type="file" name="uploadFile" size="35" value=""></td>  
	</tr>
</table>
</fieldset>
<div class="controls">
	<input name="action" class="button" type="submit" value="     OK     "> 
	<input class="button" type="button" value="  Cancel  " onclick="window.close();">
</div>
</form>

</body>
</html>';

?>