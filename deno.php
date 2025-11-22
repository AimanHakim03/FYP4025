<?php

include("header.php");
include("forms.php");

// Initialize all variables
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$vid = $_REQUEST['vid'] ?? '';
$dt = $_REQUEST['dt'] ?? date("Y-m-d");
$strURL = $_REQUEST['strURL'] ?? '';

// Handle pk as array for bulk actions
if (!is_array($pk) && $pk != '') {
	$pk = array($pk);
} elseif ($pk == '') {
	$pk = isset($_POST['pk']) && is_array($_POST['pk']) ? $_POST['pk'] : array();
}

// Date components
$dd = $_REQUEST['dd'] ?? substr($dt, 8, 2);
$mm = $_REQUEST['mm'] ?? substr($dt, 5, 2);
$yy = $_REQUEST['yy'] ?? substr($dt, 0, 4);
$ty = $_REQUEST['ty'] ?? '';

// Session check
if (!isset($_SESSION['Session.userID']) || $_SESSION['Session.userID'] == "") {
	$errPage = $errPage ?? 'Unauthorized access';
	print '<script>alert("'.addslashes($errPage).'");window.location.href="index.php";</script>';
	exit;
}

$dmy = $dd.'/'.$mm.'/'.$yy;

if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.htmlspecialchars($dmy).'");</script>';
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
if ($action == "delete" && is_array($pk) && count($pk) > 0) {
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user  = $_SESSION['Session.userName'];
		$event = "Arahan ".$docNo." - Dihapuskan";		
		$type  = $_SESSION['Session.groupName'];
		createLog($user , $event, $type);
		//--- End   : Create log 	
		$sSQL 	= "	UPDATE instruct SET isDeleted = '1' WHERE instructID =".tosql($pk[$i], "Text");
		$rs = $conn->Execute($sSQL);
	}
}
//--- END   : Delete ------------------------------------------------------------------------------
//--- BEGIN : Verify ------------------------------------------------------------------------------
if ($action == "verify" && is_array($pk) && count($pk) > 0) {
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
		$rs = $conn->Execute($sSQL);
	}
}
//--- END   : Verify ------------------------------------------------------------------------------
//--- BEGIN : Accept ------------------------------------------------------------------------------
if ($action == "accept" && is_array($pk) && count($pk) > 0) {
	$s=0;	
	for ($i = 0; $i < count($pk); $i++) {
		$st = dlookup("instruct", "status", "instructID=" . tosql($pk[$i], "Text"));
		if ($st == '1') {   // telah disahkan
			$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));
			$s++;
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
			$rs = $conn->Execute($sSQL);
		}
	}
	print '	<script>
				alert("'.$s.' rekod arahan diterima.");
			</script>';		
}
//--- END   : Accept ------------------------------------------------------------------------------
//--- BEGIN : Send ------------------------------------------------------------------------------
if ($action == "send" && is_array($pk) && count($pk) > 0) {
	$s=0;
	for ($i = 0; $i < count($pk); $i++) {
		$st = dlookup("instruct", "status", "instructID=" . tosql($pk[$i], "Text"));
		if ($st == '3') {   // replenish data entry completed
			$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[$i], "Text"));
			$s++;
			//--- Begin : Create log 
			$user  = $_SESSION['Session.userName'];
			$event = "Arahan ".$docNo." - Hantar ke Pusat ATM";		
			$type  = $_SESSION['Session.groupName'];
			createLog($user , $event, $type);
			//--- End   : Create log 	
			
			$sSQL 	= " UPDATE instruct SET status = '4' ".
					  " ,approvedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
					  " ,approvedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
					  " WHERE	instructID = '".$pk[$i]."' ";
			$rs = $conn->Execute($sSQL);
		}	
	}
	print '	<script>
				alert("'.$s.' rekod replenish dihantar ke Pusat ATM.");
			</script>';	
}
//--- END   : Send ------------------------------------------------------------------------------
//--- BEGIN : Undo ------------------------------------------------------------------------------
if ($action == "undo" && is_array($pk) && count($pk) > 0) {
	$docNo = dlookup("instruct", "docNo", "instructID=" . tosql($pk[0], "Text"));
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan ".$docNo." - Dikemaskini semula";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 				

	$sSQL 	= 	" UPDATE instruct SET status = '2', updatedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				" ,updatedBy = " . tosql($_SESSION['Session.userName'], "Text") .	
				" WHERE	instructID = '".$pk[0]."' ";
	$rs = $conn->Execute($sSQL);

	print '	<script>
				alert("'.addslashes($event).'...!");
			</script>';	
}
//--- END   : Undo ------------------------------------------------------------------------------

