<?php
$statusDesc    = $_REQUEST['statusDesc']    ?? '';
$vid    = $_REQUEST['vid']    ?? '';
//$totalE    = $_REQUEST['totalE']    ?? '';
//$totalF    = $_REQUEST['totalF']    ?? '';
//$totalG    = $_REQUEST['totalG']    ?? '';
// ADD THIS LINE - loads composer packages
require_once __DIR__ . '/vendor/autoload.php';

// ADD THIS - import the class!
use Spipu\Html2Pdf\Html2Pdf;

include ("../common.php");
//session_start();
include ("../setupinfo.php");
$today = date("F j, Y, g:i a");    

if (!isset($mm)) 	$mm	= date("n");                 		
if (!isset($yy))	$yy	= date("Y");  
if (!isset($mmT)) 	$mmT= date("n");                 		
if (!isset($yyT))	$yyT= date("Y");  
if (!isset($atm))	$atm	= '';
if (!isset($ty))	$ty	= '';
//if (!isset($st))	$st	= '';

//if ($st <> '') $statusDesc = '(&nbsp;'.$stList[$st].'&nbsp;)'; else $statusDesc = '';   

session_start();
if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

$day = date("t", mktime(0, 0, 0, $mm, 1, $yy));

if ($mm == $mmT AND $yy == $yyT) {
	$title  = 'LAPORAN BULANAN ARAHAN/REPLENISH/UNLOADED MENGIKUT ATM '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy;
} else {
	$title  = 'LAPORAN BULANAN ARAHAN/REPLENISH/UNLOADED MENGIKUT ATM '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy.' HINGGA '.sprintf("%02d",$mmT).'/'.$yyT;
}

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
ORDER BY A.instructDate, B.atmNo";
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
//include ("hf.php");

$HTML_TITLE = '
<page backtop="30mm" backbottom="15mm">
	<page_header>
		<table style="width: 100%;">
			<tr>
				<td style="text-align: left;	width: 10%">&nbsp;</td>
				<td style="text-align: center;	width: 80%"><img src="../images/'.$ogoLrenwo.'" border="0"></td>
				<td style="text-align: right;	width: 10%">&nbsp;</td>
			</tr>
		</table>
		<table style="width: 100%;" align="center">
			<tr style="font-size: 12pt;"><td align="center" ><b>'.$title.'</b></td></tr>
			<tr><td height="10">&nbsp;</td></tr>
		</table>
	</page_header>
</page>';

$HTML .= $HTML_TITLE.'
<table style="width: 100%;" align="center">
	<tr style="font-size: 7pt; font-weight: bold;">	
		<td valign="middle" align="left" bgcolor="#C0C0C0" height="25" rowspan="2"><b>SYARIKAT</b></td>	
		<td valign="middle" align="left" bgcolor="#C0C0C0" height="25" rowspan="2"><b>TERMINAL</b></td>	
		<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="4"><b>ARAHAN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="3"><b>REPLENISH</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="3"><b>UNLOADED</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>STATUS</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>BANK-IN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>JUMLAH<br>BANK-IN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>KELULUSAN</b></td>
		<td valign="middle" align="left" bgcolor="#C0C0C0" rowspan="2"><b>REMARKS</b></td>
	</tr>
	<tr style="font-size: 7pt; font-weight: bold;">	
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>DOKUMEN NO</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>TARIKH ARAHAN<br>OLEH PATM</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>TARIKH<br>JANGKAAN<br>REPLENISH</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN<br>ARAHAN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>TARIKH<br>SEBENAR<br>REPLENISH</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN<br>REPLENISH</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>VARIAN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>UNLOAD<br>FIZIKAL</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>UNLOAD<br>LOCAL</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>VARIAN</b></td>
	</tr>';		

$bil = 0;
$tempATM='';
$totalA=0; $totalB=0; $totalC=0; $totalD=0;
if ($GetRpt->RowCount() <> 0) { 
	while (!$GetRpt->EOF) {
/*		if ($GetRpt->fields('atmNo') <> $tempATM) {
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
		} */
		$bil++;
		$totalA 	+= $GetRpt->fields('totalValue');
		$totalB 	+= $GetRpt->fields('replenishValue');
		$totalVariance = $GetRpt->fields('totalValue')-$GetRpt->fields('replenishValue');
		$totalC 	+= $totalVariance;
		$totalD 	+= $GetRpt->fields('unloadValue');
		$totalE 	+= $GetRpt->fields('hostValue');
		$diffAmt	= $GetRpt->fields('unloadValue') - $GetRpt->fields('hostValue');
		$totalF 	+= $diffAmt;
		if ($GetRpt->fields('bankInDate') == '') {
			$amtIn = '';
		} else {
//			$amtIn = number_format($GetRpt->fields('totalValue'),2);
//			$totalG += $GetRpt->fields('totalValue');
			if ($totalVariance > 0) {
				$amtIn = number_format($GetRpt->fields('unloadValue')+$totalVariance,2);
				$totalG += ($GetRpt->fields('unloadValue')+$totalVariance);
			} else {
				$amtIn = number_format($GetRpt->fields('unloadValue'),2);				
				$totalG += $GetRpt->fields('unloadValue');
			}
		}	
		$HTML .= '	
		<tr style="font-size: 7pt;">
			<td valign="top" align="left" style="width: 150px;">'.$GetRpt->fields('vendorName').'</td>
			<td valign="top" align="left" style="width: 150px;">'.$GetRpt->fields('atmNo').' : '.$GetRpt->fields('atmLoc').'</td>
			<td valign="top" align="left" style="width: 100px;">'.$GetRpt->fields('docNo').'</td>
			<td valign="top" align="center" style="width: 60px;">'.todate('d/m/Y',$GetRpt->fields('createdDate')).'</td>
			<td valign="top" align="center" style="width: 60px;">'.todate('d/m/Y',$GetRpt->fields('instructDate')).'</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
			<td valign="top" align="center" style="width: 60px;">'.todate('d/m/Y',$GetRpt->fields('actualReplenishDate')).'</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('replenishValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($totalVariance,2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('unloadValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('hostValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($diffAmt,2).'&nbsp;</td>
			<td align="center" valign="top"  style="width: 50px;">'.$statusList[array_search($GetRpt->fields('status'),$statusVal)].'</td>
			<td valign="top" align="center" style="width: 50px;">'.todate('d/m/Y',$GetRpt->fields('bankInDate')).'</td>
			<td align="right" valign="top"  style="width: 50px;">'.$amtIn.'&nbsp;</td>
			<td valign="top" align="center" style="width: 50px;">'.todate('d/m/Y',$GetRpt->fields('approvedDate')).'</td>
			<td valign="top" align="left" style="width: 200px;">'.$GetRpt->fields('remarks').'</td>
		</tr>';
		$tempATM = $GetRpt->fields('atmNo');
		$GetRpt->MoveNext();
	}
	$HTML.= '
		<tr><td colspan="19"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
		<tr style="font-size: 7pt;">
			<td valign="top" align="center" colspan="5">&nbsp;</td>
			<td align="right" valign="top"  style="width: 80px;">'.number_format($totalA,2).'&nbsp;</td>
			<td valign="top" align="center">&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalB,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalC,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalD,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalE,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalF,2).'&nbsp;</td>
			<td valign="top" align="center" colspan="2">&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalG,2).'&nbsp;</td>
			<td align="right" valign="top" colspan="2">&nbsp;</td>
		</tr>
		<tr><td colspan="19"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
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

//$pdf->WriteHTML($HTML, isset($_GET['vuehtml']));		
//$pdf->Output('e-atm.pdf');
print $HTML;
?>