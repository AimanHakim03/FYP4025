<?php


$totalQty=0; $totalQtyP=0; $totalQtyB=0; $totalQtyL=0;

$qty1=0; $qty1P=0; $qty1T=0; $qty1L=0;
$sSQL = " SELECT 	count(*) AS qty1, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty1P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty1T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty1L ".
		" FROM 		house WHERE YEAR(dateApply) = '".date("Y")."'  ";		
$rsAMD1 = &$conn->Execute($sSQL);	
if ($rsAMD1 <> 0) {
	$qty1 	= $rsAMD1->fields('qty1');
	$qty1P 	= $rsAMD1->fields('qty1P');
	$qty1T 	= $rsAMD1->fields('qty1T');
	$qty1L 	= $rsAMD1->fields('qty1L');
}

$qty2=0; $qty2P=0; $qty2T=0; $qty2L=0;
$sSQL = " SELECT 	count(*) AS qty2, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty2P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty2T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty2L ".
		" FROM 		vehicle WHERE YEAR(dateApply) = '".date("Y")."' ";		
$rsAMD2 = &$conn->Execute($sSQL);	
if ($rsAMD2 <> 0) {
	$qty2 	= $rsAMD2->fields('qty2');
	$qty2P 	= $rsAMD2->fields('qty2P');
	$qty2T 	= $rsAMD2->fields('qty2T');
	$qty2L 	= $rsAMD2->fields('qty2L');
}

$qty3=0; $qty3P=0; $qty3T=0; $qty3L=0;
$sSQL = " SELECT 	count(*) AS qty3, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty3P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty3T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty3L ".
		" FROM 		education WHERE YEAR(dateApply) = '".date("Y")."' ";		
$rsAMD3 = &$conn->Execute($sSQL);	
if ($rsAMD3 <> 0) {
	$qty3 	= $rsAMD3->fields('qty3');
	$qty3P 	= $rsAMD3->fields('qty3P');
	$qty3T 	= $rsAMD3->fields('qty3T');
	$qty3L 	= $rsAMD3->fields('qty3L');
}

$qty4=0; $qty4P=0; $qty4T=0; $qty4L=0;
$sSQL = " SELECT 	count(*) AS qty4, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty4P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty4T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty4L ".
		" FROM 		share WHERE YEAR(dateApply) = '".date("Y")."' ";		
$rsAMD4 = &$conn->Execute($sSQL);	
if ($rsAMD4 <> 0) {
	$qty4 	= $rsAMD4->fields('qty4');
	$qty4P 	= $rsAMD4->fields('qty4P');
	$qty4T 	= $rsAMD4->fields('qty4T');
	$qty4L 	= $rsAMD4->fields('qty4L');
}

$qty5=0; $qty5P=0; $qty5T=0; $qty5L=0;
$sSQL = " SELECT 	count(*) AS qty5, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty5P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty5T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty5L ".
		" FROM 		computer WHERE YEAR(dateApply) = '".date("Y")."' ";		
$rsAMD5 = &$conn->Execute($sSQL);	
if ($rsAMD4 <> 0) {
	$qty5 	= $rsAMD5->fields('qty5');
	$qty5P 	= $rsAMD5->fields('qty5P');
	$qty5T 	= $rsAMD5->fields('qty5T');
	$qty5L 	= $rsAMD5->fields('qty5L');
}

$qty6=0; $qty6P=0; $qty6T=0; $qty6L=0;
$sSQL = " SELECT 	count(*) AS qty6, ".
		"			COALESCE(SUM(CASE status WHEN 1 THEN 1 ELSE 0 END),0) AS qty6P, ".
		"			COALESCE(SUM(CASE status WHEN 2 THEN 1 ELSE 0 END),0) AS qty6T, ".
		"			COALESCE(SUM(CASE status WHEN 3 THEN 1 ELSE 0 END),0) AS qty6L ".
		" FROM 		festival WHERE YEAR(dateApply) = '".date("Y")."' ";		