$sSQL = " 
SELECT	I.instructID, A.atmID, A.atmNo, A.atmLoc, A.type, C.vendorCode, C.vendorName, I.status,   
		I.qty100, I.qty50, I.qty20, I.qty10, I.totalValue, 
		I.replenish100, I.replenish50, I.replenish20,I.replenish10, I.replenishValue,
		I.unload100, I.unload50, I.unload20, I.unload10, I.unloadValue,
		I.host100, I.host50, I.host20, I.host10, I.hostValue,
		I.replenishPic, I.unloadPic, I.docNo
FROM 	instruct I 
LEFT JOIN 	atm A  		ON	I.atmID = A.atmID  
LEFT JOIN	vendor C 	ON	I.vendorID = C.vendorID

WHERE 	I.isDeleted ='0' 
AND I.instructDate = '".$dt."'
AND I.vendorID = '".$vid."' ";

if (isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1")) {
	$sSQL .= " AND I.status <> '0' ";	
}

$sSQL .= " ORDER BY A.atmNo  ";
$rsD = $conn->Execute($sSQL);	

print '
<style>
	* { box-sizing: border-box; }
	
	.deno-container {
		max-width: 1600px;
		margin: 0 auto;
		padding: 24px;
		background: #f5f7fa;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	}
	
	.filter-bar {
		background: white;
		padding: 16px 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		display: flex;
		align-items: center;
		gap: 12px;
		flex-wrap: wrap;
	}
	
	.filter-label {
		font-size: 14px;
		font-weight: 600;
		color: #1a202c;
	}
	
	.select-modern {
		padding: 8px 12px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
	}
	
	.select-modern:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.action-buttons {
		margin-left: auto;
		display: flex;
		gap: 8px;
	}
	
	.btn-modern {
		padding: 8px 16px;
		border: none;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
	}
	
	.btn-danger {
		background: #ef4444;
		color: white;
	}
	
	.btn-danger:hover {
		background: #dc2626;
	}
	
	.btn-warning {
		background: #f59e0b;
		color: white;
	}
	
	.btn-warning:hover {
		background: #d97706;
	}
	
	.btn-success {
		background: #10b981;
		color: white;
	}
	
	.btn-success:hover {
		background: #059669;
	}
	
	.btn-primary {
		background: #3b82f6;
		color: white;
	}
	
	.btn-primary:hover {
		background: #2563eb;
	}
	
	.table-container {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow-x: auto;
	}
	
	.table-modern {
		width: 100%;
		border-collapse: collapse;
		min-width: 1400px;
	}
	
	.table-modern thead {
		background: #475569;
		color: white;
	}
	
	.table-modern thead th {
		padding: 12px 8px;
		text-align: center;
		font-size: 11px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.3px;
		white-space: nowrap;
		border-right: 1px solid rgba(255,255,255,0.1);
	}
	
	.header-replenish {
		background: #10b981 !important;
	}
	
	.header-unload {
		background: #ef4444 !important;
	}
	
	.table-modern tbody tr {
		border-bottom: 1px solid #e2e8f0;
	}
	
	.table-modern tbody tr:hover {
		background: #f7fafc;
	}
	
	.table-modern tbody td {
		padding: 10px 8px;
		font-size: 13px;
		color: #2d3748;
		vertical-align: top;
		border-right: 1px solid #f3f4f6;
	}
	
	.text-right {
		text-align: right;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-left {
		text-align: left;
	}
	
	.link-primary {
		color: #4299e1;
		text-decoration: none;
		font-weight: 600;
	}
	
	.link-primary:hover {
		color: #3182ce;
		text-decoration: underline;
	}
	
	.vendor-code {
		color: #9333ea;
		font-style: italic;
		font-size: 12px;
	}
	
	.terminal-info {
		color: #6b7280;
		font-size: 12px;
	}
	
	.value-label {
		font-weight: 600;
		font-size: 11px;
		color: #4a5568;
	}
	
	.value-variant {
		font-weight: 700;
		color: #1a202c;
	}
	
	.badge-status {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 11px;
		font-weight: 600;
		text-transform: uppercase;
	}
	
	.checkbox-modern {
		width: 18px;
		height: 18px;
		cursor: pointer;
		accent-color: #4299e1;
	}
	
	.action-icon {
		width: 24px;
		height: 24px;
		cursor: pointer;
		transition: transform 0.2s;
	}
	
	.action-icon:hover {
		transform: scale(1.1);
	}
	
	.totals-row {
		background: #f7fafc;
		font-weight: 600;
	}
	
	.empty-state {
		text-align: center;
		padding: 60px 20px;
		color: #718096;
	}
	
	@media (max-width: 768px) {
		.deno-container {
			padding: 12px;
		}
		
		.filter-bar {
			flex-direction: column;
			align-items: stretch;
		}
		
		.action-buttons {
			margin-left: 0;
			width: 100%;
		}
		
		.btn-modern {
			flex: 1;
		}
	}
</style>

<div class="deno-container">
	<form name="MyZSSB" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="pk">
		<input type="hidden" name="vid" value="'.htmlspecialchars($vid).'">
		<input type="hidden" name="dt" value="'.htmlspecialchars($dt).'">
		
		<div class="filter-bar">
			<span class="filter-label">📅 ARAHAN PENGISIAN DENOMINASI ATM PADA</span>
			<select name="dd" class="select-modern" onchange="document.MyZSSB.submit();">';

if (isset($dayList)) {
	for ($d = 0; $d < count($dayList); $d++) {
		print '		<option value="'.htmlspecialchars($dayList[$d]).'"';
		if ($dd == $dayList[$d]) print ' selected';
		print '>'.htmlspecialchars($dayList[$d]).'</option>';
	}
}

print '		</select>
			<select name="mm" class="select-modern" onchange="document.MyZSSB.submit();">';

if (isset($monthList) && isset($monthVal)) {
	for ($m = 0; $m < count($monthList); $m++) {
		print '		<option value="'.htmlspecialchars($monthVal[$m]).'"';
		if ($mm == $monthVal[$m]) print ' selected';
		print '>'.strtoupper(htmlspecialchars($monthList[$m])).'</option>';
	}
}

print '		</select>
			<select name="yy" class="select-modern" onchange="document.MyZSSB.submit();">';

if (isset($yrList)) {
	for ($y = 0; $y < count($yrList); $y++) {
		print '		<option value="'.htmlspecialchars($yrList[$y]).'"';
		if ($yy == $yrList[$y]) print ' selected';
		print '>'.htmlspecialchars($yrList[$y]).'</option>';
	}
}

print '		</select>
			
			<div class="action-buttons">';

if (isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "4" OR $_SESSION['Session.groupID'] == "9")) {
	print '		<button type="button" class="btn-modern btn-danger" onClick="ITRActionButtonClick(\'delete\');">🗑️ Hapus</button>
				<button type="button" class="btn-modern btn-warning" onClick="ITRActionButtonClick(\'verify\');">✓ Sahkan</button>';
}

if (isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "1" OR $_SESSION['Session.groupID'] == "9")) {
	print '		<button type="button" class="btn-modern btn-success" onClick="ITRActionButtonClick(\'accept\');">👍 Terima</button>
				<button type="button" class="btn-modern btn-primary" onClick="ITRActionButtonClick(\'send\');">📤 Lulus</button>';
}

