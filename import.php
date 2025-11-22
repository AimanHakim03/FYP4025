<?php

include("header.php");	

$title = 'Import IBS File';

// FIX FOR UNDEFINED VARIABLES - GET POST DATA
$action = isset($_POST['action']) ? $_POST['action'] : '';
$action1 = isset($_POST['action1']) ? $_POST['action1'] : '';
$strURL = isset($_POST['strURL']) ? $_POST['strURL'] : '';
$sourcefile = isset($_POST['sourcefile']) ? $_POST['sourcefile'] : '';
$msg = ''; // Initialize msg variable

// Get array data from form
$brNo = isset($_POST['brNo']) ? $_POST['brNo'] : array();
$seqNo = isset($_POST['seqNo']) ? $_POST['seqNo'] : array();
$model = isset($_POST['model']) ? $_POST['model'] : array();
$location = isset($_POST['location']) ? $_POST['location'] : array();
$lrepDate = isset($_POST['lrepDate']) ? $_POST['lrepDate'] : array();
$lrepTime = isset($_POST['lrepTime']) ? $_POST['lrepTime'] : array();
$spare1 = isset($_POST['spare1']) ? $_POST['spare1'] : array();
$lrepDeno2 = isset($_POST['lrepDeno2']) ? $_POST['lrepDeno2'] : array();
$lrepDenod2 = isset($_POST['lrepDenod2']) ? $_POST['lrepDenod2'] : array();
$lrepDeno4 = isset($_POST['lrepDeno4']) ? $_POST['lrepDeno4'] : array();
$lrepDenod4 = isset($_POST['lrepDenod4']) ? $_POST['lrepDenod4'] : array();
$atmNo = isset($_POST['atmNo']) ? $_POST['atmNo'] : array();
$fileDate = isset($_POST['fileDate']) ? $_POST['fileDate'] : array();
$remark = isset($_POST['remark']) ? $_POST['remark'] : array();

if ($_SESSION['Session.groupID'] == 0) {
	print '<script>alert("'.$errPage.'");window.location="index.php";</script>';
}

