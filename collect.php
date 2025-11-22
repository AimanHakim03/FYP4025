<?php

// Initialize ALL request variables - CRITICAL FIX
$dd = $_REQUEST['dd'] ?? date("d");
$mm = $_REQUEST['mm'] ?? date("m");
$yy = $_REQUEST['yy'] ?? date("Y");
$ty = $_REQUEST['ty'] ?? '';
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$q = $_REQUEST['q'] ?? '';
$status = $_REQUEST['status'] ?? '';
$vendorID = $_REQUEST['vendorID'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? [];
$PHP_SELF = $_SERVER['PHP_SELF'] ?? '';

include("header.php");
include("forms.php");

// Cache session variables
$sessionUserID = $_SESSION['Session.userID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionBranchID = $_SESSION['Session.branchID'] ?? '';
$sessionIsUD = $_SESSION['Session.isUD'] ?? '';

if ($sessionUserID == "") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location.href="index.php";</script>';
	exit;
}

if ($mm <> '') {
	$dmy = $dd.'/'.$mm.'/'.$yy;
	if (ValidDate($dmy) == "N") {
		print '<script>alert("Pilihan tarikh tidak sah - '.htmlspecialchars($dmy).'");</script>';
		$dd = 1;
	}
	$ymd = $yy.'-'.$mm.'-'.$dd;
}

$strURL = urlencode($QUERY_STRING);
if ($strURL == '') {
	$strURL = 'q='.$q.'&status='.$status.'&vendorID='.$vendorID.'&mm='.$mm.'&yy='.$yy;
	$strURL = urlencode($strURL);
}

$sFileName = 'collect.php';
$sFileRef = 'collectAddUpdate.php';
$title = "COLLECTION CDM/CICO";

if ($sessionGroupID == "0" || $sessionGroupID == "1") {
	$vendorID = $sessionBranchID;
}

//--- BEGIN : VENDOR LIST -------------------------------------------------------------------
$vendorList = array();
$vendorVal = array();
$sSQL = "SELECT V.vendorID, V.vendorCode, V.vendorName FROM vendor V WHERE isDeleted = '0'";
$GetN = $conn->Execute($sSQL);
if ($GetN && $GetN->RowCount() <> 0) {
	while (!$GetN->EOF) {
		array_push($vendorList, getField($GetN, 'vendorName'));
		array_push($vendorVal, getField($GetN, 'vendorID'));
		$GetN->MoveNext();
	}
}	
//--- END   : VENDOR LIST -------------------------------------------------------------------

//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete" && is_array($pk) && count($pk) > 0) {
	$updatedBy = $sessionUserName;
	$updatedDate = date("Y-m-d H:i:s"); 	
	
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("collect", "docNo", "collectID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user = $sessionUserName;
		$event = "Collection ".$docNo." - Dihapuskan";		
		$type = $sessionGroupName;
		createLog($user, $event, $type);
		//--- End   : Create log 	
		
		$sSQL = "UPDATE collect SET isDeleted = '1' ".
				",updatedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				",updatedBy = " . tosql($sessionUserName, "Text") .	
				" WHERE collectID = '".$pk[$i]."'";
		$rs = $conn->Execute($sSQL);
	}
}
//--- END   : Delete ------------------------------------------------------------------------------

//--- BEGIN : Approved ------------------------------------------------------------------------------
if ($action == "approve" && is_array($pk) && count($pk) > 0) {
	$approvedBy = $sessionUserName;
	$approvedDate = date("Y-m-d H:i:s"); 		
	
	for ($i = 0; $i < count($pk); $i++) {
		$docNo = dlookup("collect", "docNo", "collectID=" . tosql($pk[$i], "Text"));

		//--- Begin : Create log 
		$user = $sessionUserName;
		$event = "Colllection ".$docNo." - Diluluskan (hantar ke Pusat ATM)";		
		$type = $sessionGroupName;
		createLog($user, $event, $type);
		//--- End   : Create log 	
		
		$sSQL = "UPDATE collect SET status = '4' ".
				",approvedDate = " . tosql(date("Y-m-d H:i:s"), "Text") .
				",approvedBy = " . tosql($sessionUserName, "Text") .	
				" WHERE collectID = '".$pk[$i]."'";
		$rs = $conn->Execute($sSQL);
	}
}
//--- END   : Approved ------------------------------------------------------------------------------

