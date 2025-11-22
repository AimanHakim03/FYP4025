<?php

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
	$title  = 'LAPORAN BULANAN KUTIPAN MENGIKUT CDM '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy;
} else {
	$title  = 'LAPORAN BULANAN KUTIPAN MENGIKUT CDM '.$statusDesc.' BAGI BULAN/TAHUN :  '.sprintf("%02d",$mm).'/'.$yy.' HINGGA '.sprintf("%02d",$mmT).'/'.$yyT;
}
$sSQL = "
SELECT	A.*, B.atmNo, B.atmLoc, B.type, C.vendorCode,C.vendorName 
FROM 	collect A  
INNER JOIN atm B 
ON		A.atmID = B.atmID 
LEFT JOIN vendor C 
ON		A.vendorID = C.vendorID
WHERE 	A.isDeleted ='0' 
AND 	CONVERT(VARCHAR(6), A.collectDate, 112) BETWEEN '".$yy.sprintf("%02d",$mm)."' AND '".$yyT.sprintf("%02d",$mmT)."' ";
if ($ty <> '')		$sSQL .= " AND B.type = '".$ty."' ";
if ($vid <> '') 	$sSQL .= " AND A.vendorID = '".$vid."' ";
if ($atm <> '') 	$sSQL .= " AND B.atmNo LIKE '".$atm."%' ";
$sSQL .="
ORDER BY B.atmNo, A.collectDate ";
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
		<td valign="middle" align="left" bgcolor="#C0C0C0" height="25" rowspan="2"><b>DOKUMEN NO</b></td>	
		<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>TARIKH<br>KUTIPAN</b></td>	
		<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="8"><b>KUTIPAN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>UNLOAD<br>LOCAL</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>UNLOAD<br>FIZIKAL</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>VARIAN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>STATUS</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>BANK-IN</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>JUMLAH</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>KELULUSAN</b></td>
		<td valign="middle" align="left" bgcolor="#C0C0C0" rowspan="2"><b>REMARKS</b></td>
	</tr>
	<tr style="font-size: 7pt; font-weight: bold;">	
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM5</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM2</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM1</b></td>
		<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN</b></td>
	</tr>';		

$bil = 0;
$tempATM='';
$totalA=0; 
if ($GetRpt->RowCount() <> 0) { 
	while (!$GetRpt->EOF) {
/*		if ($GetRpt->fields('atmNo') <> $tempATM) {
			$bil=0;
			if ($tempATM <> '') {
				$HTML .= '	
				<tr><td colspan="18"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
				<tr style="font-size: 7pt;">
					<td valign="top" align="center" colspan="16">&nbsp;</td>
					<td align="right" valign="top"  style="width: 80px;">'.number_format($totalA,2).'&nbsp;</td>
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
					<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>SYARIKAT</b></td>	
					<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>DOKUMEN NO</b></td>	
					<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>TARIKH<br>KUTIPAN</b></td>	
					<td valign="middle" align="center" bgcolor="#C0C0C0" colspan="8"><b>KUTIPAN</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>UNLOAD<br>LOCAL</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>UNLOAD<br>FIZIKAL</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>VARIAN</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>STATUS</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>BANK-IN</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>JUMLAH</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>REMARKS</b></td>
				</tr>
				<tr style="font-size: 7pt; font-weight: bold;">	
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM5</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM2</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM1</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>AMAUN (RM)</b></td>
				</tr>';		
		}
*/
		$bil++;
		$totalA 	+= $GetRpt->fields('totalValue');
		$totalB 	+= $GetRpt->fields('htotalValue');
		$diffAmt	= $GetRpt->fields('totalValue') - $GetRpt->fields('htotalValue');
		$totalC 	+= $diffAmt;
		if ($GetRpt->fields('bankInDate') == '') {
			$amtIn = '';
		} else {
			$amtIn = number_format($GetRpt->fields('totalValue'),2);
			$totalD += $GetRpt->fields('totalValue');
		}	
		$HTML .= '	
		<tr style="font-size: 7pt;">
			<td valign="top" align="left" style="width: 150px;">'.$GetRpt->fields('vendorName').'</td>
			<td valign="top" align="left" style="width: 150px;">'.$GetRpt->fields('atmNo').' : '.$GetRpt->fields('atmLoc').'</td>
			<td valign="top" align="left" style="width: 100px;">'.$GetRpt->fields('docNo').'</td>
			<td valign="top" align="center" style="width: 60px;">'.todate('d/m/Y',$GetRpt->fields('collectDate')).'</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty100').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty50').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty20').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty10').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty5').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty2').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 30px;">'.$GetRpt->fields('qty1').'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('htotalValue'),2).'&nbsp;</td>
			<td align="right" valign="top"  style="width: 50px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
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
		<tr><td colspan="20"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
		<tr style="font-size: 7pt; font-weight: bold;">
			<td valign="top" align="center" colspan="11">&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalA,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalB,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalA,2).'&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalC,2).'&nbsp;</td>
			<td align="right" valign="top" colspan="2">&nbsp;</td>
			<td align="right" valign="top">'.number_format($totalD,2).'&nbsp;</td>
			<td align="right" valign="top" colspan="2">&nbsp;</td>
		</tr>
		<tr><td colspan="20"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
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