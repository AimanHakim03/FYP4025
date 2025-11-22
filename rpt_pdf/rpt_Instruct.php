<?php
$pk = $_REQUEST['pk'] ?? '';
$dt = $_REQUEST['dt'] ?? '';
$dt1 = $_REQUEST['dt1'] ?? '';
//require_once('html2pdf/html2pdf.class.php');
// ADD THIS LINE - loads composer packages
require_once __DIR__ . '/vendor/autoload.php';

// ADD THIS - import the class!
use Spipu\Html2Pdf\Html2Pdf;

include ("../common.php");
//session_start();
include ("../setupinfo.php");
$today = date("F j, Y, g:i a");     

$date = date("d/m/Y");

if ($_SESSION['Session.userID'] == '') {
	print '<script>alert("'.$errPage.'");window.close();</script>';
	exit;
} elseif ($pk =='' AND $dt =='' AND $dt1 =='') {
	print '<script>alert("Ralat Parameter untuk mencetak mukasurat ini!!!");window.close();</script>';
	exit;
}

//$conn->debug=true;
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
AND 	A.instructDate ='".$dt1."' AND A.isDeleted ='0' ";
//$sSQL .= " AND A.status <> '0' ";	
$rs = $conn->Execute($sSQL);
if ($rs === false) { 
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
	</page_header>
	<page_footer>
		<table style="width: 100%;">
			<tr style="font-size: 7.5pt;">
				<td style="text-align: left;  width: 50%"><i>Cetakan pada : '.date("F j, Y, g:i a").'</i></td>
				<td style="text-align: right; width: 50%"><i>Mukasurat : [[page_cu]]/[[page_nb]]</i></td>
			</tr>
		</table>
	</page_footer>
</page>';	

$HTML_TITLE1 = '
<page  orientation="paysage" backtop="30mm" backbottom="15mm">
	<page_header>
		<table style="width: 100%;">
			<tr>
				<td style="text-align: left;	width: 10%">&nbsp;</td>
				<td style="text-align: center;	width: 80%"><img src="../images/'.$ogoLrenwo.'" border="0"></td>
				<td style="text-align: right;	width: 10%">&nbsp;</td>
			</tr>
		</table>
	</page_header>
	<page_footer>
		<table style="width: 100%;">
			<tr style="font-size: 7.5pt;">
				<td style="text-align: left;  width: 50%"><i>Cetakan pada : '.date("F j, Y, g:i a").'</i></td>
				<td style="text-align: right; width: 50%"><i>Mukasurat : [[page_cu]]/[[page_nb]]</i></td>
			</tr>
		</table>
	</page_footer>
</page>';	


$HTML = $HTML_TITLE.'
<table style="width: 650px;" align="center">
	<tr>
		<td style="width: 650px;">
		
		<table  style="width: 650px;" align="center">
			<tr style="font-size: 10pt;">
				<td style="width: 25%;">Dokumen</td>
				<td style="width: 5%;" align="center">:</td>
				<td style="width: 70%;">4/4/AKBE/REPATMLP'.$rs->fields('vendorCode').'/03/13 SULIT (e-ATM)</td>
			</tr>
			<tr style="font-size: 10pt;">
				<td style="width: 25%;">Rujukan</td>
				<td style="width: 5%;" align="center">:</td>
				<td style="width: 70%;">'.$rs->fields('vendorCode').'/'.todate('ymd',$rs->fields('createdDate')).'-'.todate('ymd',$rs->fields('instructDate')).'</td>
			</tr>
			<tr style="font-size: 10pt;">
				<td style="width: 25%;">Tarikh Keluar Arahan</td>
				<td style="width: 5%;" align="center">:</td>
				<td style="width: 70%;">'.todate('d-m-Y',$rs->fields('createdDate')).'</td>
			</tr>
			<tr style="font-size: 10pt;">
				<td style="width: 25%;">Tarikh Arahan</td>
				<td style="width: 5%;" align="center">:</td>
				<td style="width: 70%;">'.todate('d-m-Y',$dt1).'</td>
			</tr>
		</table>
		<table border="0" style="width: 650px;" align="center">
		   	<tr style="font-size: 10pt;">
				<td>
				<p>Kepada : </p>
				<p>'.$rs->fields('vendorName').'<br><pre style="font-size: 12px; font-family: Arial;">'.$rs->fields('address').'</pre>
				<br>U/P: Pengurus ATM</p>
				<p>Tuan/Puan,</p>
				<p><b><u>ARAHAN PENGISIAN TUNAI DI MESIN ATM CAWANGAN (EBC) BANK RAKYAT</u></b></p>
				<p><div style="width: 100%; align: justify;">Perkara di atas dirujuk.</div></p>
				<p><div style="width: 100%; align: justify;">
				Mohon pihak tuan melakukan pengisian tunai di mesin ATM cawangan (EBC) kami seperti <b>LAMPIRAN A</b>.</div></p>
				</td>
			</tr>
		</table><br>';
		
$bankName = $rs->fields('bankName'); 
$accNo = $rs->fields('accNo');
$bil = 0; // initialize $bil
if ($rs->RowCount() <> 0) {
	$HTML1 = $HTML_TITLE1.'
			<table border="0" style="width: 650px;" align="left">
				<tr font-size: 9pt;">	
					<td valign="middle" align="center" bgcolor="#C0C0C0" height="25" rowspan="2"><b>Bil</b></td>	
					<td valign="middle" align="left"  bgcolor="#C0C0C0" rowspan="2"><b>Doc No</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0" rowspan="2"><b>ATM No</b></td>		
					<td valign="middle" align="left"  bgcolor="#C0C0C0" rowspan="2"><b>ATM-EBC</b></td>
					<td valign="middle" align="left"  bgcolor="#C0C0C0" rowspan="2"><b>ATM Branch</b></td>
					<td valign="middle" align="center"  bgcolor="#C0C0C0" colspan="4"><b>Denominasi</b></td>
					<td valign="middle" align="center"  bgcolor="#C0C0C0" rowspan="2"><b>Jumlah</b></td>
				</tr>
				<tr font-size: 9pt;">	
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM100</b></td>		
					<td valign="middle" align="center"  bgcolor="#C0C0C0"><b>RM50</b></td>
					<td valign="middle" align="center"  bgcolor="#C0C0C0"><b>RM20</b></td>
					<td valign="middle" align="center" bgcolor="#C0C0C0"><b>RM10</b></td>
				</tr>';
	$totalAll='0';
	while (!$rs->EOF) {
	    $bil++;
		$totalAll += $rs->fields('totalValue');
		$HTML1 .= '
				<tr style="font-size: 9pt;">
					<td valign="top" align="center" style="width:20px;">'.$bil.'.</td>
					<td valign="top" align="left"" style="width:160px;">'.$rs->fields('docNo').'</td>
					<td valign="top" align="center" style="width:50px;">'.$rs->fields('atmNo').'</td>
					<td valign="top" align="left"" style="width:290px;">'.$rs->fields('atmLoc').'</td>
					<td valign="top" align="left"" style="width:120px;">'.$rs->fields('atmBranch').'</td>
					<td valign="top" align="center" style="width:60px;">'.$rs->fields('qty100').'</td>
					<td valign="top" align="center" style="width:60px;">'.$rs->fields('qty50').'</td>
					<td valign="top" align="center" style="width:60px;">'.$rs->fields('qty20').'</td>
					<td valign="top" align="center" style="width:60px;">'.$rs->fields('qty10').'</td>
					<td valign="top" align="right" style="width:80px;">'.number_format($rs->fields('totalValue'),2).'</td>
				</tr>';
		$rs->MoveNext();
	}
		$HTML1 .= '
			<tr style="font-size: 9pt;"><td colspan="9"></td><td><hr size="1" style="width:80px;"></td></tr>
			<tr style="font-size: 9pt;">
				<td align="right" colspan="9"><b>Jumlah Amaun</b></td>
				<td align="right" style="width:100px;"><b>'.number_format($totalAll,2).'</b></td>
			</tr>			
			<tr style="font-size: 9pt;"><td colspan="9"></td><td><hr size="1" style="width:80px;"></td></tr>
			</table><br>';
}	

		
$HTML .= '		
		<table border="0" style="width: 650px;" align="center">
		   	<tr style="font-size: 10pt;">
				<td>
				<p><div style="width: 100%; align: justify;">
				Pihak kami akan melakukan pindahan dana kepada tuan melalui Akaun Semasa  '.$bankName.' No '.$accNo.' 
				sebanyak RM'.number_format($totalAll,2).' pada '.todate('d-m-Y',$dt).'</div></p>
				
				<p><div style="width: 100%; align: justify;">
				Sila pihak tuan memastikan kakitangan yang ditugaskan melakukan pengisian tunai, melaksanakan <b>�Total Reset Withdrawal�</b> 
				supaya baki tunai di mesin ATM tersebut dikemaskini dan seterusnya menyerahkan <b>�jernal roll�</b> di Cawangan berkenaan untuk semakan.
				</div></p>
				</td>
			</tr>
		</table>
		<br>
		<table style="width: 100%;" align="left">
		   	<tr style="font-size: 10pt;">
				<td style="width:100%;">
				<p>Sekian, terima kasih.</p>
				<p></p><p></p><p></p>
				______________________________________<br>
				Penandatangan Berkuasa<br>
				Jabatan Pusat ATM
				</td>
			</tr>
		</table>
		
		</td>
	</tr>
</table>';

//print $HTML;
// FIXED: lowercase writeHTML and removed the second parameter
$pdf->writeHTML($HTML);		
$pdf->writeHTML($HTML1);	
// FIXED: lowercase output
$pdf->output('e-atm.pdf');
?>