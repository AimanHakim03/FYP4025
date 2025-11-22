<?php

//$conn->debug=true;

$sSQL = " 
SELECT	A.collectDate, B.vendorID, B.vendorCode, B.vendorName, A.status, count(*) AS terminalA, SUM(A.totalValue) AS totalValueA   
FROM 	collect A  LEFT JOIN vendor B  ON		A.vendorID = B.vendorID 
WHERE 	A.isDeleted ='0' ";

if ($mm <> ''){
	$sSQL .= " AND MONTH(A.collectDate) = '".$mm."' ";
}
$sSQL .= " AND YEAR(A.collectDate) = '".$yy."' ";

if ($vendorID <> '') {
	$sSQL .= " AND	A.vendorID = ".tosql($vendorID, "Text");
}
if ($ty <> '') {
	$sSQL .= " AND A.type = '".$ty."' ";
}
$sSQL .= "
GROUP BY A.collectDate, B.vendorID, B.vendorCode, B.vendorName, A.status  ";
$rsD = &$conn->Execute($sSQL);	

print '
<table border=0 cellpadding=5 cellspacing=1 align="center" width="98%">
	<tr>
		<td class="textFontBold" valign="middle" colspan="6">COLLECTION</td>
	</tr>
	<tr class="Header">
		<td rowspan="2" align="center" width="3%">BIL</td>
		<td rowspan="2" align="center">TARIKH<br>KUTIPAN</td>
		<td rowspan="2" align="center">SYARIKAT</td>
		<td colspan="2" align="center" bgcolor="#FF9900">COLLECTION</td>
		<td rowspan="2" align="center">STATUS</td>
	</tr>
	<tr class="Header">
		<td align="center" bgcolor="#FF9900">JUMLAH<br>TERMINAL</td>
		<td align="center" bgcolor="#FF9900">AMAUN (RM)</td>
	</tr>';
$sd=0;$cnt=0;
$allTerminalA=0; $allValueA=0;
if ($rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';
		
		$allTerminalA	+= $rsD->fields('terminalA'); 
		$allValueA		+= $rsD->fields('totalValueA'); 
		
		if ($rsD->fields('status') == '0') 
			$status = '<font class="redText">** BELUM SELESAI **</font>';
		else
			$status = '<font class="greenText">** SELESAI **</font>';
		print '
		<tr align="right" class="'.$strClass.'">
			<td align="right" width="20">'.$sd.'.</td>
			<td align="center" width="100">'.todate('d/m/Y',$rsD->fields('collectDate')).'</td>
			<td align="left">'.$rsD->fields('vendorName').'</td>
			<td align="right" width="80">'.$rsD->fields('terminalA').'&nbsp;</td>		
			<td align="right" width="120">'.number_format($rsD->fields('totalValueA'),2).'&nbsp;</td>	
			<td align="center" width="150">'.$status.'</td>
		</tr>';	
		$cnt++;
		$rsD->MoveNext();
	}
	print '
		<tr align="right" class="LabelH" >
			<td colspan="3">Jumlah : </td>
			<td align="right">'.$allTerminalA.'&nbsp;</td>	
			<td align="right">'.number_format($allValueA,2).'&nbsp;</td>	
			<td></td>	
		</tr>';
} else {
	print '
	<tr align="right" class="Data">
		<td align="center" valign="middle" colspan="10" height="50"><b>- Tiada rekod maklumat kutipan  -</b></td>
	</tr>';
}
print'
</table>';
?>