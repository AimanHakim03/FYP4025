<?php

// Initialize ALL request variables - CRITICAL FIX
$isZerorise = $_REQUEST['isZerorise'] ?? 0;
$vid = $_REQUEST['vid'] ?? '';
$dd = $_REQUEST['dd'] ?? date("d");
$mm = $_REQUEST['mm'] ?? date("m");
$yy = $_REQUEST['yy'] ?? date("Y");
$ty = $_REQUEST['ty'] ?? '';
$vendorID = $_REQUEST['vendorID'] ?? '';
$action = $_REQUEST['action'] ?? '';
$terminalATM = $_REQUEST['terminalATM'] ?? '';
$PHP_SELF = $_SERVER['PHP_SELF'] ?? '';

include("header.php");
include("forms.php");

// Cache session variables
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';

// Error message variable
$err1 = '';

if ($sessionGroupID == "0" && $sessionGroupID == "1") {
	print '<script>alert("'.addslashes($errPage ?? 'Unauthorized').'");window.location.href="index.php";</script>';
	exit;
}

$dmy = $dd.'/'.$mm.'/'.$yy;
if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.htmlspecialchars($dmy).'");</script>';
	$dd = 1;
}
$ymd = $yy.'-'.$mm.'-'.$dd;

$disabled = ' value="Tambah Terminal" ';
if ($yy.$mm.$dd < date('Ymd')) {
	$disabled = ' value="Arahan Ditutup" disabled ';
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

//--- BEGIN : INSTRUCT TERMINAL -------------------------------------------------------------------
if ($action == "arahan" && $terminalATM != '') {

	$existAtmID = dlookup("atm", "atmID", " isDeleted='0' AND atmNo='$terminalATM' ");
	
	if ($existAtmID == '') {
		$err1 = '<font class="redText">* No Terminal tidak wujud</font>';
	} else {	
		$type = dlookup("atm", "type", " isDeleted='0' AND atmNo='$terminalATM' ");
		
		if ($type == 'CDM') {
			$err1 = '<font class="redText">* Terminal jenis CDM</font>';
		} else {
			$seq = dlookup("instruct", "seq", " isDeleted='0' AND atmID='$existAtmID' AND instructDate = '$ymd' ");	

			if ($seq == '') {
				$seq = 1;			
			} else {
				$seq += 1;
			}
			$seq = sprintf("%02d", $seq);
			
			$sSQL = "	
				SELECT A.*, B.vendorCode 
				FROM atm A LEFT JOIN vendor B ON A.vendorID = B.vendorID
				WHERE A.isDeleted = '0' AND A.atmNo = " . tosql($terminalATM, "Text");
			$rs = $conn->Execute($sSQL);

			if ($rs && $rs->RowCount() <> 0) {	
				$atmNo = getField($rs, 'atmNo') ?? '';
				$atmID = getField($rs, 'atmID') ?? '';
				$vID = getField($rs, 'vendorID') ?? '';
				$vendorCode = getField($rs, 'vendorCode') ?? '';

				if ($isZerorise != '0') {
					$qty100 = 0;
					$qty50 = 0;
					$qty20 = 0;
					$qty10 = 0;
					$value100 = 0;
					$value50 = 0;
					$value20 = 0;
					$value10 = 0;
					$totalValue = 0;
					$remarks = 'zerorise';
				} else {
					$qty100 = getField($rs, 'qty100') ?? 0;
					$qty50 = getField($rs, 'qty50') ?? 0;
					$qty20 = getField($rs, 'qty20') ?? 0;
					$qty10 = getField($rs, 'qty10') ?? 0;
					$value100 = getField($rs, 'value100') ?? 0;
					$value50 = getField($rs, 'value50') ?? 0;
					$value20 = getField($rs, 'value20') ?? 0;
					$value10 = getField($rs, 'value10') ?? 0;
					$totalValue = getField($rs, 'totalValue') ?? 0;
					$remarks = '';
				}				
				
				$docNo = 'A'.$atmNo.$yy.$mm.$dd.$seq.$vendorCode;
				$user = $sessionUserName;
				$type = $sessionGroupName;
				$event = 'Arahan '.$docNo.' (RM'.number_format($totalValue, 2).') :';

				$sSQL = " 	
				INSERT INTO instruct 
				(instructDate, atmID, vendorID, qty100, value100, qty50, value50, qty20, value20, qty10, value10, totalValue, 
				docNo, seq, remarks, createdDate, createdBy) VALUES 
				(".tosql($ymd, "Text").",".tosql($atmID, "Text").",".tosql($vID, "Text").",".tosql($qty100, "Number").",".tosql($value100, "Number").",
				".tosql($qty50, "Number").",".tosql($value50, "Number").",".tosql($qty20, "Number").",".tosql($value20, "Number").",
				".tosql($qty10, "Number").",".tosql($value10, "Number").",".tosql($totalValue, "Number").",
				".tosql($docNo, "Text").",".tosql($seq, "Text").",".tosql($remarks, "Text").",".tosql(date("Y-m-d H:i:s"), "Text").",".tosql($sessionUserName, "Text").")";  

				$rsI = $conn->Execute($sSQL);
				if ($rsI === false) {
					$event .= ' Ralat';
				} else {
					$event .= ' Daftar';
					$err1 = '';
					$terminalATM = '';
				}
				createLog($user, $event, $type);
			} 
		} 
	}
}
//--- END   : INSTRUCT TERMINAL -------------------------------------------------------------------

$sSQL = " 
SELECT  
    A.atmID, 
    B.docNo,
    A.atmNo, 
    A.atmLoc, 
    A.type, 
    C.vendorCode, 
    C.vendorName, 
    B.status,   
    COALESCE(B.qty100, A.qty100) AS qty100A, 
    COALESCE(B.qty50, A.qty50) AS qty50A, 
    COALESCE(B.qty20, A.qty20) AS qty20A, 
    COALESCE(B.qty10, A.qty10) AS qty10A, 
    COALESCE(B.totalValue, A.totalValue) AS totalValueA 
FROM instruct B 
LEFT JOIN atm A ON B.atmID = A.atmID  
LEFT JOIN vendor C ON B.vendorID = C.vendorID
WHERE B.isDeleted = '0' 
  AND B.instructDate = '".$ymd."'";

if ($vendorID <> '') {
	$sSQL .= " AND A.vendorID = ".tosql($vendorID, "Text");
}	

$sSQL .= " ORDER BY A.atmNo";

$rsD = $conn->Execute($sSQL);
	
if ($rsD === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.htmlspecialchars($sSQL).'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

print '
<style>
.arahan-add-container {
	max-width: 98%;
	margin: 0 auto;
}

.arahan-add-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.arahan-add-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.arahan-add-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Add Terminal Card */
.add-terminal-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.add-terminal-form {
	display: flex;
	gap: 1rem;
	align-items: end;
	flex-wrap: wrap;
}

.form-group-add {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.form-label-add {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.form-input-add {
	padding: 0.75rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
	font-weight: 700;
}

.form-input-add:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.terminal-input {
	width: 150px;
	font-family: monospace;
	font-size: 1rem;
}

.checkbox-group-add {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	padding-top: 1.8rem;
}

.checkbox-add {
	width: 18px;
	height: 18px;
	cursor: pointer;
	accent-color: #10b981;
}

.checkbox-label-add {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
	cursor: pointer;
}

.btn-add-terminal {
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
	margin-top: 1.8rem;
	background: #10b981;
	color: #ffffff;
}

.btn-add-terminal:hover {
	background: #059669;
	box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-add-terminal:disabled {
	background: #94a3b8;
	cursor: not-allowed;
	box-shadow: none;
}

.error-message-add {
	color: #dc2626;
	font-size: 0.875rem;
	font-weight: 600;
	padding-top: 1.8rem;
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

/* List Section */
.list-section-header {
	background: #ffffff;
	padding: 1.25rem 1.5rem;
	border-radius: 12px 12px 0 0;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 1rem;
}

.list-title-add {
	font-size: 1rem;
	font-weight: 700;
	color: #0f172a;
}

.vendor-filter-add {
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.vendor-filter-add label {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.vendor-filter-add select {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
	font-weight: 700;
	min-width: 200px;
}

.vendor-filter-add select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table */
.arahan-add-table-container {
	background: #ffffff;
	border-radius: 0 0 12px 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.arahan-add-table {
	width: 100%;
	border-collapse: collapse;
}

.arahan-add-table thead th {
	padding: 0.875rem 0.75rem;
	text-align: center;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	background: #475569;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: nowrap;
}

.arahan-add-table tbody td {
	padding: 0.875rem 0.75rem;
	font-size: 0.875rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
}

.arahan-add-table tbody tr.Data {
	background: #ffffff;
}

.arahan-add-table tbody tr.Data1 {
	background: #f8fafc;
}

.arahan-add-table tbody tr:hover {
	background: #eff6ff !important;
}

.arahan-add-table tbody tr.LabelH {
	background: #fef3c7;
	font-weight: 700;
	color: #78350f;
	border-top: 2px solid #f59e0b;
}

.status-badge-add {
	display: inline-block;
	padding: 0.25rem 0.75rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
	background: #d1fae5;
	color: #065f46;
}

.empty-message-add {
	padding: 3rem 2rem;
	text-align: center;
	color: #64748b;
}

.empty-message-add i {
	font-size: 2.5rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

@media (max-width: 1200px) {
	.add-terminal-form {
		flex-direction: column;
		align-items: stretch;
	}
	
	.terminal-input {
		width: 100%;
	}
	
	.btn-add-terminal {
		width: 100%;
		justify-content: center;
		margin-top: 0;
	}
	
	.arahan-add-table {
		font-size: 0.75rem;
	}
}
</style>

<div class="arahan-add-container">
	<!-- Page Header -->
	<div class="arahan-add-header">
		<h1 class="arahan-add-title">
			<i class="fas fa-plus-circle"></i>
			Tambah Arahan Pengisian ATM
		</h1>
		<p class="arahan-add-subtitle">Tambah terminal ATM secara manual dengan memasukkan nombor terminal</p>
	</div>

	<form name="MyZSSB" action="'.$PHP_SELF.'" method="post">
	<input type="hidden" name="vid" value="'.htmlspecialchars($vid).'">
	<input type="hidden" name="action">
	
	<!-- Add Terminal Card -->
	<div class="add-terminal-card">
		<div class="add-terminal-form">
			<div class="form-group-add">
				<label class="form-label-add">
					<i class="far fa-calendar"></i> Tarikh
				</label>
				<div style="display: flex; gap: 0.5rem;">
					<select name="dd" class="form-input-add" onchange="document.MyZSSB.submit();" style="width: 70px;">';

if (isset($dayList)) {
	for ($d = 0; $d < count($dayList); $d++) {
		print '				<option value="'.htmlspecialchars($dayList[$d]).'"';
		if ($dd == $dayList[$d]) print ' selected';
		print '>'.htmlspecialchars($dayList[$d]).'</option>';
	}
}

print '				</select>
					<select name="mm" class="form-input-add" onchange="document.MyZSSB.submit();" style="width: 120px;">';

if (isset($monthList) && isset($monthVal)) {
	for ($m = 0; $m < count($monthList); $m++) {
		print '				<option value="'.htmlspecialchars($monthVal[$m]).'"';
		if ($mm == $monthVal[$m]) print ' selected';
		print '>'.strtoupper(htmlspecialchars($monthList[$m])).'</option>';
	}
}

print '				</select>
					<select name="yy" class="form-input-add" onchange="document.MyZSSB.submit();" style="width: 90px;">';

if (isset($yrList)) {
	for ($y = 0; $y < count($yrList); $y++) {
		print '				<option value="'.htmlspecialchars($yrList[$y]).'"';
		if ($yy == $yrList[$y]) print ' selected';
		print '>'.htmlspecialchars($yrList[$y]).'</option>';
	}
}

print '				</select>
				</div>
			</div>
			
			<div class="form-group-add">
				<label class="form-label-add">
					<i class="fas fa-money-bill-wave"></i> No. Terminal
				</label>
				<input type="text" name="terminalATM" value="'.htmlspecialchars($terminalATM).'" class="form-input-add terminal-input" maxlength="10" placeholder="Cth: 12345">
			</div>
			
			<div class="checkbox-group-add">
				<input type="checkbox" name="isZerorise" value="1" '.($isZerorise == '1' ? 'checked' : '').' class="checkbox-add" id="zerorise">
				<label for="zerorise" class="checkbox-label-add">Zerorise</label>
			</div>
			
			<button type="button" class="btn-add-terminal" onclick="InstructAct(\'arahan\');" '.($yy.$mm.$dd < date('Ymd') ? 'disabled' : '').'>
				<i class="fas fa-plus"></i>
				'.($yy.$mm.$dd < date('Ymd') ? 'Arahan Ditutup' : 'Tambah Terminal').'
			</button>';

if ($err1 != '') {
	print '
			<div class="error-message-add">
				<i class="fas fa-exclamation-circle"></i>
				<span>'.$err1.'</span>
			</div>';
}

print '
		</div>
	</div>
	
	<!-- List Section -->
	<div class="list-section-header">
		<div class="list-title-add">SENARAI TERMINAL ATM</div>
		<div class="vendor-filter-add">
			<label><i class="fas fa-building"></i> Syarikat:</label>
			<select name="vendorID" onchange="document.MyZSSB.submit();">
				<option value="">- SEMUA -</option>';

for ($s = 0; $s < count($vendorList); $s++) {
	print '		<option value="'.htmlspecialchars($vendorVal[$s]).'"';
	if ($vendorID == $vendorVal[$s]) print ' selected';
	print '>'.htmlspecialchars($vendorList[$s]).'</option>';
}

print '		</select>
		</div>
	</div>
	
	<div class="arahan-add-table-container">
		<table class="arahan-add-table">
			<thead>
				<tr>
					<th rowspan="2" style="width: 50px;">BIL</th>
					<th rowspan="2" style="min-width: 150px;">DOC NO</th>
					<th rowspan="2" style="min-width: 200px;">TERMINAL ATM</th>
					<th rowspan="2" style="width: 70px;">JENIS</th>
					<th rowspan="2" style="min-width: 120px;">SYARIKAT</th>
					<th colspan="5" style="background: #1e293b;">DENOMINASI</th>
					<th rowspan="2" style="width: 120px;">AMAUN<br>ARAHAN<br>(RM)</th>
					<th rowspan="2" style="width: 100px;">STATUS</th>
				</tr>
				<tr>
					<th style="width: 70px; background: #1e293b;">RM100</th>
					<th style="width: 70px; background: #1e293b;">RM50</th>
					<th style="width: 70px; background: #1e293b;">RM20</th>
					<th style="width: 70px; background: #1e293b;">RM10</th>
					<th style="width: 100px; background: #1e293b;">JUMLAH</th>
				</tr>
			</thead>
			<tbody>';

$sd = 0;
$cnt = 0;
$tqty100A = 0;
$tqty50A = 0;
$tqty20A = 0;
$tqty10A = 0;
$allValueA = 0;
$totalArahan = 0;

if ($rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		$strClass = ($cnt % 2 == 1) ? 'Data' : 'Data1';
		
		// Get all fields using getField
		$docNo = getField($rsD, 'docNo') ?? '';
		$atmNo = getField($rsD, 'atmNo') ?? '';
		$atmLoc = getField($rsD, 'atmLoc') ?? '';
		$atmType = getField($rsD, 'type') ?? '';
		$vendorCode = getField($rsD, 'vendorCode') ?? '';
		$qty100A = getField($rsD, 'qty100A') ?? 0;
		$qty50A = getField($rsD, 'qty50A') ?? 0;
		$qty20A = getField($rsD, 'qty20A') ?? 0;
		$qty10A = getField($rsD, 'qty10A') ?? 0;
		$totalValueA = getField($rsD, 'totalValueA') ?? 0;
		$status = getField($rsD, 'status') ?? null;
		
		$tqty100A += $qty100A; 
		$tqty50A += $qty50A; 
		$tqty20A += $qty20A; 
		$tqty10A += $qty10A; 
		$allValueA += $totalValueA; 
		$totalArahan += $totalValueA;
		
		$totalDeno = $qty100A + $qty50A + $qty20A + $qty10A;
		
		print '
				<tr class="'.$strClass.'">
					<td align="right" style="font-weight: 600; color: #64748b;">'.$sd.'</td>
					<td align="left" style="font-weight: 600; font-family: monospace;">'.htmlspecialchars($docNo).'</td>
					<td align="left" style="font-weight: 600;">'.htmlspecialchars($atmNo).' - '.htmlspecialchars($atmLoc).'</td>
					<td align="center">'.htmlspecialchars($atmType).'</td>
					<td align="left" style="font-weight: 500;">'.htmlspecialchars($vendorCode).'</td>
					<td align="right" style="background: #f8fafc; font-weight: 600;">'.number_format($qty100A).'&nbsp;</td>
					<td align="right" style="background: #f8fafc; font-weight: 600;">'.number_format($qty50A).'&nbsp;</td>
					<td align="right" style="background: #f8fafc; font-weight: 600;">'.number_format($qty20A).'&nbsp;</td>
					<td align="right" style="background: #f8fafc; font-weight: 600;">'.number_format($qty10A).'&nbsp;</td>
					<td align="right" style="background: #f8fafc; font-weight: 700;">'.number_format($totalDeno).'&nbsp;</td>
					<td align="right" style="font-weight: 700; color: #059669;">'.number_format($totalValueA, 2).'&nbsp;</td>
					<td align="center">';
		
		if ($status !== null && isset($statusList) && isset($statusVal)) {
			$statusIndex = array_search($status, $statusVal);
			if ($statusIndex !== false) {
				print '<span class="status-badge-add">'.($statusList[$statusIndex]).'</span>';
			}
		}
		
		print '
					</td>
				</tr>';	
		$cnt++;
		$rsD->MoveNext();
	}
	
	$totalDenoAll = $tqty100A + $tqty50A + $tqty20A + $tqty10A;
	
	print '
				<tr class="LabelH">
					<td colspan="5" align="right">JUMLAH : </td>
					<td align="right">'.number_format($tqty100A).'&nbsp;</td>
					<td align="right">'.number_format($tqty50A).'&nbsp;</td>
					<td align="right">'.number_format($tqty20A).'&nbsp;</td>
					<td align="right">'.number_format($tqty10A).'&nbsp;</td>
					<td align="right">'.number_format($totalDenoAll).'&nbsp;</td>
					<td align="right">'.number_format($totalArahan, 2).'&nbsp;</td>
					<td></td>
				</tr>';
} else {
	print '
				<tr>
					<td colspan="12" style="padding: 0;">
						<div class="empty-message-add">
							<i class="fas fa-inbox"></i>
							<h3 style="color: #0f172a; margin-bottom: 0.5rem;">Tiada Rekod</h3>
							<p>Tiada terminal ATM bagi tarikh yang dipilih</p>
						</div>
					</td>
				</tr>';
}

print'
			</tbody>
		</table>
	</div>
	</form>
</div>

<script>
	function InstructAct(v) {
		e = document.MyZSSB;
		if (e == null) {
			alert(\'Please confirm the form name.!\');
		} else {
			if (e.terminalATM == null || e.terminalATM.value == \'\') {
				alert(\'Sila masukkan No. Terminal.\');
			} else {
				e.action.value = v;
				e.submit();
			}
		}   
	}
</script>';

include("footer.php");	
?>