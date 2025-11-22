<?

include ("header.php");
include("forms.php");
//$conn->debug=true;
if (get_session("Session.userID") == "") {
	print '<script>alert("'.$errPage.'");window.location.href="index.php";</script>';
}

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

$sFileName 	= 'refresh.php';
$sFileRef	= 'refreshAddUpdate.php';
$title  	= "COLLECTION CDM";

//--- BEGIN : INSTRUCT TERMINAL -------------------------------------------------------------------
//--- END   : INSTRUCT TERMINAL -------------------------------------------------------------------
//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete") {
	$refreshCode = dlookup("refresh", "refreshCode", "refreshID=" . tosql($pk, "Text"));

	//--- Begin : Create log 
	$user  = get_session("Session.userName");
	$event = "Collection ".$refreshDate." - Dihapuskan";
	$type  = get_session("Session.groupName");
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= "UPDATE refresh SET isDeleted = '1' WHERE	refreshID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Delete ------------------------------------------------------------------------------

//--- BEGIN : Accept ------------------------------------------------------------------------------
if ($action == "accept") {
	$refreshCode = dlookup("refresh", "refreshCode", "refreshID=" . tosql($pk, "Text"));
	
	//--- Begin : Create log 
	$user  = get_session("Session.userName");
	$event = "Collection ".$refreshCode." - hantar ke Pusat ATM";
	$type  = get_session("Session.groupName");
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= "UPDATE refresh SET status = '1' WHERE refreshID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("'.$event.'...!");
			</script>';	
}
//--- END   : Accept ------------------------------------------------------------------------------

//$conn->debug=true;
$sSQL	= 	" 	SELECT	A.*, B.vendorCode, B.vendorName, C.atmLoc, C.atmNo, C.type 
				FROM refresh  A LEFT JOIN vendor B  ON A.vendorID = B.vendorID
								LEFT JOIN atm C  ON A.atmID = C.atmID				
				WHERE A.isDeleted = '0'  ";
				
if (get_session("Session.groupID") == "0" OR get_session("Session.groupID") == "1") {
	if (get_session("Session.branchID") <> "") {
		$sSQL .= " AND A.vendorID = '".get_session("Session.branchID")."' ";
	}
}

$sSQL .= " AND MONTH(A.refreshDate) = '".$mm."' ";
$sSQL .= " AND YEAR(A.refreshDate) = '".$yy."' ";
				
if ($q <> "") {
	$sSQL  = "	SELECT 	Q.* FROM (".$sSQL.") Q ".
			 " 	WHERE 	Q.atmLoc LIKE ".toSQL($q."%","Text").
		  	 " 	OR	 	Q.atmNo LIKE ".toSQL($q."%","Text").
			 "	ORDER BY Q.atmNo, Q.atmLoc ";			 
} else {
	$sSQL .= "	ORDER BY A.refreshDate Desc, C.atmNo, C.atmLoc";
}

