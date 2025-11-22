<?php

include("header.php");	
include("forms.php");

$sFileName = 'reports.php';
$sFileRef  = 'reports.php';
$title     = 'LAPORAN PELBAGAI';

// Initialize variables with proper defaults
$ddFrom = (isset($_GET['ddFrom']) && $_GET['ddFrom'] != '') ? $_GET['ddFrom'] : date("j");
$mmFrom = (isset($_GET['mmFrom']) && $_GET['mmFrom'] != '') ? $_GET['mmFrom'] : date("n");
$yyFrom = (isset($_GET['yyFrom']) && $_GET['yyFrom'] != '') ? $_GET['yyFrom'] : date("Y");
$ddTo   = (isset($_GET['ddTo']) && $_GET['ddTo'] != '') ? $_GET['ddTo'] : date("j");
$mmTo   = (isset($_GET['mmTo']) && $_GET['mmTo'] != '') ? $_GET['mmTo'] : date("n");
$yyTo   = (isset($_GET['yyTo']) && $_GET['yyTo'] != '') ? $_GET['yyTo'] : date("Y");
$ty     = isset($_GET['ty']) ? $_GET['ty'] : '';

$dd0 = $_REQUEST['dd0'] ?? date("j");
$mm0 = $_REQUEST['mm0'] ?? date("n");
$yy0 = $_REQUEST['yy0'] ?? date("Y");

$mm1 = $_REQUEST['mm1'] ?? date("n");
$yy1 = $_REQUEST['yy1'] ?? date("Y");
$mm2 = $_REQUEST['mm2'] ?? date("n");
$yy2 = $_REQUEST['yy2'] ?? date("Y");
$yy  = $_REQUEST['yy'] ?? date("Y");

//$ty  = $_REQUEST['ty'] ?? '';
$ty1 = $_REQUEST['ty1'] ?? '';
$st  = $_REQUEST['st'] ?? '';
$st1 = $_REQUEST['st1'] ?? '';
$atm = $_REQUEST['atm'] ?? '';
$vid = $_REQUEST['vid'] ?? '';

// Session check
if (!isset($_SESSION['Session.groupID']) || $_SESSION['Session.groupID'] == "") {
	$errPage = $errPage ?? 'Unauthorized access';
	print '<script>alert("'.addslashes($errPage).'");window.location="index.php";</script>';
	exit;
}

//--- BEGIN : PREPARE VENDOR LISTING --------------------------------------------------------------------------------------------
$vendorList = Array();
$vendorVal = Array();
$sSQL = "SELECT vendorID, vendorCode, vendorName FROM vendor WHERE isDeleted = '0' ORDER BY vendorName";
$GetList = $conn->Execute($sSQL);
if ($GetList && $GetList->RowCount() <> 0){
	while (!$GetList->EOF) {
		array_push($vendorList, $GetList->fields('vendorName').'  ('.$GetList->fields('vendorCode').')');
		array_push($vendorVal, $GetList->fields('vendorID'));
		$GetList->MoveNext();
	}
}
//--- END   : PREPARE VENDOR LISTING --------------------------------------------------------------------------------------------

