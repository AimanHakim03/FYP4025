<?php

include ("common.php");
include ("setupinfo.php");
include ("forms.php");
//$conn->debug=true;

if (!isset($pk))		$pk="";	
if (!isset($type))		$type=""; 
if (!isset($userid))	$userid=""; 
if (!isset($uid))		$uid=""; 

print '
<html>
<head>
	<title>' . $emaNetis . '</title>
<meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0"> 
<meta http-equiv="cache-control" content="no-cache">
	<LINK rel="stylesheet" href="images/zssb.css" >	
</head>
<body>';

if ($type == 1) {
	$title 		= "PEMBIAYAAN PERUMAHAN I WARGA KERJA (KELULUSAN PERTAMA)";
	$sSQL = "SELECT	* FROM 	house WHERE houseID = '".$pk."' ";
} elseif ($type == 2) {
	$title 		= "PEMBIAYAAN KENDERAAN I WARGA KERJA (KELULUSAN PERTAMA)";
	$sSQL = "SELECT	* FROM 	vehicle WHERE vehicleID = '".$pk."' ";
} elseif ($type == 3) {
	$title 		= "PEMBIAYAAN PENDIDIKAN WARGA KERJA (KELULUSAN PERTAMA)";
	$sSQL = "SELECT	* FROM 	education WHERE educationID = '".$pk."' ";
} elseif ($type == 4) {
	$title 		= "PEMBIAYAAN SAHAM WARGA KERJA (KELULUSAN PERTAMA)";
	$sSQL = "SELECT	* FROM 	share WHERE shareID = '".$pk."' ";
} elseif ($type == 5) {
	$title 		= "PEMBIAYAAN KOMPUTER PERSENDIRIAN WARGA KERJA (KELULUSAN PERTAMA)";
	$sSQL = "SELECT	* FROM 	computer WHERE computerID = '".$pk."' ";	
}

if ($id == '' AND $type == '' AND $userid =='' AND $uid =='') {
	die ('<p class="maroonText">'.$title.'</p><p class="redText">'.$errPage.'</p>');
}
$uid1 = strtoupper(md5($pk.$userid.$type));
if ($uid <> $uid1) {
	die ('<p class="maroonText">'.$title.'</p><p class="redText">'.$errPage.'</p>');
}

$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() == 0) {
	die ('<p class="maroonText">'.$title.'</p><p class="redText">*** No Dokumen tidak wujud ***</p>');
} elseif ($rs->fields('isApproved') <> '0') {
	die ('<p class="maroonText">'.$title.'</p><p class="redText">*** Harap maaf. Dokumen '.$rs->fields('docNo').' telah melalui Proses Kelulusan Pertama ***</p>');
}

