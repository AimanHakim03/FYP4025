<?php

$today = date("F j, Y, g:i a"); 
                
if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 50;
if (!isset($q))			$q="";

include("header.php");	
include("forms.php");	

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

$strURL = urlencode($QUERY_STRING);
if ($strURL =='') {
	$strURL = 'q='.$q.'&hq='.$hq.'&branch='.$branch;
	$strURL = urlencode($strURL);
}

$sFileName 	= 'refresh.php';
$sFileRef	= 'refreshAddUpdate.php';
$title  	= "REFRESH CDM";

//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete") {
	$atmLoc = dlookup("atm", "atmLoc", "atmID=" . tosql($pk, "Text"));

	//--- Begin : Create log 
	$user  = get_session("Session.userName");
	$event = "Refresh CDM : ".$atmLoc." - Dihapuskan";
	$type  = get_session("Session.groupName");
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= "UPDATE refresh SET isDeleted = '1' WHERE	refreshID = '".$pk."' ";
	$rs = &$conn->Execute($sSQL);

	print '	<script>
				alert("Rekod Refresh CDM - '.$atmLoc.' : Dihapuskan...!");
			</script>';	
}
//--- END   : Delete ------------------------------------------------------------------------------

//---- BEGIN :	Query -----------------------------------------------------------------------------
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
		  	 " 	OR	 	Q.type LIKE ".toSQL($q."%","Text").
			 "	ORDER BY Q.atmNo, Q.atmLoc ";			 
} else {
	$sSQL .= "	ORDER BY C.atmNo, C.atmLoc";
}
//---- END   :	Query -----------------------------------------------------------------------------
$GetList = &$conn->Execute($sSQL);
$GetList->Move($StartRec-1);

$TotalRec = $GetList->RowCount();
$TotalPage =  ($TotalRec/$pg);

print '
<form name="MyForm" action='.$sFileName.' method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">
<table border="0" cellspacing="1" cellpadding="3" width="1000" align="center">
	<tr>
		<td><b class="maroonText">'.$title.'</b></td>
	</tr>
    <tr valign="top" class="Label">
	   	<td align="left" height="30" valign="middle">
			Carian melalui No ATM/Lokasi/Jenis&nbsp;
			<input type="text" name="q" value="" maxlength="20" size="20" class="Data">
           	 <input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;			
			 <input type="button" class="but" value="Daftar" onclick="window.location.href=\''.$sFileRef.'?action=add\';">&nbsp;&nbsp;&nbsp;
