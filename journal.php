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
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$q = $_REQUEST['q'] ?? '';
$hqID = $_REQUEST['hqID'] ?? '';
$branchID = $_REQUEST['branchID'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$status = $_REQUEST['status'] ?? '';

include('header.php');
include('forms.php');

$sFileName = 'journal.php';
$sFileRef = 'journal.php';
$title = 'SENARAI JERNAL';

// Cache session variables
$sessionUserID = $_SESSION['Session.userID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionHqID = $_SESSION['Session.hqID'] ?? '';
$sessionBranchID = $_SESSION['Session.branchID'] ?? '';

if ($sessionUserID == "") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location.href="index.php";</script>';
	exit;
}

if ($sessionGroupID == '3' || $sessionGroupID == '4' || $sessionGroupID == '9') {
	// Admin users - no restrictions
} elseif ($sessionGroupID == '2') {
	$hqID = $sessionHqID;
} else {
	$hqID = $sessionHqID;
	$branchID = $sessionBranchID;
}

$strURL = urlencode($QUERY_STRING);
if ($strURL == '') {
	$strURL = 'q='.$q.'&hqID='.$hqID.'&branchID='.$branchID;
	$strURL = urlencode($strURL);
}

$ddmmyyFrom = sprintf("%04d-%02d-%02d", $yyFrom, $mmFrom, $ddFrom);               
$ddmmyyTo = sprintf("%04d-%02d-%02d", $yyTo, $mmTo, $ddTo); 

//--- BEGIN : IMPORT TEXT FILES -------------------------------------------------------------------------------------------------
if ($action == "export" && $pk != '') {
	$journalNo = dlookup("journal", "journalNo", "journalID=" . tosql($pk, "Text"));
	
	//--- Begin : Create log 
	$user = $sessionUserName;
	$event = $title." : Export fail (".$journalNo.".txt) - telah dijana";
	$type = $sessionGroupName;
	createLog($user, $event, $type);
	
	$genData = '';
	
	$sSQL = " 
	SELECT A.journalNo, A.journalDate, A.journalAmt, A.description  
	FROM journal A 
	INNER JOIN instruct B ON A.journalID = B.journalID 
	WHERE A.journalID = '".$pk."'";
	
	$rs = $conn->Execute($sSQL);
	
	if ($rs && $rs->RowCount() <> 0) {
		while (!$rs->EOF) {
			$shortCode = getField($rs, 'shortCode') ?? '';
			$journalAmt = getField($rs, 'journalAmt') ?? 0;
			$journalNoField = getField($rs, 'journalNo') ?? '';
			$description = getField($rs, 'description') ?? '';
			
			$genData .= str_pad('500', 3).
						str_pad($shortCode, 3, " ", STR_PAD_LEFT).
						str_pad('10130-010', 9, " ", STR_PAD_LEFT).
						str_pad('000', 5, " ", STR_PAD_LEFT).
						str_pad('2', 2, " ", STR_PAD_LEFT).
						str_pad(number_format($journalAmt, 2, '.', ''), 14, " ", STR_PAD_LEFT).
						str_pad($journalNoField, 7, " ", STR_PAD_LEFT).' '.
						str_pad('', 22).
						str_pad($description, 14, " ", STR_PAD_LEFT).
						str_pad('000', 3).PHP_EOL;			
			$rs->MoveNext();
		}
		
		$path = dirname(__FILE__); 
		$genFile = $path.'\\files\\'.$journalNo.".txt";
		
		if (file_exists($genFile)) {
			unlink($genFile);
		}
		
		$new_file = fopen($genFile, "w");
		fwrite($new_file, $genData);
		fclose($new_file);
	
		$msg = "Import fail (".$journalNo.".txt) - telah dijana";
		print '<script>alert("'.addslashes($msg).'");</script>';
	} 
}
//--- END   : IMPORT TEXT FILES -------------------------------------------------------------------------------------------------

$sSQL = " 
SELECT * FROM journal 
WHERE journalDate BETWEEN '".$ddmmyyFrom."' AND '".$ddmmyyTo."'";	

if ($q <> '') {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q ".
			" WHERE Q.journalNo LIKE ".tosql("%".$q."%", "Text");
} else {
	$sSQL .= " ORDER BY journalDate DESC, journalNo DESC";
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
	
	.btn-icon {
		padding: 6px 12px;
		border: none;
		border-radius: 4px;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
		display: inline-flex;
		align-items: center;
		gap: 6px;
	}
	
	.btn-print {
		background: #4299e1;
		color: white;
	}
	
	.btn-print:hover {
		background: #3182ce;
	}
	
	.btn-export {
		background: #48bb78;
		color: white;
	}
	
	.btn-export:hover {
		background: #38a169;
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
		min-width: 800px;
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
	
	.table-modern thead th.text-right {
		text-align: right;
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
	
	.text-muted {
		color: #718096;
		font-size: 13px;
	}
	
	.text-bold {
		font-weight: 600;
		color: #1a202c;
	}
	
	.action-buttons {
		display: flex;
		gap: 6px;
		flex-wrap: wrap;
	}
	
	.file-link {
		display: inline-block;
		margin-top: 6px;
		padding: 4px 8px;
		background: #edf2f7;
		border-radius: 4px;
		color: #2d3748;
		text-decoration: none;
		font-size: 12px;
		transition: all 0.2s;
	}
	
	.file-link:hover {
		background: #e2e8f0;
		color: #1a202c;
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
		
		.action-buttons {
			flex-direction: column;
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
		</h1>
		<p class="page-subtitle">Senarai dan pengurusan journal ATM</p>
	</div>

	<form name="ITRViewResults" action="'.$sFileName.'" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="pk">
		<input type="hidden" name="q" value="'.htmlspecialchars($q).'">
		
		<div class="filter-section">
			<div class="search-row">
				<span class="search-label">Carian:</span>
				<input type="text" name="q" value="'.htmlspecialchars($q).'" placeholder="Journal No..." maxlength="100" class="input-modern" style="min-width: 220px;">
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
			</div>
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
						<th width="180">JOURNAL NO</th>
						<th class="text-center" width="140">TARIKH JOURNAL</th>
						<th class="text-right" width="140">JUMLAH (RM)</th>
						<th class="text-center" width="200">CETAK JOURNAL</th>
						<th class="text-center" width="140">STATUS BAYARAN</th>
					</tr>
				</thead>
				<tbody>';

if ($GetList->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	
	while (!$GetList->EOF && $cnt <= $pg) {
		$journalID = getField($GetList, 'journalID') ?? '';
		$journalNo = getField($GetList, 'journalNo') ?? '';
		$journalDate = getField($GetList, 'journalDate') ?? '';
		$journalAmt = getField($GetList, 'journalAmt') ?? 0;
		$isPymt = getField($GetList, 'isPymt') ?? '';
		
		$statusDisplay = '';
		
		print '
				<tr>
					<td class="text-muted text-right">'.$bil.'.</td>
					<td>
						<span class="link-primary">'.htmlspecialchars($journalNo).'</span>
					</td>
					<td class="text-center">'.todate('d/m/Y', $journalDate).'</td>
					<td class="text-right text-bold">'.number_format($journalAmt, 2).'</td>
					<td class="text-center">
						<div class="action-buttons">
							<button type="button" class="btn-icon btn-print" onclick="PrintJrn(\''.addslashes($journalID).'\',\''.addslashes($journalNo).'\');">
								🖨️ Cetak
							</button>
							<button type="button" class="btn-icon btn-export" onclick="ExportTxt(\''.addslashes($journalID).'\',\''.addslashes($journalNo).'.txt\');">
								📄 Export
							</button>
						</div>';

		$path = dirname(__FILE__); 
		$filename = $path.'\\files\\'.$journalNo.".txt";
		if (file_exists($filename)) {
			print '		<a href="files/'.htmlspecialchars($journalNo).'.txt" target="_blank" class="file-link">📎 '.htmlspecialchars($journalNo).'.txt</a>';
		}
					
		print '		</td>
					<td class="text-center">'.htmlspecialchars($statusDisplay).'</td>
				</tr>';
		
		$cnt++;
		$bil++;
		$GetList->MoveNext();
	}
} else {
	print '
				<tr>
					<td colspan="6" class="empty-state">
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
	
	function PayID(id, code) {
		e = document.ITRViewResults;
		if(confirm(\' Kemaskini Status Bayaran Inbois No : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "payment";
			e.submit();
		}
	}
	
	function PrintJrn(id, code) {
		if(confirm(\' Cetak Jernal No : \' + code + \' ?\')) {
			window.open("rpt_pdf/rpt_Formjournal.php?pk=" + id, "sort", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}

	function ExportTxt(id, code) {
		e = document.ITRViewResults;
		if(confirm(\' Export Fail Text : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "export";
			e.submit();
		}
	}
	
	function PrintPV(id, code) {
		if(confirm(\' Bayaran untuk inbois : \' + code + \' ?\')) {
			window.open("rpt_pdf/rpt_FormPymt.php?pk=" + id, "sort", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}
</script>';

include("footer.php");
?>