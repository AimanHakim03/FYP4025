<?

include ("header.php");
include("forms.php");
//$conn->debug=true;

if (!isset($dd)) 	$dd	= date("d");                   		
if (!isset($mm)) 	$mm	= date("m");                 		
if (!isset($yy))	$yy	= date("Y");  
if (!isset($ty))	$ty	= '';
$dmy = $dd.'/'.$mm.'/'.$yy;
if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.$dmy.'");</script>';
	$dd=1;
}
$ymd = $yy.'-'.$mm.'-'.$dd;

$disabled= ' value="Keluar Arahan" ';
if ($yy.$mm.$dd < date('Ymd')) {
	$disabled = ' value= "Arahan Ditutup"  disabled ';
}

//--- BEGIN : INSTRUCT TERMINAL -------------------------------------------------------------------
//--- END   : INSTRUCT TERMINAL -------------------------------------------------------------------

//$conn->debug=true;
$sSQL = " 
SELECT	A.instructDate, A.vendorID, C.vendorName, CONVERT(date,A.createdDate) as createDate, COUNT(*) AS totalATM, SUM(totalValue) AS totalVal
FROM 	instruct A  
LEFT JOIN vendor C  ON	A.vendorID = C.vendorID
WHERE 	A.isDeleted ='0' ";
if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") {
	if ($_SESSION['Session.branchID'] <> "") {
		$sSQL .= " AND A.vendorID = '".$_SESSION['Session.branchID']."' ";
	}
}

$sSQL .= " AND MONTH(A.instructDate) = '".$mm."' ";
$sSQL .= " AND YEAR(A.instructDate) = '".$yy."' ";

$sSQL .= "
GROUP BY A.instructDate, A.vendorID, C.vendorName, CONVERT(date,A.createdDate)
ORDER BY A.instructDate DESC  ";
$rs = &$conn->Execute($sSQL);	

print '
<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">
<table border=0 cellpadding=3 cellspacing=1 width="90%" align="center">
	<tr>
		<td class="textFontBold" height="30" valign="middle" colspan="19">ARAHAN PENGISIAN DENOMINASI ATM PADA BULAN 
		<select name="mm" class="textFontBold" onchange="document.MyZSSB.submit();">';
for ($m = 0; $m < count($monthList); $m++) {
	print '		<option value="'.$monthVal[$m].'"';
	if ($mm == $monthVal[$m]) print 'selected';
	print 		'>'.strtoupper($monthList[$m]);
}
print '	</select>		
		<select name="yy" class="textFontBold" onchange="document.MyZSSB.submit();">';
for ($y = 0; $y < count($yrList); $y++) {
	print '		<option value="'.$yrList[$y].'"';
	if ($yy == $yrList[$y]) print 'selected';
	print 		'>'.$yrList[$y];
}
print '	</select>';
if ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "9") {
	print'
		<input type="Button" name="action1" '.$disabled.' onClick="InstructAct(\'arahan\');"  >';
}
print '	</td>
	</tr>
	<tr class="Header">
		<td align="right">BIL</td>
		<td align="center">TARIKH ARAHAN</td>
		<td align="center">SYARIKAT</td>
		<td align="center">JUMLAH ATM</td>
		<td align="center">AMAUN<br>ARAHAN (RM)</td>
		<td align="center">TARIKH<br>KELUAR ARAHAN</td>
		<td align="center">STATUS</td>
		<td align="center">TINDAKAN</td>
	</tr>';
$sd=0;$cnt=0;
if ($rs->RowCount() <> 0) {  
	while (!$rs->EOF) {
		$sd++;
		if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';
		print '
		<tr align="right" class="'.$strClass.'">
			<td align="right" width="5">'.$sd.'.</td>
			<td align="center">'.todate('d/m/Y',$rs->fields('instructDate')).'&nbsp;&nbsp;</td>			
			<td align="left">'.$rs->fields('vendorName').'</td>
			<td align="right" width="70">'.$rs->fields('totalATM').'&nbsp;</td>		
			<td align="right" width="120">'.number_format($rs->fields('totalVal'),2).'&nbsp;</td>	
			<td align="center">'.todate('d/m/Y',$rs->fields('createDate')).'</td>
			<td align="right" width="120">';	
			
			$sSQL1 = " 	SELECT	count(*) AS replenish  	FROM replenish 
						WHERE 	isVerified = '1'
						AND vendorID = '".$rs->fields('vendorID')."' 
						AND instructDate = '".$rs->fields('instructDate')."' ";
			$rs1 = &$conn->Execute($sSQL1);
			print 'Isian Semula ('.$rs1->fields('replenish').') <br>';

			$sSQL1 = " 	SELECT	count(*) AS recon  	FROM recon 
						WHERE 	isVerified = '1' 
						AND vendorID = '".$rs->fields('vendorID')."' 
						AND instructDate = '".$rs->fields('instructDate')."' ";
			$rs1 = &$conn->Execute($sSQL1);
			
			print 'Pengeluaran ('.$rs1->fields('recon').') </td>
			<td align="right">';
			
		if ($_SESSION['Session.groupID'] == "1" OR $_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "9") {
			print '
				<a href="denomasuk.php?action=update&pk='.$rs->fields('instructDate').'&vid='.$rs->fields('vendorID').'&strURL='.$strURL.'">
				<img src="images/atm-in-small.png" border="0" title="Masuk Deno"></a>&nbsp;
				
				<img src="images/atm-out-small.png" border="0" title="Keluar Deno"">';

		}
		print '
			</td>
		</tr>';	
		$cnt++;
		$rs->MoveNext();
	}
	print '
		<tr align="right" class="LabelH" >
		</tr>';
} else {
	print '
	<tr align="right" class="Data">
		<td align="center" valign="middle" colspan="10" height="50"><b>- Tiada rekod maklumat arahan -</b></td>
	</tr>';
}
print'
</table>
</form>

<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll(id) {
	    e = document.MyZSSB.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
			if (id == "A") {
				if(e[c].type=="checkbox" && e[c].name=="pkA[]") {
		        	e[c].checked = allChecked;
		      	}
			}
	    }
	}
	
	function InstructAct(v) {
	    e = document.MyZSSB;
	    if (e==null) {
	       	alert(\'Please confirm the form name.!\');
	    } else {
	       	count=0;
	     	for(c=0; c<e.elements.length; c++) {
	        	if(e.elements[c].name=="pkA[]" && e.elements[c].checked) {
	           		count++;
	         	}
	       	}
		}
	    if (count==0) {
	    	alert(\'Sila pilih rekod (kolum kuning) untuk Keluar Arahan.\');
	    } else {
			if(confirm(\'Keluar Arahan untuk : \' + count + \' rekod?\')) {
				e.action.value=v;
				e.submit();
				e.action1.disabled=true;		
				e.action1.value = \'Sila tunggu.....\';	
			}
		}
	}
</script>';


include("footer.php");	
?>