<?php

// Initialize ALL request variables - CRITICAL FIX
$StartRec = isset($_REQUEST['StartRec']) ? (int)$_REQUEST['StartRec'] : 1;
$pg = isset($_REQUEST['pg']) ? (int)$_REQUEST['pg'] : 50;
$ddFrom = isset($_REQUEST['ddFrom']) ? (int)$_REQUEST['ddFrom'] : 1;
$mmFrom = isset($_REQUEST['mmFrom']) ? (int)$_REQUEST['mmFrom'] : (int)date("n");
$yyFrom = isset($_REQUEST['yyFrom']) ? (int)$_REQUEST['yyFrom'] : (int)date("Y");
$ddTo = isset($_REQUEST['ddTo']) ? (int)$_REQUEST['ddTo'] : (int)date("j");
$mmTo = isset($_REQUEST['mmTo']) ? (int)$_REQUEST['mmTo'] : (int)date("n");
$yyTo = isset($_REQUEST['yyTo']) ? (int)$_REQUEST['yyTo'] : (int)date("Y");
$actionDD = isset($_REQUEST['actionDD']) ? (int)$_REQUEST['actionDD'] : (int)date("d");
$actionMM = isset($_REQUEST['actionMM']) ? (int)$_REQUEST['actionMM'] : (int)date("n");
$actionYY = isset($_REQUEST['actionYY']) ? (int)$_REQUEST['actionYY'] : (int)date("Y");
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$q = $_REQUEST['q'] ?? '';
$branchID = $_REQUEST['branchID'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? [];
$status = $_REQUEST['status'] ?? '';
$hqID = $_REQUEST['hqID'] ?? '';

include('header.php');
include('forms.php');

$sFileName = 'journalProcessCDM.php';
$sFileRef = 'journalProcessCDM.php';
$title = 'PENYEDIAAN JOURNAL - CDM';

// Cache session variables
$sessionUserID = $_SESSION['Session.userID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';

if ($sessionUserID == "") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location.href="index.php";</script>';
	exit;
}

$strURL = urlencode($QUERY_STRING);
if ($strURL == '') {
	$strURL = 'q='.$q.'&branchID='.$branchID;
	$strURL = urlencode($strURL);
}

//--- BEGIN : PROCESS journal ---------------------------------------------------------------------------------------------------
if ($action == "proses" && is_array($pk) && count($pk) > 0) {
	$actionDate = $actionYY.'-'.sprintf("%02d", $actionMM).'-'.sprintf("%02d", $actionDD);
	$action1Date = sprintf("%02d", $actionDD).'/'.sprintf("%02d", $actionMM).'/'.$actionYY;
	
	if (!checkdate($actionMM, $actionDD, $actionYY)) {
		print '<script>alert("Tarikh tidak sah - '.htmlspecialchars($action1Date).'");</script>';
	} else {
		$journalAmt = 0;
		
		for ($i = 0; $i < count($pk); $i++) {
			$sSQL = "SELECT docNo, totalValue FROM collect WHERE collectID = ".tosql($pk[$i], "Text");
			$rsSEQ = $conn->Execute($sSQL);
			
			if ($rsSEQ && !$rsSEQ->EOF) {
				$docNo = getField($rsSEQ, 'docNo') ?? '';
				$journalAmt += (float)(getField($rsSEQ, 'totalValue') ?? 0);
			}
		}
		
		$sSQL = "SELECT TOP 1 seqNo FROM journal WHERE journalDate = '".$actionDate."' ORDER BY seqNo DESC";
		$rsSEQ = $conn->Execute($sSQL);
		
		if ($rsSEQ && $rsSEQ->RowCount() <> 0) {
			$seqNo = (int)(getField($rsSEQ, 'seqNo') ?? 0) + 1;
		} else {
			$seqNo = 1;
		}
		
		$journalNo = 'JA'.$actionYY.sprintf("%02d", $actionMM).sprintf("%02d", $actionDD).'-'.sprintf("%04d", $seqNo);
		$description = 'LBH CDM LP';
		$atmType = 'CDM';
		
		$sSQL = " 	
		INSERT INTO journal 
			(seqNo, journalNo, journalDate, journalAmt, description, createdDate, createdBy, atmType) VALUES 
			(".tosql($seqNo, "Text").",".tosql($journalNo, "Text").",".tosql($actionDate, "Text").",".tosql($journalAmt, "Text").",
			  ".tosql($description, "Text").",".tosql(date("Y-m-d H:i:s"), "Text").",".tosql($sessionUserName, "Text").",".tosql($atmType, "Text").")";
		
		$rsI = $conn->Execute($sSQL);
		
		$journalID = dlookup("journal", "journalID", "journalNo = ".tosql($journalNo, "Text"));

		for ($i = 0; $i < count($pk); $i++) {
			$docNo = dlookup("collect", "docNo", "collectID = ".tosql($pk[$i], "Text"));
			$totalAmt = dlookup("collect", "totalValue", "collectID = ".tosql($pk[$i], "Text"));
			
			$user = $sessionUserName;
			$event = 'Tarikh ('.$action1Date.') | Journal ('.$journalNo.') - Jumlah (RM'.number_format($totalAmt, 2).') untuk Dokumen No ('.$docNo.') :';
			
			$sSQL = "UPDATE collect SET journalID = '".$journalID."' WHERE collectID = '".$pk[$i]."'";
			$rsU = $conn->Execute($sSQL);
			
			if ($rsU === false) {
				$event .= 'Ralat';
			} else {
				$event .= ' Proses';
			}
			
			$type = $sessionGroupName;
			createLog($user, $event, $type);
		}
	}
}
//--- END   : PROCESS journal ---------------------------------------------------------------------------------------------------

$ddmmyyFrom = sprintf("%04d-%02d-%02d", $yyFrom, $mmFrom, $ddFrom);               
$ddmmyyTo = sprintf("%04d-%02d-%02d", $yyTo, $mmTo, $ddTo); 

$sSQL = " 
SELECT A.*, B.journalNo, B.journalDate, B.atmType FROM collect A  
LEFT JOIN journal B ON A.journalID = B.journalID 
WHERE A.isDeleted = '0' 
AND A.status = '4' 
AND A.collectDate BETWEEN '".$ddmmyyFrom."' AND '".$ddmmyyTo."'";

if ($branchID <> '') {
	$sSQL .= " AND A.vendorID = ".tosql($branchID, "Text");
}

if ($q <> '') {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q ".
			" WHERE Q.docNo LIKE ".tosql("%".$q."%", "Text").
			" OR Q.journalNo LIKE ".tosql("%".$q."%", "Text");
} else {
	$sSQL .= " ORDER BY A.collectDate DESC";
}

$GetList = $conn->Execute($sSQL);

if ($GetList === false) {
	echo "Error: " . $conn->ErrorMsg() . "<br>";
	echo "SQL: " . htmlspecialchars($sSQL);
	exit;
}

// Move to start record
if ($StartRec > 1 && $GetList->RecordCount() > 0) {
	$GetList->Move($StartRec - 1);
}

//--- BEGIN : CATER FOR NUMBER OF RECORDS AND PAGE TO BE DISPLAYED --------------------------------------------------------------
$TotalRec = $GetList->RowCount();
$TotalPage = ceil($TotalRec / $pg);

if ($StartRec < $TotalRec) {
	if (($TotalRec - ($StartRec + $pg)) < 0) {
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
	
	.cdm-badge {
		display: inline-block;
		background: #10b981;
		color: white;
		padding: 4px 12px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 600;
		margin-left: 8px;
	}
	
	.filter-section {
		background: white;
		padding: 20px 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
	}
	
	.search-row {
		display: flex;
		gap: 12px;
		align-items: center;
		margin-bottom: 16px;
		flex-wrap: wrap;
	}
	
	.search-label {
		font-size: 14px;
		font-weight: 500;
		color: #4a5568;
	}
	
	.input-modern {
		padding: 8px 12px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
		transition: all 0.2s;
	}
	
	.input-modern:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.select-modern {
		padding: 8px 12px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
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
	
	.date-range-group {
		display: flex;
		gap: 8px;
		align-items: center;
		flex-wrap: wrap;
	}
	
	.date-inputs {
		display: flex;
		gap: 6px;
		align-items: center;
	}
	
	.btn-modern {
		padding: 8px 18px;
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
	
	.journal-action-box {
		background: #fffbeb;
		border: 2px solid #fbbf24;
		border-radius: 8px;
		padding: 16px;
		margin-top: 16px;
		display: flex;
		gap: 12px;
		align-items: center;
		flex-wrap: wrap;
	}
	
	.journal-label {
		font-size: 14px;
		font-weight: 600;
		color: #92400e;
	}
	
	.pagination-row {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 16px;
		flex-wrap: wrap;
		gap: 12px;
	}
	
	.per-page {
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 14px;
		color: #4a5568;
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
		min-width: 1000px;
	}
	
	.table-modern thead {
		background: #475569;
		color: white;
	}
	
	.table-modern thead th {
		padding: 14px 12px;
		text-align: left;
		font-size: 13px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		white-space: nowrap;
	}
	
	.table-modern thead th.text-center {
		text-align: center;
	}
	
	.table-modern thead th.highlight {
		background: #fbbf24;
		color: #1a202c;
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
	
	.table-modern tbody td.highlight-cell {
		background: #fffbeb;
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
	
	.text-muted {
		color: #718096;
		font-size: 13px;
	}
	
	.text-bold {
		font-weight: 600;
		color: #1a202c;
	}
	
	.badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
	}
	
	.badge-approved {
		background: #d1fae5;
		color: #065f46;
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
	
	.footer-info {
		background: white;
		padding: 16px 24px;
		margin-top: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 12px;
	}
	
	.pagination-links {
		display: flex;
		gap: 8px;
		flex-wrap: wrap;
	}
	
	.page-link {
		padding: 6px 12px;
		background: white;
		border: 1px solid #e2e8f0;
		border-radius: 4px;
		color: #4299e1;
		text-decoration: none;
		font-weight: 500;
		font-size: 14px;
		transition: all 0.2s;
	}
	
	.page-link:hover {
		background: #4299e1;
		color: white;
		border-color: #4299e1;
	}
	
	@media (max-width: 768px) {
		.container-modern {
			padding: 12px;
		}
		
		.search-row,
		.date-range-group {
			flex-direction: column;
			align-items: stretch;
		}
		
		.input-modern,
		.select-modern,
		.btn-modern {
			width: 100%;
		}
	}
</style>

<div class="container-modern">
	<div class="page-header-modern">
		<h1 class="page-title-modern">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
				<polyline points="14 2 14 8 20 8"/>
				<line x1="16" y1="13" x2="8" y2="13"/>
				<line x1="16" y1="17" x2="8" y2="17"/>
			</svg>
			'.strtoupper($title).'
			<span class="cdm-badge">CDM</span>
		</h1>
		<p class="page-subtitle">Pengurusan dan pemprosesan journal CDM</p>
	</div>

	<form name="ITRViewResults" action="'.$sFileName.'" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="pk">
		<input type="hidden" name="branchID" value="'.htmlspecialchars($branchID).'">
		<input type="hidden" name="status" value="'.htmlspecialchars($status).'">
		
		<div class="filter-section">
			<div class="search-row">
				<span class="search-label">Carian:</span>
				<input type="text" name="q" value="'.htmlspecialchars($q).'" placeholder="Dokumen/Jernal No..." maxlength="100" class="input-modern" style="min-width: 220px;">
				<button type="submit" class="btn-modern btn-primary">Cari</button>
			</div>
			
			<div class="date-range-group">
				<span class="search-label">Tarikh Dari:</span>
				<div class="date-inputs">
					<select name="ddFrom" class="select-modern">';

for ($d = 1; $d < 32; $d++) {
	print '				<option value="'.$d.'"';
	if ($ddFrom == $d) print ' selected';
	print '>'.$d.'</option>';
}

print '				</select>
					<select name="mmFrom" class="select-modern">';

for ($m = 1; $m < 13; $m++) {
	print '				<option value="'.$m.'"';
	if ($mmFrom == $m) print ' selected';
	print '>'.$m.'</option>';
}

print '				</select>
					<input type="text" name="yyFrom" value="'.$yyFrom.'" maxlength="4" class="input-modern" style="width: 80px;">
				</div>
				
				<span class="search-label">Hingga:</span>
				<div class="date-inputs">
					<select name="ddTo" class="select-modern">';

for ($d = 1; $d < 32; $d++) {
	print '				<option value="'.$d.'"';
	if ($ddTo == $d) print ' selected';
	print '>'.$d.'</option>';
}

print '				</select>
					<select name="mmTo" class="select-modern">';

for ($m = 1; $m < 13; $m++) {
	print '				<option value="'.$m.'"';
	if ($mmTo == $m) print ' selected';
	print '>'.$m.'</option>';
}

print '				</select>
					<input type="text" name="yyTo" value="'.$yyTo.'" maxlength="4" class="input-modern" style="width: 80px;">
				</div>
				
				<button type="submit" class="btn-modern btn-primary">Capai</button>
			</div>';

if ($sessionGroupID == '3' || $sessionGroupID == '4') {
	print '
			<div class="journal-action-box">
				<span class="journal-label">Tarikh Journal:</span>
				<div class="date-inputs">';
	
	if (isset($dayList)) {
		print FormEntry("", "actionDD", "select", $actionDD, $dayList, $dayList, "1", "1");
	}
	print ' / ';
	if (isset($monthVal)) {
		print FormEntry("", "actionMM", "select", $actionMM, $monthVal, $monthVal, "1", "1");
	}
	print ' / ';
	if (isset($yrList)) {
		print FormEntry("", "actionYY", "select", $actionYY, $yrList, $yrList, "1", "1");
	}
	
	print '		</div>
				<button type="button" class="btn-modern btn-success" onclick="ProcessID(\'proses\');">Proses Journal</button>
			</div>';
}

print '
		</div>
		
		<div class="pagination-row">
			<div style="font-size: 14px; color: #4a5568;">
				<strong>Jumlah rekod:</strong> '.$TotalRec.'
			</div>
			<div class="per-page">
				<span>Paparan:</span>
				<select name="pg" class="select-modern" onchange="doListAll();">';

$pageOptions = array(50, 100, 150, 200, 250, 300);
foreach ($pageOptions as $option) {
	$selected = ($pg == $option) ? ' selected' : '';
	print '			<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
}

print '			</select>
				<span>/ mukasurat</span>
			</div>
		</div>
		
		<div class="table-container">
			<table class="table-modern">
				<thead>
					<tr>
						<th width="40">BIL</th>
						<th width="180">DOKUMEN NO<br>TARIKH COLLECTION</th>
						<th width="200">NO ATM</th>
						<th class="text-center" width="140">JUMLAH COLLECTION</th>
						<th class="text-center" width="120">TARIKH LULUS</th>
						<th class="text-center" width="100">STATUS</th>
						<th class="text-center highlight" width="60">
							<input type="checkbox" onClick="ITRViewSelectAll()" class="checkbox-modern">
						</th>
						<th width="180">NO JERNAL<br>TARIKH JERNAL</th>
					</tr>
				</thead>
				<tbody>';

if ($GetList->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	
	while (!$GetList->EOF && $cnt <= $pg) {
		$collectID = getField($GetList, 'collectID') ?? '';
		$docNo = getField($GetList, 'docNo') ?? '';
		$collectDate = getField($GetList, 'collectDate') ?? '';
		$atmID = getField($GetList, 'atmID') ?? '';
		$totalValue = getField($GetList, 'totalValue') ?? 0;
		$approvedDate = getField($GetList, 'approvedDate') ?? '';
		$collectStatus = getField($GetList, 'status') ?? '';
		$journalID = getField($GetList, 'journalID') ?? 0;
		$journalNo = getField($GetList, 'journalNo') ?? '';
		$journalDate = getField($GetList, 'journalDate') ?? '';
		
		$statusDisplay = '';
		if ($collectStatus !== '' && isset($statusList) && isset($statusVal)) {
			$statusIndex = array_search($collectStatus, $statusVal);
			if ($statusIndex !== false) {
				$statusDisplay = $statusList[$statusIndex];
			}
		}
		
		$atmNo = dlookup("atm", "atmNo", "atmID=" .tosql($atmID, "Text")) . " - ".
				 dlookup("atm", "atmLoc", "atmID=" .tosql($atmID, "Text"));
		
		print '
				<tr>
					<td class="text-muted text-right">'.$bil.'.</td>
					<td>
						<span class="link-primary">'.htmlspecialchars($docNo).'</span><br>
						<span class="text-muted">'.todate('d/m/Y', $collectDate).'</span>
					</td>
					<td><span class="text-bold">'.htmlspecialchars($atmNo).'</span></td>
					<td class="text-right text-bold">'.number_format($totalValue, 2).'</td>
					<td class="text-center">'.todate('d/m/Y', $approvedDate).'</td>
					<td class="text-center"><span class="badge badge-approved">'.$statusDisplay.'</span></td>';
		
		if ($sessionGroupID == '4' || $sessionGroupID == '3') {
			print '		<td class="text-center highlight-cell">';
			if ($journalID == 0) {
				print '<input type="checkbox" name="pk[]" value="'.htmlspecialchars($collectID).'" class="checkbox-modern">';
			}
			print '</td>';
		} else {
			print '		<td class="text-center highlight-cell"></td>';
		}
		
		print '		<td>
						<span class="link-primary">'.htmlspecialchars($journalNo).'</span><br>
						<span class="text-muted"><i>'.todate('d/m/Y', $journalDate).'</i></span>
					</td>
				</tr>';
		
		$cnt++;
		$bil++;
		$GetList->MoveNext();
	}
} else {
	print '
				<tr>
					<td colspan="8" class="empty-state">
						<div class="empty-icon">📋</div>
						<div class="empty-text">';
	
	if ($q <> '') {
		print 'Rekod "'.htmlspecialchars($q).'" tidak dijumpai';
	} else {
		print 'Tiada rekod dalam sistem';
	}
	
	print '			</div>
					</td>
				</tr>';
}

print '
				</tbody>
			</table>
		</div>';

if ($TotalRec > $pg && $GetList->RowCount() > 0) {
	$numPage = ($TotalRec % $pg == 0) ? $TotalPage : $TotalPage + 1;
	
	print '
		<div class="footer-info">
			<div style="font-size: 14px; color: #4a5568;">
				<strong>Halaman:</strong>
			</div>
			<div class="pagination-links">';
	
	for ($i = 1; $i <= $numPage; $i++) {
		$linkRef = $sFileName.'?StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.urlencode($q).'&hqID='.urlencode($hqID).'&branchID='.urlencode($branchID).'&ddFrom='.$ddFrom.'&mmFrom='.$mmFrom.'&yyFrom='.$yyFrom.'&yyTo='.$yyTo.'&ddTo='.$ddTo.'&mmTo='.$mmTo.'&status='.urlencode($status);
		print '		<a href="'.htmlspecialchars($linkRef).'" class="page-link">'.$i.'</a>';
	}
	
	print '		</div>
		</div>';
}

print '
	</form>
</div>

<script>
	var allChecked = false;
	function ITRViewSelectAll() {
		e = document.ITRViewResults.elements;
		allChecked = !allChecked;
		for(c = 0; c < e.length; c++) {
			if(e[c].type == "checkbox" && e[c].name != "all") {
				e[c].checked = allChecked;
			}
		}
	}
	
	function doListAll() {
		c = document.forms[\'ITRViewResults\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&hqID='.addslashes($hqID).'&branchID='.addslashes($branchID).'&pg=" + c.options[c.selectedIndex].value;
	}
	
	function selGroup() {
		c = document.forms[\'ITRViewResults\'].q;
		document.location = "' . $sFileName . '?StartRec=1&hqID='.addslashes($hqID).'&branchID='.addslashes($branchID).'&q=" + c.options[c.selectedIndex].value;
	}
	
	function ProcessID(v) {
		e = document.ITRViewResults;
		if (e == null) {
			alert(\'Please confirm the form name.!\');
		} else {
			count = 0;
			for(c = 0; c < e.elements.length; c++) {
				if(e.elements[c].name == "pk[]" && e.elements[c].checked) {
					count++;
				}
			}
		}
		if (count == 0) {
			alert(\'Sila pilih rekod untuk proses Jernal.\');
		} else {
			if(confirm(\'Proses Jernal untuk : \' + count + \' rekod?\')) {
				e.action.value = v;
				e.submit();
				
				var actionBtn = document.querySelector(\'button[onclick*="ProcessID"]\');
				if (actionBtn) {
					actionBtn.disabled = true;
					actionBtn.textContent = \'Sila tunggu.....\';
				}
			}
		}
	}
	
	function PrintRec(id, code) {
		if(confirm(\' Cetak Hantaran Borang Tempahan ?\')) {
			window.open("rpt_pdf/rpt_FormTempah.php?pk=" + id, "sort", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}
</script>';

include("footer.php");
?>