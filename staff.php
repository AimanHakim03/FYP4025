<?php

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($branch))	$branch="";
include("header.php");	
//$connM->debug=true;

$sFileName = 'staff.php';
$sFileRef  = 'staffAddUpdate.php';
$title     = 'WARGA KERJA';

if ($_SESSION['Session.groupID'] <> "3" AND $_SESSION['Session.groupID'] <> "9")	{
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

$strURL = urlencode($QUERY_STRING);
if ($strURL =='') {
	$strURL = 'q='.$q.'&hq='.$hq.'&branch='.$branch;
	$strURL = urlencode($strURL);
}

if ($_SESSION['Session.groupID'] <> "3" AND $_SESSION['Session.groupID'] <> "9")	{
  $branchID = $_SESSION['Session.branchID'];
}

//--- BEGIN : PREPARE DEPT/BRANCH LISTING ---------------------------------------------------------------------------------------
$branchList = Array();
$branchVal = Array();
$sSQL = " SELECT Code, Name FROM Departments ORDER BY Name ";
$GetList = &$connM->Execute($sSQL);
if ($GetList->RowCount() <> 0){
	while (!$GetList->EOF) {
		array_push ($branchList, $GetList->fields('Name'));
		array_push ($branchVal, $GetList->fields('Code'));
		$GetList->MoveNext();
	}
}
//--- END   : PREPARE DEPT/BRANCH LISTING ---------------------------------------------------------------------------------------

$sSQL = "	SELECT	A.*, B.Name AS DeptDesc, C.Name AS PositionDesc FROM StaffProfiles A 
			LEFT JOIN Departments B 
			ON 		A.DeptCode = B.Code 
			LEFT JOIN Positions C 
			ON 		A.PositionCode = C.Code ";	
if ($branch <> '') $sSQL .= " WHERE A.DeptCode = '".$branch."' ";
if ($q <> '') {
	$sSQL	=	" SELECT 	Q.* FROM (".$sSQL.") Q ".
				" WHERE		Q.StaffNo	LIKE ".tosql($q."%","Text").
				" OR 		Q.StaffName LIKE ".tosql($q."%","Text").
				" ORDER BY 	Q.StaffName ";
} else {
	$sSQL .= " ORDER BY A.StaffNo ";
}

$GetList = &$connM->Execute($sSQL);
$GetList->Move($StartRec-1);

//--- BEGIN : CATER FOR NUMBER OF RECORDS AND PAGE TO BE DISPLAYED --------------------------------------------------------------
$TotalRec = $GetList->RowCount();
$TotalPage =  ($TotalRec/$pg);

if ($StartRec < $TotalRec) {
	if (($TotalRec-($StartRec + $pg)) < 0) {
		$NextRec = $StartRec;
	} else {
		$NextRec = $StartRec + $pg;
	}
} else {
	$NextRec = $StartRec;
}
if ($StartRec != 1) {
	$PrevRec = $StartRec - $pg;
} else {
	$PrevRec = 1;
}		
if ($TotalRec != 0) {
	if (($TotalRec % $pg) == 0) {
		$LastRec = ($TotalPage * $pg) - $pg + 1;
	} else {
		$LastRec = ($TotalPage * $pg) + 1;
	}
} else {
	$LastRec = 1;
}
//--- END   : CATER FOR NUMBER OF RECORDS AND PAGE TO BE DISPLAYED --------------------------------------------------------------

print '
<form name="ITRViewResults" action=' .$sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="q" value="'.$q.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td	 colspan="2"><b class="maroonText">'.$title.'</b></td>
	</tr>
    <tr valign="top" class="Label">
	   	<td align="left"  class="textFontBold" height="30" valign="middle">
		Carian melalui No Pekerja/Nama <input type="text" name="q" value="" maxlength="100" size="30" class="Data">
		<input type="submit" class="but" value="Capai">
		</td>
	</tr>
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
				<td class="textFont">
				Jabatan/Cawangan
		    	<select name="branch" class="Data"  onchange="document.ITRViewResults.submit();"> 
			   		<option value="">- Semua - ';
	for ($i = 0; $i < count($branchList); $i++) {
		print '		<option value="'.$branchVal[$i].'"';
		if ($branch == $branchVal[$i]) print 'selected';
		print 		'>'.$branchList[$i];
	}			
	print '		</select>
				</td>
				<td align="right" class="textFont">
				Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
				if ($pg == 50)	print '<option value="50"  selected>50</option>'; 	else print '<option value="50">50</option>';				
				if ($pg == 100)	print '<option value="100" selected>100</option>'; 	else print '<option value="100">100</option>';				
				if ($pg == 150)	print '<option value="150" selected>150</option>'; 	else print '<option value="150">150</option>';				
				if ($pg == 200)	print '<option value="200" selected>200</option>'; 	else print '<option value="200">200</option>';				
				if ($pg == 250)	print '<option value="250" selected>250</option>';	else print '<option value="250">250</option>';				
				if ($pg == 300)	print '<option value="300" selected>300</option>';	else print '<option value="300">300</option>';				
	print '		</select> setiap mukasurat.
				</td>
				</tr>
			</table>
		</td>
	</tr>';				
	if ($GetList->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="3" width="100%" class="lineBG">
					<tr class="header">
						<td width="1%" height="30">&nbsp;</td>
						<td>No Pekerja-Nama</td>
						<td>Jabatan/Cawangan</td>
						<td>Jawatan</td>
						<td align="center">Tarikh Berkhidmat</td>
						<td>Melapor Kepada</td>
					</tr>';	
					
		while (!$GetList->EOF && $cnt <= $pg) {
			if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';

			$DirectBossId = $GetList->fields('DirectBossId');
			$sSQL = "SELECT StaffName FROM StaffProfiles WHERE StaffNo = '".$GetList->fields('DirectBossId')."' ";
			$rs = &$connM->Execute($sSQL);
			if ($rs->RowCount() == 1) $DirectBossId .= "&nbsp;-&nbsp;".$rs->fields('StaffName');
			
			print ' <tr>
						<td class="'.$strClass.'" align="right" valign="top">'.$bil.'&nbsp;</td>
						<td class="'.$strClass.'" valign="middle">
						'.$GetList->fields('StaffNo').'&nbsp;-&nbsp;'.$GetList->fields('Staffname').'<br>
						<font class="blueText"><i>'.$GetList->fields('Email').'</i></font>
						</td>
						<td class="'.$strClass.'" valign="middle">'.$GetList->fields('DeptDesc').'</td>
						<td class="'.$strClass.'" valign="middle">'.$GetList->fields('PositionDesc').'</td>
						<td class="'.$strClass.'" valign="middle" align="center">'.todate('d-m-Y',$GetList->fields('JoinDate')).'</td>
						<td class="'.$strClass.'" valign="middle">'.$DirectBossId.'</td>
					</tr>';
				$cnt++;
				$bil++;
			$GetList->MoveNext();
		}
		print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
				if ($TotalRec > $pg) {
					print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
					if ($TotalRec % $pg == 0) {
						$numPage = $TotalPage;
					} else {
						$numPage = $TotalPage + 1;
					}
					print '<tr><td class="textFont" valign="top" align="left">Rekod dari : <br>';
					for ($i=1; $i <= $numPage; $i++) {
						$linkRef = $sFileName.'?StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.$q.'&branch='.$branch;
						print '<A href="'.$linkRef.'">';
						print '<b class="blueText"><u>'.$i.'</u></b></a>&nbsp; ';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Rekod : <b>' . $GetList->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q <> '') {
			print '
			<tr>
				<td align="center">
				<hr size=1"><b class="textFont">- Rekod ('.$q.') tidak wujud -</b><hr size=1">
				</td>
			</tr>';		
		} else {
			print '
			<tr>
				<td align="center">
				<hr size=1"><b class="textFont">- Tiada Rekod -</b><hr size=1">
				</td>
			</tr>';
		}
	}
print ' 
</table>
</form>';

include("footer.php");	


print '
<script language="JavaScript">
	function doListAll() {
		c = document.forms[\'ITRViewResults\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function selGroup() {
		c = document.forms[\'ITRViewResults\'].q;
		document.location = "' . $sFileName . '?StartRec=1&q=" + c.options[c.selectedIndex].value;
	}
	
</script>';
?>
