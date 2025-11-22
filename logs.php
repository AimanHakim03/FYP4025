<?php

$today = date("F j, Y, g:i a"); 

// Initialize ALL request variables - CRITICAL FIX
$ddFrom = $_REQUEST['ddFrom'] ?? date("j");
$mmFrom = $_REQUEST['mmFrom'] ?? date("n");
$yyFrom = $_REQUEST['yyFrom'] ?? date("Y");
$ddTo = $_REQUEST['ddTo'] ?? date("j");
$mmTo = $_REQUEST['mmTo'] ?? date("n");
$yyTo = $_REQUEST['yyTo'] ?? date("Y");
$StartRec = isset($_REQUEST['StartRec']) ? (int)$_REQUEST['StartRec'] : 1;
$pg = isset($_REQUEST['pg']) ? (int)$_REQUEST['pg'] : 50;
$group = $_REQUEST['group'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? [];
$q = $_REQUEST['q'] ?? '';
$userID = $_REQUEST['userID'] ?? '';

include("header.php");	

$sFileName = 'logs.php';
$sFileRef = 'logs.php';
$title = 'Audit Aktiviti';

// Cache session variables
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionProfile = $_SESSION['Session.profile'] ?? '';

if ($sessionGroupID <> "3" && $sessionGroupID <> "9") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location="index.php";</script>';
	exit;
}

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "delete" && is_array($pk) && count($pk) > 0) {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "logID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM logs WHERE " . $sWhere;
		$rs = $conn->Execute($sSQL);
	}
	print '<script>alert("'.count($pk).' rekod audit telah dihapuskan."); window.location.href="'.$sFileName.'";</script>';
	exit;
}
//--- End : deletion based on checked box -------------------------------------------------------

//--- BEGIN : PREPARE USER LIST BASED ON GROUP --------------------------------------------------------------------------------
$userList = array();
$userVal = array();

if ($group <> '') {
	if (isset($groupList) && isset($groupVal)) {
		$groupIndex = array_search($group, $groupList);
		if ($groupIndex !== false) {
			$groupID = $groupVal[$groupIndex];
			$sSQL = "SELECT userName, fullName FROM users WHERE groupID = '".$groupID."' ORDER BY userName";
			$GetN = $conn->Execute($sSQL);
			if ($GetN && $GetN->RowCount() <> 0) {
				while (!$GetN->EOF) {
					array_push($userList, getField($GetN, 'userName').' - '.getField($GetN, 'fullName'));
					array_push($userVal, getField($GetN, 'userName'));
					$GetN->MoveNext();
				}
			}
		}
	}
}		
//--- END : PREPARE USER LIST BASED ON GROUP --------------------------------------------------------------------------------

$dtFrom = sprintf("%04d-%02d-%02d", $yyFrom, $mmFrom, $ddFrom);
$dtTo = sprintf("%04d-%02d-%02d", $yyTo, $mmTo, $ddTo);
$tomorrow = date('Y-m-d', mktime(0, 0, 0, $mmTo, $ddTo + 1, $yyTo));

if ($dtFrom > $dtTo) {
	print "<script>alert('Date From cannot be more than Date To...!');</script>";
}

$sSQL = "SELECT * FROM logs ".
		"WHERE dateEvent BETWEEN '".$dtFrom."' AND '".$tomorrow."'";

if ($group <> '') { 
	$sSQL .= " AND groupName = '".$group."'"; 
}

if ($userID <> '') { 
	$sSQL .= " AND userName = '".$userID."'"; 
}

if ($sessionGroupID <> "9") {
	$sSQL .= " AND groupName <> 'PENYELIA SISTEM'"; 
}

if ($q <> '') {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q WHERE Q.eventText LIKE '%".$q."%'";
	$sSQL .= " ORDER BY Q.logID DESC";
} else {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q";
	$sSQL .= " ORDER BY Q.logID DESC";
}

$GetLog = $conn->Execute($sSQL);

if (!$GetLog) {
	print '<div class="error">Database error: '.$conn->ErrorMsg().'</div>';
	include("footer.php");
	exit;
}