<!---			 <input type="button" class="but" value="Cetak Senarai" onClick="PrintID();">--->
		</td>
	</tr>
	<tr valign="top" class="textFont">
		<td>
			<table width="100%">
				<tr>
					<td class="textFont">
					Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
					if ($pg == 10)	print '<option value="10" selected>10</option>'; 	else print '<option value="10">10</option>';				
					if ($pg == 20)	print '<option value="20" selected>20</option>'; 	else print '<option value="20">20</option>';				
					if ($pg == 30)	print '<option value="30" selected>30</option>'; 	else print '<option value="30">30</option>';				
					if ($pg == 40)	print '<option value="40" selected>40</option>'; 	else print '<option value="40">40</option>';				
					if ($pg == 50)	print '<option value="50" selected>50</option>';	else print '<option value="50">50</option>';				
					if ($pg == 100)	print '<option value="100" selected>100</option>';	else print '<option value="100">100</option>';				
					if ($pg == 200)	print '<option value="200" selected>200</option>';	else print '<option value="200">200</option>';				
					if ($pg == 300)	print '<option value="300" selected>300</option>';	else print '<option value="300">300</option>';				
					if ($pg == 400)	print '<option value="400" selected>400</option>';	else print '<option value="400">400</option>';				
					if ($pg == 500)	print '<option value="500" selected>500</option>';	else print '<option value="500">500</option>';					
					if ($pg == 600)	print '<option value="600" selected>600</option>';	else print '<option value="600">600</option>';													
					if ($pg == 700)	print '<option value="700" selected>700</option>';	else print '<option value="700">700</option>';				
					if ($pg == 800)	print '<option value="800" selected>800</option>';	else print '<option value="800">800</option>';				
					if ($pg == 900)	print '<option value="900" selected>900</option>';	else print '<option value="900">900</option>';					
					if ($pg == 1000)	print '<option value="1000" selected>1000</option>';	else print '<option value="1000">1000</option>';													
	print '			</select> setiap mukasurat.&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr valign="top" >
		<td valign="top">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" class="lineBG">
				<tr class="header">
					<td valign="bottom" nowrap height="20" width="1%">No.</td>
					<td valign="bottom" nowrap>No ATM</td>
					<td valign="bottom" nowrap>Lokasi</td>
					<td valign="bottom" nowrap align="center">Jenis</td>
					<td valign="bottom" nowrap align="left">Kod GL(IN)</td>
					<td valign="bottom" nowrap align="left">Kod GL (OUT)</td>
					<td valign="bottom" nowrap align="left">Syarikat</td>
					<td valign="bottom" nowrap align="center">&nbsp;Tindakan</td>
				</tr>';
	$grandTotal = 0;
	if ($GetList->RowCount() <> 0) {  
		$bil = $StartRec;
		$cnt = 1;
		while (!$GetList->EOF && $cnt <= $pg) {
			if ($cnt % 2 == 1) $strClass = 'Data'; else $strClass = 'Data1';		
		
			print ' 
				<tr>
					<td class="'.$strClass.'" valign="top" height="20" align="right">'.$bil.'.&nbsp;</td>						
					<td class="'.$strClass.'" valign="top">
					<a href="'.$sFileRef.'?action=update&pk='.$GetList->fields('atmID').'&strURL='.$strURL.'"><u><font class="blueText">'
					.$GetList->fields('atmName').'</font></u>&nbsp;&nbsp;'.$GetList->fields('atmNo').'</td>
					<td class="'.$strClass.'" valign="top">'.$GetList->fields('atmLoc').'</td>
					<td class="'.$strClass.'" valign="top" align="left">'.$GetList->fields('type').'</td>
					<td class="'.$strClass.'" valign="top" align="left">'.$GetList->fields('glCode').'</td>
					<td class="'.$strClass.'" valign="top" align="left">'.$GetList->fields('glCode1').'</td>
					<td class="'.$strClass.'" valign="top" align="left">'.$GetList->fields('vendorName').' ('.$GetList->fields('vendorCode').')</td>
					<td valign="top" class="'.$strClass.'" align="center">
						<a href="#" onclick="DeleteID(\''.$GetList->fields('atmID').'\',\''.$GetList->fields('atmNo').'\')" >
						<img src="images/delete.gif" border="0" title="Hapuskan refresh ATM"></a>
					</td>
				</tr>';
			$cnt++;
			$bil++;
			$GetList->MoveNext();
		}
		print ' 	
			</table>
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
					$passURL = '&pg='.$pg.'&q='.$q.'&regionID='.$regionID.'&stateID='.$stateID;
					for ($i=1; $i <= $numPage; $i++) {
						print '<A href="'.$sFileName.'?StartRec='.(($i * $pg) + 1 - $pg).$passURL.'">';
						print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a>&nbsp; ';
					}
					print '</td>
						</tr>
					</table>';
				}				
		print '
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetList->RowCount() . '</b></td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center" colspan="9" height="30" valign="middle" class="Data">
			<b class="textFont">- Tiada Rekod  -</b></td></tr>';
		} else {
			print '
			<tr><td align="center" colspan="9" height="30" valign="middle" class="Data">
			<b class="textFont">- Carian rekod "'.$q.'" tidak dijumpai  -</b></td></tr>';
		}
	}
print ' 
</table>
</form>';

include("footer.php");	
print '	
<script>
	function DeleteID(id,code) {
		e = document.MyForm;
		if(confirm(\' Hapuskan pemborong : \'+code+\' ?\')) {
			e.pk.value = id;
			e.action.value = "delete";
			e.submit();
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&pg=" + c.options[c.selectedIndex].value+"&q='.$q.'";
	}
	
	function PrintID() {
		e = document.MyForm;
		if(confirm(\' Cetak senarai ?\')) {
			window.open("rpt_atmList.php?","rpt","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");			
		}
    }	   		
</script>';
?>
