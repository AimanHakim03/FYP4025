<?

include ("header.php");
include("forms.php");
//$conn->debug=true;

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

if (!isset($dd)) 	$dd	= SUBSTR($dt,8,2);                   		
if (!isset($mm)) 	$mm	= SUBSTR($dt,5,2); ;                 		
if (!isset($yy))	$yy	= SUBSTR($dt,0,4); 
if (!isset($ty))	$ty	= '';
$dmy = $dd.'/'.$mm.'/'.$yy;

if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.$dmy.'");</script>';
}
$ymd = $yy.'-'.$mm.'-'.$dd;

if ($dt =='') $dt = $ymd;
if ($strURL =='') {
	$strURL = 'dt='.$dt.'&vid='.$vid;
	$strURL = urlencode($strURL);
}

//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete") {
	$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));

	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan ".$docNo." - Dihapuskan";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= "UPDATE instruct SET isDeleted = '1' WHERE	instructID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Delete ------------------------------------------------------------------------------
//--- BEGIN : Accept ------------------------------------------------------------------------------
if ($action == "accept") {
	$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan ".$docNo." - Diterima";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= 	" UPDATE instruct SET status = '1', acceptedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				" ,acceptedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
				" WHERE	instructID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Accept ------------------------------------------------------------------------------
//--- BEGIN : Send ------------------------------------------------------------------------------
if ($action == "send") {
	$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan ".$docNo." - Hantar ke Pusat ATM";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= 	" UPDATE instruct SET status = '3',approvedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				" ,approvedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
				" WHERE	instructID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Send ------------------------------------------------------------------------------

//--- BEGIN : INSTRUCT TERMINAL -------------------------------------------------------------------
//--- END   : INSTRUCT TERMINAL -------------------------------------------------------------------

$sSQL = " 
SELECT	I.instructID, A.atmID, A.atmNo, A.atmLoc, A.type, C.vendorCode, C.vendorName, I.status,   
		I.qty100, I.qty50, I.qty20, I.qty10, I.totalValue, 
		I.replenish100, I.replenish50, I.replenish20,I.replenish10, I.replenishValue,
		I.unload100, I.unload50, I.unload20, I.unload10, I.unloadValue,
		I.replenishPic, I.unloadPic
FROM 	instruct I 
LEFT JOIN 	atm A  		ON	I.atmID = A.atmID  
LEFT JOIN	vendor C 	ON	I.vendorID = C.vendorID

WHERE 	I.isDeleted ='0' 
AND I.instructDate = '".$dt."'
AND I.vendorID = '".$vid."' ";

$sSQL .= "
ORDER BY A.atmNo  ";
$rsD = &$conn->Execute($sSQL);	

print '
<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">
<input type="hidden" name="vid" value="'.$vid.'">
<table border=0 cellpadding=3 cellspacing=1 width="98%" align="center">
	<tr>
		<td class="textFontBold" height="30" valign="middle" colspan="19">ARAHAN PENGISIAN DENOMINASI ATM PADA 
		<select name="dd" class="textFontBold" onchange="document.MyZSSB.submit();">';
for ($d = 0; $d < count($dayList); $d++) {
	print '		<option value="'.$dayList[$d].'"';
	if ($dd == $dayList[$d]) print 'selected';
	print 		'>'.$dayList[$d];
}
print '	</select>		
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

print '	</td>
	</tr>
	<tr class="Header">
		<td rowspan="2" align="right">BIL</td>
		<td rowspan="2">TERMINAL</td>
		<td rowspan="2">SYARIKAT</td>
		<td rowspan="2" align="center">JENIS</td>
		<td rowspan="2" align="center">&nbsp;</td>
		<td colspan="5" align="center" bgcolor="#009900">DENOMINASI REPLENISH</td>
		<td colspan="5" align="center" bgcolor="#FF0000">DENOMINASI UNLOADED</td>
		<td rowspan="2" align="center">STATUS</td>
		<td rowspan="2" align="center">TINDAKAN</td>
	</tr>
	<tr class="Header">
		<td align="center" bgcolor="#009900">RM100</td>
		<td align="center" bgcolor="#009900">RM50</td>
		<td align="center" bgcolor="#009900">RM20</td>
		<td align="center" bgcolor="#009900">RM10</td>
		<td align="center" bgcolor="#009900">AMAUN (RM)</td>
		<td align="center" bgcolor="#FF0000">RM100</td>
		<td align="center" bgcolor="#FF0000">RM50</td>
		<td align="center" bgcolor="#FF0000">RM20</td>
		<td align="center" bgcolor="#FF0000">RM10</td>
		<td align="center" bgcolor="#FF0000">AMAUN (RM)</td>
	</tr>';
$sd=0;$cnt=0;
$$totalValueU=0; $$totalValueR=0; $totalArahan=0;

if ($rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';

		$totalValueU += $rsD->fields('unloadValue');				
		$totalValueR += $rsD->fields('replenishValue');
		$totalArahan += $rsD->fields('totalValue');

		$replenish100 = $rsD->fields('replenish100'); $qty100 = $rsD->fields('qty100'); $beza100 = $replenish100 - $qty100;
		$replenish50 = $rsD->fields('replenish50'); $qty50 = $rsD->fields('qty50'); $beza50 = $replenish50 - $qty50;
		$replenish20 = $rsD->fields('replenish20'); $qty20 = $rsD->fields('qty20'); $beza20 = $replenish20 - $qty20;
		$replenish10 = $rsD->fields('replenish10'); $qty10 = $rsD->fields('qty10'); $beza10 = $replenish10 - $qty10;
		$beza = $rsD->fields('replenishValue') - $rsD->fields('totalValue');
		
		print '
		<tr align="right" class="'.$strClass.'">
			<td align="right" width="5">'.$sd.'.</td>';
		
		print '	
			<td align="left" nowrap>'.$rsD->fields('atmNo').'&nbsp;-&nbsp;'.$rsD->fields('atmLoc').'</td>
			<td align="center">'.$rsD->fields('vendorCode').'</td>
			<td align="center">'.$rsD->fields('type').'</td>
			<td align="right"><b>Replenish<br>Arahan<br>Varian</b></td>			
			<td align="right" width="60">'.$replenish100.'&nbsp;<br>'.$qty100.'&nbsp;<br><b>'.$beza100.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish50.'&nbsp;<br>'.$qty50.'&nbsp;<br><b>'.$beza50.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish20.'&nbsp;<br>'.$qty20.'&nbsp;<br><b>'.$beza20.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish10.'&nbsp;<br>'.$qty10.'&nbsp;<br><b>'.$beza10.'</b>&nbsp;</td>		
			<td align="right" width="50">';
//			if ($rsD->fields('replenishPic') <> '') {
//				print '
//				<div class="hover_img"><a href="#">'.number_format($rsD->fields('replenishValue'),2).'&nbsp;
//				<img src="'.$rsD->fields('replenishPic').'" alt="image" /></a></div>';
//			} else {
				print number_format($rsD->fields('replenishValue'),2).'&nbsp;<br>';
//			}	
			print number_format($rsD->fields('totalValue'),2).'&nbsp;<br><b>'.number_format($beza,2).'</b>&nbsp;</td>
			<td align="right" width="60">'.$rsD->fields('unload100').'&nbsp;</td>		
			<td align="right" width="60">'.$rsD->fields('unload50').'&nbsp;</td>	
			<td align="right" width="60">'.$rsD->fields('unload20').'&nbsp;</td>	
			<td align="right" width="60">'.$rsD->fields('unload10').'&nbsp;</td>	
			<td align="right" width="60">'.number_format($rsD->fields('unloadValue'),2).'&nbsp;</td>';
		
		print '
			<td align="center" width="60">';
		if ($rsD->fields('status') <> NULL) {
			print $statusList[array_search($rsD->fields('status'),$statusVal)];
		}
		print '
			</td>
			<td align="center" width="90">';
			
			if ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "4" OR $_SESSION['Session.groupID'] == "9") {
				if ($rsD->fields('status') == '0' OR $rsD->fields('status') == '1'){
					print '
				<a href="#" onclick="DeleteID(\''.$rsD->fields('instructID').'\',\''.$docNo.'\')" >
				<img src="images/delete.gif" border="0" title="Hapuskan Rekod Arahan"></a>';
				}
			} 
			if ($rsD->fields('status') == '0' AND $_SESSION['Session.groupID'] == "1"){
				print '
				<a href="#" onclick="AcceptID(\''.$rsD->fields('instructID').'\',\''.$docNo.'\')" >
				<img src="images/app.gif" border="0" title="Terima Rekod Arahan"></a>';
			}
			if ($rsD->fields('status') == '2' AND $_SESSION['Session.groupID'] == "1"){
				print '
				<a href="#" onclick="SendID(\''.$rsD->fields('instructID').'\',\''.$docNo.'\')" >
				<img src="images/send.png" border="0" title="Hantar ke Pusat ATM"></a>';
			}
			if ($rsD->fields('status') == '1' OR $rsD->fields('status') == '2'){
			print '
				<a href="#" onclick="window.location.href = \'denoMasuk.php?action=update&amp;pk='.$rsD->fields('instructID').'&strURL='.$strURL.'\'">
				<img src="images/in.gif" border="0" title="Deno Masuk">&nbsp;</a>';
			}
		print '
			</td>
		</tr>';	
		$cnt++;
		$rsD->MoveNext();
	}
	$totalBeza = $totalValueR - $totalArahan;
	print '
		<tr align="right" class="LabelH">
			<td colspan="8">Jumlah : </td>
			<td>Replenish<br>Arahan<br>Varian</td>
			<td align="right">	'.number_format($totalValueR,2).'&nbsp;<br>
								'.number_format($totalArahan,2).'&nbsp;<br>
								'.number_format($totalBeza,2).'&nbsp;</td>
			<td colspan="4">&nbsp;Unloaded</td>	
			<td align="right">'.number_format($totalValueU,2).'&nbsp;</td>	
			<td colspan="2">&nbsp;</td>	
		</tr>';
} else {
	print '
	<tr align="right" class="Data">
		<td align="center" valign="middle" colspan="18" height="50"><b>- Tiada rekod maklumat terminal ATM -</b></td>
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

	function DeleteID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Hapuskan rekod Arahan : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "delete";
			e.submit();
		}
	}

	function AcceptID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Terima rekod Arahan : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "accept";
			e.submit();
		}
	}	

	function SendID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Hantar rekod Arahan ke Pusat ATM : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "send";
			e.submit();
		}
	}	
	
</script>';


include("footer.php");	
?>