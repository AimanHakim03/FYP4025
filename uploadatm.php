<?php

include("header.php");
include("forms.php");

// Initialize variables
$action = $_REQUEST['action'] ?? '';
$action1 = $_REQUEST['action1'] ?? '';
$sourcefile = $_REQUEST['sourcefile'] ?? '';
$msg = '';
$errDesc = '';
$nfDesc = '';

// Initialize column arrays
$col0 = $_REQUEST['col0'] ?? [];
$col1 = $_REQUEST['col1'] ?? [];
$col2 = $_REQUEST['col2'] ?? [];
$col3 = $_REQUEST['col3'] ?? [];
$col4 = $_REQUEST['col4'] ?? [];
$col5 = $_REQUEST['col5'] ?? [];
$col6 = $_REQUEST['col6'] ?? [];
$col7 = $_REQUEST['col7'] ?? [];
$col8 = $_REQUEST['col8'] ?? [];
$col9 = $_REQUEST['col9'] ?? [];
$col10 = $_REQUEST['col10'] ?? [];
$col11 = $_REQUEST['col11'] ?? [];
$col12 = $_REQUEST['col12'] ?? [];
$col13 = $_REQUEST['col13'] ?? [];
$col14 = $_REQUEST['col14'] ?? [];
$col15 = $_REQUEST['col15'] ?? [];
$col16 = $_REQUEST['col16'] ?? [];
$col17 = $_REQUEST['col17'] ?? [];
$col18 = $_REQUEST['col18'] ?? [];
$col19 = $_REQUEST['col19'] ?? [];
$col20 = $_REQUEST['col20'] ?? [];
$col21 = $_REQUEST['col21'] ?? [];

// Session checks
if (!isset($_SESSION['Session.userID']) || $_SESSION['Session.userID'] == "" || 
    !isset($_SESSION['Session.isUD']) || $_SESSION['Session.isUD'] == "0") {
	$errPage = $errPage ?? 'Unauthorized access';
	print '<script>alert("'.addslashes($errPage).'");window.location.href="index.php";</script>';
	exit;
}

$vendorID = '';
if ((isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "0") || 
    (isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "1")) {
	$vendorID = $_SESSION['Session.branchID'] ?? '';
}