print '		</div>
		</div>
		
		<div class="table-container">
			<table class="table-modern">
				<thead>
					<tr>
						<th rowspan="2" width="40">BIL</th>
						<th rowspan="2" width="250">DOC NO / TERMINAL /<br>SYARIKAT</th>
						<th rowspan="2" width="50">JENIS</th>
						<th rowspan="2" width="70">&nbsp;</th>
						<th colspan="5" class="header-replenish">DENOMINASI REPLENISH</th>
						<th rowspan="2" width="70">&nbsp;</th>
						<th colspan="5" class="header-unload">DENOMINASI UNLOADED</th>
						<th rowspan="2" width="80">STATUS</th>
						<th rowspan="2" width="70">
							<input type="checkbox" onClick="ITRViewSelectAll(\'A\')" class="checkbox-modern">
						</th>
					</tr>
					<tr>
						<th class="header-replenish">RM100</th>
						<th class="header-replenish">RM50</th>
						<th class="header-replenish">RM20</th>
						<th class="header-replenish">RM10</th>
						<th class="header-replenish">AMAUN<br>(RM)</th>
						<th class="header-unload">RM100</th>
						<th class="header-unload">RM50</th>
						<th class="header-unload">RM20</th>
						<th class="header-unload">RM10</th>
						<th class="header-unload">AMAUN<br>(RM)</th>
					</tr>
				</thead>
				<tbody>';

