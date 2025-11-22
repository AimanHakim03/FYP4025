<?php

$today = date("F j, Y, g:i a"); 
                
// Initialize ALL request variables - CRITICAL FIX
$StartRec = isset($_REQUEST['StartRec']) ? (int)$_REQUEST['StartRec'] : 1;
$pg = isset($_REQUEST['pg']) ? (int)$_REQUEST['pg'] : 50;
$q = $_REQUEST['q'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? '';
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$hq = $_REQUEST['hq'] ?? '';
$branch = $_REQUEST['branch'] ?? '';
$regionID = $_REQUEST['regionID'] ?? '';
$stateID = $_REQUEST['stateID'] ?? '';

include("header.php");	

// Cache session variables
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

$sFileName = 'vendor.php';
$sFileRef = 'vendorAddUpdate.php';
$title = "SYARIKAT";

//--- BEGIN : Delete ------------------------------------------------------------------------------
if ($action == "delete" && $pk != '') {
	$vendorName = dlookup("vendor", "vendorName", "vendorID=" . tosql($pk, "Text"));

	//--- Begin : Create log 
	$user = $sessionUserName;
	$event = "Pemborong : ".$vendorName." - Dihapuskan";
	$type = $sessionGroupName;
	createLog($user, $event, $type);
	//--- End   : Create log 				

	$sSQL = "UPDATE vendor SET isDeleted = '1' WHERE vendorID = '".$pk."'";
	$rs = $conn->Execute($sSQL);

	print '<script>
			alert("Pembekal - '.addslashes($vendorName).' : Dihapuskan...!");
		</script>';	
}
//--- END   : Delete ------------------------------------------------------------------------------

//---- BEGIN : Query -----------------------------------------------------------------------------
$sSQL = "SELECT * FROM vendor WHERE isDeleted = '0'";
if ($q <> "") {
	$sSQL = "SELECT Q.* FROM (".$sSQL.") Q ".
			" WHERE Q.vendorName LIKE ".toSQL($q."%","Text").
			" OR Q.vendorCode LIKE ".toSQL($q."%","Text").
			" ORDER BY Q.vendorName, Q.vendorCode";			 
} else {
	$sSQL .= " ORDER BY vendorName, vendorCode";
}
//---- END : Query ------------------------------------------------------ -----------------------

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
/* Vendor Page Styles */
.vendor-page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.vendor-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.vendor-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

/* Filter Card */
.vendor-filter-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.vendor-filter-row {
	display: flex;
	gap: 1rem;
	align-items: end;
	flex-wrap: wrap;
}

.vendor-search-group {
	flex: 1;
	min-width: 250px;
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.vendor-filter-label {
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
}

.vendor-search-input {
	padding: 0.75rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
}

.vendor-search-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.vendor-btn {
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

.vendor-btn-primary {
	background: #3b82f6;
	color: #ffffff;
}

.vendor-btn-primary:hover {
	background: #2563eb;
	box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	transform: translateY(-1px);
}

.vendor-btn-success {
	background: #10b981;
	color: #ffffff;
}

.vendor-btn-success:hover {
	background: #059669;
	box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
	transform: translateY(-1px);
}

.vendor-pagination-controls {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	font-size: 0.875rem;
	color: #64748b;
}

.vendor-select {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
}

.vendor-select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table Container */
.vendor-table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.vendor-table {
	width: 100%;
	border-collapse: collapse;
}

.vendor-table thead th {
	padding: 1rem 0.75rem;
	text-align: left;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	background: #475569;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: normal;
	line-height: 1.4;
}

.vendor-table tbody td {
	padding: 1rem 0.75rem;
	font-size: 0.875rem;
	color: #1e293b;
	border-bottom: 1px solid #f1f5f9;
	vertical-align: top;
}

.vendor-table tbody tr:hover {
	background: #f8fafc;
}

.vendor-name-cell {
	min-width: 250px;
}

.vendor-company-name {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 600;
	font-size: 1rem;
	transition: color 0.2s;
	display: block;
	margin-bottom: 0.25rem;
}

.vendor-company-name:hover {
	color: #2563eb;
	text-decoration: underline;
}

.vendor-code {
	display: inline-block;
	padding: 0.25rem 0.625rem;
	background: #f1f5f9;
	color: #64748b;
	border-radius: 6px;
	font-size: 0.75rem;
	font-weight: 600;
	margin-bottom: 0.5rem;
}

.vendor-contact-person {
	color: #64748b;
	font-size: 0.85rem;
	display: flex;
	align-items: center;
	gap: 0.375rem;
}

.vendor-info-cell {
	min-width: 200px;
}

.vendor-info-line {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	margin-bottom: 0.375rem;
	font-size: 0.85rem;
}

.vendor-info-line i {
	color: #94a3b8;
	width: 16px;
}

.vendor-bank-cell {
	min-width: 220px;
}

.vendor-bank-name {
	font-weight: 600;
	color: #0f172a;
	margin-bottom: 0.25rem;
}

.vendor-acc-no {
	color: #64748b;
	font-family: monospace;
	font-size: 0.85rem;
}

.vendor-rep-cell {
	min-width: 200px;
}

.vendor-rep-item {
	display: flex;
	align-items: start;
	gap: 0.5rem;
	margin-bottom: 0.5rem;
	padding: 0.5rem;
	background: #f8fafc;
	border-radius: 6px;
	font-size: 0.85rem;
}

.vendor-rep-item i {
	color: #3b82f6;
	margin-top: 0.125rem;
	flex-shrink: 0;
}

.vendor-rep-name {
	font-weight: 600;
	color: #0f172a;
}

.vendor-rep-ic {
	color: #64748b;
	font-size: 0.8rem;
}

.upload-badge {
	display: inline-flex;
	align-items: center;
	gap: 0.375rem;
	padding: 0.375rem 0.75rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
}

.upload-yes {
	background: #d1fae5;
	color: #065f46;
}

.upload-no {
	background: #fee2e2;
	color: #991b1b;
}

.upload-dot {
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: currentColor;
}

.delete-icon {
	cursor: pointer;
	transition: transform 0.2s;
	padding: 0.5rem;
	border-radius: 6px;
	display: inline-block;
}

.delete-icon:hover {
	background: #fee2e2;
	transform: scale(1.1);
}

.delete-icon img {
	width: 20px;
	height: 20px;
}

/* Empty State */
.vendor-empty-state {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.vendor-empty-state i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.vendor-empty-state h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
	font-size: 1.25rem;
}

/* Pagination */
.vendor-pagination-container {
	padding: 1.5rem;
	background: #ffffff;
	border-top: 1px solid #e2e8f0;
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 1rem;
}

.vendor-pagination-info {
	color: #64748b;
	font-size: 0.875rem;
}

.vendor-pagination-info strong {
	color: #0f172a;
	font-weight: 600;
}

.vendor-pagination-links {
	display: flex;
	gap: 0.5rem;
	flex-wrap: wrap;
}

.vendor-page-link {
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

.vendor-page-link:hover {
	border-color: #3b82f6;
	color: #3b82f6;
	background: #eff6ff;
}

/* Responsive */
@media (max-width: 768px) {
	.vendor-filter-row {
		flex-direction: column;
	}
	
	.vendor-search-group {
		width: 100%;
	}
	
	.vendor-table-container {
		overflow-x: auto;
	}
	
	.vendor-table {
		min-width: 1200px;
	}
}
</style>

<form name="MyForm" action="'.$sFileName.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="pk">

<!-- Page Header -->
<div class="vendor-page-header">
	<h1 class="vendor-title">
		<i class="fas fa-building"></i>
		Syarikat
	</h1>
	<p class="vendor-subtitle">Urus maklumat syarikat dan pembekal perkhidmatan ATM</p>
</div>

<!-- Filter Section -->
<div class="vendor-filter-card">
	<div class="vendor-filter-row">
		<div class="vendor-search-group">
			<label class="vendor-filter-label">
				<i class="fas fa-search"></i> Carian
			</label>
			<input type="text" name="q" value="'.htmlspecialchars($q).'" placeholder="Kod atau nama syarikat..." class="vendor-search-input" maxlength="20">
		</div>
		
		<div style="display: flex; gap: 0.75rem; align-items: end;">
			<button type="submit" class="vendor-btn vendor-btn-primary">
				<i class="fas fa-search"></i>
				Cari
			</button>
			
			<button type="button" class="vendor-btn vendor-btn-success" onclick="window.location.href=\''.htmlspecialchars($sFileRef).'?action=add\';">
				<i class="fas fa-plus"></i>
				Daftar Syarikat
			</button>
		</div>
	</div>
	
	<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
		<div class="vendor-pagination-controls">
			<label>Paparan:</label>
			<select name="pg" class="vendor-select" onchange="doListAll();" style="width: 80px;">';

$pageOptions = array(10, 20, 30, 40, 50, 100, 200, 300, 400, 500);
foreach ($pageOptions as $option) {
	$selected = ($pg == $option) ? ' selected' : '';
	print '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
}

print '		</select>
			<span>/ mukasurat</span>
		</div>
	</div>
</div>

<!-- Table Container -->
<div class="vendor-table-container">
	<table class="vendor-table">
		<thead>
			<tr>
				<th style="width: 50px; text-align: right;">NO.</th>
				<th>NAMA SYARIKAT<br><span style="font-weight: 400; font-size: 0.7rem;">Dihubungi</span></th>
				<th>EMAIL<br><span style="font-weight: 400; font-size: 0.7rem;">No Tel / Fax</span></th>
				<th>MAKLUMAT BANK</th>
				<th>WAKIL</th>
				<th style="text-align: center; width: 140px;">UPLOAD / DOWNLOAD<br>FAIL</th>
				<th style="text-align: center; width: 100px;">TINDAKAN</th>
			</tr>
		</thead>
		<tbody>';

$grandTotal = 0;
if ($GetList->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	while (!$GetList->EOF && $cnt <= $pg) {
		$wakil = '';
		$picName = getField($GetList, 'picName') ?? '';
		$picIC = getField($GetList, 'picIC') ?? '';
		
		if ($picName <> '') {
			$picNameArr = explode('|', $picName);
			$picICArr = explode('|', $picIC);
			for ($a = 0; $a < count($picNameArr); $a++) {
				$picICValue = isset($picICArr[$a]) ? $picICArr[$a] : '';
				$wakil .= '<div class="vendor-rep-item">
							<i class="fas fa-user-tie"></i>
							<div>
								<div class="vendor-rep-name">'.htmlspecialchars($picNameArr[$a]).'</div>
								<div class="vendor-rep-ic">'.htmlspecialchars($picICValue).'</div>
							</div>
						</div>';
			}
		}
		
		$vendorID = getField($GetList, 'vendorID') ?? '';
		$vendorName = getField($GetList, 'vendorName') ?? '';
		$vendorCode = getField($GetList, 'vendorCode') ?? '';
		$contactPerson = getField($GetList, 'contactPerson') ?? '';
		$email = getField($GetList, 'email') ?? '';
		$telNo = getField($GetList, 'telNo') ?? '';
		$faxNo = getField($GetList, 'faxNo') ?? '';
		$bankName = getField($GetList, 'bankName') ?? '';
		$accNo = getField($GetList, 'accNo') ?? '';
		$isUD = getField($GetList, 'isUD') ?? '0';
		
		$uploadBadgeClass = 'upload-badge ';
		$uploadText = '';
		if ($isUD == '1' || strtoupper($isUD) == 'Y') {
			$uploadBadgeClass .= 'upload-yes';
			$uploadText = 'Ya';
		} else {
			$uploadBadgeClass .= 'upload-no';
			$uploadText = 'Tidak';
		}
		
		print ' 
			<tr>
				<td style="text-align: right; font-weight: 600; color: #64748b;">'.$bil.'</td>						
				<td class="vendor-name-cell">
					<a href="'.htmlspecialchars($sFileRef).'?action=update&pk='.htmlspecialchars($vendorID).'&strURL='.htmlspecialchars($strURL).'" class="vendor-company-name">
						'.htmlspecialchars($vendorName).'
					</a>
					<div>
						<span class="vendor-code">'.htmlspecialchars($vendorCode).'</span>
					</div>';
		
		if ($contactPerson) {
			print '		<div class="vendor-contact-person">
							<i class="fas fa-user"></i>
							'.htmlspecialchars($contactPerson).'
						</div>';
		}
		
		print '		</td>
				<td class="vendor-info-cell">';
		
		if ($email) {
			print '		<div class="vendor-info-line">
							<i class="fas fa-envelope"></i>
							<span>'.htmlspecialchars($email).'</span>
						</div>';
		}
		if ($telNo) {
			print '		<div class="vendor-info-line">
							<i class="fas fa-phone"></i>
							<span>'.htmlspecialchars($telNo).'</span>
						</div>';
		}
		if ($faxNo) {
			print '		<div class="vendor-info-line">
							<i class="fas fa-fax"></i>
							<span>'.htmlspecialchars($faxNo).'</span>
						</div>';
		}
		
		print '		</td>
				<td class="vendor-bank-cell">';
		
		if ($bankName) {
			print '		<div class="vendor-bank-name">'.htmlspecialchars($bankName).'</div>';
		}
		if ($accNo) {
			print '		<div class="vendor-acc-no">'.htmlspecialchars($accNo).'</div>';
		}
		
		print '		</td>
				<td class="vendor-rep-cell">'.$wakil.'</td>
				<td style="text-align: center;">
					<span class="'.$uploadBadgeClass.'">
						<span class="upload-dot"></span>
						'.$uploadText.'
					</span>
				</td>
				<td style="text-align: center;">
					<a href="#" onclick="DeleteID(\''.htmlspecialchars($vendorID).'\',\''.addslashes($vendorName).'\'); return false;" class="delete-icon" title="Hapuskan">
						<i class="fas fa-trash-alt" style="color: #ef4444; font-size: 1.1rem;"></i>
					</a>
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
	<div class="vendor-pagination-container">
		<div class="vendor-pagination-info">
			Jumlah Rekod: <strong>' . $GetList->RowCount() . '</strong>
		</div>';
	
	if ($TotalRec > $pg) {
		$numPage = ($TotalRec % $pg == 0) ? $TotalPage : $TotalPage + 1;
		$passURL = '&pg='.$pg.'&q='.urlencode($q);
		
		print '
		<div class="vendor-pagination-links">';
		
		for ($i = 1; $i <= min($numPage, 10); $i++) {
			$startRec = (($i * $pg) + 1 - $pg);
			print '<a href="'.htmlspecialchars($sFileName.'?StartRec='.$startRec.$passURL).'" class="vendor-page-link">'.$i.'</a>';
		}
		
		if ($numPage > 10) {
			print '<span class="vendor-page-link" style="border: none; cursor: default;">...</span>';
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
	<div class="vendor-empty-state">';
	
	if ($q == "") {
		print '
		<i class="fas fa-building"></i>
		<h3>Tiada Syarikat</h3>
		<p>Belum ada syarikat didaftarkan dalam sistem</p>';
	} else {
		print '
		<i class="fas fa-search"></i>
		<h3>Tiada Rekod Dijumpai</h3>
		<p>Carian "'.htmlspecialchars($q).'" tidak menjumpai sebarang syarikat</p>';
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
	function DeleteID(id, code) {
		e = document.MyForm;
		if(confirm(\' Hapuskan pemborong : \' + code + \' ?\')) {
			e.pk.value = id;
			e.action.value = "delete";
			e.submit();
		}
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&pg=" + c.options[c.selectedIndex].value + "&q='.addslashes($q).'";
	}
	
	function PrintID() {
		e = document.MyForm;
		if(confirm(\' Cetak senarai ?\')) {
			window.open("rpt_VendorList.php?","rpt","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");			
		}
	}	   		
</script>';
?>