<?php
$ty = $_REQUEST['ty'] ?? '';
$vid = $_REQUEST['vid'] ?? '';
// ADD THIS LINE - loads composer packages
require_once __DIR__ . '/vendor/autoload.php';

// ADD THIS - import the class!
use Spipu\Html2Pdf\Html2Pdf;

include ("../common.php");
//session_start();
include ("../setupinfo.php");
$today = date("F j, Y, g:i a");     

if (!isset($ddFrom)) 	$ddFrom	= 1;                 		
if (!isset($mmFrom)) 	$mmFrom	= date("n");                 		
if (!isset($yyFrom))	$yyFrom	= date("Y");     
if (!isset($ddTo)) 	 	$ddTo  	= date("j");                 		
if (!isset($mmTo)) 		$mmTo	= date("n");                 		
if (!isset($yyTo))		$yyTo	= date("Y");    
if (!isset($yyTo))		$yyTo	= date("Y");  
if (!isset($st))		$st	= '';

if ($st <> '') $statusDesc = '(&nbsp;'.$stList[$st].'&nbsp;)'; else $statusDesc = ''; 


if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

$ddmmyyFrom = date("Y-m-d", mktime(0, 0, 0, $mmFrom, $ddFrom, $yyFrom));               
$ddmmyyTo	= date("Y-m-d", mktime(0, 0, 0, $mmTo, $ddTo, $yyTo));      
$ddmmyyTo1	= date("Y-m-d", mktime(0, 0, 0, $mmTo, $ddTo+1, $yyTo));                     

if ($ddFrom == $ddTo AND $mmFrom == $mmTo AND $yyFrom == $yyTo) {
	$title  = 'LAPORAN SENARAI KUTIPAN DENOMINASI '.$statusDesc.' PADA  '.strtoupper(toDate("d/m/Y",$ddmmyyFrom));
} else {
	$title  = 'LAPORAN SENARAI KUTIPAN DENOMINASI '.$statusDesc.' DARI '.strtoupper(toDate("d/m/Y",$ddmmyyFrom)).' HINGGA '.strtoupper(toDate("d/m/Y",$ddmmyyTo));
}

//$conn->debug=true;

