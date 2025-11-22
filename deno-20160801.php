<?

include ("header.php");
include("forms.php");
//$conn->debug=true;

if ($_SESSION['Session.userID'] == "") {
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

if (!isset($dd)) 	$dd	= SUBSTR($dt,8,2);                   		
if (!isset($mm)) 	$mm	= SUBSTR($dt,5,2);            		
if (!isset($yy))	$yy	= SUBSTR($dt,0,4); 
if (!isset($ty))	$ty	= '';
$dmy = $dd.'/'.$mm.'/'.$yy;

if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.$dmy.'");</script>';
	$dd	= date("d");                   		
	$mm	= date("n");                 		
	$yy	= date("Y");
}
$ymd = $yy.'-'.$mm.'-'.$dd;

if ($dt =='') $dt = $ymd;
if ($strURL =='') {
	$strURL = 'dt='.$dt.'&vid='.$vid;
	$strURL = urlencode($strURL);
}

//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete") {
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		$event = "Arahan ".$docNo." - Dihapuskan";		
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 	
		$sSQL 	= "	UPDATE instruct SET isDeleted = '1' WHERE instructID =".tosql($pk[$i], "Text");
		$rs = &$conn->Execute($sSQL);
	}
}
//--- END   : Delete ------------------------------------------------------------------------------
//--- BEGIN : Verify ------------------------------------------------------------------------------
if ($action == "verify") {
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		$event = "Arahan ".$docNo." - Disahkan";		
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 	
		$sSQL 	= " UPDATE instruct SET status = '1' ".
				  " ,verifiedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				  " ,verifiedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
				  " WHERE	instructID = '".$pk[$i]."' ";
		$rs = &$conn->Execute($sSQL);
	}
}
//--- END   : Verify ------------------------------------------------------------------------------
//--- BEGIN : Accept ------------------------------------------------------------------------------
if ($action == "accept") {
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		$event = "Arahan ".$docNo." - Diterima";		
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 
		
		$sSQL 	= 	" UPDATE instruct SET status = '2' ".
					" ,acceptedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
					" ,acceptedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
					" WHERE	instructID = '".$pk[$i]."' ";
		$rs = &$conn->Execute($sSQL);
	}
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

	$sSQL 	= 	" UPDATE instruct SET status = '4', approvedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				" ,approvedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
				" WHERE	instructID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Send ------------------------------------------------------------------------------
//--- BEGIN : Undo ------------------------------------------------------------------------------
if ($action == "undo") {
	$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk, "Text"));
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan ".$docNo." - Dikemaskini semula";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= 	" UPDATE instruct SET status = '2', updatedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				" ,updatedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
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
		I.replenishPic, I.unloadPic, I.docNo
FROM 	instruct I 
LEFT JOIN 	atm A  		ON	I.atmID = A.atmID  
LEFT JOIN	vendor C 	ON	I.vendorID = C.vendorID

WHERE 	I.isDeleted ='0' 
AND I.instructDate = '".$dt."'
AND I.vendorID = '".$vid."' ";

if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") {
	$sSQL .= " AND I.status <> '0' ";	
}

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
print '	</select>&nbsp;';

if ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "4" OR $_SESSION['Session.groupID'] == "9") {
	print '
		<input type="button" class="but" value="Hapus" onClick="ITRActionButtonClick(\'delete\');">
		<input type="button" class="but" value="Sahkan" onClick="ITRActionButtonClick(\'verify\');">';
}
if ($_SESSION['Session.groupID'] == "1" OR $_SESSION['Session.groupID'] == "9") {
	print '
		<input type="button" class="but" value="Terima" onClick="ITRActionButtonClick(\'accept\');">';
}
print '
		</td>
	</tr>
	<tr class="Header">
		<td rowspan="2" align="right">BIL</td>
		<td rowspan="2" align="right">DOC NO</td>
		<td rowspan="2">TERMINAL</td>
		<td rowspan="2">SYARIKAT</td>
		<td rowspan="2" align="center">JENIS</td>
		<td rowspan="2" align="center">&nbsp;</td>
		<td colspan="5" align="center">DENOMINASI REPLENISH</td>
		<td colspan="5" align="center">DENOMINASI UNLOADED</td>
		<td rowspan="2" align="center">STATUS</td>
		<td rowspan="2" align="center">TINDAKAN<br>
		<input type="checkbox" onClick="ITRViewSelectAll(\'A\')" class="textFont"></td>
	</tr>
	<tr class="Header">
		<td align="center">RM100</td>
		<td align="center">RM50</td>
		<td align="center">RM20</td>
		<td align="center">RM10</td>
		<td align="center">AMAUN<br>(RM)</td>
		<td align="center">RM100</td>
		<td align="center">RM50</td>
		<td align="center">RM20</td>
		<td align="center">RM10</td>
		<td align="center">AMAUN<br>(RM)</td>
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
		
		$replenishValue = $rsD->fields('replenishValue');
		$totalValue = $rsD->fields('totalValue');
		$beza = $rsD->fields('replenishValue') - $rsD->fields('totalValue');
		
		print '
		<tr align="right" class="'.$strClass.'">
			<td align="right" width="5">'.$sd.'.</td>
			<td align="right" width="5">
				<a href="#" onclick="window.location.href = \'denoMasuk.php?action=update&amp;pk='.$rsD->fields('instructID').'&strURL='.$strURL.'\'">
				<b class="blueText">'.$rsD->fields('docNo').'</b></a>
			</td>
			<td align="left" nowrap>'.$rsD->fields('atmNo').'&nbsp;-&nbsp;'.$rsD->fields('atmLoc').'</td>
			<td align="center">'.$rsD->fields('vendorCode').'</td>
			<td align="center">'.$rsD->fields('type').'</td>
			<td align="right"><b>Replenish<br>Arahan<br>Varian</b></td>			
			<td align="right" width="60">'.$replenish100.'&nbsp;<br>'.$qty100.'&nbsp;<br><b>'.$beza100.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish50.'&nbsp;<br>'.$qty50.'&nbsp;<br><b>'.$beza50.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish20.'&nbsp;<br>'.$qty20.'&nbsp;<br><b>'.$beza20.'</b>&nbsp;</td>		
			<td align="right" width="60">'.$replenish10.'&nbsp;<br>'.$qty10.'&nbsp;<br><b>'.$beza10.'</b>&nbsp;</td>		
			<td align="right" width="50">';

			print number_format($rsD->fields('replenishValue'),2).'&nbsp;<br>';	
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
			if ($rsD->fields('status') == '0'){
				if ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "4" OR $_SESSION['Session.groupID'] == "9") {
					print ' <input type="checkbox" name="pk[]" value="'.$rsD->fields('instructID').'">';
				}
			}
			if ($rsD->fields('status') == '1'){
				if ($_SESSION['Session.groupID'] == "1" OR $_SESSION['Session.groupID'] == "9") {
					print ' <input type="checkbox" name="pk[]" value="'.$rsD->fields('instructID').'">';
				}
			}
//			if ($rsD->fields('status') == '2'){
//				if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "9") {
//					print ' <input type="checkbox" name="pk[]" value="'.$rsD->fields('instructID').'">';
//				}
//			}
			if ($rsD->fields('status') == '2' AND ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1")){
			print '
				<a href="#" onclick="window.location.href = \'denoMasuk.php?action=update&amp;pk='.$rsD->fields('instructID').'&strURL='.$strURL.'\'">
				<img src="images/money-icon.png" border="0" title="Deno Masuk"></a>';
			}			
			if ($rsD->fields('status') == '3' AND $_SESSION['Session.groupID'] == "1"){
				print '
				<a href="#" onclick="ReverseID(\''.$rsD->fields('instructID').'\',\''.$docNo.'\')" >
				<img src="images/undo-icon.png" border="0" title="Kemaskini Semula"></a>
				<a href="#" onclick="SendID(\''.$rsD->fields('instructID').'\',\''.$docNo.'\')" >
				<img src="images/send-icon.png" border="0" title="Hantar ke Pusat ATM"></a>';
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
			<td colspan="9">Jumlah : </td>
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
				if(e[c].type=="checkbox" && e[c].name=="pk[]") {
		        	e[c].checked = allChecked;
		      	}
			}
	    }
	}

	function ITRActionButtonClick(v) {
	     e = document.MyZSSB;
	     if(e==null) {
	       alert(\'Confirm the form name.!\');
	     } else {
	       count=0;
	       for(c=0; c<e.elements.length; c++) {
	         if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	           count++;
	         }
	       }
	       
	       if(count==0) {
	          alert(\'Sila pilih rekod.\');
	       } else {
			  if (v==\'delete\'){
				   vv = \'Hapuskan \';
			  } 
			  if (v==\'verify\'){
				   vv = \'Sahkan \';
			  } 
			  if (v==\'accept\'){
				   vv = \'Terima \';
			  }	  
	          if(confirm(vv + count + \' rekod ?\')) {
	           e.action.value = v;
	           e.submit();
	         }
	       }
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

	function ReverseID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Kemaskini Semula rekod Arahan : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "undo";
			e.submit();
		}
	}	
	
</script>';


include("footer.php");	
?>