print '
<style>
	* { box-sizing: border-box; }
	
	.upload-container {
		max-width: 1600px;
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
	
	.upload-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
		max-width: 700px;
		margin: 0 auto 24px auto;
	}
	
	.card-header {
		background: #475569;
		color: white;
		padding: 16px 24px;
		font-size: 15px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.card-body {
		padding: 32px 24px;
	}
	
	.upload-zone {
		border: 2px dashed #cbd5e0;
		border-radius: 8px;
		padding: 40px 24px;
		text-align: center;
		background: #f7fafc;
		transition: all 0.3s;
		cursor: pointer;
	}
	
	.upload-zone:hover {
		border-color: #4299e1;
		background: #ebf8ff;
	}
	
	.upload-icon {
		font-size: 48px;
		color: #cbd5e0;
		margin-bottom: 16px;
	}
	
	.upload-text {
		font-size: 16px;
		color: #4a5568;
		margin-bottom: 8px;
		font-weight: 500;
	}
	
	.upload-subtext {
		font-size: 14px;
		color: #718096;
		margin-bottom: 16px;
	}
	
	.file-input {
		display: none;
	}
	
	.file-select-btn {
		padding: 10px 24px;
		background: #4299e1;
		color: white;
		border: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
	}
	
	.file-select-btn:hover {
		background: #3182ce;
	}
	
	.selected-file {
		margin-top: 16px;
		padding: 12px;
		background: #edf2f7;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		display: none;
	}
	
	.form-actions {
		display: flex;
		gap: 12px;
		justify-content: center;
		margin-top: 24px;
	}
	
	.btn-modern {
		padding: 10px 24px;
		border: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
	}
	
	.btn-primary {
		background: #4299e1;
		color: white;
	}
	
	.btn-primary:hover {
		background: #3182ce;
	}
	
	.btn-secondary {
		background: #e2e8f0;
		color: #4a5568;
	}
	
	.btn-secondary:hover {
		background: #cbd5e0;
	}
	
	.format-section {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
		margin-bottom: 24px;
	}
	
	.format-header {
		background: #f7fafc;
		padding: 16px 24px;
		border-bottom: 2px solid #e2e8f0;
		text-align: center;
	}
	
	.format-title {
		font-size: 16px;
		font-weight: 600;
		color: #1a202c;
		margin: 0;
	}
	
	.table-container {
		overflow-x: auto;
		padding: 16px;
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
		padding: 10px 8px;
		text-align: center;
		font-size: 11px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.3px;
		white-space: nowrap;
		border-right: 1px solid rgba(255,255,255,0.1);
	}
	
	.table-modern thead th:last-child {
		border-right: none;
	}
	
	.header-group {
		background: #e2e8f0;
		color: #1a202c;
	}
	
	.table-modern tbody tr {
		border-bottom: 1px solid #e2e8f0;
	}
	
	.table-modern tbody tr:hover {
		background: #f7fafc;
	}
	
	.table-modern tbody td {
		padding: 10px 8px;
		font-size: 12px;
		color: #2d3748;
		text-align: center;
		border-right: 1px solid #f3f4f6;
	}
	
	.table-modern tbody td:last-child {
		border-right: none;
	}
	
	.text-left {
		text-align: left;
	}
	
	.preview-header {
		background: white;
		padding: 16px 24px;
		margin-bottom: 16px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		text-align: center;
	}
	
	.preview-title {
		font-size: 18px;
		font-weight: 600;
		color: #1a202c;
		margin: 0;
	}
	
	.results-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		padding: 24px;
		max-width: 600px;
		margin: 24px auto;
	}
	
	.results-title {
		font-size: 18px;
		font-weight: 600;
		color: #1a202c;
		margin-bottom: 16px;
		text-align: center;
	}
	
	.result-item {
		display: flex;
		padding: 12px 0;
		border-bottom: 1px solid #e2e8f0;
	}
	
	.result-item:last-child {
		border-bottom: none;
	}
	
	.result-label {
		flex: 1;
		font-size: 14px;
		color: #4a5568;
		font-weight: 500;
	}
	
	.result-value {
		font-size: 14px;
		font-weight: 600;
		color: #1a202c;
	}
	
	.result-success {
		color: #10b981;
	}
	
	.result-error {
		color: #ef4444;
	}
	
	.result-warning {
		color: #f59e0b;
	}
	
	@media (max-width: 768px) {
		.upload-container {
			padding: 12px;
		}
		
		.form-actions {
			flex-direction: column;
		}
		
		.btn-modern {
			width: 100%;
		}
	}
</style>