// Move to start record
if ($StartRec > 1) {
	$GetLog->Move($StartRec - 1);
}

$TotalRec = $GetLog->RowCount();
$TotalPage = ceil($TotalRec / $pg);

print '
<style>
/* Logs Page Styles */
.logs-page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.logs-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.logs-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Filter Card */
.logs-filter-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.logs-filter-section {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.logs-filter-row {
	display: flex;
	gap: 1rem;
	align-items: end;
	flex-wrap: wrap;
}

.logs-search-group {
	flex: 1;
	min-width: 250px;
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.logs-filter-label {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.logs-search-input {
	padding: 0.75rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
}

.logs-search-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.date-filter-group {
	display: flex;
	gap: 1rem;
	align-items: center;
	flex-wrap: wrap;
}

.date-picker-group {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.date-inputs {
	display: flex;
	gap: 0.5rem;
	align-items: center;
}

.date-select {
	padding: 0.625rem 0.75rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
}

.date-select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.date-year-input {
	width: 80px;
	padding: 0.625rem 0.75rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
}

.date-year-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.secondary-filters {
	display: flex;
	gap: 1rem;
	align-items: center;
	flex-wrap: wrap;
}

.filter-group-logs {
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.filter-select-logs {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
	min-width: 150px;
}

.filter-select-logs:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.logs-btn {
	padding: 0.75rem 1.5rem;
	border: none;
	border-radius: 8px;
	font-size: 0.875rem;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.2s;
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
}

.logs-btn-primary {
	background: #3b82f6;
	color: #ffffff;
}

.logs-btn-primary:hover {
	background: #2563eb;
	box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.logs-btn-danger {
	background: #ef4444;
	color: #ffffff;
}

.logs-btn-danger:hover {
	background: #dc2626;
	box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.pagination-controls-logs {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	font-size: 0.875rem;
	color: #64748b;
	margin-left: auto;
}

.checkbox-all-logs {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	font-size: 0.875rem;
	color: #475569;
}

.checkbox-logs {
	width: 18px;
	height: 18px;
	cursor: pointer;
	accent-color: #3b82f6;
}

/* Table Container */
.logs-table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow: hidden;
}

.logs-table {
	width: 100%;
	border-collapse: collapse;
}

.logs-table thead th {
	padding: 1rem 0.75rem;
	text-align: left;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	background: #475569;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: nowrap;
}

.logs-table tbody td {
	padding: 0.875rem 0.75rem;
	font-size: 0.875rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
	vertical-align: top;
}

.logs-table tbody tr:hover {
	background: #f8fafc;
}

.profile-badge-logs {
	display: inline-block;
	padding: 0.25rem 0.75rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
	background: #e0e7ff;
	color: #4338ca;
}

.activity-text {
	color: #64748b;
	line-height: 1.5;
}

.datetime-text {
	font-family: monospace;
	font-size: 0.8rem;
	color: #64748b;
	white-space: nowrap;
}

.ip-badge {
	display: inline-block;
	padding: 0.25rem 0.625rem;
	background: #f1f5f9;
	color: #64748b;
	border-radius: 6px;
	font-size: 0.75rem;
	font-family: monospace;
	white-space: nowrap;
}

/* Empty State */
.logs-empty-state {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.logs-empty-state i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.logs-empty-state h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
	font-size: 1.25rem;
}

/* Pagination */
.logs-pagination-container {
	padding: 1.5rem;
	background: #ffffff;
	border-top: 1px solid #e2e8f0;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 1rem;
}

.logs-pagination-info {
	color: #64748b;
	font-size: 0.875rem;
}

.logs-pagination-info strong {
	color: #0f172a;
	font-weight: 600;
}

.logs-pagination-links {
	display: flex;
	gap: 0.5rem;
	flex-wrap: wrap;
}

.logs-page-link {
	padding: 0.5rem 0.75rem;
	border: 1px solid #e2e8f0;
	background: #ffffff;
	color: #475569;
	border-radius: 6px;
	font-size: 0.875rem;
	text-decoration: none;
	transition: all 0.15s;
	display: inline-block;
}

.logs-page-link:hover {
	border-color: #3b82f6;
	color: #3b82f6;
	background: #eff6ff;
}

/* Responsive */
@media (max-width: 768px) {
	.logs-filter-row,
	.date-filter-group,
	.secondary-filters {
		flex-direction: column;
		align-items: stretch;
	}
	
	.logs-search-group,
	.date-picker-group {
		width: 100%;
	}
	
	.pagination-controls-logs {
		margin-left: 0;
		width: 100%;
	}
	
	.logs-table-container {
		overflow-x: auto;
	}
	
	.logs-table {
		min-width: 1000px;
	}
}
</style>

<form name="ITRViewResults" action="'.$sFileName.'" method="post">
<input type="hidden" name="action">

<!-- Page Header -->
<div class="logs-page-header">
	<h1 class="logs-title">
		<i class="fas fa-clipboard-list"></i>
		Audit Aktiviti
	</h1>
	<p class="logs-subtitle">Pantau dan jejak semua aktiviti pengguna dalam sistem</p>
</div>

<!-- Filter Card -->
<div class="logs-filter-card">
	<div class="logs-filter-section">
		<!-- Search and Date Range -->
		<div class="logs-filter-row">
			<div class="logs-search-group">
				<label class="logs-filter-label">
					<i class="fas fa-search"></i> Carian Aktiviti
				</label>
				<input type="text" name="q" value="'.htmlspecialchars($q).'" placeholder="Cari aktiviti..." class="logs-search-input" maxlength="100">
			</div>
			
			<div class="date-filter-group">
				<div class="date-picker-group">
					<label class="logs-filter-label">
						<i class="far fa-calendar"></i> Dari
					</label>
					<div class="date-inputs">
						<select name="ddFrom" class="date-select" style="width: 70px;">';
for ($d = 1; $d < 32; $d++) {
	print '				<option value="'.$d.'"';
	if ($ddFrom == $d) print ' selected';
	print 				'>'.$d.'</option>';
}
print '				</select>
						<select name="mmFrom" class="date-select" style="width: 70px;">';
for ($m = 1; $m < 13; $m++) {
	print '				<option value="'.$m.'"';
	if ($mmFrom == $m) print ' selected';
	print 				'>'.$m.'</option>';
}
print '				</select>
						<input type="text" name="yyFrom" value="'.$yyFrom.'" class="date-year-input" maxlength="4">
					</div>
				</div>
				
				<div style="padding-top: 1.5rem; color: #94a3b8; font-weight: 600;">
					<i class="fas fa-arrow-right"></i>
				</div>
				
				<div class="date-picker-group">
					<label class="logs-filter-label">
						<i class="far fa-calendar"></i> Hingga
					</label>
					<div class="date-inputs">
						<select name="ddTo" class="date-select" style="width: 70px;">';
for ($d = 1; $d < 32; $d++) {
	print '				<option value="'.$d.'"';
	if ($ddTo == $d) print ' selected';
	print 				'>'.$d.'</option>';
}
print '				</select>
						<select name="mmTo" class="date-select" style="width: 70px;">';
for ($m = 1; $m < 13; $m++) {
	print '				<option value="'.$m.'"';
	if ($mmTo == $m) print ' selected';
	print 				'>'.$m.'</option>';
}
print '				</select>
						<input type="text" name="yyTo" value="'.$yyTo.'" class="date-year-input" maxlength="4">
					</div>
				</div>
			</div>
			
			<div style="padding-top: 1.8rem;">
				<button type="submit" class="logs-btn logs-btn-primary">
					<i class="fas fa-search"></i>
					Retrieve
				</button>
			</div>
		</div>
		
		<!-- Secondary Filters -->
		<div class="secondary-filters">
			<div class="filter-group-logs">
				<label class="logs-filter-label">
					<i class="fas fa-user-tag"></i> Profil:
				</label>
				<select class="filter-select-logs" name="group" onchange="selGroup();">
					<option value="">- Semua -</option>';

if (isset($groupList) && is_array($groupList)) {
	for ($cnt = 0; $cnt < count($groupList); $cnt++) {
		print '			<option value="'.htmlspecialchars($groupList[$cnt]).'"';
		if ($groupList[$cnt] == $group) print ' selected';
		print 			'>'.htmlspecialchars($groupList[$cnt]).'</option>';
	}
}

print '			</select>
			</div>';

if ($group <> '' && count($userList) > 0) {
	print '
			<div class="filter-group-logs">
				<label class="logs-filter-label">
					<i class="fas fa-user"></i> ID Pengguna:
				</label>
				<select class="filter-select-logs" name="userID" onchange="selUser();">
					<option value="">- Semua -</option>';
	for ($cnt = 0; $cnt < count($userList); $cnt++) {
		print '			<option value="'.htmlspecialchars($userVal[$cnt]).'"';
		if ($userVal[$cnt] == $userID) print ' selected';
		print 			'>'.htmlspecialchars($userList[$cnt]).'</option>';
	}	
	print '			</select>
			</div>';
}

print '
			<div class="pagination-controls-logs">
				<label>Paparan:</label>
				<select name="pg" class="date-select" onchange="doListAll();" style="width: 80px;">';

$pageOptions = array(10, 20, 30, 40, 50, 100, 200);
foreach ($pageOptions as $option) {
	$selected = ($pg == $option) ? ' selected' : '';
	print '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
}

print '			</select>
				<span>/ mukasurat</span>
			</div>
		</div>
		
		<!-- Action Buttons -->
		<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
			<div style="display: flex; gap: 1rem; align-items: center;">';

if ($sessionProfile == '3' || $sessionProfile == '4' || $sessionProfile == '9') {
	print '
				<div class="checkbox-all-logs">
					<input type="checkbox" onClick="ITRViewSelectAll()" class="checkbox-logs">
					<span>Pilih semua</span>
				</div>';
}

if ($sessionGroupID == "9") {	
	print '
				<button type="button" class="logs-btn logs-btn-danger" onClick="ITRActionButtonClick(\'delete\');">
					<i class="fas fa-trash"></i>
					Hapus Terpilih
				</button>';
}

print '
			</div>
		</div>
	</div>
</div>

<!-- Table Container -->
<div class="logs-table-container">
	<table class="logs-table">
		<thead>
			<tr>
				<th style="width: 50px; text-align: right;">NO.</th>
				<th style="width: 150px;">ID PENGGUNA</th>
				<th style="width: 180px; text-align: center;">PROFIL</th>
				<th>AKTIVITI</th>
				<th style="width: 180px; text-align: center;">TARIKH AKTIVITI</th>
				<th style="width: 130px; text-align: center;">TERMINAL IP</th>
			</tr>
		</thead>
		<tbody>';

if ($GetLog->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	
	while (!$GetLog->EOF && $cnt <= $pg) {
		$logID = getField($GetLog, 'logID') ?? '';
		$userName = getField($GetLog, 'userName') ?? '';
		$groupName = getField($GetLog, 'groupName') ?? '';
		$eventText = getField($GetLog, 'eventText') ?? '';
		$dateEvent = getField($GetLog, 'dateEvent') ?? '';
		$terminal = getField($GetLog, 'terminal') ?? '';
		
		print '
			<tr>
				<td style="text-align: right; font-weight: 600; color: #64748b;">' . $bil . '</td>
				<td style="font-weight: 600;">';
		
		if ($sessionGroupID == "9") {	
			print '		<input type="checkbox" name="pk[]" value="'.htmlspecialchars($logID).'" class="checkbox-logs" style="margin-right: 0.5rem;">';
		}
		
		print 			htmlspecialchars($userName).'
				</td>
				<td style="text-align: center;">
					<span class="profile-badge-logs">'.htmlspecialchars($groupName).'</span>
				</td>
				<td>
					<div class="activity-text">'.htmlspecialchars($eventText).'</div>
				</td>
				<td style="text-align: center;">
					<span class="datetime-text">'.toDate("d/m/Y H:i:s A", $dateEvent).'</span>
				</td>
				<td style="text-align: center;">
					<span class="ip-badge">'.htmlspecialchars($terminal).'</span>
				</td>
			</tr>';
		$cnt++;
		$bil++;
		$GetLog->MoveNext();
	}
	
	print '
		</tbody>
	</table>
	
	<!-- Pagination -->
	<div class="logs-pagination-container">
		<div class="logs-pagination-info">
			Jumlah Rekod: <strong>' . $GetLog->RowCount() . '</strong>
		</div>';
	
	if ($TotalRec > $pg) {
		$numPage = ($TotalRec % $pg == 0) ? $TotalPage : $TotalPage + 1;
		
		print '
		<div class="logs-pagination-links">';
		
		for ($i = 1; $i <= min($numPage, 10); $i++) {
			$linkRef = $sFileName.'?StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&q='.urlencode($q).'&group='.urlencode($group).'&userID='.urlencode($userID).'&ddFrom='.$ddFrom.'&mmFrom='.$mmFrom.'&yyFrom='.$yyFrom.'&ddTo='.$ddTo.'&mmTo='.$mmTo.'&yyTo='.$yyTo;
			print '<a href="'.htmlspecialchars($linkRef).'" class="logs-page-link">'.$i.'</a>';
		}
		
		if ($numPage > 10) {
			print '<span class="logs-page-link" style="border: none; cursor: default;">...</span>';
		}
		
		print '
		</div>';
	} else {
		print '<div></div>';
	}
	
	print '
	</div>';
} else {
	print '
		</tbody>
	</table>
	<div class="logs-empty-state">';
	
	if ($q <> '') {
		print '
		<i class="fas fa-search"></i>
		<h3>Tiada Rekod Dijumpai</h3>
		<p>Carian "'.htmlspecialchars($q).'" tidak menjumpai sebarang aktiviti</p>';
	} else {
		print '
		<i class="fas fa-clipboard-list"></i>
		<h3>Tiada Rekod</h3>
		<p>Tiada aktiviti audit dalam tempoh yang dipilih</p>';
	}
	
	print '
	</div>';
}

print ' 
	</div>
</form>';

include("footer.php");	

print '
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

	function ITRActionButtonClick(v) {
		e = document.ITRViewResults;
		if(e == null) {
			alert(\'Sila pastikan nama borang.!\');
		} else {
			count = 0;
			for(c = 0; c < e.elements.length; c++) {
				if(e.elements[c].name == "pk[]" && e.elements[c].checked) {
					count++;
				}
			}
			
			if(count == 0) {
				alert(\'Sila pilih rekod untuk dihapuskan.\');
			} else {
				if(confirm(\'Hapuskan untuk \' + count + \' rekod?\')) {
					e.action.value = v;
					e.submit();
				}
			}
		}
	}
	
	function doListAll() {
		c = document.forms[\'ITRViewResults\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&group='.addslashes($group).'&userID='.addslashes($userID).'&pg=" + c.options[c.selectedIndex].value + "&q='.addslashes($q).'&ddFrom='.$ddFrom.'&mmFrom='.$mmFrom.'&yyFrom='.$yyFrom.'&ddTo='.$ddTo.'&mmTo='.$mmTo.'&yyTo='.$yyTo.'";
	}

	function selGroup() {
		c = document.forms[\'ITRViewResults\'].group;
		document.location = "' . $sFileName . '?StartRec=1&group=" + c.options[c.selectedIndex].value + "&q='.addslashes($q).'&pg='.$pg.'&ddFrom='.$ddFrom.'&mmFrom='.$mmFrom.'&yyFrom='.$yyFrom.'&ddTo='.$ddTo.'&mmTo='.$mmTo.'&yyTo='.$yyTo.'";
	}	  
	
	function selUser() {
		c = document.forms[\'ITRViewResults\'].userID;
		document.location = "' . $sFileName . '?StartRec=1&group='.addslashes($group).'&userID=" + c.options[c.selectedIndex].value + "&q='.addslashes($q).'&pg='.$pg.'&ddFrom='.$ddFrom.'&mmFrom='.$mmFrom.'&yyFrom='.$yyFrom.'&ddTo='.$ddTo.'&mmTo='.$mmTo.'&yyTo='.$yyTo.'";
	}	  
	  
</script>';

?>