print '
<style>
	* { box-sizing: border-box; }
	
	.import-container {
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
	
	.upload-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
		max-width: 600px;
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
	
	.upload-zone.dragover {
		border-color: #4299e1;
		background: #ebf8ff;
		transform: scale(1.02);
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
	
	.file-input-wrapper {
		position: relative;
		display: inline-block;
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
	
	.preview-section {
		margin-top: 24px;
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
	
	.table-container {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow-x: auto;
		margin-bottom: 20px;
	}
	
	.table-modern {
		width: 100%;
		border-collapse: collapse;
		min-width: 1200px;
	}
	
	.table-modern thead {
		background: #475569;
		color: white;
	}
	
	.table-modern thead th {
		padding: 12px 10px;
		text-align: center;
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		white-space: nowrap;
	}
	
	.table-modern tbody tr {
		border-bottom: 1px solid #e2e8f0;
	}
	
	.table-modern tbody tr:hover {
		background: #f7fafc;
	}
	
	.table-modern tbody td {
		padding: 10px;
		font-size: 13px;
		color: #2d3748;
		vertical-align: middle;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-right {
		text-align: right;
	}
	
	.text-left {
		text-align: left;
	}
	
	.badge-warning {
		background: #fef3c7;
		color: #92400e;
		padding: 4px 8px;
		border-radius: 4px;
		font-size: 11px;
		font-weight: 600;
	}
	
	.badge-removed {
		background: #fee2e2;
		color: #991b1b;
		padding: 4px 8px;
		border-radius: 4px;
		font-size: 11px;
		font-weight: 600;
	}
	
	@media (max-width: 768px) {
		.import-container {
			padding: 12px;
		}
		
		.upload-card {
			max-width: 100%;
		}
		
		.form-actions {
			flex-direction: column;
		}
		
		.btn-modern {
			width: 100%;
		}
	}
</style>

<div class="import-container">
	<div class="page-header-modern">
		<h1 class="page-title-modern">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
				<polyline points="17 8 12 3 7 8"/>
				<line x1="12" y1="3" x2="12" y2="15"/>
			</svg>
			'.strtoupper($title).'
		</h1>
		<p class="page-subtitle">Upload dan proses fail IBS untuk kemaskini data ATM</p>
	</div>

	<form name="FrmImport" action="import.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action">
		<input type="hidden" name="strURL" value="'.htmlspecialchars($strURL).'">
		
		<div class="upload-card">
			<div class="card-header">
				📁 Import IBS File
			</div>
			<div class="card-body">
				<div class="upload-zone" id="uploadZone">
					<div class="upload-icon">📤</div>
					<div class="upload-text">Pilih fail atau drag & drop di sini</div>
					<div class="upload-subtext">Fail yang disokong: CSV, TXT</div>
					<div class="file-input-wrapper">
						<button type="button" class="file-select-btn" onclick="document.getElementById(\'uploadFile\').click()">
							Pilih Fail
						</button>
						<input type="file" name="uploadFile" id="uploadFile" class="file-input">
					</div>
					<div class="selected-file" id="selectedFile"></div>
				</div>
				
				<div class="form-actions">
					<button type="submit" name="action" value="Verify" class="btn-modern btn-primary">
						✓ Verify
					</button>
					<button type="button" class="btn-modern btn-secondary" onclick="window.location.href=\'index.php\';">
						✕ Batal
					</button>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	const uploadZone = document.getElementById("uploadZone");
	const fileInput = document.getElementById("uploadFile");
	const selectedFileDiv = document.getElementById("selectedFile");
	
	// Handle file selection
	fileInput.addEventListener("change", function() {
		if (this.files && this.files[0]) {
			const fileName = this.files[0].name;
			const fileSize = (this.files[0].size / 1024).toFixed(2);
			selectedFileDiv.innerHTML = "📄 <strong>" + fileName + "</strong> (" + fileSize + " KB)";
			selectedFileDiv.style.display = "block";
		}
	});
	
	// Drag and drop functionality
	["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
		uploadZone.addEventListener(eventName, preventDefaults, false);
	});
	
	function preventDefaults(e) {
		e.preventDefault();
		e.stopPropagation();
	}
	
	["dragenter", "dragover"].forEach(eventName => {
		uploadZone.addEventListener(eventName, () => {
			uploadZone.classList.add("dragover");
		}, false);
	});
	
	["dragleave", "drop"].forEach(eventName => {
		uploadZone.addEventListener(eventName, () => {
			uploadZone.classList.remove("dragover");
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
	
	// Click anywhere in upload zone to open file dialog
	uploadZone.addEventListener("click", function(e) {
		if (e.target !== fileInput && !e.target.classList.contains("file-select-btn")) {
			fileInput.click();
		}
	});
</script>';

//--- BEGIN : RETRIEVE IBS FILE ---------------------------------------------------------------------------------------------
if ($action == "Verify") {
	$fileName = $_FILES['uploadFile']['name'];
   	$tmpName  = $_FILES['uploadFile']['tmp_name'];
   	$fileSize = $_FILES['uploadFile']['size'];
   	$fileType = $_FILES['uploadFile']['type'];
	$fileExt = explode('.', $_FILES['uploadFile']['name']);

	$fp = @fopen($tmpName,"r");
	$buffer = "";
	if (!$fp){
		$msg = 'ERROR - Cannot read/select file..!!!.';
	} else {
		print '
		<div class="import-container">
			<div class="preview-header">
				<h2 class="preview-title">📊 IBS DATA FROM ('.htmlspecialchars($fileName).')</h2>
			</div>
			
			<form name="MyZSSB" action="import.php" method="post" enctype="multipart/form-data">  
				<input type="hidden" name="action1" value="Import">
				<input type="hidden" name="sourcefile" value="'.htmlspecialchars($fileName).'">
				
				<div class="table-container">
					<table class="table-modern">
						<thead>
							<tr>
								<th>NO</th>
								<th>BR NO</th>
								<th>SEQ NO</th>
								<th>MODEL</th>
								<th>LOCATION</th>
								<th>LREP DATE</th>
								<th>LREP TIME</th>
								<th>SPARE1</th>
								<th>LREPDENO2</th>
								<th>LREPDENOd2</th>
								<th>LREPDENO4</th>
								<th>LREPDENOd4</th>
								<th>ATM NO</th>
								<th>FILE DATE</th>
								<th>REMARK</th>
							</tr>
						</thead>
						<tbody>';

		$fileDate = substr($fileName,14,4).'-'.substr($fileName,12,2).'-'.substr($fileName,10,2);
		$a=0;
		while (!feof($fp)) {
		    $buffer = fgets($fp, 4096); 
			if (substr($buffer,7,1) == 'I' OR substr($buffer,7,1) == 'F') {
				$brNo		= substr($buffer,0,3);
				$seqNo 		= substr($buffer,4,1);
				$model		= substr($buffer,7,1);
				$location	= trim(substr($buffer,9,31));
				$lrepDate 	= substr($buffer,40,8);
				$lrepTime 	= substr($buffer,49,4);
				$spare1 	= substr($buffer,54,2);
				$lrepDeno2	= trim(substr($buffer,66,6));
				$lrepDenod2	= trim(substr($buffer,96,6));
				$lrepDeno4	= trim(substr($buffer,111,6));
				$lrepDenod4	= trim(substr($buffer,141,6));
				$atmNo		= $brNo.$seqNo;
				$a++;
				
				$insertRec = 0;$remark = '';
				if (preg_match("/\AOLD/i", $location)) {
					$remark = '*** Removed ATM terminal ***';
					$remarkClass = 'badge-removed';
				} else {
					$insertRec = 1;
					if (dlookup("atm", "atmID", "atmNo=" . tosql($atmNo, "Text")) == '') {
						$remark = '*** ATM terminal record not found ***';
						$remarkClass = 'badge-warning';
					} else {
						$remarkClass = '';
					}
				}	
					
				print '
							<tr>		
								<td class="text-center">'.htmlspecialchars($a).'.</td>					
								<td class="text-center">'.htmlspecialchars($brNo).'</td>
								<td class="text-center">'.htmlspecialchars($seqNo).'</td>
								<td class="text-center">'.htmlspecialchars($model).'</td>
								<td class="text-left">'.htmlspecialchars($location).'</td>
								<td class="text-center">'.htmlspecialchars($lrepDate).'</td>
								<td class="text-center">'.htmlspecialchars($lrepTime).'</td>
								<td class="text-center">'.htmlspecialchars($spare1).'</td>
								<td class="text-right">'.htmlspecialchars($lrepDeno2).'</td>
								<td class="text-right">'.htmlspecialchars($lrepDenod2).'</td>
								<td class="text-right">'.htmlspecialchars($lrepDeno4).'</td>
								<td class="text-right">'.htmlspecialchars($lrepDenod4).'</td>
								<td class="text-right">'.htmlspecialchars($atmNo).'</td>
								<td class="text-right">'.htmlspecialchars($fileDate).'</td>
								<td class="text-center">';
				
				if ($remark != '') {
					print '<span class="'.$remarkClass.'">'.htmlspecialchars($remark).'</span>';
				}
				
				print '			</td>					
							</tr>';
				
				if ($insertRec == 1) {
					print '
					<input type="Hidden" name="brNo[]" value="'.htmlspecialchars($brNo).'">
					<input type="Hidden" name="seqNo[]" value="'.htmlspecialchars($seqNo).'">
					<input type="Hidden" name="model[]" value="'.htmlspecialchars($model).'">
					<input type="Hidden" name="location[]" value="'.htmlspecialchars($location).'">
					<input type="Hidden" name="lrepDate[]" value="'.htmlspecialchars($lrepDate).'">
					<input type="Hidden" name="lrepTime[]" value="'.htmlspecialchars($lrepTime).'">
					<input type="Hidden" name="spare1[]" value="'.htmlspecialchars($spare1).'">
					<input type="Hidden" name="lrepDeno2[]" value="'.htmlspecialchars($lrepDeno2).'">
					<input type="Hidden" name="lrepDenod2[]" value="'.htmlspecialchars($lrepDenod2).'">
					<input type="Hidden" name="lrepDeno4[]" value="'.htmlspecialchars($lrepDeno4).'">
					<input type="Hidden" name="lrepDenod4[]" value="'.htmlspecialchars($lrepDenod4).'">
					<input type="Hidden" name="atmNo[]" value="'.htmlspecialchars($atmNo).'">
					<input type="Hidden" name="fileDate[]" value="'.htmlspecialchars($fileDate).'">
					<input type="Hidden" name="remark[]" value="'.htmlspecialchars($remark).'">';	
				}
			}
		}
		print '
						</tbody>
					</table>
				</div>
				
				<div style="text-align: center; padding: 24px;">
					<button type="submit" name="action" value="Process" class="btn-modern btn-primary" style="padding: 12px 48px; font-size: 16px;">
						🚀 Process Import
					</button>
				</div>
			</form>
		</div>';
		
		fclose($fp);
	}

	if ($msg <> '') {
		print '<script>alert("'.addslashes($msg).'");</script>';
	}
}
//--- END   : RETRIEVE IBS FILE ---------------------------------------------------------------------------------------------

//--- BEGIN : IMPORT IBS FILE -----------------------------------------------------------------------------------------------
if ($action1 == "Import") {

	$recRead=0; $recExist=0; $recWrite=0;
	if (count($atmNo) <> 0) {
		$totalRec = count($atmNo);
		for ($i = 0; $i < count($atmNo); $i++) {
			$recRead++;
			//--- BEGIN	:	Create IBS file
			$sSQL = "";
			$importedBy	= $_SESSION['Session.userName'];
			$importedDate = date("Y-m-d H:i:s");  

			if ($lrepDeno2[$i] > 0) {
				$percentD2 = ($lrepDenod2[$i] / $lrepDeno2[$i]) * 100; 
			} else  { 
				$percentD2 = 0; 
			}
			
			if ($lrepDeno4[$i] > 0) { 
				$percentD4 = ($lrepDenod4[$i] / $lrepDeno4[$i]) * 100; 
			} else  { 
				$percentD4 = 0; 
			}
			
			$sSQL	= "INSERT INTO cash (" . 
								  "brNo," . 
						          "seqNo," . 
								  "model," . 
						          "location," . 
								  "lrepDate," . 
						          "lrepTime," . 
						          "spare1," . 
						          "lrepDeno2," . 
						          "lrepDenod2," . 
								  "percentD2," .
						          "lrepDeno4," . 
						          "lrepDenod4," . 
								  "percentD4," . 
						          "atmNo," . 
						          "remark," . 
						          "fileDate," . 
						          "importedDate," . 
						          "importedBy)" . 	
						          " VALUES (" . 
						          tosql($brNo[$i], "Text") . "," .			  
								  tosql($seqNo[$i], "Text") . "," .			  
						          tosql($model[$i], "Text") . "," .		
								  tosql($location[$i], "Text") . "," .			  	  
						          tosql($lrepDate[$i], "Text") . "," .			  
						          tosql($lrepTime[$i], "Text") . "," .
								  tosql($spare1[$i], "Text") . "," .
								  tosql($lrepDeno2[$i], "Number") . "," .
						          tosql($lrepDenod2[$i], "Number") . ",".
						          tosql($percentD2, "Number") . ",".
								  tosql($lrepDeno4[$i], "Number") . "," .
						          tosql($lrepDenod4[$i], "Number") . ",".
						          tosql($percentD4, "Number") . ",".
								  tosql($atmNo[$i], "Text") . "," .
								  tosql($remark[$i], "Text") . "," .
								  tosql($fileDate[$i], "Text") . "," .
								  tosql($importedDate, "Text") . "," .
						          tosql($importedBy, "Text") . ")";
			$rs = $conn->Execute($sSQL);
			if ($rs === false) { 
				$recExist++;
			} else {
				$recWrite++;
			}
		}
		print '	<script>
				alert("File ('.addslashes($sourcefile).') successfully imported!\\n Record Read ['.$recRead.']  Record Imported ['.$recWrite.']  Import Error ['.$recExist.'] ");
				window.location.href = \'index.php\';
			</script>';
	} else {
		print '	<script>
					alert("File ('.addslashes($sourcefile).') cannot be imported.!");
					window.location.href = \'import.php\';
				</script>';
	}
}
//--- END   : IMPORT IBS FILE -----------------------------------------------------------------------------------------------

include("footer.php");

?>