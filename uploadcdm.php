<?php

include("header.php");
include("forms.php");

if ($_SESSION['Session.userID'] == "" or $_SESSION['Session.isUD'] == "0") {
	print '<script>alert("' . $errPage . '");window.location.href="index.php";</script>';
}

// FIX FOR UNDEFINED VARIABLES - GET ALL POST/GET DATA
$action = isset($_POST['action']) ? $_POST['action'] : '';
$action1 = isset($_POST['action1']) ? $_POST['action1'] : '';
$sourcefile = isset($_POST['sourcefile']) ? $_POST['sourcefile'] : '';
$msg = '';
$errDesc = '';
$nfDesc = '';

// Get all column data from CSV
$col0 = isset($_POST['col0']) ? $_POST['col0'] : array();
$col1 = isset($_POST['col1']) ? $_POST['col1'] : array();
$col2 = isset($_POST['col2']) ? $_POST['col2'] : array();
$col3 = isset($_POST['col3']) ? $_POST['col3'] : array();
$col4 = isset($_POST['col4']) ? $_POST['col4'] : array();
$col5 = isset($_POST['col5']) ? $_POST['col5'] : array();
$col6 = isset($_POST['col6']) ? $_POST['col6'] : array();
$col7 = isset($_POST['col7']) ? $_POST['col7'] : array();
$col8 = isset($_POST['col8']) ? $_POST['col8'] : array();
$col9 = isset($_POST['col9']) ? $_POST['col9'] : array();
$col10 = isset($_POST['col10']) ? $_POST['col10'] : array();
$col11 = isset($_POST['col11']) ? $_POST['col11'] : array();
$col12 = isset($_POST['col12']) ? $_POST['col12'] : array();
$col13 = isset($_POST['col13']) ? $_POST['col13'] : array();
$col14 = isset($_POST['col14']) ? $_POST['col14'] : array();
$col15 = isset($_POST['col15']) ? $_POST['col15'] : array();
$col16 = isset($_POST['col16']) ? $_POST['col16'] : array();
$col17 = isset($_POST['col17']) ? $_POST['col17'] : array();
$col18 = isset($_POST['col18']) ? $_POST['col18'] : array();
$col19 = isset($_POST['col19']) ? $_POST['col19'] : array();
$col20 = isset($_POST['col20']) ? $_POST['col20'] : array();
$col21 = isset($_POST['col21']) ? $_POST['col21'] : array();

if ($_SESSION['Session.groupID'] == "0" or $_SESSION['Session.groupID'] == "1") {
	$vendorID = $_SESSION['Session.branchID'];
}

?>