$rsAMD6 = &$conn->Execute($sSQL);	
if ($rsAMD4 <> 0) {
	$qty6 	= $rsAMD6->fields('qty6');
	$qty6P 	= $rsAMD6->fields('qty6P');
	$qty6T 	= $rsAMD6->fields('qty6T');
	$qty6L 	= $rsAMD6->fields('qty6L');
}

$totalQty 	= $qty1 + $qty2 + $qty3 + $qty4 + $qty5 + $qty6;
$totalQtyP 	= $qty1P + $qty2P + $qty3P + $qty4P + $qty5P + $qty6P;
$totalQtyT	= $qty1T + $qty2T + $qty3T + $qty4T + $qty5T + $qty6T;
$totalQtyL 	= $qty1L + $qty2L + $qty3L + $qty4L + $qty5L + $qty6L;

print '
<table border=0 cellpadding=3 cellspacing=1 width=700 align="center">
	<tr>
		<td class="textFontBold" height="30" valign="middle" colspan="5">AKTIVITI TAHUNAN PADA '.date("Y").'</td>
	</tr>
	<tr class="Header">
		<td rowspan="2">E-Borang</td>
		<td width="80" rowspan="2" align="center">Jumlah</td>
		<td width="240" align="center" valign="middle" colspan="3" align="center">Status</td>
	</tr>
	<tr class="Header">
		<td align="center" width="80">Proses</td>
		<td align="center" width="80">Tolak</td>
		<td align="center" width="80">Lulus</td>
	</tr>
<!---	<tr align="right" class="Data">
		<td align="left">Pembiayaan Perumahan i Warga Kerja</td>
		<td>'.$qty1.'&nbsp;</td>		
		<td>'.$qty1P.'&nbsp;</td>		
		<td>'.$qty1T.'&nbsp;</td>		
		<td>'.$qty1L.'&nbsp;</td>		
	</tr>
	<tr align="right" class="Data1">
		<td align="left">Pembiayaan Kenderaan i Warga Kerja</td>
		<td>'.$qty2.'&nbsp;</td>		
		<td>'.$qty2P.'&nbsp;</td>		
		<td>'.$qty2T.'&nbsp;</td>		
		<td>'.$qty2L.'&nbsp;</td>		
	</tr>
	<tr align="right" class="Data">
		<td align="left">Pembiayaan Pendidikan Warga Kerja</td>
		<td>'.$qty3.'&nbsp;</td>		
		<td>'.$qty3P.'&nbsp;</td>		
		<td>'.$qty3T.'&nbsp;</td>		
		<td>'.$qty3L.'&nbsp;</td>		
	</tr>	
	<tr align="right" class="Data1">
		<td align="left">Pembiayaan Saham Warga Kerja</td>
		<td>'.$qty4.'&nbsp;</td>		
		<td>'.$qty4P.'&nbsp;</td>		
		<td>'.$qty4T.'&nbsp;</td>		
		<td>'.$qty4L.'&nbsp;</td>		
	</tr>	
	<tr align="right" class="Data">
		<td align="left">Pembiayaan Komputer Persendirian Warga Kerja</td>
		<td>'.$qty5.'&nbsp;</td>		
		<td>'.$qty5P.'&nbsp;</td>		
		<td>'.$qty5T.'&nbsp;</td>		
		<td>'.$qty5L.'&nbsp;</td>		
	</tr>	--->
	<tr align="right" class="Data">
		<td align="left">Pembiayaan Perayaan Warga Kerja</td>
		<td>'.$qty6.'&nbsp;</td>		
		<td>'.$qty6P.'&nbsp;</td>		
		<td>'.$qty6T.'&nbsp;</td>		
		<td>'.$qty6L.'&nbsp;</td>		
	</tr>	
	<tr align="right" class="LabelH" >
		<td>Jumlah : </td>
		<td>'.$totalQty.'&nbsp;</td>		
		<td>'.$totalQtyP.'&nbsp;</td>		
		<td>'.$totalQtyT.'&nbsp;</td>		
		<td>'.$totalQtyL.'&nbsp;</td>		
	</tr>	
</table>';
?>