<div class="upload-container">
	<div class="page-header-modern">
		<h1 class="page-title-modern">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
				<polyline points="17 8 12 3 7 8"/>
				<line x1="12" y1="3" x2="12" y2="15"/>
			</svg>
			ARAHAN PENGISIAN ATM - UPLOAD FILE
		</h1>
		<p class="page-subtitle">Upload fail CSV untuk kemaskini data arahan pengisian ATM</p>
	</div>

	<form name="MyATM" action="uploadatm.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action">
		
		<div class="upload-card">
			<div class="card-header">
				📁 Upload CSV File
			</div>
			<div class="card-body">
				<div class="upload-zone" id="uploadZone">
					<div class="upload-icon">📤</div>
					<div class="upload-text">Pilih fail CSV atau drag & drop di sini</div>
					<div class="upload-subtext">Fail mestilah dalam format CSV (maksimum 1MB)</div>
					<div>
						<button type="button" class="file-select-btn" onclick="document.getElementById(\'uploadFile\').click()">
							Pilih Fail
						</button>
						<input type="file" name="uploadFile" id="uploadFile" class="file-input" accept=".csv">
					</div>
					<div class="selected-file" id="selectedFile"></div>
				</div>
				
				<div class="form-actions">
					<button type="submit" name="action" value="OK" class="btn-modern btn-primary">
						✓ Upload
					</button>
					<button type="button" class="btn-modern btn-secondary" onclick="window.location.href=\'arahan.php\';">
						✕ Kembali
					</button>
				</div>
			</div>
		</div>
	</form>
	
	<div class="format-section">
		<div class="format-header">
			<h3 class="format-title">📋 FORMAT YANG DIPERLUKAN</h3>
		</div>
		<div class="table-container">
			<table class="table-modern">
				<thead>
					<tr>
						<th rowspan="2">BIL</th>
						<th rowspan="2">DOC NO</th>
						<th rowspan="2">REPLENISH<br>DATE</th>
						<th rowspan="2">BANK-IN<br>DATE</th>
						<th rowspan="2">ATM NO</th>
						<th rowspan="2">ATM-EBC</th>
						<th colspan="5" class="header-group">REPLENISH</th>
						<th colspan="5" class="header-group">UNLOAD (PHYSICAL)</th>
						<th colspan="5" class="header-group">UNLOAD (LOCAL)</th>
						<th rowspan="2">REMARK</th>
					</tr>
					<tr>
						<th>RM100</th>
						<th>RM50</th>
						<th>RM20</th>
						<th>RM10</th>
						<th>JUMLAH(RM)</th>
						<th>RM100</th>
						<th>RM50</th>
						<th>RM20</th>
						<th>RM10</th>
						<th>JUMLAH(RM)</th>
						<th>RM100</th>
						<th>RM50</th>
						<th>RM20</th>
						<th>RM10</th>
						<th>JUMLAH(RM)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>A00132016081601CIT</td>
						<td>17/08/2016</td>
						<td>18/06/2016</td>
						<td>0013</td>
						<td class="text-left">ATM TABUNG HAJI</td>
						<td>100.00</td>
						<td>50.00</td>
						<td>20.00</td>
						<td>10.00</td>
						<td>180.00</td>
						<td>100.00</td>
						<td>50.00</td>
						<td>20.00</td>
						<td>10.00</td>
						<td>180.00</td>
						<td>100.00</td>
						<td>50.00</td>
						<td>20.00</td>
						<td>10.00</td>
						<td>180.00</td>
						<td class="text-left">XXXX XXXX XXX XXXX</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	const uploadZone = document.getElementById("uploadZone");
	const fileInput = document.getElementById("uploadFile");
	const selectedFileDiv = document.getElementById("selectedFile");
	
	fileInput.addEventListener("change", function() {
		if (this.files && this.files[0]) {
			const fileName = this.files[0].name;
			const fileSize = (this.files[0].size / 1024).toFixed(2);
			selectedFileDiv.innerHTML = "📄 <strong>" + fileName + "</strong> (" + fileSize + " KB)";
			selectedFileDiv.style.display = "block";
		}
	});
	
	["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
		uploadZone.addEventListener(eventName, function(e) {
			e.preventDefault();
			e.stopPropagation();
		}, false);
	});
	
	["dragenter", "dragover"].forEach(eventName => {
		uploadZone.addEventListener(eventName, () => {
			uploadZone.style.borderColor = "#4299e1";
			uploadZone.style.background = "#ebf8ff";
		}, false);
	});
	
	["dragleave", "drop"].forEach(eventName => {
		uploadZone.addEventListener(eventName, () => {
			uploadZone.style.borderColor = "#cbd5e0";
			uploadZone.style.background = "#f7fafc";
		}, false);
	});
	
	uploadZone.addEventListener("drop", function(e) {
		const files = e.dataTransfer.files;
		if (files.length > 0) {
			fileInput.files = files;
			const event = new Event("change");
			fileInput.dispatchEvent(event);
		}
	});
	
	uploadZone.addEventListener("click", function(e) {
		if (e.target !== fileInput && !e.target.classList.contains("file-select-btn")) {
			fileInput.click();
		}
	});
</script>';

