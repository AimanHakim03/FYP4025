<?php
$percen     = $_REQUEST['percen']    ?? '';
$ddmmyyFrom = $_REQUEST['ddmmyyFrom'] ?? '';
$ddmmyyTo   = $_REQUEST['ddmmyyTo']   ?? '';
$q          = $_REQUEST['q']          ?? '';
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

if ($percen <> '') $percent = '(Kurang &nbsp;'.$percen.'&nbsp;%)'; else $percent = '';

session_start();
if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}

if ($ddmmyyFrom == $ddmmyyTo) {
	$title  = 'SENARAI DATA IBS '.$percent.' PADA  '.strtoupper(toDate("d/m/Y",$ddmmyyFrom));
} else {
	$title  = 'SENARAI DATA IBS '.$percent.' DARI '.strtoupper(toDate("d/m/Y",$ddmmyyFrom)).' HINGGA '.strtoupper(toDate("d/m/Y",$ddmmyyTo));
}

//$conn->debug=true;

$sSQL	= "	
SELECT 	* FROM 	cash 
WHERE	isDeleted = '0'	
AND		fileDate BETWEEN ".tosql($ddmmyyFrom , "Text")." AND ".tosql($ddmmyyTo , "Text")."	";

if ($percen <> '') $sSQL .= " AND	(percentD2 < ".tosql($percen, "Text")." OR percentD4 < ".tosql($percen, "Text").")";

if ($q <> '') $sSQL .=  " AND (location	LIKE ".tosql("%".$q."%","Text").
						"  OR  atmNo LIKE ".tosql("%".$q."%","Text").")";

$sSQL .= " ORDER BY fileDate, atmNo ";

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
if ($GetRpt->RowCount() <> 0) { 
	$HTML = $HTML_TITLE.'
	<table style="width: 100%;" align="center">
		<tr style="width: 100%; font-size: 9pt;">	
			<td valign="middle" align="center" bgcolor="#C0C0C0"  rowspan="2" height="25"><b>BIL</b></td>	
			<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>FAIL</b></td>
			<td valign="middle" align="left" 	bgcolor="#C0C0C0" rowspan="2"><b>TERMINAL</b></td>		
			<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>JENIS</b></td>
			<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>TARIKH<br>LREP</b></td>
			<td valign="middle" align="center"  bgcolor="#C0C0C0" colspan="3"><b>RM100/RM50</b></td>
			<td valign="middle" align="center"  bgcolor="#C0C0C0" colspan="3"><b>RM20/RM10</b></td>
		</tr>
		<tr style="width: 100%; font-size: 9pt;">	
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>JUMLAH</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>BAKI</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>%</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>JUMLAH</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>BAKI</b></td>
			<td valign="middle" align="center" bgcolor="#C0C0C0"><b>%</b></td>
		</tr>';
		
	while (!$GetRpt->EOF) {
		$bil++;

			$deno2 = $GetRpt->fields('percentD2');
			$deno4 = $GetRpt->fields('percentD4');
			if ($percen <> '') {
				if ($deno2 < $percen) {
					$deno2 = '<b>'.number_format($deno2,2).'</b>';
				}
				if ($deno4 < $percen) {
					$deno4 = '<b>'.number_format($deno4,2).'</b>';
				}
			}


		$HTML .= '	
		<tr style="font-size: 9pt;">
			<td align="center" valign="top">'.$bil.'</td>
			<td align="center" valign="top" style="width: 60px;">'.todate('d/m/Y',$GetRpt->fields('fileDate')).'</td>
			<td align="left"   valign="top" >'.$GetRpt->fields('atmNo').' - '.$GetRpt->fields('location').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('model').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$GetRpt->fields('lrepDate').'</td>
			<td align="right"  valign="top" style="width: 70px;">'.$GetRpt->fields('lrepDeno2').'</td>
			<td align="right"  valign="top" style="width: 60px;">'.$GetRpt->fields('lrepDenod2').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$deno2.'</td>
			<td align="right"  valign="top" style="width: 70px;">'.$GetRpt->fields('lrepDeno4').'</td>
			<td align="right"  valign="top" style="width: 60px;">'.$GetRpt->fields('lrepDenod4').'</td>
			<td align="center" valign="top" style="width: 60px;">'.$deno4.'</td>
		</tr>';
		$GetRpt->MoveNext();
	}
	$HTML.= '
		<tr><td colspan="11"><hr style="border-size:0.5px; border-color:#999999"></td></tr>
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
$pdf->output('e-cash.pdf');
//print $HTML;
?>