$sd=0;
$totalValueU=0; 
$totalValueR=0; 
$totalArahan=0;
$totalValueH=0;

if ($rsD && $rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;

		$totalValueR += $rsD->fields('replenishValue');
		$totalArahan += $rsD->fields('totalValue');

		$replenish100 = $rsD->fields('replenish100'); $qty100 = $rsD->fields('qty100'); $beza100 = $replenish100 - $qty100;
		$replenish50 = $rsD->fields('replenish50'); $qty50 = $rsD->fields('qty50'); $beza50 = $replenish50 - $qty50;
		$replenish20 = $rsD->fields('replenish20'); $qty20 = $rsD->fields('qty20'); $beza20 = $replenish20 - $qty20;
		$replenish10 = $rsD->fields('replenish10'); $qty10 = $rsD->fields('qty10'); $beza10 = $replenish10 - $qty10;
		
		$replenishValue = $rsD->fields('replenishValue');
		$totalValue = $rsD->fields('totalValue');
		$beza = $rsD->fields('replenishValue') - $rsD->fields('totalValue');
		
		$totalValueU += $rsD->fields('unloadValue');			
		$totalValueH += $rsD->fields('hostValue');	
		
		$uh100 	= $rsD->fields('unload100') - $rsD->fields('host100');
		$uh50 	= $rsD->fields('unload50') - $rsD->fields('host50');
		$uh20 	= $rsD->fields('unload20') - $rsD->fields('host20');
		$uh10	= $rsD->fields('unload10') - $rsD->fields('host10');
		$uh		= $rsD->fields('unloadValue') - $rsD->fields('hostValue');
		
		print '
					<tr>
						<td class="text-right">'.htmlspecialchars($sd).'.</td>
						<td class="text-left">
							<a href="#" onclick="window.location.href = \'denoMasuk.php?action=update&amp;pk='.htmlspecialchars($rsD->fields('instructID')).'&strURL='.htmlspecialchars($strURL).'\'; return false;" class="link-primary">
								'.htmlspecialchars($rsD->fields('docNo')).'
							</a><br>
							<span class="terminal-info">'.htmlspecialchars($rsD->fields('atmNo')).' - '.htmlspecialchars($rsD->fields('atmLoc')).'</span><br>
							<span class="vendor-code">'.htmlspecialchars($rsD->fields('vendorCode')).'</span>
						</td>
						<td class="text-center">'.htmlspecialchars($rsD->fields('type')).'</td>
						<td class="text-right">
							<span class="value-label">Replenish</span><br>
							<span class="value-label">Arahan</span><br>
							<span class="value-label value-variant">Varian</span>
						</td>
						<td class="text-right">
							'.htmlspecialchars($replenish100).'<br>
							'.htmlspecialchars($qty100).'<br>
							<strong>'.htmlspecialchars($beza100).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($replenish50).'<br>
							'.htmlspecialchars($qty50).'<br>
							<strong>'.htmlspecialchars($beza50).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($replenish20).'<br>
							'.htmlspecialchars($qty20).'<br>
							<strong>'.htmlspecialchars($beza20).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($replenish10).'<br>
							'.htmlspecialchars($qty10).'<br>
							<strong>'.htmlspecialchars($beza10).'</strong>
						</td>
						<td class="text-right">
							'.number_format($rsD->fields('replenishValue'),2).'<br>
							'.number_format($rsD->fields('totalValue'),2).'<br>
							<strong>'.number_format($beza,2).'</strong>
						</td>
						<td class="text-right">
							<span class="value-label">Fizikal</span><br>
							<span class="value-label">Local</span><br>
							<span class="value-label value-variant">Varian</span>
						</td>
						<td class="text-right">
							'.htmlspecialchars($rsD->fields('unload100')).'<br>
							'.htmlspecialchars($rsD->fields('host100')).'<br>
							<strong>'.htmlspecialchars($uh100).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($rsD->fields('unload50')).'<br>
							'.htmlspecialchars($rsD->fields('host50')).'<br>
							<strong>'.htmlspecialchars($uh50).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($rsD->fields('unload20')).'<br>
							'.htmlspecialchars($rsD->fields('host20')).'<br>
							<strong>'.htmlspecialchars($uh20).'</strong>
						</td>
						<td class="text-right">
							'.htmlspecialchars($rsD->fields('unload10')).'<br>
							'.htmlspecialchars($rsD->fields('host10')).'<br>
							<strong>'.htmlspecialchars($uh10).'</strong>
						</td>
						<td class="text-right">
							'.number_format($rsD->fields('unloadValue'),2).'<br>
							'.number_format($rsD->fields('hostValue'),2).'<br>
							<strong>'.number_format($uh,2).'</strong>
						</td>
						<td class="text-center">';
		
		if ($rsD->fields('status') !== NULL && isset($statusList) && isset($statusVal)) {
			$statusIndex = array_search($rsD->fields('status'),$statusVal);
			if ($statusIndex !== false) {
				print '<span class="badge-status">'.htmlspecialchars($statusList[$statusIndex]).'</span>';
			}
		}
		
		print '		</td>
						<td class="text-center">';
			
		if ($rsD->fields('status') == '0'){
			if (isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "3" OR $_SESSION['Session.groupID'] == "4" OR $_SESSION['Session.groupID'] == "9")) {
				print '<input type="checkbox" name="pk[]" value="'.htmlspecialchars($rsD->fields('instructID')).'" class="checkbox-modern">';
			}
		}
		
		if ($rsD->fields('status') == '1'){
			if (isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "1" OR $_SESSION['Session.groupID'] == "9")) {
				print '<input type="checkbox" name="pk[]" value="'.htmlspecialchars($rsD->fields('instructID')).'" class="checkbox-modern">';
			}
		}

		if ($rsD->fields('status') == '2' && isset($_SESSION['Session.groupID']) && ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1")){
			print '<a href="#" onclick="window.location.href = \'denoMasuk.php?action=update&amp;pk='.htmlspecialchars($rsD->fields('instructID')).'&strURL='.htmlspecialchars($strURL).'\'; return false;">
					<img src="images/money-icon.png" border="0" title="Deno Masuk" class="action-icon"></a>';
		}
		
		if ($rsD->fields('status') == '3' && isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "1"){
			print '<input type="checkbox" name="pk[]" value="'.htmlspecialchars($rsD->fields('instructID')).'" class="checkbox-modern">
				<a href="#" onclick="ReverseID(\''.htmlspecialchars($rsD->fields('instructID')).'\',\''.htmlspecialchars($rsD->fields('docNo')).'\'); return false;">
					<img src="images/undo-icon.png" border="0" title="Kemaskini Semula" class="action-icon"></a>';
		}
		
		print '		</td>
					</tr>';
		
		$rsD->MoveNext();
	}
	
	$totalBeza = $totalValueR - $totalArahan;
	$totalValueUH = $totalValueU - $totalValueH;
	
	print '
					<tr class="totals-row">
						<td colspan="7" class="text-right"><strong>JUMLAH:</strong></td>
						<td class="text-right">
							<span class="value-label">Replenish</span><br>
							<span class="value-label">Arahan</span><br>
							<span class="value-label value-variant">Varian</span>
						</td>
						<td class="text-right">
							<strong>'.number_format($totalValueR,2).'</strong><br>
							<strong>'.number_format($totalArahan,2).'</strong><br>
							<strong class="value-variant">'.number_format($totalBeza,2).'</strong>
						</td>
						<td colspan="5" class="text-right">
							<span class="value-label">Fizikal</span><br>
							<span class="value-label">Local</span><br>
							<span class="value-label value-variant">Varian</span>
						</td>
						<td class="text-right">
							<strong>'.number_format($totalValueU,2).'</strong><br>
							<strong>'.number_format($totalValueH,2).'</strong><br>
							<strong class="value-variant">'.number_format($totalValueUH,2).'</strong>
						</td>
						<td colspan="2">&nbsp;</td>
					</tr>';
} else {
	print '
					<tr>
						<td colspan="17" class="empty-state">
							<div style="font-size: 48px; margin-bottom: 12px;">📭</div>
							<div><strong>Tiada rekod maklumat terminal ATM</strong></div>
						</td>
					</tr>';
}