print '
<style>
	* { box-sizing: border-box; }
	
	.reports-container {
		max-width: 1200px;
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
	
	.filter-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		padding: 20px 24px;
		margin-bottom: 20px;
	}
	
	.filter-row {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 12px;
		flex-wrap: wrap;
	}
	
	.filter-label {
		font-size: 14px;
		font-weight: 500;
		color: #4a5568;
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
	
	.select-modern:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.input-modern {
		padding: 8px 12px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
		transition: all 0.2s;
		width: 80px;
	}
	
	.input-modern:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.reports-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
		gap: 20px;
		margin-bottom: 20px;
	}
	
	.report-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
		transition: all 0.3s;
	}
	
	.report-card:hover {
		box-shadow: 0 4px 12px rgba(0,0,0,0.12);
		transform: translateY(-2px);
	}
	
	.report-header {
		background: #475569;
		color: white;
		padding: 16px 20px;
		font-size: 14px;
		font-weight: 600;
		display: flex;
		align-items: center;
		gap: 10px;
	}
	
	.report-body {
		padding: 20px;
	}
	
	.report-title {
		font-size: 15px;
		font-weight: 600;
		color: #1a202c;
		margin: 0 0 12px 0;
	}
	
	.report-actions {
		display: flex;
		gap: 8px;
		flex-wrap: wrap;
	}
	
	.report-btn {
		padding: 8px 16px;
		border: none;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
		display: inline-flex;
		align-items: center;
		gap: 6px;
		text-decoration: none;
	}
	
	.btn-pdf {
		background: #ef4444;
		color: white;
	}
	
	.btn-pdf:hover {
		background: #dc2626;
	}
	
	.btn-excel {
		background: #10b981;
		color: white;
	}
	
	.btn-excel:hover {
		background: #059669;
	}
	
	.btn-html {
		background: #3b82f6;
		color: white;
	}
	
	.btn-html:hover {
		background: #2563eb;
	}
	
	.section-divider {
		border: none;
		height: 2px;
		background: linear-gradient(to right, transparent, #e2e8f0, transparent);
		margin: 24px 0;
	}
	
	.filter-section-title {
		font-size: 15px;
		font-weight: 600;
		color: #1a202c;
		margin-bottom: 12px;
		padding-bottom: 8px;
		border-bottom: 2px solid #e2e8f0;
	}
	
	@media (max-width: 768px) {
		.reports-container {
			padding: 12px;
		}
		
		.reports-grid {
			grid-template-columns: 1fr;
		}
		
		.filter-row {
			flex-direction: column;
			align-items: stretch;
		}
		
		.select-modern,
		.input-modern {
			width: 100%;
		}
	}
</style>

<div class="reports-container">
	<div class="page-header-modern">
		<h1 class="page-title-modern">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
				<polyline points="14 2 14 8 20 8"/>
				<line x1="16" y1="13" x2="8" y2="13"/>
				<line x1="16" y1="17" x2="8" y2="17"/>
				<polyline points="10 9 9 9 8 9"/>
			</svg>
			'.strtoupper($title).'
		</h1>
		<p class="page-subtitle">Jana dan muat turun pelbagai laporan sistem ATM</p>
	</div>

	<form name="MyZSSB" action="'.$sFileName.'" method="post">
		<input type="hidden" name="action">
		
		<div class="filter-card">
			<div class="filter-row">
				<span class="filter-label">Syarikat:</span>';

if ($_SESSION['Session.groupID'] == '0' OR $_SESSION['Session.groupID'] == '1') {	
	print '		<input type="hidden" name="vid" value="'.htmlspecialchars($_SESSION['Session.branchID']).'">
				<strong>'.htmlspecialchars($_SESSION['Session.branchName']).'</strong>';
} else {
	print '		<select name="vid" class="select-modern" onchange="document.MyZSSB.submit();">
					<option value="">- Semua -</option>';
	for ($i = 0; $i < count($vendorList); $i++) {
		print '		<option value="'.htmlspecialchars($vendorVal[$i]).'"';
		if ($vid == $vendorVal[$i]) print ' selected';
		print '>'.htmlspecialchars($vendorList[$i]).'</option>';
	}			
	print '		</select>';
}

print '			</div>
		</div>
		
		<div class="filter-card">
			<div class="filter-section-title">📅 Filter Berdasarkan Tarikh</div>
			<div class="filter-row">
				<span class="filter-label">Tarikh Dari:</span>
				<select name="ddFrom" class="select-modern">';
for ($d = 1; $d < 32; $d++) {
	print '			<option value="'.$d.'"';
	if ($ddFrom == $d) print ' selected';
	print '>'.$d.'</option>';
}
print '			</select>
				<select name="mmFrom" class="select-modern">';
for ($m = 1; $m < 13; $m++) {
	print '			<option value="'.$m.'"';
	if ($mmFrom == $m) print ' selected';
	print '>'.$m.'</option>';
}
print '			</select>
				<input type="text" name="yyFrom" maxlength="4" value="'.htmlspecialchars($yyFrom).'" class="input-modern">
				
				<span class="filter-label" style="margin-left: 12px;">Hingga:</span>
				<select name="ddTo" class="select-modern">';
for ($d = 1; $d < 32; $d++) {
	print '			<option value="'.$d.'"';
	if ($ddTo == $d) print ' selected';
	print '>'.$d.'</option>';
}
print '			</select>
				<select name="mmTo" class="select-modern">';
for ($m = 1; $m < 13; $m++) {
	print '			<option value="'.$m.'"';
	if ($mmTo == $m) print ' selected';
	print '>'.$m.'</option>';
}
print '			</select>
				<input type="text" name="yyTo" maxlength="4" value="'.htmlspecialchars($yyTo).'" class="input-modern">
				
				<span class="filter-label" style="margin-left: 12px;">Jenis:</span>
				<select name="ty" class="select-modern">
					<option value="">- Semua -</option>';
if (isset($atmList)) {
	for ($s = 0; $s < count($atmList); $s++) {
		print '		<option value="'.$s.'"';
		if ($ty == $s) print ' selected';
		print '>'.htmlspecialchars($atmList[$s]).'</option>';
	}
}
print '			</select>
			</div>
		</div>
		
		<div class="reports-grid">
			<div class="report-card">
				<div class="report-header">
					📊 Laporan Berdasarkan Tarikh
				</div>
				<div class="report-body">
					<h3 class="report-title">Arahan Denominasi</h3>
					<div class="report-actions">
						<a href="#" onclick="selectRpt(\'rptA\'); return false;" class="report-btn btn-pdf">
							📄 PDF
						</a>
						<a href="#" onclick="selectRpt(\'rptAE\'); return false;" class="report-btn btn-excel">
							📊 Excel
						</a>
					</div>
					
				</div>
			</div>
		</div>
		
		<div class="filter-card">
			<div class="filter-section-title">📅 Filter Berdasarkan Terminal & Bulan</div>
			<div class="filter-row">
				<span class="filter-label">Terminal:</span>
				<input type="text" name="atm" value="'.htmlspecialchars($atm).'" maxlength="5" class="input-modern" placeholder="No ATM">
				
				<span class="filter-label" style="margin-left: 12px;">Dari:</span>
				<select name="mm1" class="select-modern">';
for ($m = 1; $m < 13; $m++) {
	print '			<option value="'.$m.'"';
	if ($mm1 == $m) print ' selected';
	print '>'.$m.'</option>';
}
print '			</select>
				<input type="text" name="yy1" maxlength="4" value="'.htmlspecialchars($yy1).'" class="input-modern">
				
				<span class="filter-label" style="margin-left: 12px;">Hingga:</span>
				<select name="mm2" class="select-modern">';
for ($m = 1; $m < 13; $m++) {
	print '			<option value="'.$m.'"';
	if ($mm2 == $m) print ' selected';
	print '>'.$m.'</option>';
}
print '			</select>
				<input type="text" name="yy2" maxlength="4" value="'.htmlspecialchars($yy2).'" class="input-modern">
				
				<span class="filter-label" style="margin-left: 12px;">Jenis:</span>
				<select name="ty1" class="select-modern">
					<option value="">- Semua -</option>';
if (isset($atmList)) {
	for ($s = 0; $s < count($atmList); $s++) {
		print '		<option value="'.$s.'"';
		if ($ty1 == $s) print ' selected';
		print '>'.htmlspecialchars($atmList[$s]).'</option>';
	}
}
print '			</select>
			</div>
		</div>
		
		<div class="reports-grid">
			<div class="report-card">
				<div class="report-header">
					🏧 Laporan Mengikut Terminal
				</div>
				<div class="report-body">
					<h3 class="report-title">Arahan Denominasi Mengikut Terminal</h3>
					<div class="report-actions">
						<a href="#" onclick="selectRptMY(\'rptMA\'); return false;" class="report-btn btn-pdf">
							📄 PDF (Terperinci)
						</a>
						<a href="#" onclick="selectRptMY(\'rptMAE\'); return false;" class="report-btn btn-excel">
							📊 Excel (Terperinci)
						</a>
						<a href="#" onclick="selectRptMY(\'rptMAS\'); return false;" class="report-btn btn-pdf">
							📄 PDF (Ringkasan)
						</a>
						<a href="#" onclick="selectRptMY(\'rptMASE\'); return false;" class="report-btn btn-excel">
							📊 Excel (Ringkasan)
						</a>
					</div>
				</div>
			</div>';

if ($_SESSION['Session.groupID'] <> '0' AND $_SESSION['Session.groupID'] <> '1') {	
	print '
			<div class="report-card">
				<div class="report-header">
					📈 Laporan KWSI
				</div>
				<div class="report-body">
					<h3 class="report-title">KWSI: Arahan/Replenish/Unloaded Mengikut ATM</h3>
					<div class="report-actions">
						<a href="#" onclick="selectRptMY(\'rptMC\'); return false;" class="report-btn btn-html">
							🌐 HTML
						</a>
						<a href="#" onclick="selectRptMY(\'rptMCE\'); return false;" class="report-btn btn-excel">
							📊 Excel
						</a>
					</div>
					
					<hr class="section-divider">
					
					<h3 class="report-title">KWSI: Kutipan Mengikut CDM</h3>
					<div class="report-actions">
						<a href="#" onclick="selectRptMY(\'rptMD\'); return false;" class="report-btn btn-html">
							🌐 HTML
						</a>
						<a href="#" onclick="selectRptMY(\'rptMDE\'); return false;" class="report-btn btn-excel">
							📊 Excel
						</a>
					</div>
				</div>
			</div>';
}

print '
		</div>
	</form>
</div>

<script>
	function selectRpt(rpt) {
		e = document.MyZSSB;	
		url = "rpt_pdf/" + rpt + ".php?vid="+e.vid.value+"&ddFrom="+e.ddFrom.value+"&mmFrom="+e.mmFrom.value+"&yyFrom="+e.yyFrom.value+"&ddTo="+e.ddTo.value+"&mmTo="+e.mmTo.value+"&yyTo="+e.yyTo.value+"&ty="+e.ty.value;
		window.open(url, "pop1", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,width=1000,height=700");		
	}	
	
	function selectRptD(rpt) {
		e = document.MyZSSB;	
		url = "rpt_pdf/" + rpt + ".php?vid="+e.vid.value+"&dmy="+e.yy0.value+"-"+e.mm0.value+"-"+e.dd0.value;
		window.open(url, "pop1", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,width=1000,height=700");
	}  
	
	function selectRptMY(rpt) {
		e = document.MyZSSB;	
		url = "rpt_pdf/" + rpt + ".php?vid="+e.vid.value+"&mm="+e.mm1.value+"&yy="+e.yy1.value+"&mmT="+e.mm2.value+"&yyT="+e.yy2.value+"&ty="+e.ty1.value+"&atm="+e.atm.value;
		window.open(url, "pop1", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,width=1000,height=700");
	}  

	function selectRptY(rpt) {
		e = document.MyZSSB;	
		url = "rpt_pdf/" + rpt + ".php?vid="+e.vid.value+"&yr="+e.yy.value;
		window.open(url, "pop1", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,width=1000,height=700");	
	}  
	
	function selectRptMgmt(rpt) {
		e = document.MyZSSB;	
		url = "rpt_pdf/" + rpt + ".php?vid="+e.vid.value;
		window.open(url, "pop1", "scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,width=1000,height=700");	
	}  
</script>';

include("footer.php");	
?>