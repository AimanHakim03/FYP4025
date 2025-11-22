<?php

$today = date("F j, Y, g:i a"); 
                
// Initialize ALL request variables - CRITICAL FIX
$StartRec = isset($_REQUEST['StartRec']) ? (int)$_REQUEST['StartRec'] : 1;
$pg = isset($_REQUEST['pg']) ? (int)$_REQUEST['pg'] : 50;
$q = $_REQUEST['q'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? [];
$vendorID = $_REQUEST['vendorID'] ?? '';
$typeID = $_REQUEST['typeID'] ?? '';
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$hq = $_REQUEST['hq'] ?? '';
$branch = $_REQUEST['branch'] ?? '';
$regionID = $_REQUEST['regionID'] ?? '';
$stateID = $_REQUEST['stateID'] ?? '';

include("header.php");	
// getField() function is now in setupinfo.php - no need to declare it here!

// Cache session variables - CRITICAL FIX
$sessionUserID = $_SESSION['Session.userID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';

if ($sessionUserID == "") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location.href="index.php";</script>';
	exit;
}

$strURL = urlencode($QUERY_STRING);
if ($strURL == '') {
	$strURL = 'q='.$q.'&hq='.$hq.'&branch='.$branch;
	$strURL = urlencode($strURL);
}

$sFileName = 'terminal.php';
$sFileRef = 'terminalAddUpdate.php';
$title = "TERMINAL ATM";

//--- BEGIN : DELETION BASED ON CHECKED BOX -------------------------------------------------------------
if ($action == "delete" && is_array($pk) && count($pk) > 0) {
	for ($i = 0; $i < count($pk); $i++) {
		$atmNo = dlookup("atm", "atmNo", "atmID=" . tosql($pk[$i], "Text"));
		$atmLoc = dlookup("atm", "atmLoc", "atmID=" . tosql($pk[$i], "Text"));
		
		//--- Begin : Create log 
		$user = $sessionUserName;
		$event = "Terminal ATM : ".$atmNo." ".$atmLoc." - Dihapuskan";
		$type = $sessionGroupName;
		createLog($user, $event, $type);
		//--- End   : Create log 	
		
		$sSQL = "UPDATE atm SET isDeleted = '1' WHERE atmID =".tosql($pk[$i], "Text");
		$rs = $conn->Execute($sSQL);
	}
	// Redirect after delete
	print '<script>
			alert("'.count($pk).' terminal telah dihapuskan.");
			window.location.href = "'.$sFileName.'";
		</script>';
	exit;
}
//--- END   : DELETION BASED ON CHECKED BOX ----------------------------------------------------------

//--- BEGIN : VENDOR LIST -------------------------------------------------------------------
$vendorList = array();
$vendorVal = array();
$sSQL = "SELECT V.vendorID, V.vendorCode, V.vendorName FROM vendor V WHERE isDeleted <> '1'";
$GetN = $conn->Execute($sSQL);
if ($GetN && $GetN->RowCount() <> 0) {
	while (!$GetN->EOF) {
		array_push($vendorList, getField($GetN, 'vendorName'));
		array_push($vendorVal, getField($GetN, 'vendorID'));
		$GetN->MoveNext();
	}
}	
//--- END   : VENDOR LIST -------------------------------------------------------------------

//---- BEGIN : Query -----------------------------------------------------------------------------
$sSQL = "SELECT A.*, B.vendorCode, B.vendorName FROM atm A LEFT JOIN vendor B ON A.vendorID = B.vendorID 
		 WHERE A.isDeleted = '0'";

if ($q <> "") {
	$sSQL .= " AND (A.atmLoc LIKE ".toSQL("%".$q."%","Text").
		     " OR A.atmNo LIKE ".toSQL($q."%","Text").")";	 
} 

if ($vendorID <> "") {
	$sSQL .= " AND A.vendorID = ".toSQL($vendorID,"Text");
}

if ($typeID <> "") {
	$sSQL .= " AND A.[type] = ".toSQL($typeID,"Text");
}

$sSQL .= " ORDER BY A.atmNo, A.atmLoc";
//---- END : Query -----------------------------------------------------------------------------

$GetList = $conn->Execute($sSQL);

if (!$GetList) {
	print '<div class="error">Database error: '.$conn->ErrorMsg().'</div>';
	include("footer.php");
	exit;
}

// Move to start record
if ($StartRec > 1) {
	$GetList->Move($StartRec - 1);
}

$TotalRec = $GetList->RowCount();
$TotalPage = ceil($TotalRec / $pg);

print '
<style>
/* Terminal Page Styles */
.terminal-page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.terminal-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.terminal-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Filter Card */
.terminal-filter-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.filter-row-terminal {
	display: flex;
	gap: 1rem;
	align-items: end;
	flex-wrap: wrap;
	margin-bottom: 1rem;
}

.filter-group-terminal {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.filter-label-terminal {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.filter-input-terminal {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
	background: #ffffff;
}

.filter-input-terminal:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select-terminal {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
	min-width: 180px;
}

.filter-select-terminal:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-terminal {
	padding: 0.625rem 1.25rem;
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

.btn-primary-terminal {
	background: #3b82f6;
	color: #ffffff;
}

.btn-primary-terminal:hover {
	background: #2563eb;
	box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-success-terminal {
	background: #10b981;
	color: #ffffff;
}

.btn-success-terminal:hover {
	background: #059669;
	box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-danger-terminal {
	background: #ef4444;
	color: #ffffff;
}

.btn-danger-terminal:hover {
	background: #dc2626;
	box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.action-buttons-terminal {
	display: flex;
	gap: 0.75rem;
	flex-wrap: wrap;
}

.pagination-controls {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	font-size: 0.875rem;
	color: #64748b;
}

/* Table Container */
.terminal-table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.terminal-table {
	width: 100%;
	border-collapse: collapse;
}

.terminal-table thead th {
	padding: 0.75rem 0.5rem;
	text-align: center;
	font-size: 0.7rem;
	font-weight: 700;
	color: #ffffff;
	background: #475569;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: normal;
	line-height: 1.3;
}

.terminal-table tbody td {
	padding: 0.75rem 0.5rem;
	font-size: 0.85rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
	border-right: 1px solid #f1f5f9;
}

.terminal-table tbody tr:hover {
	background: #f8fafc;
}

.atm-link {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 600;
	transition: color 0.2s;
}

.atm-link:hover {
	color: #2563eb;
	text-decoration: underline;
}

.type-badge {
	display: inline-block;
	padding: 0.25rem 0.5rem;
	border-radius: 6px;
	font-size: 0.7rem;
	font-weight: 700;
	text-align: center;
}

.type-atm {
	background: #dbeafe;
	color: #1e40af;
}

.type-cdm {
	background: #d1fae5;
	color: #065f46;
}

.type-cico {
	background: #fef3c7;
	color: #92400e;
}

.checkbox-terminal {
	width: 18px;
	height: 18px;
	cursor: pointer;
	accent-color: #3b82f6;
}

/* Denomination highlight */
.deno-cell {
	background: #f8fafc;
	font-weight: 600;
}

/* Empty State */
.terminal-empty-state {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.terminal-empty-state i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.terminal-empty-state h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
	font-size: 1.25rem;
}

/* Pagination */
.pagination-container-terminal {
	padding: 1.5rem;
	background: #ffffff;
	border-top: 1px solid #e2e8f0;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 1rem;
}

.pagination-info-terminal {
	color: #64748b;
	font-size: 0.875rem;
}

.pagination-info-terminal strong {
	color: #0f172a;
	font-weight: 600;
}

.pagination-links-terminal {
	display: flex;
	gap: 0.5rem;
	flex-wrap: wrap;
}

.page-link-terminal {
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

.page-link-terminal:hover {
	border-color: #3b82f6;
	color: #3b82f6;
	background: #eff6ff;
}

/* Responsive */
@media (max-width: 768px) {
	.filter-row-terminal {
		flex-direction: column;
	}
	
	.filter-group-terminal {
		width: 100%;
	}
	
	.filter-select-terminal,
	.filter-input-terminal {
		width: 100%;
	}
	
	.action-buttons-terminal {
		width: 100%;
	}
	
	.btn-terminal {
		flex: 1;
		justify-content: center;
	}
	
	.terminal-table-container {
		overflow-x: auto;
	}
	
	.terminal-table {
		min-width: 1800px;
	}
}
</style>

<form name="MyForm" action="'.$sFileName.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">

<!-- Page Header -->
<div class="terminal-page-header">
	<h1 class="terminal-title">
		<i class="fas fa-money-bill-wave"></i>
		Terminal ATM
	</h1>
	<p class="terminal-subtitle">Urus maklumat dan konfigurasi terminal ATM</p>
</div>

<!-- Filter Section -->
<div class="terminal-filter-card">
	<div class="filter-row-terminal">
		<div class="filter-group-terminal">
			<label class="filter-label-terminal">
				<i class="fas fa-search"></i> Carian
			</label>
			<input type="text" name="q" value="'.htmlspecialchars($q).'" placeholder="No ATM/Lokasi..." class="filter-input-terminal" maxlength="20" style="width: 200px;">
		</div>
		
		<div class="filter-group-terminal">
			<label class="filter-label-terminal">
				<i class="fas fa-building"></i> Syarikat
			</label>
			<select name="vendorID" class="filter-select-terminal" onchange="document.MyForm.submit();">
				<option value="">- Semua -</option>';

for ($i = 0; $i < count($vendorList); $i++) {
	print '		<option value="'.htmlspecialchars($vendorVal[$i]).'"';
	if ($vendorID == $vendorVal[$i]) print ' selected';
	print '>'.htmlspecialchars($vendorList[$i]).'</option>';
}			

print '		</select>
		</div>
		
		<div class="filter-group-terminal">
			<label class="filter-label-terminal">
				<i class="fas fa-desktop"></i> Jenis
			</label>
			<select name="typeID" class="filter-select-terminal" onchange="document.MyForm.submit();">
				<option value="">- Semua -</option>';

$atmList = $atmList ?? array('ATM', 'CDM', 'CICO');
for ($i = 0; $i < count($atmList); $i++) {
	print '		<option value="'.htmlspecialchars($atmList[$i]).'"';
	if ($typeID == $atmList[$i]) print ' selected';
	print '>'.htmlspecialchars($atmList[$i]).'</option>';
}			

print '		</select>
		</div>
		
		<div class="filter-group-terminal">
			<label class="filter-label-terminal" style="opacity: 0;">Action</label>
			<input type="submit" class="btn-terminal btn-primary-terminal" value="🔍 Cari">
		</div>
	</div>
	
	<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-top: 1rem;">
		<div class="action-buttons-terminal">
			<button type="button" class="btn-terminal btn-danger-terminal" onclick="ITRActionButtonClick(\'delete\');">
				<i class="fas fa-trash"></i>
				Hapus Terpilih
			</button>
		</div>
		
		<div style="display: flex; gap: 1rem; align-items: center;">
			<div class="pagination-controls">
				<label>Paparan:</label>
				<select name="pg" class="filter-select-terminal" onchange="doListAll();" style="min-width: 80px;">';

$pageOptions = array(10, 20, 30, 40, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000);
foreach ($pageOptions as $option) {
	$selected = ($pg == $option) ? ' selected' : '';
	print '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
}

print '			</select>
				<span>/ mukasurat</span>
			</div>
			
			<button type="button" class="btn-terminal btn-success-terminal" onclick="window.location.href=\''.htmlspecialchars($sFileRef).'?action=add\';">
				<i class="fas fa-plus"></i>
				Daftar Terminal
			</button>
		</div>
	</div>
</div>

<!-- Table Container -->
<div class="terminal-table-container">
	<table class="terminal-table">
		<thead>
			<tr>
				<th rowspan="2" style="width: 50px;">NO.</th>
				<th rowspan="2" style="width: 80px;">ATM NO</th>
				<th rowspan="2" style="min-width: 200px;">LOKASI</th>
				<th rowspan="2" style="min-width: 130px;">CAWANGAN</th>
				<th rowspan="2" style="width: 70px;">JENIS</th>
				<th colspan="4" style="background: #1e293b;">DENOMINASI</th>
				<th rowspan="2" style="width: 100px;">JUMLAH<br>(RM)</th>
				<th rowspan="2" style="width: 80px;">KOD GL<br>IN/OUT</th>
				<th rowspan="2" style="width: 100px;">CAJ<br>BULANAN<br>(RM)</th>
				<th rowspan="2" style="min-width: 180px;">SYARIKAT</th>
				<th rowspan="2" style="width: 70px;">
					<input type="checkbox" onClick="ITRViewSelectAll(\'A\')" class="checkbox-terminal">
					PILIH
				</th>
			</tr>
			<tr>
				<th style="width: 70px; background: #1e293b;">RM100</th>
				<th style="width: 70px; background: #1e293b;">RM50</th>
				<th style="width: 70px; background: #1e293b;">RM20</th>
				<th style="width: 70px; background: #1e293b;">RM10</th>
			</tr>
		</thead>
		<tbody>';

$grandTotal = 0;
if ($GetList->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	while (!$GetList->EOF && $cnt <= $pg) {
		$atmType = getField($GetList, 'type') ?? '';
		$typeBadgeClass = 'type-badge ';
		if ($atmType == 'ATM') {
			$typeBadgeClass .= 'type-atm';
		} elseif ($atmType == 'CDM') {
			$typeBadgeClass .= 'type-cdm';
		} else {
			$typeBadgeClass .= 'type-cico';
		}
	
		print ' 
			<tr>
				<td style="text-align: right; font-weight: 600; color: #64748b;">'.$bil.'</td>						
				<td style="font-weight: 700;">
					<a href="'.htmlspecialchars($sFileRef).'?action=update&pk='.htmlspecialchars(getField($GetList, 'atmID')).'&strURL='.htmlspecialchars($strURL).'" class="atm-link">
						'.htmlspecialchars(getField($GetList, 'atmNo')).'
					</a>
				</td>
				<td>'.htmlspecialchars(getField($GetList, 'atmLoc') ?? '').'</td>
				<td>'.htmlspecialchars(getField($GetList, 'atmBranch') ?? '').'</td>
				<td style="text-align: center;">
					<span class="'.$typeBadgeClass.'">'.$atmType.'</span>
				</td>
				<td class="deno-cell" style="text-align: center;">'.htmlspecialchars(getField($GetList, 'qty100') ?? '0').'</td>
				<td class="deno-cell" style="text-align: center;">'.htmlspecialchars(getField($GetList, 'qty50') ?? '0').'</td>
				<td class="deno-cell" style="text-align: center;">'.htmlspecialchars(getField($GetList, 'qty20') ?? '0').'</td>
				<td class="deno-cell" style="text-align: center;">'.htmlspecialchars(getField($GetList, 'qty10') ?? '0').'</td>
				<td style="text-align: right; font-weight: 600; color: #059669;">'.number_format(getField($GetList, 'totalValue') ?? 0, 2).'</td>
				<td style="text-align: center; font-size: 0.75rem; line-height: 1.4;">
					<div>'.htmlspecialchars(getField($GetList, 'glCode') ?? '').'</div>
					<div style="color: #64748b;">'.htmlspecialchars(getField($GetList, 'glCode1') ?? '').'</div>
				</td>
				<td style="text-align: right; font-weight: 600;">'.number_format(getField($GetList, 'mthPay') ?? 0, 2).'</td>
				<td style="color: #64748b;">'.htmlspecialchars(getField($GetList, 'vendorName') ?? '').'</td>
				<td style="text-align: center;">
					<input type="checkbox" name="pk[]" value="'.htmlspecialchars(getField($GetList, 'atmID')).'" class="checkbox-terminal">
				</td>
			</tr>';
		$cnt++;
		$bil++;
		$GetList->MoveNext();
	}
	
	print '
		</tbody>
	</table>
	
	<!-- Pagination -->
	<div class="pagination-container-terminal">
		<div class="pagination-info-terminal">
			Jumlah Rekod: <strong>' . $GetList->RowCount() . '</strong>
		</div>';
	
	if ($TotalRec > $pg) {
		$numPage = ($TotalRec % $pg == 0) ? $TotalPage : $TotalPage + 1;
		$passURL = '&pg='.$pg.'&q='.urlencode($q).'&vendorID='.urlencode($vendorID).'&typeID='.urlencode($typeID);
		
		print '
		<div class="pagination-links-terminal">';
		
		for ($i = 1; $i <= min($numPage, 10); $i++) {
			$startRec = (($i * $pg) + 1 - $pg);
			$endRec = ($i * $pg);
			print '<a href="'.htmlspecialchars($sFileName.'?StartRec='.$startRec.$passURL).'" class="page-link-terminal">'.$i.'</a>';
		}
		
		if ($numPage > 10) {
			print '<span class="page-link-terminal" style="border: none; cursor: default;">...</span>';
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
	<div class="terminal-empty-state">';
	
	if ($q == "") {
		print '
		<i class="fas fa-money-bill-wave"></i>
		<h3>Tiada Terminal</h3>
		<p>Belum ada terminal ATM didaftarkan dalam sistem</p>';
	} else {
		print '
		<i class="fas fa-search"></i>
		<h3>Tiada Rekod Dijumpai</h3>
		<p>Carian "'.htmlspecialchars($q).'" tidak menjumpai sebarang terminal</p>';
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
	function ITRViewSelectAll(id) {
		e = document.MyForm.elements;
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
		e = document.MyForm;
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
				alert(\'Sila pilih rekod yang hendak dihapuskan.\');
			} else {
				if(confirm(\'Hapuskan \' + count + \' rekod ?\')) {
					e.action.value = v;
					e.submit();
				}
			}
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&pg=" + c.options[c.selectedIndex].value + "&q='.addslashes($q).'&vendorID='.addslashes($vendorID).'&typeID='.addslashes($typeID).'";
	}
	
	function PrintID() {
		e = document.MyForm;
		if(confirm(\' Cetak senarai ?\')) {
			window.open("rpt_atmList.php?","rpt","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");			
		}
	}	   		
</script>';
?>