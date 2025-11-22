<?php
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
	<page_footer>
		<table style="width: 100%;">
			<tr style="font-size: 7.5pt;">
				<td style="text-align: left;  width: 50%"><i>Cetak pada : '.date("F j, Y, g:i a").'</i></td>
				<td style="text-align: right; width: 50%"><i>Mukasurat : [[page_cu]]/[[page_nb]]</i></td>
			</tr>
		</table>
	</page_footer>
</page>';
?>