//--- BEGIN : RETRIEVE UPLOAD CSV INFORMATION -----------------------------------------------------------------------------------
if ($action != "") {
	$allowedExtension = array("csv"); 
	
	$fileName = $_FILES['uploadFile']['name'] ?? '';
   	$tmpName  = $_FILES['uploadFile']['tmp_name'] ?? '';
   	$fileSize = $_FILES['uploadFile']['size'] ?? 0;
   	$fileType = $_FILES['uploadFile']['type'] ?? '';
	$fileExt = $fileName ? explode('.', $fileName) : [];

	if (!empty($fileExt) && !in_array(strtolower(end($fileExt)), $allowedExtension)) {
		$msg = 'Filename not allow to upload ('.htmlspecialchars($fileName).') only csv.';
	} elseif ($fileSize > 1000000) {
		$msg = 'Document size ('.htmlspecialchars($fileName).') must be less than 1M.';
	} else {
		$fp = @fopen($tmpName,"r");
 		$buffer = "";
		if(!$fp){
			$msg = 'ERROR opening file.';
		} else {
			print '
			<div class="upload-container">
				<div class="preview-header">
					<h2 class="preview-title">📊 DATA FROM ('.htmlspecialchars($fileName).')</h2>
				</div>
				
				<form name="MyATMCSV" action="uploadatm.php" method="post">
					<input type="hidden" name="sourcefile" value="'.htmlspecialchars($fileName).'">
					
					<div class="table-container" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
						<table class="table-modern">';
			
			$a=0;
			while(!feof($fp)) {
				$buffer = str_replace("\"","",fgets($fp, 4096));
				$content = explode(",",$buffer);
				if (count($content) <> 0) {
					if (isset($content[0]) && $content[0] <> '') {
						print '<tr>';
						for ($z = 0; $z < count($content); $z++) {
							$col = "col".$z;
							$cellValue = isset($content[$z]) ? htmlspecialchars(trim($content[$z])) : '';
							print '<td>'.($cellValue ?: '&nbsp;').'</td>';
							print '<input type="hidden" name="'.$col.'[]" value="'.$cellValue.'">';
						}
						print '</tr>';
						$a++;
					}
				}
			}	
			
			print '
						</table>
					</div>
					
					<div style="text-align: center; padding: 24px;">
						<button type="submit" name="action1" value="Process" class="btn-modern btn-primary" style="padding: 12px 48px; font-size: 16px;">
							🚀 Process Data
						</button>
					</div>
				</form>
			</div>';
		}
		fclose($fp);
	}
	
	if ($msg <> '') {
		print '<script>alert("'.addslashes($msg).'");</script>';
	}
}
//--- END   : RETRIEVE UPLOAD CSV INFORMATION------------------------------------------------------------------------------------

