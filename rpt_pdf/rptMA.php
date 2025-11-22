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

// FIX THIS - uncomment and define $statusDesc
$statusDesc = ''; // default empty value
// if ($st <> '') $statusDesc = '(&nbsp;'.$stList[$st].'&nbsp;)';

session_start();
if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

$day = date("t", mktime(0, 0, 0, $mm, 1, $yy));

if ($mm == $mmT AND $yy == $yyT) {
	$title  = 'LAPORAN BULANAN ARAHAN PENGISIAN MENGIKUT TERMINAL '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy;
} else {
	$title  = 'LAPORAN BULANAN ARAHAN PENGISIAN MENGIKUT TERMINAL '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy.' HINGGA '.sprintf("%02d",$mmT).'/'.$yyT;
}

// REMOVE THIS LINE - we'll create PDF later
// $pdf=new Html2Pdf('L','A4', 'en');

$sSQL = "
SELECT	A.*, B.atmNo, B.atmLoc, B.type, C.vendorCode,C.vendorName 
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
ORDER BY B.atmNo, A.instructDate ";
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
$tempATM='';
$totalA=0; $totalB=0; $totalC=0; $totalD=0;
if ($GetRpt->RowCount() <> 0) { 
	while (!$GetRpt->EOF) {
		if ($GetRpt->fields('atmNo') <> $tempATM) {
			$bil=0;
			if ($tempATM <> '') {
				$HTML .= '	
				<tr><td colspan="18"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
				<tr style="font-size: 7pt;">
					<td valign="top" align="center" colspan="5">&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">'.number_format($totalA,2).'&nbsp;</td>
					<td valign="top" align="center" colspan="4">&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">'.number_format($totalB,2).'&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">'.number_format($totalC,2).'&nbsp;</td>
					<td valign="top" align="center" colspan="4">&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">'.number_format($totalD,2).'&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">&nbsp;</td>
				</tr>
				<tr><td colspan="18"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
				<tr><td height="20" colspan="18"></td></tr>';						
				$totalA=0; $totalB=0; $totalC=0; $totalD=0;
				$HTML.= '
				</table>';
			} 
			$HTML .= $HTML_TITLE.'
			<table style="width: 100%;" align="center">
				<tr style="font-size: 9pt;">	
					<td colspan="18" height="30" valign="middle"><b>'.$GetRpt->fields('atmNo').' : '.$GetRpt->fields('atmLoc').'</b></td>
				</tr>
				<tr style="font-size: 7pt; font-weight: bold;">	
					<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>TARIKH</b></td>	
					<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="5"><b>ARAHAN</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="5"><b>REPLENISH</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>VARIAN</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="5"><b>UNLOADED</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>STATUS</b></td>
				</tr>
				<tr style="font-size: 7pt; font-weight: bold;">	
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN (RM)</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN (RM)</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN (RM)</b></td>
				</tr>';		
		}
		$bil++;
		$totalA 	+= $GetRpt->fields('totalValue');
		$totalB 	+= $GetRpt->fields('replenishValue');
		$totalVariance = '0.00';
		if ($GetRpt->fields('replenishValue') <> '0.00') {
			$totalVariance = $GetRpt->fields('totalValue')-$GetRpt->fields('replenishValue');
			$totalC 	+= ($GetRpt->fields('totalValue')-$GetRpt->fields('replenishValue'));
		}
		$totalD 	+= $GetRpt->fields('unloadValue');
		$HTML .= '	
		<tr style="font-size: 7pt;">
			<td valign="top" align="center"style="width: 100px;">'.todate('d/m/Y',$GetRpt->fields('instructDate')).'</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('qty100').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('qty50').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('qty20').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('qty10').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('replenish100').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('replenish50').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('replenish20').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('replenish10').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($GetRpt->fields('replenishValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalVariance,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('unload100').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('unload50').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('unload20').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 40px;">'.$GetRpt->fields('unload10').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($GetRpt->fields('unloadValue'),2).'&nbsp;</td>
			<td align="center" valign="top"  style="width: 80px;">'.$statusList[array_search($GetRpt->fields('status'),$statusVal)].'</td>
		</tr>';
		$tempATM = $GetRpt->fields('atmNo');
		$GetRpt->MoveNext();
	}
	$HTML.= '
		<tr><td colspan="18"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
		<tr style="font-size: 7pt;">
			<td valign="top" align="center" colspan="5">&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalA,2).'&nbsp;</td>
			<td valign="top" align="center" colspan="4">&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalB,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalC,2).'&nbsp;</td>
			<td valign="top" align="center" colspan="4">&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalD,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">&nbsp;</td>
		</tr>
		<tr><td colspan="18"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
	</table>';			
} else {
   $err='';
	$HTML = $HTML_TITLE.'			
	<table style="border: solid 1px #000000; width: 100%;" align="center">
		<tr style="font-size: 7pt;">
			<td align="center" height="50" valign="middle" width="700">- Tiada Maklumat Rekod -'.$err.'</td>
		</tr>
	</table>';	
}

$pdf->writeHTML($HTML);		
$pdf->output('e-atm.pdf');
?>