print '
				</tbody>
			</table>
		</div>
	</form>
</div>

<script>
	var allChecked = false;
	function ITRViewSelectAll(id) {
		e = document.MyZSSB.elements;
		allChecked = !allChecked;
		for(c = 0; c < e.length; c++) {
			if (id == "A") {
				if(e[c].type == "checkbox" && e[c].name == "pk[]") {
					e[c].checked = allChecked;
				}
			}
		}
	}

	function ITRActionButtonClick(v) {
		e = document.MyZSSB;
		if(e == null) {
			alert(\'Confirm the form name.!\');
		} else {
			count = 0;
			for(c = 0; c < e.elements.length; c++) {
				if(e.elements[c].name == "pk[]" && e.elements[c].checked) {
					count++;
				}
			}
			
			if(count == 0) {
				alert(\'Sila pilih rekod.\');
			} else {
				var vv = "";
				if (v == \'delete\') {
					vv = \'Hapuskan \';
				} 
				if (v == \'verify\') {
					vv = \'Sahkan \';
				} 
				if (v == \'accept\') {
					vv = \'Terima \';
				}
				if (v == \'send\') {
					vv = \'Hantar \';
				}
				
				if(confirm(vv + count + \' rekod ?\')) {
					e.action.value = v;
					e.submit();
				}
			}
		}
	}

	function AcceptID(id, code) {
		e = document.MyZSSB;
		if(confirm(\' Terima rekod Arahan : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "accept";
			e.submit();
		}
	}

	function ReverseID(id, code) {
		e = document.MyZSSB;
		if(confirm(\' Kemaskini Semula rekod Arahan : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "undo";
			e.submit();
		}
	}
</script>';

include("footer.php");	
?>