$sSQL	= "	
SELECT 	A.*, B.atmNo, B.atmLoc, B.type, C.vendorCode,C.vendorName	FROM 	collect A	
INNER JOIN atm B 
ON		A.atmID = B.atmID 
LEFT JOIN vendor C 
ON		A.vendorID = C.vendorID
WHERE	A.collectDate BETWEEN ".tosql($ddmmyyFrom , "Text")." AND ".tosql($ddmmyyTo , "Text")."	
AND		A.isDeleted = '0'  ";
if ($ty <> '')		$sSQL .= " AND B.type = '".$ty."' ";
if ($vid <> '') 	$sSQL .= " AND A.vendorID = '".$vid."' ";
$sSQL .= "
ORDER BY A.vendorID, A.atmID, A.collectDate ";
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
$tempVendor='';
$qty100=0; $qty50=0; $qty20=0; $qty10=0; $qty5=0; $qty2=0; $qty1=0; $totalAmt=0;
if ($GetRpt->RowCount() <> 0) { 
	$HTML = $HTML_TITLE.'
	<table style="width: 100%;" align="center">
		<tr style="width: 100%; font-size: 9pt;">	
			<td valign="middle" align="center" bgcolor="#C0C0C0"  rowspan="2" height="25"><b>BIL</b></td>	
			<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>TARIKH KUTIPAN</b></td>
			<td valign="middle" align="left" bgcolor="#C0C0C0" rowspan="2"><b>TERMINAL</b></td>		
			<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>JENIS</b></td>
			<td valign="middle" align="center"  bgcolor="#C0C0C0" colspan="7"><b>DENOMINASI UNIT</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>AMAUN (RM)</b></td>
		</tr>
		<tr style="width: 100%; font-size: 9pt;">	
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM50</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM20</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM5</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM2</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM1</b></td>
		</tr>';		
	while (!$GetRpt->EOF) {
		if ($GetRpt->fields('vendorCode') <> $tempVendor) {
			$bil=0;
			if ($tempVendor <> '') {
				
//				$HTML.= '
//				</table>';
//				$pdf->WriteHTML($HTML, isset($_GET['vuehtml']));			
				$HTML .= '	
				<tr><td colspan="12"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
				<tr style="font-size: 9pt;">
					<td valign="top" align="right" colspan="4">&nbsp;</td>
					<td align="center" valign="top" style="width: 60px;">'.$qty100.'</td>
					<td align="center" valign="top" style="width: 60px;">'.$qty50.'</td>
					<td align="center" valign="top" style="width: 60px;">'.$qty20.'</td>
					<td align="center" valign="top" style="width: 60px;">'.$qty10.'</td>
					<td align="center" valign="top" style="width: 50px;">'.$qty5.'</td>
					<td align="center" valign="top" style="width: 50px;">'.$qty2.'</td>
					<td align="center" valign="top" style="width: 50px;">'.$qty1.'</td>
					<td align="right" valign="top" style="width: 120px;">'.number_format($totalAmt,2).'&nbsp;</td>
				</tr>
				<tr><td colspan="12"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
				<tr><td height="20" colspan="12"></td></tr>';						
				$qty100=0; $qty50=0; $qty20=0; $qty10=0; $qty5=0; $qty2=0; $qty1=0; $totalAmt=0;
			} 
			$HTML .= '
			<tr style="font-size: 9pt;">	
				<td colspan="12" height="30" valign="middle"><b>'.$GetRpt->fields('vendorCode').' : '.$GetRpt->fields('vendorName').'</b></td>
			</tr>';
		}
		$bil++;
		$qty100 	+= $GetRpt->fields('qty100');
		$qty50  	+= $GetRpt->fields('qty50');
		$qty20  	+= $GetRpt->fields('qty20');
		$qty10  	+= $GetRpt->fields('qty10');
		$qty5  		+= $GetRpt->fields('qty5');
		$qty2  		+= $GetRpt->fields('qty2');
		$qty1  		+= $GetRpt->fields('qty1');
		$totalAmt 	+= $GetRpt->fields('totalValue');
		$HTML .= '	
		<tr style="font-size: 9pt;">
			<td valign="top" align="center">'.$bil.'</td>
			<td align="left" valign="center">'.todate('d/m/Y',$GetRpt->fields('collectDate')).'</td>
			<td align="left" valign="top" style="width: 300px;">'.$GetRpt->fields('atmNo').' - '.$GetRpt->fields('atmLoc').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('type').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('qty100').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('qty50').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('qty20').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('qty10').'</td>
			<td align="center" valign="top" style="width: 50px;">'.$GetRpt->fields('qty5').'</td>
			<td align="center" valign="top" style="width: 50px;">'.$GetRpt->fields('qty2').'</td>
			<td align="center" valign="top" style="width: 50px;">'.$GetRpt->fields('qty1').'</td>
			<td align="right" valign="top" style="width: 120px;">'.number_format($GetRpt->fields('totalValue'),2).'&nbsp;</td>
		</tr>';
		$tempVendor = $GetRpt->fields('vendorCode');
		$GetRpt->MoveNext();
	}
	$HTML.= '
		<tr><td colspan="12"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
		<tr style="font-size: 9pt;">
			<td valign="top" align="right" colspan="4">&nbsp;</td>
			<td align="center" valign="top" style="width: 60px;">'.$qty100.'</td>
			<td align="center" valign="top" style="width: 60px;">'.$qty50.'</td>
			<td align="center" valign="top" style="width: 60px;">'.$qty20.'</td>
			<td align="center" valign="top" style="width: 60px;">'.$qty10.'</td>
			<td align="center" valign="top" style="width: 50px;">'.$qty5.'</td>
			<td align="center" valign="top" style="width: 50px;">'.$qty2.'</td>
			<td align="center" valign="top" style="width: 50px;">'.$qty1.'</td>
			<td align="right" valign="top" style="width: 120px;">'.number_format($totalAmt,2).'&nbsp;</td>
		</tr>
		<tr><td colspan="12"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
	</table>';			
} else {
	$err='';
	$HTML = $HTML_TITLE.'			
	<table style="border: solid 1px #000000; width: 100%;" align="center">
		<tr style="font-size: 9pt;">
			<td align="center" height="50" valign="middle" style="width: 100%;">- Tiada Maklumat Rekod -'.$err.'</td>
		</tr>
	</table>';
}

$pdf->writeHTML($HTML);		
$pdf->Output('e-atm.pdf');
?>