$rs = &$conn->Execute($sSQL);	
if ($rs === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.$sSQL.'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

print '
<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">
<table border=0 cellpadding=3 cellspacing=1 align="center">
	<tr>
		<td class="textFontBold" height="30" valign="middle" colspan="19">'.$title.' PADA BULAN 
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
if (get_session("Session.groupID") == "0") {
	print '
		<input type="Button" name="action1"  value="Tambah" class="butLogin" onClick="window.location.href=\'refreshAddUpdate.php\';"  >';
}
print '	</td>
	</tr>
	<tr class="Header">
		<td align="right" rowspan="2">BIL</td>
		<td align="center" rowspan="2">TARIKH</td>
		<td align="center" rowspan="2">KOD</td>
		<td align="center" rowspan="2">TERMINAL</td>
		<td align="center" rowspan="2">SYARIKAT</td>
		<td align="center" colspan="7" bgcolor="#FF9900">DENOMINASI</td>
		<td align="center" rowspan="2">JUMLAH (RM)</td>
		<td align="center" rowspan="2">STATUS</td>
		<td align="center" rowspan="2">TINDAKAN</td>
	</tr>
	<tr class="Header">
		<td align="center" bgcolor="#FF9900">100</td>
		<td align="center" bgcolor="#FF9900">50</td>
		<td align="center" bgcolor="#FF9900">20</td>
		<td align="center" bgcolor="#FF9900">10</td>
		<td align="center" bgcolor="#FF9900">5</td>
		<td align="center" bgcolor="#FF9900">2</td>
		<td align="center" bgcolor="#FF9900">1</td>
	</tr>';
$sd=0;$cnt=0;
if ($rs->RowCount() <> 0) {  
	while (!$rs->EOF) {
		$sd++;
		if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';
		print '
		<tr align="right" class="'.$strClass.'">
			<td align="right" width="5" height="20">'.$sd.'.</td>
			<td align="center" width="100">';		
		if (get_session("Session.groupID") == "0") {
			print '
			<a href="'.$sFileRef.'?action=update&pk='.$rs->fields('refreshID').'&strURL='.$strURL.'">
			<font class="blueText">'.todate('d/m/Y',$rs->fields('refreshDate')).'</font>&nbsp;</a>';
		} else {
			print todate('d/m/Y',$rs->fields('refreshDate'));
		}
		print '
			</td>
			<td align="left" width="100">'.$rs->fields('refreshCode').'&nbsp;</td>			
			<td align="left" width="250">'.$rs->fields('atmNo').'&nbsp;-&nbsp;'.$rs->fields('atmLoc').'&nbsp;</td>			
			<td align="center" width="100">'.$rs->fields('vendorCode').'</td>
			<td align="center" width="50">'.$rs->fields('qty100').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty50').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty20').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty10').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty5').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty2').'&nbsp;</td>		
			<td align="center" width="50">'.$rs->fields('qty1').'&nbsp;</td>		
			<td align="right" width="80">';
			if ($rs->fields('refreshPic') <> '') {
				print '
				<div class="hover_img"><a href="#">'.number_format($rs->fields('totalValue'),2).'&nbsp;
				<span><img src="scanned/'.$rs->fields('refreshPic').'" alt="image" /></span></a></div>';
			} else {
				print number_format($rs->fields('totalValue'),2).'&nbsp;';
			}
			print '
			</td>	
			<td align="center" width="120">'.$statusList[array_search($rs->fields('status'),$statusVal)].'&nbsp;</td>		
			<td align="center" width="100">';

		if (get_session("Session.groupID") == "1" AND $rs->fields('status') == "0") {
			print '
			<a href="#" onclick="DeleteID(\''.$rs->fields('refreshID').'\',\''.$rs->fields('refreshCode').'\')" >
			<img src="images/delete.gif" border="0" title="Hapuskan Rekod Collection"></a>

			<a href="#" onclick="AcceptID(\''.$rs->fields('refreshID').'\',\''.$rs->fields('refreshCode').'\')" >
			<img src="images/app.gif" border="0" title="Hantar Collection ke Pusat ATM"></a>';
		}
				
		if (get_session("Session.groupID") == "3" OR get_session("Session.groupID") == "9") {
			print '
				<img src="images/transfer.png" border="0" title="Fail Pindahan Data"></a>&nbsp;';
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
		<td align="center" valign="middle" colspan="15" height="50"><b>- Tiada rekod maklumat arahan -</b></td>
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
	
	function AcceptID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Hantar rekod ke Pusat ATM : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "accept";
			e.submit();
		}
	}

	function DeleteID(id,code) {
	    e = document.MyZSSB;
		if(confirm(\' Hapuskan rekod Collection : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "delete";
			e.submit();
		}
	}
	
	function PrintID(id,dt) {
		if(confirm(\' Cetak Surat Arahan untuk \'+dt+\' ?\')) {
			window.open("rpt_pdf/rpt_Instruct.php?pk="+id+"&dt="+dt,"sort","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}

</script>';


include("footer.php");	
?>