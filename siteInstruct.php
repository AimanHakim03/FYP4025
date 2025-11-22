<?php


//$conn->debug=true;
$sSQL = " 
SELECT	instructDate, count(*) AS total FROM instruct 
WHERE 	isDeleted ='0' AND MONTH(instructDate)= '".$mm."' AND YEAR(instructDate) = '".$yy."' ";
if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") {
	if ($_SESSION['Session.branchID'] <> "") {
		$sSQL .= " AND vendorID = '".$_SESSION['Session.branchID']."' ";
	}
}
if ($ty <> '') {
	$sSQL .= " AND type = '".$ty."' ";
}
$sSQL .= "
GROUP BY instructDate  ";
$rsD = &$conn->Execute($sSQL);	

$siList = Array();
$siVal = Array();
if ($rsD->RowCount() <> 0){
	while (!$rsD->EOF) {
		array_push ($siList, todate('d-m-Y',$rsD->fields('instructdate')));
		array_push ($siVal, $rsD->fields('total'));
		$rsD->MoveNext();
	}
}

print '
<table border=0 cellpadding=3 cellspacing=1 align="center">
	<tr>
		<td class="textFontBold" height="30" valign="middle" colspan="'.count($siList).'">
		ACTIVITI ARAHAN BAGI BULAN '.strtoupper($monthList[array_search($mm, $monthVal)]).'&nbsp;'.$yy.'.</td>
	</tr>
	<tr class="Header"><td height="30" valign="middle" align="center" colspan="'.count($siList).'">TARIKH</td></tr>';
if (count($siList) <> 0) {  
	print '
	<tr class="Label">';
	for($j=0 ; $j < count($siList); $j++) {
		print'
		<td align="center" width="60">'.$siList[$j].'</td>';
	}
	print '
	</td>
	<tr class="data">';
	for($j=0 ; $j < count($siList); $j++) {
		print'
		<td align="center" width="60">'.$siVal[$j].'</td>';
	}
	print '
	</td>';
} else {
	print '<tr class="data"><td align="center" valign="middle" colspan="'.count($siList).'" height="50"><b>- Tiada rekod maklumat ARAHAN -</b></td></tr>';
}
print '
	</tr>
</table>';
?>