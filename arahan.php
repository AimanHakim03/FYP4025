<?php

// Initialize ALL request variables - CRITICAL FIX
$mmyy = $_REQUEST['mmyy'] ?? date("mY");
$vendorID = $_REQUEST['vendorID'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$dt = $_REQUEST['dt'] ?? '';
$dt1 = $_REQUEST['dt1'] ?? '';
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

if ($mmyy == '') {	
	$mm = '';
	$yy = '';
} else {
	$mm = substr($mmyy, 0, 2);
	$yy = substr($mmyy, 2, 4);
}

if ($sessionGroupID == "0" || $sessionGroupID == "1") {
	$vendorID = $sessionBranchID;
}

//--- BEGIN : VENDOR LIST -------------------------------------------------------------------
$vendorList = array();
$vendorVal = array();
$sSQL = "SELECT vendorID, vendorCode, vendorName FROM vendor WHERE isDeleted = '0'";
$GetN = $conn->Execute($sSQL);
if ($GetN && $GetN->RowCount() <> 0) {
	while (!$GetN->EOF) {
		array_push($vendorList, getField($GetN, 'vendorName'));
		array_push($vendorVal, getField($GetN, 'vendorID'));
		$GetN->MoveNext();
	}
}	
//--- END   : VENDOR LIST -------------------------------------------------------------------

//--- BEGIN : IMPORT TEXT FILES -------------------------------------------------------------------------------------------------
if ($action == "import" && $pk != '') {
	$vendorCode = dlookup("vendor", "vendorCode", "vendorID=" . tosql($pk, "Text"));
	$csvName = $vendorCode.'-'.todate('ymd', $dt).'-'.todate('ymd', $dt1);
	
	//--- Begin : Create log 
	$user = $sessionUserName;
	$event = "Import fail (".$csvName.".csv) - telah dijana";
	$type = $sessionGroupName;
	createLog($user, $event, $type);
	
	$genData = '';
	$sSQL = "
	SELECT A.*, B.vendorCode, B.vendorName, C.atmNo, C.atmLoc, C.atmBranch  
	FROM instruct A  
	LEFT JOIN vendor B ON A.vendorID = B.vendorID 	
	LEFT JOIN atm C ON A.atmID = C.atmID
	WHERE A.vendorID = '".$pk."' 
	AND CONVERT(VARCHAR(10),A.createdDate,120) = '".$dt."'
	AND A.instructDate ='".$dt1."' AND A.isDeleted = '0'";
	
	$rs = $conn->Execute($sSQL);
	
	if ($rs && $rs->RowCount() <> 0) {
		$a = 0;
		$genHeader = "BIL,DOC NO,ATM NO,ATM-EBC,ATM BRANCH,RM100,RM50,RM20,RM10,JUMLAH (RM)".PHP_EOL;
		
		while (!$rs->EOF) {
			$a++;
			$docNo = getField($rs, 'docNo') ?? '';
			$atmNo = getField($rs, 'atmNo') ?? '';
			$atmLoc = getField($rs, 'atmLoc') ?? '';
			$atmBranch = getField($rs, 'atmBranch') ?? '';
			$qty100 = getField($rs, 'qty100') ?? 0;
			$qty50 = getField($rs, 'qty50') ?? 0;
			$qty20 = getField($rs, 'qty20') ?? 0;
			$qty10 = getField($rs, 'qty10') ?? 0;
			$totalValue = getField($rs, 'totalValue') ?? 0;
			
			$genData .= $a.','.$docNo.','.$atmNo.','.$atmLoc.','.$atmBranch.','.$qty100.','.
						$qty50.','.$qty20.','.$qty10.','.$totalValue.PHP_EOL;
			$rs->MoveNext();
		}
		
		$path = dirname(__FILE__); 
		$genFile = $path.'\\files\\'.$csvName.".csv";
		$new_file = fopen($genFile, "w");
		fwrite($new_file, $genHeader.$genData);
		fclose($new_file);
		$msg = "Import fail (".$csvName.".csv) - telah dijana";
		print '<script>alert("'.addslashes($msg).'");</script>';
	} 
}
//--- END   : IMPORT TEXT FILES -------------------------------------------------------------------------------------------------

$sSQL = " 
SELECT  
    CONVERT(VARCHAR(10), A.createdDate, 120) AS createdDate, 
    A.instructDate, 
    A.vendorID, 
    C.vendorCode,  
    COUNT(*) AS totalATM, 
    SUM(A.totalValue) AS totalVal, 
    
    -- Replenish total value
    SUM((A.replenish100*100) + (A.replenish50*50) + (A.replenish20*20) + (A.replenish10*10)) AS replenishVal,
    -- Replenish ATM count
    SUM(CASE WHEN (A.replenish100+A.replenish50+A.replenish20+A.replenish10) <> 0 THEN 1 ELSE 0 END) AS replenishQty,

    -- Unload total value
    SUM((A.unload100*100) + (A.unload50*50) + (A.unload20*20) + (A.unload10*10)) AS unloadVal,
    -- Unload ATM count
    SUM(CASE WHEN (A.unload100+A.unload50+A.unload20+A.unload10) <> 0 THEN 1 ELSE 0 END) AS unloadQty,

    SUM(A.status) AS status
FROM instruct A  
LEFT JOIN vendor C ON A.vendorID = C.vendorID
WHERE A.isDeleted = '0'
";

if ($vendorID <> '') {
	$sSQL .= " AND A.vendorID = ".tosql($vendorID, "Text");
}

if ($mm <> '' && $yy <> '') {
	$sSQL .= " AND MONTH(A.instructDate) = '".$mm."'";
	$sSQL .= " AND YEAR(A.instructDate) = '".$yy."'";
}

if ($sessionGroupID == "0" || $sessionGroupID == "1") {
	$sSQL .= " AND A.status <> '0'";	
}

$sSQL .= "
GROUP BY CONVERT(VARCHAR(10),A.createdDate,120), A.instructDate, A.vendorID, C.vendorCode  
ORDER BY A.instructDate DESC
";

$rs = $conn->Execute($sSQL);	

if ($rs === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.htmlspecialchars($sSQL).'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

print '
<style>
/* Modern Arahan Page Styles */
.arahan-page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.arahan-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.arahan-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Filter Section */
.arahan-filter-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.filter-controls {
	display: flex;
	gap: 1.5rem;
	align-items: center;
	flex-wrap: wrap;
}

.filter-item {
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.filter-item label {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
	white-space: nowrap;
}

.filter-select {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
	min-width: 180px;
}

.filter-select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-upload {
	padding: 0.625rem 1.25rem;
	background: #8b5cf6;
	color: #ffffff;
	border: none;
	border-radius: 8px;
	font-size: 0.875rem;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.2s;
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
	margin-left: auto;
}

.btn-upload:hover {
	background: #7c3aed;
	box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
	transform: translateY(-1px);
}

/* Table Container */
.arahan-table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.arahan-table {
	width: 100%;
	border-collapse: collapse;
}

.arahan-table thead th {
	padding: 0.75rem 0.5rem;
	text-align: center;
	font-size: 0.7rem;
	font-weight: 700;
	color: #ffffff;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: normal;
	line-height: 1.3;
}

.arahan-table tbody td {
	padding: 0.75rem 0.5rem;
	font-size: 0.85rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
	border-right: 1px solid #f1f5f9;
	white-space: nowrap;
}

.arahan-table tbody tr:hover {
	background: #f8fafc;
}

/* Color-coded columns */
.col-header-main {
	background: #475569;
}

.col-arahan-dark {
	background: #1e293b;
}

.col-replenish-green {
	background: #10b981;
}

.col-varian-dark {
	background: #1e293b;
}

.col-unload-red {
	background: #ef4444;
}

/* Status Badge */
.status-complete {
	display: inline-flex;
	align-items: center;
	gap: 0.375rem;
	padding: 0.25rem 0.75rem;
	background: #d1fae5;
	color: #065f46;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
}

.status-dot {
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: currentColor;
}

/* Action Icons */
.action-icons {
	display: flex;
	gap: 0.5rem;
	align-items: center;
	justify-content: center;
	flex-wrap: wrap;
	min-width: 130px;
}

.action-icon {
	cursor: pointer;
	transition: transform 0.2s;
	width: 28px;
	height: 28px;
	flex-shrink: 0;
}

.action-icon:hover {
	transform: scale(1.15);
}

.file-link {
	display: block;
	margin-top: 0.25rem;
	font-size: 0.7rem;
	color: #3b82f6;
	text-decoration: none;
	white-space: normal;
	word-break: break-all;
	line-height: 1.2;
}

.file-link:hover {
	text-decoration: underline;
}

/* Date link styling */
.date-link {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 600;
	transition: color 0.2s;
}

.date-link:hover {
	color: #2563eb;
	text-decoration: underline;
}

/* Empty State */
.arahan-empty-state {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.arahan-empty-state i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.arahan-empty-state h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
	font-size: 1.25rem;
}

/* Negative values styling */
.negative-value {
	color: #ef4444;
	font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
	.filter-controls {
		flex-direction: column;
		align-items: stretch;
	}
	
	.filter-item {
		flex-direction: column;
		align-items: stretch;
	}
	
	.filter-select {
		width: 100%;
	}
	
	.btn-upload {
		margin-left: 0;
		width: 100%;
		justify-content: center;
	}
	
	.arahan-table-container {
		overflow-x: auto;
	}
	
	.arahan-table {
		min-width: 1600px;
	}
}
</style>

<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">
<input type="hidden" name="dt">
<input type="hidden" name="dt1">

<!-- Page Header -->
<div class="arahan-page-header">
	<h1 class="arahan-title">
		<i class="fas fa-file-invoice"></i>
		Senarai Arahan Pengisian ATM
	</h1>
	<p class="arahan-subtitle">Pantau dan urus arahan pengisian untuk semua terminal ATM</p>
</div>

<!-- Filter Section -->
<div class="arahan-filter-card">
	<div class="filter-controls">
		<div class="filter-item">
			<label>
				<i class="far fa-calendar"></i> Pada Bulan:
			</label>
			<select name="mmyy" class="filter-select" onchange="document.MyZSSB.submit();">
				<option value="">- Semua -</option>';

if (isset($mmyrList) && isset($mmyrVal)) {
	for ($i = 0; $i < count($mmyrList); $i++) {
		print '		<option value="'.htmlspecialchars($mmyrVal[$i]).'"';
		if ($mmyy == $mmyrVal[$i]) print ' selected';
		print '>'.htmlspecialchars($mmyrList[$i]).'</option>';
	}
}

print '		</select>
		</div>';

if ($sessionGroupID <> '0' && $sessionGroupID <> '1') {	
	print '
		<div class="filter-item">
			<label>
				<i class="fas fa-building"></i> Syarikat:
			</label>
			<select name="vendorID" class="filter-select" onchange="document.MyZSSB.submit();">
				<option value="">- Semua -</option>';
	for ($i = 0; $i < count($vendorList); $i++) {
		print '		<option value="'.htmlspecialchars($vendorVal[$i]).'"';
		if ($vendorID == $vendorVal[$i]) print ' selected';
		print '>'.htmlspecialchars($vendorList[$i]).'</option>';
	}			
	print '		</select>
		</div>';
}

if ($sessionIsUD == "1") {
	print '
		<button type="button" class="btn-upload" onclick="window.location.href=\'uploadatm.php\';">
			<i class="fas fa-cloud-upload-alt"></i>
			Muatnaik Fail
		</button>';
}

print '
	</div>
</div>

<!-- Table Container -->
<div class="arahan-table-container">
	<table class="arahan-table">
		<thead>
			<tr>
				<th rowspan="2" class="col-header-main" style="width: 60px;">BIL</th>
				<th rowspan="2" class="col-header-main" style="width: 110px;">TARIKH<br>KELUAR<br>ARAHAN</th>
				<th rowspan="2" class="col-header-main" style="width: 110px;">TARIKH<br>PENGISIAN</th>
				<th rowspan="2" class="col-header-main" style="width: 100px;">SYARIKAT</th>
				<th colspan="2" class="col-arahan-dark">ARAHAN</th>
				<th colspan="2" class="col-replenish-green">REPLENISH</th>
				<th colspan="2" class="col-varian-dark">VARIAN</th>
				<th colspan="2" class="col-unload-red">UNLOADED</th>
				<th rowspan="2" class="col-header-main" style="width: 100px;">STATUS</th>
				<th rowspan="2" class="col-header-main" style="width: 150px;">TINDAKAN</th>
			</tr>
			<tr>
				<th class="col-arahan-dark" style="width: 90px;">JUMLAH<br>TERMINAL</th>
				<th class="col-arahan-dark" style="width: 110px;">AMAUN<br>(RM)</th>
				<th class="col-replenish-green" style="width: 90px;">JUMLAH<br>TERMINAL</th>
				<th class="col-replenish-green" style="width: 110px;">AMAUN<br>(RM)</th>
				<th class="col-varian-dark" style="width: 90px;">JUMLAH<br>TERMINAL</th>
				<th class="col-varian-dark" style="width: 110px;">AMAUN<br>(RM)</th>
				<th class="col-unload-red" style="width: 90px;">JUMLAH<br>TERMINAL</th>
				<th class="col-unload-red" style="width: 110px;">AMAUN<br>(RM)</th>
			</tr>
		</thead>
		<tbody>';

$sd = 0;
$cnt = 0;

if ($rs->RowCount() <> 0) {  
	while (!$rs->EOF) {
		$sd++;

		// Get all fields using getField
		$createdDate = getField($rs, 'createdDate') ?? '';
		$instructDate = getField($rs, 'instructDate') ?? '';
		$rsVendorID = getField($rs, 'vendorID') ?? '';
		$vendorCode = getField($rs, 'vendorCode') ?? '';
		$totalATM = getField($rs, 'totalATM') ?? 0;
		$totalVal = getField($rs, 'totalVal') ?? 0;
		$replenishQty = getField($rs, 'replenishQty') ?? 0;
		$replenishVal = getField($rs, 'replenishVal') ?? 0;
		$unloadQty = getField($rs, 'unloadQty') ?? 0;
		$unloadVal = getField($rs, 'unloadVal') ?? 0;
		$statusVal = getField($rs, 'status') ?? 0;

		$status = '&nbsp;';
		if ($totalATM > 0 && ($statusVal / $totalATM) == 4) {
			$status = '<span class="status-complete"><span class="status-dot"></span>SELESAI</span>';
		}
		
		$varianQty = $replenishQty - $totalATM;
		$varianVal = $replenishVal - $totalVal;
		
		$varianQtyDisplay = $varianQty;
		$varianValDisplay = number_format($varianVal, 2);
		
		if ($varianQty < 0) {
			$varianQtyDisplay = '<span class="negative-value">'.$varianQty.'</span>';
		}
		if ($varianVal < 0) {
			$varianValDisplay = '<span class="negative-value">'.number_format($varianVal, 2).'</span>';
		}
		
		$csvName = $vendorCode.'-'.todate('ymd', $createdDate).'-'.todate('ymd', $instructDate);

		print '
			<tr>
				<td style="text-align: right; font-weight: 600; color: #64748b;">'.$sd.'</td>
				<td style="text-align: center;">'.todate('d/m/Y', $createdDate).'</td>			
				<td style="text-align: center;">
					<a href="deno.php?dt='.htmlspecialchars($instructDate).'&vid='.htmlspecialchars($rsVendorID).'" class="date-link">
						'.todate('d/m/Y', $instructDate).'
					</a>
				</td>			
				<td style="font-weight: 600;">'.htmlspecialchars($vendorCode).'</td>
				<td style="text-align: center; background: #f8fafc; font-weight: 600;">'.htmlspecialchars($totalATM).'</td>		
				<td style="text-align: right; background: #f8fafc; font-weight: 600;">'.number_format($totalVal, 2).'</td>	
				<td style="text-align: center; background: #ecfdf5; font-weight: 600;">'.htmlspecialchars($replenishQty).'</td>		
				<td style="text-align: right; background: #ecfdf5; font-weight: 600;">'.number_format($replenishVal, 2).'</td>	
				<td style="text-align: center; background: #f8fafc;">'.$varianQtyDisplay.'</td>		
				<td style="text-align: right; background: #f8fafc;">'.$varianValDisplay.'</td>	
				<td style="text-align: center; background: #fef2f2; font-weight: 600;">'.htmlspecialchars($unloadQty).'</td>		
				<td style="text-align: right; background: #fef2f2; font-weight: 600;">'.number_format($unloadVal, 2).'</td>	
				<td style="text-align: center;">'.$status.'</td>
				<td style="text-align: center;">
					<div class="action-icons">
						<img src="images/pdf.png" class="action-icon" title="Cetak Surat Arahan (PDF)" 
						onclick="PrintID(\''.addslashes($rsVendorID).'\',\''.addslashes($createdDate).'\',\''.addslashes($instructDate).'\');">
						
						<img src="images/excel.png" class="action-icon" title="Cetak Surat Arahan (XLS)" 
						onclick="ExcelID(\''.addslashes($rsVendorID).'\',\''.addslashes($createdDate).'\',\''.addslashes($instructDate).'\');">';
		
		if ($sessionGroupID == "9" || $sessionGroupID == "3" || $sessionIsUD == "1") {
			print '
						<img src="images/csv.png" class="action-icon" title="Import Arahan Fail CSV" 
						onclick="ImportCSV(\''.addslashes($rsVendorID).'\',\''.addslashes($createdDate).'\',\''.addslashes($instructDate).'\');">';
			
			$path = dirname(__FILE__); 
			$filename = $path.'\\files\\'.$csvName.".csv";
			if (file_exists($filename)) {
				print '
					</div>
					<a href="files/'.htmlspecialchars($csvName).'.csv" target="new" class="file-link">
						<i class="fas fa-file-csv"></i> '.htmlspecialchars($csvName).'.csv
					</a>';
			} else {
				print '</div>';
			}
		} else {
			print '</div>';
		}
		
		print '
				</td>
			</tr>';	
		$cnt++;
		$rs->MoveNext();
	}
} else {
	print '
			<tr>
				<td colspan="14" style="padding: 0;">
					<div class="arahan-empty-state">
						<i class="fas fa-inbox"></i>
						<h3>Tiada Arahan Dijumpai</h3>
						<p>Tiada rekod maklumat arahan untuk kriteria yang dipilih</p>
					</div>
				</td>
			</tr>';
}

print'
		</tbody>
	</table>
</div>

</form>';

include("footer.php");	

print '
<script>
	var allChecked = false;
	
	function ITRViewSelectAll(id) {
		e = document.MyZSSB.elements;
		allChecked = !allChecked;
		for(c = 0; c < e.length; c++) {
			if (id == "A") {
				if(e[c].type == "checkbox" && e[c].name == "pkA[]") {
					e[c].checked = allChecked;
				}
			}
		}
	}
	
	function InstructAct(v) {
		e = document.MyZSSB;
		if (e == null) {
			alert(\'Please confirm the form name.!\');
		} else {
			count = 0;
			for(c = 0; c < e.elements.length; c++) {
				if(e.elements[c].name == "pkA[]" && e.elements[c].checked) {
					count++;
				}
			}
		}
		if (count == 0) {
			alert(\'Sila pilih rekod (kolum kuning) untuk Keluar Arahan.\');
		} else {
			if(confirm(\'Keluar Arahan untuk : \' + count + \' rekod?\')) {
				e.action.value = v;
				e.submit();
				e.action1.disabled = true;		
				e.action1.value = \'Sila tunggu.....\';	
			}
		}
	}
	
	function PrintID(id, dt, dt1) {
		if(confirm(\' Cetak Surat Arahan Pengisian untuk \' + dt1 + \' ?\')) {
			window.open("rpt_pdf/rpt_Instruct.php?pk=" + id + "&dt=" + dt + "&dt1=" + dt1, "sort", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}
	
	function ExcelID(id, dt, dt1) {
		if(confirm(\' Cetak Surat Arahan Pengisian untuk \' + dt1 + \' ?\')) {
			window.open("rpt_pdf/rpt_InstructE.php?pk=" + id + "&dt=" + dt + "&dt1=" + dt1, "sort", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}
	
	function ImportCSV(id, dt, dt1) {
		e = document.MyZSSB;
		if(confirm(\' Import Fail CSV : \' + dt1 + \' ?\')) {
			e.pk.value = id;
			e.dt.value = dt;
			e.dt1.value = dt1;
			e.action.value = "import";
			e.submit();
		}
	}
</script>';
?>