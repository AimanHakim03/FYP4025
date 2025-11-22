<?php

include ("header.php");
include("forms.php");

// Get form data - PROPERLY CAPTURE POST VARIABLES
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$atmNo = isset($_POST['atmNo']) ? $_POST['atmNo'] : (isset($_GET['atmNo']) ? $_GET['atmNo'] : '');

// Capture month/year from POST or use defaults
$mm = isset($_POST['mm']) ? $_POST['mm'] : (isset($_GET['mm']) ? $_GET['mm'] : date("m"));
$yy = isset($_POST['yy']) ? $_POST['yy'] : (isset($_GET['yy']) ? $_GET['yy'] : date("Y"));
$mmT = isset($_POST['mmT']) ? $_POST['mmT'] : (isset($_GET['mmT']) ? $_GET['mmT'] : date("m"));
$yyT = isset($_POST['yyT']) ? $_POST['yyT'] : (isset($_GET['yyT']) ? $_GET['yyT'] : date("Y"));

// Initialize variables
$atmID = '';
$atmFilter = '';
$atmDisplayText = 'Semua Terminal';

// Format year-month for SQL query
$yymm = sprintf("%04d%02d", $yy, $mm);
$yymmT = sprintf("%04d%02d", $yyT, $mmT);

// Get atmID if atmNo is provided (OPTIONAL FILTER)
if ($atmNo <> '') {
	// Pad with leading zeros to match database format (4 digits)
	$atmNo = str_pad(trim($atmNo), 4, '0', STR_PAD_LEFT);
	$atmID = dlookup("atm", "atmID", "isDeleted='0' AND atmNo=" . tosql($atmNo, "Text"));
	
	// Only add filter if we found a valid atmID
	if ($atmID <> '') {
		$atmFilter = " AND atmID = '".$atmID."' ";
		$atmDisplayText = 'Terminal '.$atmNo;
	} else {
		// atmNo was entered but not found in database
		$atmDisplayText = 'Terminal '.$atmNo.' (Tidak Dijumpai)';
	}
}

// Always run query based on date range (atmNo is optional)
$sSQL = " 
SELECT	Z.atmID, Y.atmNo, Y.atmLoc, Y.type, Z.vendorID, X.vendorCode, X.vendorName, Z.instructDate, COALESCE(SUM(Z.totalValue),'0') AS totalValue, 
		Z.replenishDate, COALESCE(SUM(Z.replenishAmt),'0') AS replenishAmt, 
		Z.unloadDate, COALESCE(SUM(Z.unloadAmt),'0') AS unloadAmt, 
		Z.collectDate, COALESCE(SUM(Z.collectAmt),'0') AS collectAmt , Z.status
FROM 
(		SELECT	atmID, vendorID, instructDate, totalValue, replenishDate, ((replenish100*100)+(replenish50*50)+(replenish20*20)+(replenish10*10)) AS replenishAmt, 
				unloadDate, ((unload100*100)+(unload50*50)+(unload20*20)+(unload10*10)) AS unloadAmt, NULL AS collectDate, '0' AS collectAmt, status 
		FROM 	instruct   
		WHERE 	isDeleted ='0' ".$atmFilter."
		AND 	CONVERT(VARCHAR(6), instructDate, 112) BETWEEN '".$yymm."' AND '".$yymmT."'
		UNION ALL 
		SELECT	atmID, vendorID, NULL AS instructDate, '0' AS totalValue, NULL AS replenishDate, '0' AS replenishAmt, NULL AS unloadDate, '0' AS unloadAmt, 
				collectDate, totalValue AS collectAmt, status 
		FROM	collect 
		WHERE 	isDeleted ='0' ".$atmFilter."
		AND 	CONVERT(VARCHAR(6), collectDate, 112) BETWEEN '".$yymm."' AND '".$yymmT."'
) Z 
LEFT JOIN atm Y 
ON		Z.atmID = Y.atmID 
LEFT JOIN vendor X 
ON		Z.vendorID = X.vendorID 
GROUP BY Z.atmID, Y.atmNo, Y.atmLoc, Y.type, Z.vendorID, X.vendorCode, X.vendorName, Z.instructDate, Z.replenishDate, Z.unloadDate, Z.collectDate, Z.status 
ORDER BY Y.atmNo, COALESCE(Z.instructDate,Z.collectDate)";

