<?php 
ob_start();

$pk = $_REQUEST['pk'] ?? '';
$dt = $_REQUEST['dt'] ?? '';
$dt1 = $_REQUEST['dt1'] ?? '';

include ("../common.php");
include ("../setupinfo.php");
session_start(); 

if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
} elseif ($pk =='' AND $dt =='' AND $dt1 =='') {
	print '<script>alert("Ralat Parameter untuk mencetak mukasurat ini!!!");window.close();</script>';
	exit;
}

$sSQL = "
SELECT	A.*, B.vendorCode, B.vendorName, B.address, B.contactPerson, COALESCE(B.bankName,'.........................................') AS bankName, 
		COALESCE(B.accNo,'......................') AS accNo, C.atmNo, C.atmLoc, C.atmBranch     
FROM 	instruct A  
LEFT JOIN vendor B 
ON		A.vendorID = B.vendorID 	
LEFT JOIN atm C 
ON		A.atmID = C.atmID
WHERE 	A.vendorID = '".$pk."' 
AND 	CONVERT(VARCHAR(10),A.createdDate,120) = '".$dt."'
AND 	A.instructDate ='".$dt1."'  AND A.isDeleted ='0'  ";
	
$GetRpt = $conn->Execute($sSQL);
if ($GetRpt === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.$sSQL.'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

$noRujukan = $GetRpt->fields('vendorCode').'-'.todate('ymd',$GetRpt->fields('createdDate')).'-'.todate('ymd',$GetRpt->fields('instructDate'));
$title = 'ARAHAN PENGISIAN TUNAI DI MESIN ATM CAWANGAN (EBC) BANK RAKYAT PADA '.todate('d-m-Y',$dt1);

unset($_SESSION['report_header0']);
unset($_SESSION['report_header1']);
unset($_SESSION['report_header']);
unset($_SESSION['report_values']);

$_SESSION['report_header0']=array(" "); 
$_SESSION['report_header1']=array($title); 
$_SESSION['report_header']=array("BIL","DOC NO","ATM NO","ATM-EBC","ATM BRANCH","RM100","RM50","RM20","RM10","JUMLAH (RM)"); 

$bil=1;
if ($GetRpt->RowCount() <> 0) { 
	$i=0;
	while (!$GetRpt->EOF) {
   		$_SESSION['report_values'][$i][0]=''.$bil.'';
		$_SESSION['report_values'][$i][1]=''.$GetRpt->fields('docNo').'';
		$_SESSION['report_values'][$i][2]=''.$GetRpt->fields('atmNo').'';
		$_SESSION['report_values'][$i][3]=''.$GetRpt->fields('atmLoc').'';
		$_SESSION['report_values'][$i][4]=''.$GetRpt->fields('atmBranch').'';
		$_SESSION['report_values'][$i][5]=''.$GetRpt->fields('qty100').'';
		$_SESSION['report_values'][$i][6]=''.$GetRpt->fields('qty50').'';
		$_SESSION['report_values'][$i][7]=''.$GetRpt->fields('qty20').'';
		$_SESSION['report_values'][$i][8]=''.$GetRpt->fields('qty10').'';
		$_SESSION['report_values'][$i][9]=''.number_format($GetRpt->fields('totalValue'),2).'';
		$GetRpt->MoveNext();
		$i++;
		$bil++;
	}
}

ob_end_clean();
$_GET['fn'] = $noRujukan;
include("exprt_rpt.php");
exit;