$strErrMsg = Array();
if ($action <> '') {
	$emailTo 		= dlookupM("StaffProfiles", "email", "StaffNo=" .tosql($rs->fields('staffNo'), "Text"));
	$emailFrom		= dlookupM("StaffProfiles", "email", "StaffNo=" .tosql($userid, "Text"));
	$staffName		= dlookupM("StaffProfiles", "StaffName", "StaffNo=" .tosql($userid, "Text"));

	if ($isApproved <> '1' AND $isApproved <> '2') {
		$err1 = '<br><font class="redText">*** Sila pilih tindakan ***</font>';
		array_push ($strErrMsg, 'isApproved');
	}
	if ($isApproved == '1' AND $approvedNote == '') {
		$err2 = '<br><font class="redText">*** Sila masukkan catatan ***</font>';
		array_push ($strErrMsg, 'approvedNote');
	}
	
	if (count($strErrMsg) == "0") {
		$approvedDate = date("Y-m-d H:i:s");
		$statusUpd = '';
		if ($isApproved == 1) $statusUpd = ", status = '1' ";
		if ($type == 1) {
			$sSQL 	= "	UPDATE 	house SET 
						isApproved = '".$isApproved."', approvedBy = '".$userid."', approvedDate = '".$approvedDate."', approvedNote = '".$approvedNote."' ".$statusUpd." 
						WHERE 	houseID = '".$pk."' ";  
		} elseif ($type == 2) {
			$sSQL 	= "	UPDATE 	vehicle SET 
						isApproved = '".$isApproved."', approvedBy = '".$userid."', approvedDate = '".$approvedDate."', approvedNote = '".$approvedNote."' ".$statusUpd." 
						WHERE 	vehicleID = '".$pk."' ";  
		} elseif ($type == 3) {
			$sSQL 	= "	UPDATE 	education SET 
						isApproved = '".$isApproved."', approvedBy = '".$userid."', approvedDate = '".$approvedDate."', approvedNote = '".$approvedNote."' ".$statusUpd." 
						WHERE 	educationID = '".$pk."' ";  
		} elseif ($type == 4) {
			$sSQL 	= "	UPDATE 	share SET 
						isApproved = '".$isApproved."', approvedBy = '".$userid."', approvedDate = '".$approvedDate."', approvedNote = '".$approvedNote."' ".$statusUpd." 
						WHERE 	shareID = '".$pk."' ";  
		} elseif ($type == 5) {
			$sSQL 	= "	UPDATE 	computer SET 
						isApproved = '".$isApproved."', approvedBy = '".$userid."', approvedDate = '".$approvedDate."', approvedNote = '".$approvedNote."' ".$statusUpd." 
						WHERE 	computerID = '".$pk."' ";  
		}
		$rs = &$conn->Execute($sSQL);		
	
		//--- Begin : Create log 
		$user  = $userid;
		$event = $title." - ".$ltl1List[array_search($isApproved, $ltl1Val)];
		$type  = '0';
		createLog($user , $event, $type);
		//--- End   : Create log 		
		
		
		$header = "From: ".$emailFrom." \r\n"
				 ."Bcc: ".$CCBliame." \r\n"
				 ."Content-Type: text/html;\n" 
			     ."X-Mailer: PHP/" . phpversion();					
		$content = '<hr size=1>'.$event.'<br>( <i>Tindakan Oleh : '.$userid.'-'.$staffName.' ; Tarikh : '.toDate("d/m/Y H:i:s A",$approvedDate).'</i> )<hr size=1>';
//		mail($emailTo, $subject, $content, $header);		
		
		print '	<script>opener.document.forms[\'ITRViewResults\'].submit();</script>';
		die ('<p class=textfont><b>Terima kasih</b> . '.$event.'</p><p><input type="Button" value="Tutup" onclick="window.close();"></p>');		
		
	}
}

print '
<form name="MyForm" action="'.$PHP_SELF.'" method="post">
<input type="Hidden" name="pk" value="'.$pk.'">
<input type="Hidden" name="type" value="'.$type.'">
<input type="hidden" name="userid" value="'.$userid.'">
<input type="hidden" name="uid" value="'.$uid.'">
<table border="0" cellspacing="1" cellpadding="0" width="500" align="center" class="Header">
	<tr>
		<td class="contentH" align="center" height="30" valign="middle">'.$title.'</td>
	</tr>
	<tr>
		<td class="contentD" valign="top">
			<table border="0" cellspacing="0" cellpadding="3" width="100%" align="center">
				<tr>
					<td align="right" class="Data" valign="top">Tindakan :&nbsp;</td>
					<td class="Data" valign="top">';
					print FormEntry("","isApproved","radio",$isApproved,$ltl1List,$ltl1Val,"1","1").$err1.'
					</td>
				</tr>
				<tr>
					<td align="right" class="Data" valign="top">Catatan :&nbsp;</td>
					<td class="Data" valign="top">';
					print FormEntry("","approvedNote","textarea100",$approvedNote,"","","50","3").$err2.'
					</td>
				</tr>
				<tr>
					<td class="Data" valign="top"></td>
					<td class="Data" valign="top"><input type="submit" name="action" value="Hantar" class="but"></td>
				</tr>				
			</table>			
		</td>
	</tr>
</table>
</form>
</body>
</html>';
?>