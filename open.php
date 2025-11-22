<?php
// Parameter	:	type 1-Cuti, 2-Khairat Kematian, 3-Bencana Alam, 4-Mengunjungi Wilayah Asal, 5-Menangguh Tuntutan Pindah
//				 	arr	 - array no
//					pk   - document ID	
include("common.php");
include("setupinfo.php");

if ($type == 1) {
	$sSQL = " SELECT * FROM cuti WHERE leaveID = '".$pk."' ";
} elseif ($type == 2) {
	$sSQL = " SELECT * FROM khairat WHERE khairatID = '".$pk."' ";
} elseif ($type == 3) {
	$sSQL = " SELECT * FROM bencana WHERE bencanaID = '".$pk."' ";
} elseif ($type == 4) {
	$sSQL = " SELECT * FROM wilayah WHERE wilayahID = '".$pk."' ";
} elseif ($type == 5) {
	$sSQL = " SELECT * FROM pindah WHERE pindahID = '".$pk."' ";
} elseif ($type == 6) {
	$sSQL = " SELECT * FROM festival WHERE festivalID = '".$pk."' ";
}

$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() == 1) {
	$docName	= explode('|||',$rs->fields('docName'));
	$docType	= explode('|||',$rs->fields('docType'));
	$docSize	= explode('|||',$rs->fields('docSize'));
	$docContent	= explode('|||',$rs->fields('docContent'));

	header("Content-type: ".$docType[$arr]."");
	echo stripslashes(base64_decode($docContent[$arr]));
}
?>