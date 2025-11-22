<?php 


include ("../common.php");
include ("../setupinfo.php");
session_start(); 
//$conn->debug=true;

$today = date("F j, Y, g:i a");     

if (!isset($ddFrom)) 	$ddFrom	= 1;                 		
if (!isset($mmFrom)) 	$mmFrom	= date("n");                 		
if (!isset($yyFrom))	$yyFrom	= date("Y");     
if (!isset($ddTo)) 	 	$ddTo  	= date("j");                 		
if (!isset($mmTo)) 		$mmTo	= date("n");                 		
if (!isset($yyTo))		$yyTo	= date("Y");     
if (!isset($st))		$st	= '';
if (!isset($ty))	$ty	= '';
if (!isset($vid))	$vid	= ''; // ADD THIS - it was missing too!

if ($st <> '') $statusDesc = '(&nbsp;'.$stList[$st].'&nbsp;)'; else $statusDesc = ''; 

if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
} 

$ddmmyyFrom = date("Y-m-d", mktime(0, 0, 0, $mmFrom, $ddFrom, $yyFrom));               
$ddmmyyTo	= date("Y-m-d", mktime(0, 0, 0, $mmTo, $ddTo, $yyTo));      
$ddmmyyTo1	= date("Y-m-d", mktime(0, 0, 0, $mmTo, $ddTo+1, $yyTo));                     

if ($ddFrom == $ddTo AND $mmFrom == $mmTo AND $yyFrom == $yyTo) {
	$title  = 'LAPORAN SENARAI ARAHAN PENGISIAN DENOMINASI '.$statusDesc.' PADA  '.strtoupper(toDate("d/m/Y",$ddmmyyFrom));
} else {
	$title  = 'LAPORAN SENARAI ARAHAN PENGISIAN DENOMINASI '.$statusDesc.' DARI '.strtoupper(toDate("d/m/Y",$ddmmyyFrom)).' HINGGA '.strtoupper(toDate("d/m/Y",$ddmmyyTo));
}

$sSQL	= "	
SELECT 	A.*, B.atmNo, B.atmLoc, B.type, C.vendorCode,C.vendorName	FROM 	instruct A	
INNER JOIN atm B 
ON		A.atmID = B.atmID 
LEFT JOIN vendor C 
ON		A.vendorID = C.vendorID
WHERE	A.instructDate BETWEEN ".tosql($ddmmyyFrom , "Text")." AND ".tosql($ddmmyyTo , "Text")."	
AND		A.isDeleted = '0'  ";
if ($ty <> '')		$sSQL .= " AND B.type = '".$ty."' ";
if ($vid <> '') 	$sSQL .= " AND A.vendorID = '".$vid."' ";
$sSQL .= "
ORDER BY A.vendorID, A.atmID, A.instructDate ";
$GetRpt = $conn->Execute($sSQL);

//first of all unset these variables
unset($_SESSION['report_header0']);
unset($_SESSION['report_header1']);
unset($_SESSION['report_header']);
unset($_SESSION['report_values']);

//note that the header contain the three columns and its a array
$_SESSION['report_header0']=array(" "); 
$_SESSION['report_header1']=array($title); 
$_SESSION['report_header']=array("BIL","SYARIKAT","TARIKH ARAHAN","TERMINAL","JENIS","RM100","RM50","RM20","RM10","AMAUN (RM)"); 
// now the excel data field should be two dimentational array with three column

$bil=1;
if ($GetRpt->RowCount() <> 0) { 
	$i=0;
	while (!$GetRpt->EOF) {
   		$_SESSION['report_values'][$i][0]=''.$bil.'';
		$_SESSION['report_values'][$i][1]=''.$GetRpt->fields('vendorName').'';
		$_SESSION['report_values'][$i][2]=''.todate('d-m-Y',$GetRpt->fields('instructDate')).'';
		$_SESSION['report_values'][$i][3]=''.$GetRpt->fields('atmNo').' - '.$GetRpt->fields('atmLoc').'';
		$_SESSION['report_values'][$i][4]=''.$GetRpt->fields('type').'';
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
?>
<script>window.location.href = "exprt_rpt.php?fn=rptAE";</script>
<!--<a href="exprt_rpt.php?fn=rptA">Click here to generate report</a>
the export_report.php takes one variable called fn as GET parameter which is name of 
the file to be generated, if you pass member_report as a value, then the name of the generated file would be member_report.php
 -->
 
