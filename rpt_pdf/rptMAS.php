<?php
// ADD THIS LINE - loads composer packages
require_once __DIR__ . '/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
include ("../common.php");
include ("../setupinfo.php");
$today = date("F j, Y, g:i a");    

if (!isset($mm)) 	$mm	= date("n");                 		
if (!isset($yy))	$yy	= date("Y"); 
if (!isset($mmT)) 	$mmT= date("n");                 		
if (!isset($yyT))	$yyT= date("Y");    
if (!isset($atm))	$atm	= '';
if (!isset($ty))	$ty	= '';
if (!isset($vid))	$vid	= ''; // ADD THIS - it was missing too!
if (!isset($statusDesc))	$statusDesc	= ''; // ADD THIS - it was missing too!
//if (!isset($st))	$st	= '';

//if ($st <> '') $statusDesc = '(&nbsp;'.$stList[$st].'&nbsp;)'; else $statusDesc = '';   

session_start();
if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

$day = date("t", mktime(0, 0, 0, $mm, 1, $yy));

if ($mm == $mmT AND $yy == $yyT) {
	$title  = 'LAPORAN RINGKASAN BULANAN ARAHAN PENGISIAN MENGIKUT TERMINAL '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy;
} else {
	$title  = 'LAPORAN RINGKASAN BULANAN ARAHAN PENGISIAN MENGIKUT TERMINAL '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy.' HINGGA '.sprintf("%02d",$mmT).'/'.$yyT;
}

$sSQL = "
SELECT	B.atmNo, B.atmLoc, SUM(A.totalValue) AS totalValue, SUM(A.replenishValue) AS replenishValue, SUM(A.totalValue-A.replenishValue) AS varianceAmt,
		SUM(A.unloadValue) AS unloadValue
FROM 	instruct A  
INNER JOIN atm B 
ON		A.atmID = B.atmID 
LEFT JOIN vendor C 
ON		A.vendorID = C.vendorID
WHERE 	A.isDeleted ='0' 
AND 	CONVERT(VARCHAR(6), A.instructDate, 112) BETWEEN '".$yy.sprintf("%02d",$mm)."' AND '".$yyT.sprintf("%02d",$mmT)."' ";
if ($ty <> '')		$sSQL .= " AND B.type = '".$ty."' ";
if ($vid <> '') 	$sSQL .= " AND A.vendorID = '".$vid."' ";
if ($atm <> '') 	$sSQL .= " AND B.atmNo LIKE '".$atm."%' ";
$sSQL .="
GROUP BY B.atmNo, B.atmLoc
ORDER BY B.atmNo ";
$GetRpt = $conn->Execute($sSQL);
if ($GetRpt === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.$sSQL.'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

ob_start();
$HTML = ob_get_clean();

// KEEP ONLY ONE PDF CREATION - this is the right place
$pdf = new Html2Pdf('L','A4', 'en');
include ("hf.php");
$bil = 0;
$totalA=0; $totalB=0; $totalC=0; $totalD=0;
if ($GetRpt->RowCount() <> 0) { 
	$HTML .= $HTML_TITLE.'
	<table style="width: 100%;" align="center">
		<tr style="font-size: 9pt; font-weight: bold;">	
			<td valign="middle" align="left" bgcolor="#C0C0C0" height="25"><b>TERMINAL</b></td>	
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>ARAHAN (RM)</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>REPLENISH (RM)</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>VARIAN (RM)</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>UNLOADED(RM)</b></td>
		</tr>';		
	while (!$GetRpt->EOF) {
		$bil++;
		$totalA 	+= $GetRpt->fields('totalValue');
		$totalB 	+= $GetRpt->fields('replenishValue');
		$totalC 	+= $GetRpt->fields('varianceAmt');
		$totalD 	+= $GetRpt->fields('unloadValue');
		$HTML .= '	
		<tr style="font-size: 9pt;">
			<td valign="top" align="left"style="width: 300px;">'.$GetRpt->fields('atmNo').' : '.$GetRpt->fields('atmLoc').'</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($GetRpt->fields('replenishValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($GetRpt->fields('varianceAmt'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($GetRpt->fields('unloadValue'),2).'&nbsp;</td>
		</tr>';
		$GetRpt->MoveNext();
	}
	$HTML.= '
		<tr><td colspan="5"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
		<tr style="font-size: 9pt;">
			<td valign="top" align="center">&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($totalA,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($totalB,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($totalC,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 100px;">'.number_format($totalD,2).'&nbsp;</td>
		</tr>
		<tr><td colspan="5"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
	</table>';			
} else {
   $err='';
	$HTML = $HTML_TITLE.'			
	<table style="border: solid 1px #000000; width: 100%;" align="center">
		<tr style="font-size: 9pt;">
			<td align="center" height="50" valign="middle" width="700">- Tiada Maklumat Rekod -'.$err.'</td>
		</tr>
	</table>';	
}

$pdf->writeHTML($HTML);		
$pdf->output('e-atm.pdf');
?>