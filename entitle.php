<?php
//$conn->debug=true;
if (!isset($basicSalary) OR $basicSalary == '')	$basicSalary = '0.00';
if (!isset($fixedAllow) OR $fixedAllow == '')	$fixedAllow = '0.00';
print '
	<input type="Hidden" name="rate" value="'.$rate.'">
	<tr class=dashboard2>
		<td height="25" valign="middle" colspan="4">
		<b>SEMAKAN KELAYAKAN</b> (Kadar Keuntungan : <b>'.$rate.'%</b>&nbsp;|&nbsp;Tempoh Pembiayaan : 
		<select name="period" class="Data"  onchange="document.MyDHSB.submit();">
			<option value="">-- ';
for ($i = 0; $i < count($homTenureList); $i++) {
	print '	<option value="'.$homTenureList[$i].'"';
	if ($period == $homTenureList[$i]) print 'selected';
	print '>'.$homTenureList[$i];
}			
print '	</select>&nbsp;|&nbsp;Jumlah Anuiti : ';
$value='0.00';
if ($period <> '') {
	$value = dlookup("rate", "value", "rate= '".$rate."' AND period = '".$period."'");
}
print '	<input type="Hidden" name="value" value="'.$value.'">
		<b>'.$value.'</b>)
		</td>
	</tr>
	<tr>
		<td class="Label" valign="middle" align="right">Gaji Pokok :</td>
		<td class="Data" valign="middle">RM
		<input type="'.$inputText.'" name="basicSalary" value="'.$basicSalary.'" size="10" maxlength="10" onchange="autoCalcLayak();" style="text-align:right;" tabindex="1">'.$errL1;
print '	(cth: 1000.00)</td>
		<td class="Data" valign="middle" rowspan="2" colspan="2">
		<i class="maroonText">Sila pilih Tempoh Pembiayaan, masukkan Gaji Pokok dan Elaun Tetap untuk mendapatkan Jumlah Kelayakan Pinjaman Anda.</i>
		</td>
	</tr>
	<tr>
		<td class="Label" valign="middle" align="right">Elaun Tetap :</td>
		<td class="Data" valign="middle">RM
		<input type="'.$inputText.'" name="fixedAllow" value="'.$fixedAllow.'" size="10" maxlength="10" onchange="autoCalcLayak();" style="text-align:right;" tabindex="1">'.$errL2;
print '	(cth: 250.00</td>
	</tr>
	<tr>
		<td class="Label" valign="middle" align="right">Gaji Kasar :</td>
		<td class="Data" valign="middle">RM
		<input type="text" name="grossSalary" value="'.$grossSalary.'" size="10" maxlength="10" class="Data" style="border:none; text-align:right;font-weight:bold;" readonly=""></b>';
print '	</td>
		<td class="dashboard2" valign="middle" align="right"><b>JUMLAH KELAYAKAN PEMBIAYAAN :</b></td>
		<td class="Data" valign="middle">RM<input type="text" name="jkvalue" value="'.$jkvalue.'" size="20" maxlength="20" class="Data" style="border:none; text-align:left;font-weight:bold;" readonly=""></b></td>
	</tr>
<script>
	function autoCalcLayak() {
		e = document.MyDHSB;	
		AMT=0;TOTALGROSS=0;
	    for(c=0; c<e.elements.length; c++) {
	    	if (e.elements[c].name=="basicSalary" || e.elements[c].name=="fixedAllow") {
				AMT = e.elements[c].value;
				if (isNaN(AMT)) {	
					alert("Masukkan digit/nombor sahaja...!");
					e.elements[c].value=0;		
					AMT=0;
				}
				TOTALGROSS += eval(AMT);
	    	}
		}
		e.grossSalary.value = TOTALGROSS.toFixed(2);
		if (e.grossSalary.value != "0.00") {
			e.jkvalue.value = eval(((TOTALGROSS/3) / e.value.value) * 1000).toFixed(2);
		}
	}	
</script>';
?>