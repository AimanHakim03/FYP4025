<?php 
if ($branchID == '') {
	$sSQL = " SELECT * FROM branch WHERE branchID =  ".toSQL($_SESSION['Session.branchID'], "Text");
} else {
	$sSQL = " SELECT * FROM branch WHERE branchID =  ".toSQL($branchID, "Text");
}
$rsB = &$conn->Execute($sSQL);
$address	= str_replace("<pre>","",$rsB->fields('address'));
$address	= str_replace("</pre>","",$address);

$HTML_TITLE = '
<page backtop="25mm" backbottom="15mm">
	<page_header>
		<table style="width: 100%;">
			<tr>
				<td style="text-align: left;	width: 10%">&nbsp;</td>
				<td style="text-align: center;	width: 80%"><img src="../images/'.$ogoLedocrab.'" border="0"></td>
				<td style="text-align: right;	width: 10%">&nbsp;</td>
			</tr>
		</table>
		<table style="width: 100%;" align="center">
			<tr style="font-size: 12pt;"><td align="center"><b>'.strtoupper($rsB->fields('branchName')).'</b></td></tr>
			<tr style="font-size: 12pt;"><td align="center" ><b>'.$title.'</b></td></tr>
		</table>
	</page_header>
	<page_footer>
		<table style="width: 100%;">
			<tr style="font-size: 7.5pt;">
				<td style="text-align: center; width: 100%"><i>'.strtoupper($rsB->fields('branchName')).'<br>'.$address.'
				<br>Tel : '.$rsB->fields('telNo').' , Fax : '.$rsB->fields('faxNo').'</i></td> 
			</tr>		
		</table>
	</page_footer>
</page>';
?>