<style>
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	.upload-container {
		max-width: 1400px;
		margin: 0 auto;
		padding: 24px;
		background: #f5f7fa;
		min-height: 100vh;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	.upload-header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-radius: 16px;
		padding: 32px;
		margin-bottom: 24px;
		box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
		color: white;
	}

	.upload-title {
		font-size: 28px;
		font-weight: 700;
		margin-bottom: 8px;
		letter-spacing: -0.5px;
	}

	.upload-subtitle {
		font-size: 14px;
		opacity: 0.9;
		font-weight: 500;
	}

	.upload-card {
		background: white;
		border-radius: 16px;
		padding: 32px;
		margin-bottom: 24px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.file-drop-zone {
		border: 3px dashed #e2e8f0;
		border-radius: 16px;
		padding: 48px 32px;
		text-align: center;
		background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
		transition: all 0.3s ease;
		cursor: pointer;
		position: relative;
		overflow: hidden;
	}

	.file-drop-zone:hover {
		border-color: #667eea;
		background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
		transform: translateY(-2px);
		box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
	}

	.file-drop-zone::before {
		content: '📁';
		font-size: 64px;
		display: block;
		margin-bottom: 16px;
		animation: float 3s ease-in-out infinite;
	}

	@keyframes float {

		0%,
		100% {
			transform: translateY(0px);
		}

		50% {
			transform: translateY(-10px);
		}
	}

	.file-input-label {
		font-size: 18px;
		font-weight: 700;
		color: #1a1a1a;
		margin-bottom: 8px;
		display: block;
	}

	.file-input-hint {
		font-size: 14px;
		color: #64748b;
		margin-bottom: 20px;
	}

	.file-input-wrapper {
		position: relative;
		display: inline-block;
	}

	.file-input {
		opacity: 0;
		position: absolute;
		z-index: -1;
	}

	.file-input-btn {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 14px 32px;
		border-radius: 12px;
		font-size: 15px;
		font-weight: 700;
		cursor: pointer;
		transition: all 0.3s ease;
		display: inline-block;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
		border: none;
	}

	.file-input-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
	}

	.action-buttons {
		display: flex;
		justify-content: center;
		gap: 12px;
		margin-top: 24px;
	}

	.btn {
		padding: 12px 32px;
		border: none;
		border-radius: 10px;
		font-size: 14px;
		font-weight: 700;
		cursor: pointer;
		transition: all 0.2s;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
	}

	.btn-primary {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.btn-secondary {
		background: #64748b;
		color: white;
		box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
	}

	.format-section {
		background: white;
		border-radius: 16px;
		padding: 24px;
		margin-top: 24px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.format-title {
		font-size: 20px;
		font-weight: 700;
		color: #1a1a1a;
		margin-bottom: 20px;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 12px;
	}

	.format-title::before {
		content: '📋';
		font-size: 24px;
	}

	.format-table-wrapper {
		overflow-x: auto;
		border-radius: 12px;
		border: 1px solid #e2e8f0;
	}

	.format-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		font-size: 13px;
	}

	.format-table th {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 12px 8px;
		text-align: center;
		font-weight: 700;
		font-size: 11px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		border: 1px solid rgba(255, 255, 255, 0.1);
	}

	.format-table td {
		padding: 10px 8px;
		text-align: center;
		border: 1px solid #e2e8f0;
		background: white;
		font-size: 12px;
		color: #475569;
	}

	.format-table tbody tr:hover {
		background: #f8fafc;
	}

	.section-header {
		background: #f1f5f9 !important;
		color: #1e293b !important;
		font-weight: 700;
	}

	.data-preview-card {
		background: white;
		border-radius: 16px;
		padding: 24px;
		margin-top: 24px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.preview-title {
		font-size: 20px;
		font-weight: 700;
		color: #1a1a1a;
		margin-bottom: 20px;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 12px;
	}

	.preview-title::before {
		content: '👀';
		font-size: 24px;
	}

	.preview-filename {
		display: inline-block;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 6px 16px;
		border-radius: 8px;
		font-size: 14px;
		font-weight: 600;
	}

	.data-table-wrapper {
		overflow-x: auto;
		border-radius: 12px;
		border: 1px solid #e2e8f0;
		max-height: 500px;
		overflow-y: auto;
	}

	.data-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		font-size: 13px;
	}

	.data-table td {
		padding: 12px 8px;
		text-align: center;
		border: 1px solid #e2e8f0;
		background: white;
		font-size: 12px;
		color: #475569;
		font-weight: 500;
	}

	.data-table tr:nth-child(even) td {
		background: #f8fafc;
	}

	.data-table tr:hover td {
		background: #eff6ff;
	}

	.result-card {
		background: white;
		border-radius: 16px;
		padding: 32px;
		margin-top: 24px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.result-title {
		font-size: 22px;
		font-weight: 700;
		color: #1a1a1a;
		margin-bottom: 24px;
		text-align: center;
	}

	.result-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 16px;
	}

	.result-item {
		background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
		padding: 20px;
		border-radius: 12px;
		border: 2px solid #e2e8f0;
		transition: all 0.3s ease;
	}

	.result-item:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
		border-color: #667eea;
	}

	.result-label {
		font-size: 12px;
		color: #64748b;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-bottom: 8px;
	}

	.result-value {
		font-size: 28px;
		font-weight: 700;
		color: #1a1a1a;
	}

	.result-created {
		background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
		border-color: #86efac;
	}

	.result-created .result-value {
		color: #16a34a;
	}

	.result-error {
		background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
		border-color: #fca5a5;
	}

	.result-error .result-value {
		color: #dc2626;
	}

	.result-notfound {
		background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
		border-color: #fcd34d;
	}

	.result-notfound .result-value {
		color: #d97706;
	}

	.result-details {
		font-size: 13px;
		color: #475569;
		margin-top: 8px;
		line-height: 1.6;
	}

	.alert-info {
		background: #eff6ff;
		border-left: 4px solid #3b82f6;
		padding: 16px 20px;
		border-radius: 8px;
		margin-bottom: 20px;
		font-size: 14px;
		color: #1e40af;
		font-weight: 500;
	}

	@media (max-width: 768px) {
		.upload-container {
			padding: 16px;
		}

		.upload-header {
			padding: 24px;
		}

		.upload-card,
		.format-section,
		.data-preview-card,
		.result-card {
			padding: 20px;
		}

		.action-buttons {
			flex-direction: column;
		}

		.btn {
			width: 100%;
		}

		.result-grid {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="upload-container">

	<!-- Page Header -->
	<div class="upload-header">
		<h1 class="upload-title">COLLECTION CDM/CICO - UPLOAD FILE</h1>
		<p class="upload-subtitle">Upload your CSV file to process collection data</p>
	</div>

	<!-- Upload Form -->
	<div class="upload-card">
		<form name="MyATM" action="uploadcdm.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action">

			<div class="file-drop-zone">
				<label class="file-input-label">Select CSV File</label>
				<p class="file-input-hint">Drag and drop or click to browse</p>

				<div class="file-input-wrapper">
					<input type="file" name="uploadFile" id="uploadFile" class="file-input" accept=".csv">
					<label for="uploadFile" class="file-input-btn">Choose File</label>
				</div>
			</div>

			<div class="action-buttons">
				<button type="submit" name="action" class="btn btn-primary" value="OK">Upload & Preview</button>
				<button type="button" class="btn btn-secondary"
					onclick="window.location.href='collect.php';">Kembali</button>
			</div>
		</form>
	</div>

	<!-- Format Requirements -->
	<div class="format-section">
		<h2 class="format-title">Required CSV Format</h2>

		<div class="alert-info">
			💡 Please ensure your CSV file follows this exact format before uploading
		</div>

		<div class="format-table-wrapper">
			<table class="format-table">
				<thead>
					<tr>
						<th rowspan="2">BIL</th>
						<th rowspan="2">CDM NO</th>
						<th rowspan="2">COLLECTION<br>DATE</th>
						<th rowspan="2">BANK-IN<br>DATE</th>
						<th rowspan="2">CDM LOC</th>
						<th colspan="8" class="section-header">UNLOAD (PHYSICAL)</th>
						<th colspan="8" class="section-header">UNLOAD (LOCAL)</th>
						<th rowspan="2">REMARK</th>
					</tr>
					<tr>
						<th>RM1</th>
						<th>RM2</th>
						<th>RM5</th>
						<th>RM10</th>
						<th>RM20</th>
						<th>RM50</th>
						<th>RM100</th>
						<th>JUMLAH</th>
						<th>RM1</th>
						<th>RM2</th>
						<th>RM5</th>
						<th>RM10</th>
						<th>RM20</th>
						<th>RM50</th>
						<th>RM100</th>
						<th>JUMLAH</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>BIL</td>
						<td>0014</td>
						<td>17/08/2016</td>
						<td>18/06/2016</td>
						<td>TH KANGAR ATM LP1</td>
						<td>1.00</td>
						<td>2.00</td>
						<td>5.00</td>
						<td>10.00</td>
						<td>20.00</td>
						<td>50.00</td>
						<td>100.00</td>
						<td>188.00</td>
						<td>1.00</td>
						<td>2.00</td>
						<td>5.00</td>
						<td>10.00</td>
						<td>20.00</td>
						<td>50.00</td>
						<td>100.00</td>
						<td>188.00</td>
						<td>XXXX XXXX XXX XXXX</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php

	//--- BEGIN : RETRIEVE UPLOAD CSV INFORMATION
	if ($action <> "") {
		$allowedExtension = array("csv");

		$fileName = isset($_FILES['uploadFile']['name']) ? $_FILES['uploadFile']['name'] : '';
		$tmpName = isset($_FILES['uploadFile']['tmp_name']) ? $_FILES['uploadFile']['tmp_name'] : '';
		$fileSize = isset($_FILES['uploadFile']['size']) ? $_FILES['uploadFile']['size'] : 0;
		$fileType = isset($_FILES['uploadFile']['type']) ? $_FILES['uploadFile']['type'] : '';

		if ($fileName != '') {
			$fileExt = explode('.', $fileName);

			if (!in_array($fileExt[1], $allowedExtension)) {
				$msg = 'Filename not allow to upload (' . $fileName . ') only csv.';
			} elseif ($fileSize > 1000000) {
				$msg = 'Document size (' . $fileName . ') must be less than 1M.';
			} else {
				$fp = @fopen($tmpName, "r");
				$buffer = "";
				if (!$fp) {
					$msg = 'ERROR opening file.';
				} else {
					echo '
				<div class="data-preview-card">
					<h2 class="preview-title">Data Retrieved from <span class="preview-filename">' . $fileName . '</span></h2>
					
					<form name="MyATMCSV" action="uploadcdm.php" method="post">
						<input type="hidden" name="sourcefile" value="' . $fileName . '">
						
						<div class="data-table-wrapper">
							<table class="data-table">';

					$a = 0;
					while (!feof($fp)) {
						$buffer = str_replace("\"", "", fgets($fp, 4096));
						$content = explode(",", $buffer);
						if (count($content) <> 0) {
							if ($content[0] <> '') {
								echo '<tr>';
								for ($z = 0; $z < count($content); $z++) {
									$col = "col" . $z;
									echo '<td>' . $content[$z] . '</td>';
									echo '<input type="hidden" name="' . $col . '[]" value="' . $content[$z] . '">';
								}
								echo '</tr>';
								$a++;
							}
						}
					}

					echo '
							</table>
						</div>
						
						<div class="action-buttons">
							<button type="submit" name="action1" value="Process" class="btn btn-primary">Process Data</button>
							<button type="button" class="btn btn-secondary" onclick="window.location.href=\'uploadcdm.php\';">Cancel</button>
						</div>
					</form>
				</div>';
				}
				fclose($fp);
			}
		} else {
			$msg = 'Please select a CSV file to upload.';
		}

		if ($msg <> '') {
			echo '<script>alert("' . $msg . '");</script>';
		}
	}
	//--- END : RETRIEVE UPLOAD CSV INFORMATION
	
if ($action1 <> '') {
    $rec_read = 0;
    $rec_cre = 0;
    $rec_err = 0;
    $rec_nf = 0;

    for ($z = 1; $z < count($col1); $z++) {
        // Skip header row
        if (strtoupper(trim($col1[$z])) == 'BIL') {
            continue;
        }
        
        $rec_read++;

        // Look up ATM - remove vendor restriction for flexibility
        $atmID = dlookup("atm", "atmID", "[type] IN ('CDM','CICO') AND atmNo=" . tosql(sprintf('%04s', $col1[$z]), "Text"));
        
        if ($atmID <> '') {
            // Get vendor from the ATM itself
            $vendorID = dlookup("atm", "vendorID", "atmID=" . tosql($atmID, "Text"));
            $vendorCode = dlookup("vendor", "vendorCode", "vendorID=" . tosql($vendorID, "Text"));

            // Calculate quantities and values from PHYSICAL columns (col5-col12)
            $v100 = strval($col11[$z]);
            $qty100 = strval($col11[$z] / 100);
            $v50 = strval($col10[$z]);
            $qty50 = strval($col10[$z] / 50);
            $v20 = strval($col9[$z]);
            $qty20 = strval($col9[$z] / 20);
            $v10 = strval($col8[$z]);
            $qty10 = strval($col8[$z] / 10);
            $v5 = strval($col7[$z]);
            $qty5 = strval($col7[$z] / 5);
            $v2 = strval($col6[$z]);
            $qty2 = strval($col6[$z] / 2);
            $v1 = strval($col5[$z]);
            $qty1 = strval($col5[$z] / 1);
            $tvalue = strval($col12[$z]);

            // Generate document number
            $docNo = "C" . sprintf('%04s', $col1[$z]) . date('Ymd') . $vendorCode;
            
            // Parse dates
            $cd = explode('/', $col2[$z]);
            if (count($cd) == 3) {
                $collectDate = sprintf("%04d", $cd[2]) . '-' . sprintf("%02d", $cd[1]) . '-' . sprintf("%02d", $cd[0]);
            } else {
                echo "<div style='background:red;color:white;padding:5px;'>Invalid collection date for ATM " . $col1[$z] . "</div>";
                $rec_err++;
                $errDesc .= '<br>' . sprintf('%04s', $col1[$z]);
                continue;
            }
            
            

            // Build SQL INSERT based on ACTUAL database columns
$sSQL = "INSERT INTO collect (" .
    "collectDate," .
    "atmID," .
    "vendorID," .
    "qty100," .
    "qty50," .
    "qty20," .
    "qty10," .
    "qty5," .
    "qty2," .
    "qty1," .
    "value100," .
    "value50," .
    "value20," .
    "value10," .
    "value5," .
    "value2," .
    "value1," .
    "totalValue," .
    "docNo," .
    "createdDate," .
    "createdBy," .
    "updatedDate," .
    "updatedBy" .
    ") VALUES (" .
    tosql($collectDate, "Text") . "," .
    tosql($atmID, "Text") . "," .
    tosql($vendorID, "Text") . "," .
    tosql($qty100, "Text") . "," .
    tosql($qty50, "Text") . "," .
    tosql($qty20, "Text") . "," .
    tosql($qty10, "Text") . "," .
    tosql($qty5, "Text") . "," .
    tosql($qty2, "Text") . "," .
    tosql($qty1, "Text") . "," .
    tosql($v100, "Text") . "," .
    tosql($v50, "Text") . "," .
    tosql($v20, "Text") . "," .
    tosql($v10, "Text") . "," .
    tosql($v5, "Text") . "," .
    tosql($v2, "Text") . "," .
    tosql($v1, "Text") . "," .
    tosql($tvalue, "Text") . "," .
    tosql($docNo, "Text") . "," .
    tosql(date("Y-m-d H:i:s"), "Text") . "," .
    tosql($_SESSION['Session.userName'], "Text") . "," .
    tosql(date("Y-m-d H:i:s"), "Text") . "," .
    tosql($_SESSION['Session.userName'], "Text") . ")";
            // Execute the insert
            $rsI = $conn->Execute($sSQL);
            
            if ($rsI === false) {
                $rec_err++;
                $errDesc .= '<br>' . sprintf('%04s', $col1[$z]);
                
                // Log the error
                echo "<div style='background:red;color:white;padding:10px;margin:5px;'>";
                echo "SQL ERROR for ATM " . $col1[$z] . ": " . $conn->ErrorMsg() . "</div>";
                
                $errSQL = "INSERT INTO errorlogs (errDate, errSource, errQry, createdBy) VALUES (" .
                    tosql(date("Y-m-d H:i:s"), "Text") . "," .
                    tosql($sourcefile, "Text") . "," .
                    tosql($sSQL, "Text") . "," .
                    tosql($_SESSION['Session.userName'], "Text") . ")";
                $conn->Execute($errSQL);
            } else {
                $rec_cre++;
            }
        } else {
            $rec_nf++;
            $nfDesc .= '<br>' . sprintf('%04s', $col1[$z]);
        }
    }

    // Create log
    $user = $_SESSION['Session.userName'];
    $event = "Collection CDM/CICO - Upload File (" . $sourcefile . ") - Kemaskini (" . $rec_cre . ") Ralat (" . $rec_err . ")";
    $type = $_SESSION['Session.groupName'];
    createLog($user, $event, $type);

    // Display results
    echo '
    <div class="result-card">
        <h2 class="result-title">Processing Results</h2>
        
        <div style="text-align: center; margin-bottom: 24px;">
            <span class="preview-filename">' . $sourcefile . '</span>
        </div>
        
        <div class="result-grid">
            <div class="result-item">
                <div class="result-label">Records Read</div>
                <div class="result-value">' . $rec_read . '</div>
            </div>
            
            <div class="result-item result-created">
                <div class="result-label">✓ Successfully Created</div>
                <div class="result-value">' . $rec_cre . '</div>
            </div>
            
            <div class="result-item result-notfound">
                <div class="result-label">⚠ Not Found</div>
                <div class="result-value">' . $rec_nf . '</div>';
    if ($nfDesc) {
        echo '<div class="result-details"><strong>ATM Numbers:</strong>' . $nfDesc . '</div>';
    }
    echo '
            </div>
            
            <div class="result-item result-error">
                <div class="result-label">✕ Errors</div>
                <div class="result-value">' . $rec_err . '</div>';
    if ($errDesc) {
        echo '<div class="result-details"><strong>Failed Records:</strong>' . $errDesc . '</div>';
    }
    echo '
            </div>
        </div>
        
        <div class="action-buttons">
            <button type="button" class="btn btn-primary" onclick="window.location.href=\'collect.php\';">View Collections</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href=\'uploadcdm.php\';">Upload Another File</button>
        </div>
    </div>';
}
?>

</div>

<script>
	// File input visual feedback
	document.getElementById('uploadFile')?.addEventListener('change', function (e) {
		const fileName = e.target.files[0]?.name || 'No file chosen';
		const label = document.querySelector('.file-input-btn');
		if (label) {
			label.textContent = fileName;
			label.style.background = 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)';
		}
	});

	// Drag and drop functionality
	const dropZone = document.querySelector('.file-drop-zone');
	const fileInput = document.getElementById('uploadFile');

	if (dropZone && fileInput) {
		['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
			dropZone.addEventListener(eventName, preventDefaults, false);
		});

		function preventDefaults(e) {
			e.preventDefault();
			e.stopPropagation();
		}

		['dragenter', 'dragover'].forEach(eventName => {
			dropZone.addEventListener(eventName, highlight, false);
		});

		['dragleave', 'drop'].forEach(eventName => {
			dropZone.addEventListener(eventName, unhighlight, false);
		});

		function highlight(e) {
			dropZone.style.borderColor = '#667eea';
			dropZone.style.background = 'linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%)';
		}

		function unhighlight(e) {
			dropZone.style.borderColor = '#e2e8f0';
			dropZone.style.background = 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)';
		}

		dropZone.addEventListener('drop', handleDrop, false);

		function handleDrop(e) {
			const dt = e.dataTransfer;
			const files = dt.files;

			if (files.length > 0) {
				fileInput.files = files;
				const event = new Event('change', { bubbles: true });
				fileInput.dispatchEvent(event);
			}
		}

		// Make entire drop zone clickable
		dropZone.addEventListener('click', function (e) {
			if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
				fileInput.click();
			}
		});
	}
</script>

<?php
include("footer.php");
?>