$rs = $conn->Execute($sSQL);	
if ($rs === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.$sSQL.'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

print '
<style>
/* Debug info box */
.debug-info {
	background: #fef3c7;
	border: 2px solid #fbbf24;
	border-radius: 8px;
	padding: 1rem;
	margin-bottom: 1.5rem;
	font-size: 0.875rem;
	font-family: monospace;
}

.debug-info strong {
	color: #92400e;
}

/* Page Styling */
.track-page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.track-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.track-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Filter Card */
.filter-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.filter-row {
	display: flex;
	gap: 1rem;
	align-items: end;
	flex-wrap: wrap;
}

.filter-group {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
	flex: 1;
	min-width: 150px;
}

.filter-label {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.filter-input {
	padding: 0.75rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
	background: #ffffff;
}

.filter-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.date-range-group {
	display: flex;
	align-items: center;
	gap: 1rem;
}

.date-picker-wrapper {
	display: flex;
	gap: 0.5rem;
	align-items: center;
}

.btn-track {
	padding: 0.75rem 1.5rem;
	border: none;
	border-radius: 8px;
	font-size: 0.875rem;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.2s;
	background: #3b82f6;
	color: #ffffff;
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
}

.btn-track:hover {
	background: #2563eb;
	box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	transform: translateY(-1px);
}

/* Table Styling */
.track-table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.track-table {
	width: 100%;
	border-collapse: collapse;
}

.track-table thead th {
	padding: 1rem;
	text-align: center;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
}

.track-table tbody td {
	padding: 1rem;
	font-size: 0.875rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
	border-right: 1px solid #f1f5f9;
}

.track-table tbody tr:hover {
	background: #f8fafc;
}

/* Color-coded column headers */
.col-arahan {
	background: #1e293b;
}

.col-replenish {
	background: #10b981;
}

.col-varian {
	background: #1e293b;
}

.col-unload {
	background: #ef4444;
}

.col-collection {
	background: #f59e0b;
}

/* Status badges */
.status-badge-track {
	display: inline-block;
	padding: 0.375rem 0.875rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
	text-align: center;
}

.status-complete {
	background: #d1fae5;
	color: #065f46;
}

.status-pending {
	background: #fef3c7;
	color: #92400e;
}

.status-process {
	background: #dbeafe;
	color: #1e40af;
}

/* Summary Row */
.summary-row {
	background: #f8fafc !important;
	font-weight: 600;
}

.summary-row td {
	border-top: 2px solid #e2e8f0 !important;
	color: #0f172a !important;
	font-size: 0.9rem !important;
}

/* Empty State */
.empty-state-track {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.empty-state-track i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.empty-state-track h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
	font-size: 1.25rem;
}

.empty-state-track p {
	color: #64748b;
	font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
	.filter-row {
		flex-direction: column;
	}
	
	.date-range-group {
		flex-direction: column;
		align-items: stretch;
	}
	
	.track-table-container {
		overflow-x: auto;
	}
	
	.track-table {
		min-width: 1200px;
	}
}

/* Print button styling */
.btn-print {
	padding: 0.5rem 1rem;
	background: #6366f1;
	color: white;
	border: none;
	border-radius: 6px;
	cursor: pointer;
	font-size: 0.8rem;
	transition: all 0.2s;
}

.btn-print:hover {
	background: #4f46e5;
	box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}
</style>

<form name="MyZSSB" action="" method="post">
<input type="hidden" name="action">
<input type="hidden" name="atmID">

<!-- Page Header -->
<div class="track-page-header">
	<h1 class="track-title">
		<i class="fas fa-chart-line"></i>
		Pantau Terminal
	</h1>
	<p class="track-subtitle">Pantau status dan aktiviti terminal ATM</p>
</div>

<!-- Filter Card -->
<div class="filter-card">
	<div class="filter-row">
		<div class="filter-group" style="max-width: 200px;">
			<label class="filter-label">
				<i class="fas fa-money-bill-wave"></i> No. Terminal <span style="color: #94a3b8; font-weight: 400;">(Pilihan)</span>
			</label>
			<input type="text" name="atmNo" value="'.htmlspecialchars($atmNo).'" class="filter-input" placeholder="Kosongkan untuk semua" maxlength="5">
		</div>
		
		<div class="date-range-group" style="flex: 2;">
			<div class="filter-group">
				<label class="filter-label">
					<i class="far fa-calendar"></i> Dari
				</label>
				<div class="date-picker-wrapper">
					<select name="mm" class="filter-input" style="width: 140px;">';
for ($m = 0; $m < count($monthList); $m++) {
	print '				<option value="'.$monthVal[$m].'"';
	if ($mm == $monthVal[$m]) print ' selected';
	print 				'>'.strtoupper($monthList[$m]).'</option>';
}
print '				</select>
					<select name="yy" class="filter-input" style="width: 100px;">';
for ($y = 0; $y < count($yrList); $y++) {
	print '				<option value="'.$yrList[$y].'"';
	if ($yy == $yrList[$y]) print ' selected';
	print 				'>'.$yrList[$y].'</option>';
}
print '				</select>
				</div>
			</div>
			
			<div style="padding-top: 1.5rem; color: #94a3b8; font-weight: 600;">
				<i class="fas fa-arrow-right"></i>
			</div>
			
			<div class="filter-group">
				<label class="filter-label">
					<i class="far fa-calendar"></i> Hingga
				</label>
				<div class="date-picker-wrapper">
					<select name="mmT" class="filter-input" style="width: 140px;">';
for ($m = 0; $m < count($monthList); $m++) {
	print '				<option value="'.$monthVal[$m].'"';
	if ($mmT == $monthVal[$m]) print ' selected';
	print 				'>'.strtoupper($monthList[$m]).'</option>';
}
print '				</select>
					<select name="yyT" class="filter-input" style="width: 100px;">';
for ($y = 0; $y < count($yrList); $y++) {
	print '				<option value="'.$yrList[$y].'"';
	if ($yyT == $yrList[$y]) print ' selected';
	print 				'>'.$yrList[$y].'</option>';
}
print '				</select>
				</div>
			</div>
		</div>
		
		<div class="filter-group" style="max-width: 150px;">
			<label class="filter-label" style="opacity: 0;">Action</label>
			<input type="submit" name="submitBtn" value="🔍 Capai Data" class="btn-track" style="cursor: pointer; width: 100%;">
			<input type="hidden" name="action" value="search">
		</div>
	</div>
</div>

<!-- Table Container -->
<div class="track-table-container">
	<table class="track-table">
		<thead>
			<tr>
				<th rowspan="2" style="background: #475569; width: 50px;">BIL</th>
				<th rowspan="2" style="background: #475569; min-width: 200px;">TERMINAL</th>
				<th rowspan="2" style="background: #475569; width: 80px;">JENIS</th>
				<th rowspan="2" style="background: #475569; min-width: 120px;">SYARIKAT</th>
				<th colspan="2" class="col-arahan">ARAHAN</th>
				<th colspan="2" class="col-replenish">REPLENISH</th>
				<th rowspan="2" class="col-varian">VARIAN<br>(RM)</th>
				<th colspan="2" class="col-unload">UNLOADED</th>
				<th colspan="2" class="col-collection">COLLECTION</th>
				<th rowspan="2" style="background: #475569; min-width: 150px;">STATUS</th>
			</tr>
			<tr>
				<th class="col-arahan" style="width: 100px;">TARIKH</th>
				<th class="col-arahan" style="width: 120px;">AMAUN (RM)</th>
				<th class="col-replenish" style="width: 100px;">TARIKH</th>
				<th class="col-replenish" style="width: 120px;">AMAUN (RM)</th>
				<th class="col-unload" style="width: 100px;">TARIKH</th>
				<th class="col-unload" style="width: 120px;">AMAUN (RM)</th>
				<th class="col-collection" style="width: 100px;">TARIKH</th>
				<th class="col-collection" style="width: 120px;">AMAUN (RM)</th>
			</tr>
		</thead>
		<tbody>';

$sd=0;$cnt=0; $diffAmt='0';
$totalA='0';$totalB='0';$totalC='0';$totalD='0';$totalE='0';

if (is_object($rs) && $rs->RowCount() <> 0) {  
	while (!$rs->EOF) {
		$sd++;
		$totalA += $rs->fields('totalValue');
		$totalB += $rs->fields('replenishAmt');
		$diffAmt='0.00';
		if ($rs->fields('replenishAmt') <> '0.00') {
			$diffAmt = $rs->fields('totalValue') - $rs->fields('replenishAmt');
		}
		$totalC += $diffAmt;
		$totalD += $rs->fields('unloadAmt');
		$totalE += $rs->fields('collectAmt');
		
		// Determine status badge
		$statusIndex = array_search($rs->fields('status'), $statusVal);
		$statusText = ($statusIndex !== false) ? $statusList[$statusIndex] : 'Unknown';
		$statusClass = 'status-badge-track ';
		
		if (stripos($statusText, 'SELESAI') !== false || stripos($statusText, 'COMPLETE') !== false) {
			$statusClass .= 'status-complete';
		} elseif (stripos($statusText, 'PROSES') !== false || stripos($statusText, 'PROCESS') !== false) {
			$statusClass .= 'status-process';
		} else {
			$statusClass .= 'status-pending';
		}
		
		print '
			<tr>
				<td style="text-align: right; font-weight: 600; color: #64748b;">'.$sd.'</td>
				<td style="font-weight: 600;">'.htmlspecialchars($rs->fields('atmNo')).' - '.htmlspecialchars($rs->fields('atmLoc')).'</td>
				<td style="text-align: center;">'.htmlspecialchars($rs->fields('type')).'</td>
				<td>'.htmlspecialchars($rs->fields('vendorCode')).'</td>
				<td style="text-align: center; background: #f8fafc;">'.todate('d/m/Y',$rs->fields('instructDate')).'</td>			
				<td style="text-align: right; background: #f8fafc; font-weight: 600;">'.number_format($rs->fields('totalValue'),2).'</td>	
				<td style="text-align: center; background: #ecfdf5;">'.todate('d/m/Y',$rs->fields('replenishDate')).'</td>			
				<td style="text-align: right; background: #ecfdf5; font-weight: 600;">'.number_format($rs->fields('replenishAmt'),2).'</td>	
				<td style="text-align: right; background: #f8fafc; font-weight: 600; color: '.($diffAmt < 0 ? '#ef4444' : '#0f172a').';">'.number_format($diffAmt,2).'</td>	
				<td style="text-align: center; background: #fef2f2;">'.todate('d/m/Y',$rs->fields('unloadDate')).'</td>			
				<td style="text-align: right; background: #fef2f2; font-weight: 600;">'.number_format($rs->fields('unloadAmt'),2).'</td>	
				<td style="text-align: center; background: #fffbeb;">'.todate('d/m/Y',$rs->fields('collectDate')).'</td>			
				<td style="text-align: right; background: #fffbeb; font-weight: 600;">'.number_format($rs->fields('collectAmt'),2).'</td>	
				<td style="text-align: center;"><span class="'.$statusClass.'">'.$statusText.'</span></td>		
			</tr>';	
		$cnt++;
		$rs->MoveNext();
	}
	
	if ($totalA <> '0') {
		print '
			<tr class="summary-row">
				<td colspan="4" style="text-align: right; font-weight: 700;">
					<i class="fas fa-calculator"></i> JUMLAH BULANAN
				</td>
				<td colspan="2" style="text-align: right; font-weight: 700; background: #f8fafc;">'.number_format($totalA,2).'</td>	
				<td colspan="2" style="text-align: right; font-weight: 700; background: #ecfdf5;">'.number_format($totalB,2).'</td>	
				<td style="text-align: right; font-weight: 700; background: #f8fafc; color: '.($totalC < 0 ? '#ef4444' : '#0f172a').';">'.number_format($totalC,2).'</td>	
				<td colspan="2" style="text-align: right; font-weight: 700; background: #fef2f2;">'.number_format($totalD,2).'</td>	
				<td colspan="2" style="text-align: right; font-weight: 700; background: #fffbeb;">'.number_format($totalE,2).'</td>	
				<td></td>	
			</tr>';
	}
} else {
	print '
			<tr>
				<td colspan="14" style="padding: 0;">
					<div class="empty-state-track">
						<i class="fas fa-inbox"></i>
						<h3>Tiada Rekod Dijumpai</h3>
						<p>Tiada maklumat untuk '.$atmDisplayText.' dalam tempoh '.$mm.'/'.$yy.' hingga '.$mmT.'/'.$yyT.'</p>
					</div>
				</td>
			</tr>';
}

print '
		</tbody>
	</table>
</div>

</form>';

include("footer.php");	

print '
<script>
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
	
	function InstructAct(v) {
	    e = document.MyZSSB;
	    if (e==null) {
	       	alert(\'Please confirm the form name.!\');
	    } else {
	       	count=0;
	     	for(c=0; c<e.elements.length; c++) {
	        	if(e.elements[c].name=="pkA[]" && e.elements[c].checked) {
	           		count++;
	         	}
	       	}
		}
	    if (count==0) {
	    	alert(\'Sila pilih rekod (kolum kuning) untuk Keluar Arahan.\');
	    } else {
			if(confirm(\'Keluar Arahan untuk : \' + count + \' rekod?\')) {
				e.action.value=v;
				e.submit();
				e.action1.disabled=true;		
				e.action1.value = \'Sila tunggu.....\';	
			}
		}
	}
	
	function PrintID(id,dt) {
		if(confirm(\' Cetak Surat Arahan untuk \'+dt+\' ?\')) {
			window.open("rpt_pdf/rpt_Instruct.php?pk="+id+"&dt="+dt,"sort","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
		}
	}
</script>';
?>