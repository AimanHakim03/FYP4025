<?php  
$pk = $_REQUEST['pk'] ?? '';
// ADD THIS LINE - loads composer packages
require_once __DIR__ . '/vendor/autoload.php';

// ADD THIS - import the class!
use Spipu\Html2Pdf\Html2Pdf;

include ("../common.php");
//session_start();
include ("../setupinfo.php");
$today = date("F j, Y, g:i a");     

session_start();
if ($_SESSION['Session.userID'] == '' OR $pk == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
}


//$conn->debug=true;
$sSQL = " 
SELECT	A.journalNo, A.journalDate, A.journalAmt, A.description, A.atmType  
FROM	journal A 
WHERE 	A.journalID = '".$pk."' ";
$GetA = $conn->Execute($sSQL);

if ($GetA->RowCount() <> 0){
	$journalNo  	= $GetA->fields('journalNo');
	$atmType	  	= $GetA->fields('atmType');
	$journalDate 	= todate('d/m/Y',$GetA->fields('journalDate'));
	$description 	= $GetA->fields('description');
	$journalAmt	 	= $GetA->fields('journalAmt');
}	

if ($atmType == 'ATM') {
	$sSQL = " 
	SELECT	B.atmID, C.atmNo, C.glCode, B.unloadValue AS totalValue, B.unloadDate  AS tarikh
	FROM	instruct B LEFT JOIN atm C  ON	B.atmID = C.atmID 
	WHERE 	B.journalID = '".$pk."'   ";
} else {
	$sSQL = " 
	SELECT	B.atmID, C.atmNo, C.glCode, B.totalValue AS totalValue, B.collectDate  AS tarikh
	FROM	collect B LEFT JOIN atm C  ON	B.atmID = C.atmID 
	WHERE 	B.journalID = '".$pk."'   ";
}
$GetB = $conn->Execute($sSQL);

$bil=0; $totalAmt=0;

ob_start();
$HTML = ob_get_clean();

// KEEP ONLY ONE PDF CREATION - this is the right place
$pdf = new Html2Pdf('L','A4', 'en');

$HTML_TITLE = '
<page backtop="40mm" backbottom="15mm">
	<page_header>
		<table style="width: 100%;">
			<tr>
				<td style="text-align: left;	width: 10%">&nbsp;</td>
				<td style="text-align: center;	width: 80%"><img src="../images/bkrm_logo.gif" border="0"><br>
				<h2><b>JERNAL JABATAN PUSAT ATM</b></h2></td>
				<td style="text-align: right;	width: 10%">&nbsp;</td>
			</tr>
			<tr><td colspan="3" height="20">&nbsp;</td></tr>
		</table>
	</page_header>
	<page_footer>
		<table style="width: 100%;">
			<tr style="font-size: 10pt; color: #999999;"><td style="text-align: center; width: 100%">
			<p align="center"><small><i><b>BANK RAKYAT BERHAD</b><br>
			TINGKAT 21, NO 33 JLN RAKYAT, 50470 Kuala Lumpur<br>
			No. Telefon : 03-xxxx xxxx  No.Faksimili : 03-xxxx xxxx</i></small></p></td> 
			</tr>		
		</table>
	</page_footer>
</page>';	

$HTML = $HTML_TITLE.'
<table style="width: 700px;" align="center">
	<tr>
		<td style="width: 700px;">
		
<table style="width: 100%;" align="left">
	<tr style="font-size: 10pt;">
		<td style="width: 25%;"	valign="top">No Jernal</td><td>:</td><td>&nbsp;'.$journalNo.'</td>
	</tr>
	<tr style="font-size: 10pt;">
		<td style="width: 25%;" valign="top">Tarikh Jernal</td><td>:</td><td>&nbsp;'. $journalDate.'</td>
	</tr>
	<tr style="font-size: 10pt;">
		<td style="width: 25%;"  valign="top" height="40">Perkara</td><td height="40" valign="top" >:</td><td height="40" valign="top" >&nbsp;'.$description.'</td>	
	</tr>
	<tr style="font-size: 10pt;">
		<td style="width: 25%;"  height="40">JUMLAH KESELURUHAN</td><td>:</td><td>&nbsp;'.number_format($journalAmt,2).'<br></td>	
	</tr>
</table>';

if ($GetB->RowCount() <> 0){
	$HTML .='
<table cellpadding="1" cellspacing="1"   style="width: 100%;" align="left">
   	<tr style="font-size: 9pt;">
		<td style="width:5%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>BIL</b></td>
		<td style="width:10%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>KOD CAW</b></td>
		<td style="width:10%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>DPT</b></td>
		<td style="width:15%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>KOD G/L</b></td>
		<td style="width:15%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>DEBIT</b></td>
		<td style="width:15%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>KREDIT</b></td>
		<td style="width:25%; border: solid 0.5px #000000; background-color: #EDEDED;" align="center" height="30" valign="middle"><b>CATATAN</b></td>
	</tr>';

	while (!$GetB->EOF) {	
		$bil++;
		$totalAmt += $GetB->fields('totalValue');
		$desc = 'LBH '.$atmType.' '.$GetB->fields('atmNo').' '.todate('d/m/Y',$GetB->fields('tarikh'));

	$HTML .= '
	<tr style="font-size: 9pt;">
		<td align="right" valign="top" height="30">&nbsp;'.$bil.'&nbsp;</td>
		<td style="width:10%;" valign="top">&nbsp;500</td>
		<td style="width:10%;" valign="top">&nbsp;00</td>
		<td style="width:15%;" valign="top" align="left">&nbsp;'.$GetB->fields('glCode').'&nbsp;</td>
		<td style="width:15%;" valign="top" align="right">&nbsp;</td>
		<td style="width:15%;" valign="top" align="right">&nbsp;'.number_format($GetB->fields('totalValue'),2).'&nbsp;</td>
		<td style="width:25%;" valign="top" align="left">&nbsp;'.$desc.'&nbsp;</td>
	</tr>';
		$GetB->MoveNext();
	}
$HTML .= '
	<tr style="font-size: 9pt;">
		<td align="right" valign="top" height="30">&nbsp;'.$bil++.'&nbsp;</td>
		<td style="width:10%;" valign="top">&nbsp;500</td>
		<td style="width:10%;" valign="top">&nbsp;00</td>
		<td style="width:15%;" valign="top" align="left">&nbsp;10200-100</td>
		<td style="width:15%;" valign="top" align="right">&nbsp;'.number_format($totalAmt,2).'&nbsp;</td>
		<td style="width:15%;" valign="top" align="right">&nbsp;</td>
		<td style="width:15%;" valign="top" align="left">&nbsp;JA LBH '.$atmType.'&nbsp;</td>
	</tr>
</table>
<br><br><br>

<table border="0" cellpadding="0" cellspacing="0"  width="700">
	<tr><td height="40">Disemak Oleh&nbsp;</td><td align="left"><b>..............................................</b></td></tr>
	<tr><td height="40">Diluluskan Oleh&nbsp;</td><td align="left"><b>..............................................</b></td></tr>
	<tr><td height="40">No Batch&nbsp;</td><td align="left"><b>..............................................</b></td></tr>
</table>';
}

$HTML .= '
		</td>
	</tr>
</table>';

$pdf->writeHTML($HTML);
$pdf->output('jernal.pdf');

?>