//--- BEGIN : PROCESS DATA ------------------------------------------------------------------------------------------------------
if ($action1 != '') {
	$rec_read=0; $rec_upd=0; $rec_err=0; $rec_nf=0;
	
	for ($z = 1; $z < count($col1); $z++) {
		$rec_read++;
		
		$docNo = dlookup("instruct", "docNo", "docNo=" . tosql($col1[$z], "Text"));
		
		// Parse dates safely
		$actualReplenishDate = '';
		$bankInDate = '';
		
		if (isset($col2[$z]) && $col2[$z] != '') {
			$rd = explode('/', $col2[$z]);
			if (count($rd) == 3) {
				$actualReplenishDate = sprintf("%04d",$rd[2]).'-'.sprintf("%02d",$rd[1]).'-'.sprintf("%02d",$rd[0]);
			}
		}
		
		if (isset($col3[$z]) && $col3[$z] != '') {
			$bd = explode('/', $col3[$z]);
			if (count($bd) == 3) {
				$bankInDate = sprintf("%04d",$bd[2]).'-'.sprintf("%02d",$bd[1]).'-'.sprintf("%02d",$bd[0]);
			}
		}
		
		if ($docNo !='') {
			$sSQL = "UPDATE instruct SET 
			replenish100 = '".strval(($col6[$z] ?? 0)/100)."', 
			replenish50 = '".strval(($col7[$z] ?? 0)/50)."',
			replenish20 = '".strval(($col8[$z] ?? 0)/20)."',
			replenish10 = '".strval(($col9[$z] ?? 0)/10)."', 
			replenishValue = '".strval($col10[$z] ?? 0)."',
			unload100 = '".strval(($col11[$z] ?? 0)/100)."', 
			unload50 = '".strval(($col12[$z] ?? 0)/50)."', 
			unload20 = '".strval(($col13[$z] ?? 0)/20)."', 
			unload10 = '".strval(($col14[$z] ?? 0)/10)."', 
			unloadValue = '".strval($col15[$z] ?? 0)."',
			host100 = '".strval(($col16[$z] ?? 0)/100)."', 
			host50 = '".strval(($col17[$z] ?? 0)/50)."', 
			host20 = '".strval(($col18[$z] ?? 0)/20)."', 
			host10 = '".strval(($col19[$z] ?? 0)/10)."', 
			hostValue = '".strval($col20[$z] ?? 0)."', 
			remarks = '".($col21[$z] ?? '')."',
			replenishBy = '".$_SESSION['Session.userName']."', 
			replenishDate = '".date("Y-m-d H:i:s")."' ";
			
			if ($actualReplenishDate != '') {
				$sSQL .= ", actualReplenishDate = '".$actualReplenishDate."' ";
			}
			if ($bankInDate != '') {
				$sSQL .= ", bankInDate='".$bankInDate."' ";
			}
			
			$sSQL .= " WHERE docNo = '".($col1[$z] ?? '')."' ";
			
			$rsU = $conn->Execute($sSQL);
			if ($rsU === false) { 
				$rec_err++; 
				$errDesc .= '<br>'.htmlspecialchars($col1[$z] ?? '');
				$errSQL = "INSERT INTO errorlogs (errDate, errSource, errQry, createdBy) VALUES ('".date("Y-m-d H:i:s")."','".($sourcefile ?? '')."',".tosql($sSQL,"text").",'".$_SESSION['Session.userName']."')";
				$rsI = $conn->Execute($errSQL);
			} else {
				$rec_upd++; 
			}
		} else {
			$rec_nf++; 
			$nfDesc .= '<br>'.htmlspecialchars($col1[$z] ?? '');
		}
	}
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'];
	$event = "Arahan Pengisian - Upload File (".$sourcefile.")  - Kemaskini (".$rec_upd.") Ralat (".$rec_err.")";
	$type  = $_SESSION['Session.groupName'];
	createLog($user , $event, $type);
	//--- End   : Create log 	
	
	print '
	<div class="upload-container">
		<div class="results-card">
			<h3 class="results-title">📈 Keputusan Pemprosesan</h3>
			<div class="result-item">
				<div class="result-label">📁 Source File:</div>
				<div class="result-value">'.htmlspecialchars($sourcefile).'</div>
			</div>
			<div class="result-item">
				<div class="result-label">📖 Record(s) Read:</div>
				<div class="result-value">'.$rec_read.'</div>
			</div>
			<div class="result-item">
				<div class="result-label">✅ Record(s) Updated:</div>
				<div class="result-value result-success">'.$rec_upd.'</div>
			</div>
			<div class="result-item">
				<div class="result-label">⚠️ Record(s) Not Found:</div>
				<div class="result-value result-warning">'.$rec_nf.($nfDesc ? $nfDesc : '').'</div>
			</div>
			<div class="result-item">
				<div class="result-label">❌ Record(s) Error:</div>
				<div class="result-value result-error">'.$rec_err.($errDesc ? $errDesc : '').'</div>
			</div>
			<div style="text-align: center; margin-top: 24px;">
				<button type="button" class="btn-modern btn-primary" onclick="window.location.href=\'arahan.php\';">
					← Kembali ke Arahan
				</button>
			</div>
		</div>
	</div>';
}
//--- END   : PROCESS DATA ------------------------------------------------------------------------------------------------------

include("footer.php");	
?>