$sSQL = "SELECT A.*, B.vendorCode, B.vendorName, C.atmLoc, C.atmNo, C.type 
		 FROM collect A LEFT JOIN vendor B ON A.vendorID = B.vendorID
						LEFT JOIN atm C ON A.atmID = C.atmID				
		 WHERE A.isDeleted = '0'";
				
if ($vendorID <> '') {
	$sSQL .= " AND A.vendorID = ".tosql($vendorID, "Text");
}

if ($status <> '') {
	$sSQL .= " AND A.status = ".tosql($status, "Text");
}

if ($mm <> '') {
	$sSQL .= " AND MONTH(A.collectDate) = '".$mm."'";
}

$sSQL .= " AND YEAR(A.collectDate) = '".$yy."'";
				
if ($q <> "") {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q ".
			" WHERE Q.atmLoc LIKE ".toSQL($q."%","Text").
			" OR Q.atmNo LIKE ".toSQL($q."%","Text").
			" ORDER BY Q.atmNo, Q.atmLoc";			 
} else {
	$sSQL .= " ORDER BY A.status, A.collectDate DESC, C.atmNo, C.atmLoc";
}

$rs = $conn->Execute($sSQL);	

if ($rs === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.htmlspecialchars($sSQL).'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

print '
<style>
	* { box-sizing: border-box; }
	
	.container-modern {
		max-width: 1400px;
		margin: 0 auto;
		padding: 24px;
		background: #f5f7fa;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	}
	
	.page-header-modern {
		background: white;
		padding: 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
	}
	
	.page-title-modern {
		font-size: 24px;
		font-weight: 600;
		color: #1a202c;
		margin: 0;
		display: flex;
		align-items: center;
		gap: 12px;
	}
	
	.page-subtitle {
		font-size: 14px;
		color: #718096;
		margin: 4px 0 0 0;
	}
	
	.filter-section {
		background: white;
		padding: 20px 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
	}
	
	.filter-grid {
		display: flex;
		flex-wrap: wrap;
		gap: 16px;
		align-items: center;
		margin-bottom: 16px;
	}
	
	.filter-item {
		display: flex;
		align-items: center;
		gap: 8px;
	}
	
	.filter-item label {
		font-size: 14px;
		font-weight: 500;
		color: #4a5568;
		white-space: nowrap;
	}
	
	.select-modern {
		padding: 8px 32px 8px 12px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
		min-width: 140px;
		cursor: pointer;
		transition: all 0.2s;
	}
	
	.select-modern:hover {
		border-color: #cbd5e0;
	}
	
	.select-modern:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.button-group {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}
	
	.btn-modern {
		padding: 9px 18px;
		border: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
		white-space: nowrap;
	}
	
	.btn-primary {
		background: #4299e1;
		color: white;
	}
	
	.btn-primary:hover {
		background: #3182ce;
	}
	
	.btn-success {
		background: #48bb78;
		color: white;
	}
	
	.btn-success:hover {
		background: #38a169;
	}
	
	.btn-danger {
		background: #f56565;
		color: white;
	}
	
	.btn-danger:hover {
		background: #e53e3e;
	}
	
	.table-container {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
	}
	
	.table-modern {
		width: 100%;
		border-collapse: collapse;
	}
	
	.table-modern thead {
		background: #475569;
		color: white;
	}
	
	.table-modern thead th {
		padding: 12px;
		text-align: left;
		font-size: 13px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.table-modern thead th.text-center {
		text-align: center;
	}
	
	.table-modern thead th.text-right {
		text-align: right;
	}
	
	.header-orange {
		background: #f97316 !important;
	}
	
	.header-blue {
		background: #3b82f6 !important;
	}
	
	.table-modern tbody tr {
		border-bottom: 1px solid #e2e8f0;
	}
	
	.table-modern tbody tr:hover {
		background: #f7fafc;
	}
	
	.table-modern tbody td {
		padding: 12px;
		font-size: 14px;
		color: #2d3748;
		vertical-align: top;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-right {
		text-align: right;
	}
	
	.link-primary {
		color: #4299e1;
		text-decoration: none;
		font-weight: 500;
	}
	
	.link-primary:hover {
		color: #3182ce;
		text-decoration: underline;
	}
	
	.badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
	}
	
	.badge-new {
		background: #dbeafe;
		color: #1e40af;
	}
	
	.badge-complete {
		background: #d1fae5;
		color: #065f46;
	}
	
	.badge-vendor {
		background: #fef3c7;
		color: #92400e;
		display: inline-block;
		margin-top: 4px;
	}
	
	.text-muted {
		color: #718096;
		font-size: 13px;
	}
	
	.text-bold {
		font-weight: 600;
		color: #1a202c;
	}
	
	.variance-positive {
		color: #f59e0b;
		font-weight: 600;
	}
	
	.variance-negative {
		color: #ef4444;
		font-weight: 600;
	}
	
	.variance-zero {
		color: #10b981;
		font-weight: 600;
	}
	
	.checkbox-modern {
		width: 18px;
		height: 18px;
		cursor: pointer;
		accent-color: #4299e1;
	}
	
	.empty-state {
		text-align: center;
		padding: 60px 20px;
	}
	
	.empty-icon {
		font-size: 48px;
		color: #cbd5e0;
		margin-bottom: 12px;
	}
	
	.empty-text {
		font-size: 16px;
		color: #718096;
	}
	
	@media (max-width: 768px) {
		.container-modern {
			padding: 12px;
		}
		
		.filter-grid {
			flex-direction: column;
			align-items: stretch;
		}
		
		.filter-item {
			flex-direction: column;
			align-items: stretch;
		}
		
		.select-modern {
			width: 100%;
		}
		
		.button-group {
			flex-direction: column;
		}
		
		.btn-modern {
			width: 100%;
		}
	}
</style>

<div class="container-modern">
	<div class="page-header-modern">
		<h1 class="page-title-modern">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<rect x="2" y="5" width="20" height="14" rx="2"/>
				<line x1="2" y1="10" x2="22" y2="10"/>
			</svg>
			'.$title.'
		</h1>
		<p class="page-subtitle">Pantau bakin koleksi ATM dari sistem</p>
	</div>

	<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="pk">
		
		<div class="filter-section">
			<div class="filter-grid">
				<div class="filter-item">
					<label>Bulan:</label>
					<select name="mm" class="select-modern" onchange="document.MyZSSB.submit();">
						<option value="">- Semua -</option>';

if (isset($monthList) && isset($monthVal)) {
	for ($m = 0; $m < count($monthList); $m++) {
		print '				<option value="'.htmlspecialchars($monthVal[$m]).'"';
		if ($mm == $monthVal[$m]) print ' selected';
		print '>'.strtoupper(htmlspecialchars($monthList[$m])).'</option>';
	}
}

print '				</select>
				</div>
				
				<div class="filter-item">
					<label>Tahun:</label>
					<select name="yy" class="select-modern" onchange="document.MyZSSB.submit();">';

if (isset($yrList)) {
	for ($y = 0; $y < count($yrList); $y++) {
		print '				<option value="'.htmlspecialchars($yrList[$y]).'"';
		if ($yy == $yrList[$y]) print ' selected';
		print '>'.htmlspecialchars($yrList[$y]).'</option>';
	}
}

print '				</select>
				</div>';

if ($sessionGroupID <> '0' && $sessionGroupID <> '1') {	
	print '		<div class="filter-item">
					<label>Syarikat:</label>
					<select name="vendorID" class="select-modern" onchange="document.MyZSSB.submit();">
						<option value="">- Semua -</option>';
	for ($i = 0; $i < count($vendorList); $i++) {
		print '			<option value="'.htmlspecialchars($vendorVal[$i]).'"';
		if ($vendorID == $vendorVal[$i]) print ' selected';
		print '>'.htmlspecialchars($vendorList[$i]).'</option>';
	}			
	print '			</select>
				</div>';
}

$collectList = array('- Semua -', 'BARU', 'SELESAI');	
$collectVal = array("", "0", "4");

print '		<div class="filter-item">
					<label>Status:</label>
					<select name="status" class="select-modern" onchange="document.MyZSSB.submit();">';
for ($y = 0; $y < count($collectList); $y++) {
	print '			<option value="'.htmlspecialchars($collectVal[$y]).'"';
	if ($status == $collectVal[$y]) print ' selected';
	print '>'.htmlspecialchars($collectList[$y]).'</option>';
}

print '			</select>
				</div>
			</div>
			
			<div class="button-group">';

if ($sessionGroupID == "0") {
	print '			<button type="button" class="btn-modern btn-primary" onclick="window.location.href=\'collectAddUpdate.php?action=add\';">
					+ Tambah
				</button>';
}

if ($sessionIsUD == "1") {
	print '			<button type="button" class="btn-modern btn-primary" onclick="window.location.href=\'uploadcdm.php\';">
					Muatnaik Fail
				</button>';
}

if ($sessionGroupID == "1" || $sessionGroupID == "9") {
	print '			<button type="button" class="btn-modern btn-danger" onclick="ITRActionButtonClick(\'delete\');">
					Hapus Terpilih
				</button>
				<button type="button" class="btn-modern btn-success" onclick="ITRActionButtonClick(\'approve\');">
					Luluskan
				</button>';
}

print '		</div>
		</div>
		
		<div class="table-container">
			<table class="table-modern">
				<thead>
					<tr>
						<th class="text-right" rowspan="2" width="40">BIL</th>
						<th rowspan="2" width="140">TARIKH COLLECTION<br>TARIKH BANK IN</th>
						<th rowspan="2" width="250">NO DOKUMEN<br>TERMINAL<br>SYARIKAT</th>
						<th class="header-orange text-center" colspan="8">DENOMINASI</th>
						<th class="text-center" rowspan="2" width="100">JUMLAH (RM)</th>
						<th class="text-center" rowspan="2" width="100">VARIAN</th>
						<th class="text-center" rowspan="2" width="80">STATUS</th>
						<th class="text-center" rowspan="2" width="60">
							<input type="checkbox" onClick="ITRViewSelectAll(\'A\')" class="checkbox-modern">
						</th>
					</tr>
					<tr>
						<th class="header-orange text-center" width="60">&nbsp;</th>
						<th class="header-orange text-center" width="60">100</th>
						<th class="header-orange text-center" width="60">50</th>
						<th class="header-orange text-center" width="60">20</th>
						<th class="header-orange text-center" width="60">10</th>
						<th class="header-orange text-center" width="60">5</th>
						<th class="header-orange text-center" width="60">2</th>
						<th class="header-orange text-center" width="60">1</th>
					</tr>
				</thead>
				<tbody>';

$sd = 0;

if ($rs->RowCount() <> 0) {  
	while (!$rs->EOF) {
		$sd++;
		
		$collectID = getField($rs, 'collectID') ?? '';
		$docNo = getField($rs, 'docNo') ?? '';
		$collectDate = getField($rs, 'collectDate') ?? '';
		$bankInDate = getField($rs, 'bankInDate') ?? '';
		$collectPic = getField($rs, 'collectPic') ?? '';
		$atmNo = getField($rs, 'atmNo') ?? '';
		$atmLoc = getField($rs, 'atmLoc') ?? '';
		$vendorCode = getField($rs, 'vendorCode') ?? '';
		$qty100 = getField($rs, 'qty100') ?? 0;
		$hqty100 = getField($rs, 'hqty100') ?? 0;
		$qty50 = getField($rs, 'qty50') ?? 0;
		$hqty50 = getField($rs, 'hqty50') ?? 0;
		$qty20 = getField($rs, 'qty20') ?? 0;
		$hqty20 = getField($rs, 'hqty20') ?? 0;
		$qty10 = getField($rs, 'qty10') ?? 0;
		$hqty10 = getField($rs, 'hqty10') ?? 0;
		$qty5 = getField($rs, 'qty5') ?? 0;
		$hqty5 = getField($rs, 'hqty5') ?? 0;
		$qty2 = getField($rs, 'qty2') ?? 0;
		$hqty2 = getField($rs, 'hqty2') ?? 0;
		$qty1 = getField($rs, 'qty1') ?? 0;
		$hqty1 = getField($rs, 'hqty1') ?? 0;
		$totalValue = getField($rs, 'totalValue') ?? 0;
		$htotalValue = getField($rs, 'htotalValue') ?? 0;
		$collectStatus = getField($rs, 'status') ?? '';
		
		$vValue = $totalValue - $htotalValue;
		$vDesc = '';
		$vClass = 'variance-zero';
		
		if ($vValue > 0) {
			$vDesc = 'excess';
			$vClass = 'variance-positive';
		} elseif ($vValue < 0) {
			$vDesc = 'short';
			$vClass = 'variance-negative';
		}
		
		print '
				<tr>
					<td class="text-right text-muted">'.$sd.'.</td>
					<td>
						<a href="'.htmlspecialchars($sFileRef).'?action=update&pk='.htmlspecialchars($collectID).'&strURL='.htmlspecialchars($strURL).'" class="link-primary">
							'.todate('d/m/Y', $collectDate).'
						</a><br>
						<span class="text-muted">'.todate('d/m/Y', $bankInDate).'</span>
					</td>
					<td>';
		
		if ($collectPic <> '') {
			print '<a href="scanned/'.htmlspecialchars($collectPic).'" target="_blank" class="text-bold">'.htmlspecialchars($docNo).'</a>';
		} else {
			print '<span class="text-bold">'.htmlspecialchars($docNo).'</span>';
		}
				
		print '<br>
						<span class="text-muted">'.htmlspecialchars($atmNo).' - '.htmlspecialchars($atmLoc).'</span><br>
						<span class="badge badge-vendor">'.htmlspecialchars($vendorCode).'</span>
					</td>
					<td class="text-center"><span class="text-muted" style="font-size:12px;">Fizikal<br>Local</span></td>
					<td class="text-center">'.htmlspecialchars($qty100).'<br><span class="text-muted">'.htmlspecialchars($hqty100).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty50).'<br><span class="text-muted">'.htmlspecialchars($hqty50).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty20).'<br><span class="text-muted">'.htmlspecialchars($hqty20).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty10).'<br><span class="text-muted">'.htmlspecialchars($hqty10).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty5).'<br><span class="text-muted">'.htmlspecialchars($hqty5).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty2).'<br><span class="text-muted">'.htmlspecialchars($hqty2).'</span></td>
					<td class="text-center">'.htmlspecialchars($qty1).'<br><span class="text-muted">'.htmlspecialchars($hqty1).'</span></td>
					<td class="text-right">'.number_format($totalValue, 2).'<br><span class="text-muted">'.number_format($htotalValue, 2).'</span></td>
					<td class="text-right '.$vClass.'">'.number_format($vValue, 2);
		
		if ($vDesc != '') {
			print '<br><span style="font-size:11px;">'.htmlspecialchars($vDesc).'</span>';
		}
		
		print '</td>';
		
		$statusClass = 'badge-new';
		$statusDisplay = '';
		if (isset($statusList) && isset($statusVal)) {
			$statusIndex = array_search($collectStatus, $statusVal);
			$statusDisplay = ($statusIndex !== false) ? $statusList[$statusIndex] : '';
			if ($collectStatus == '4') {
				$statusClass = 'badge-complete';
			}
		}
		
		print '			<td class="text-center"><span class="badge '.$statusClass.'">'.$statusDisplay.'</span></td>
					<td class="text-center">';

		if ($collectStatus == '0') {
			if ($sessionGroupID == "1" || $sessionGroupID == "9") {
				print '<input type="checkbox" name="pk[]" value="'.htmlspecialchars($collectID).'" class="checkbox-modern">';
			}
		}
				
		print '		</td>
				</tr>';
		
		$rs->MoveNext();
	}
} else {
	print '
				<tr>
					<td colspan="14" class="empty-state">
						<div class="empty-icon">📭</div>
						<div class="empty-text">Tiada rekod maklumat kutipan</div>
					</td>
				</tr>';
}

print'
				</tbody>
			</table>
		</div>
	</form>
</div>

<script language="JavaScript">

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
				if (v == \'delete\') {
					vv = \'Hapuskan \';
				} 
				if (v == \'approve\') {
					vv = \'Lulus \';
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
		if(confirm(\' Hantar rekod ke Pusat ATM : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "accept";
			e.submit();
		}
	}
	
</script>';

include("footer.php");	
?>