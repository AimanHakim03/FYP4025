<?php

include("forms.php");

// Initialize variables from REQUEST with defaults
$dd = $_REQUEST['dd'] ?? date("d");
$mm = $_REQUEST['mm'] ?? date("n");
$yy = $_REQUEST['yy'] ?? date("Y");
$delDoc = $_REQUEST['delDoc'] ?? '';
$vendorID = $_REQUEST['vendorID'] ?? '';

$path = dirname(__FILE__); 

$ymd = $yy.sprintf("%02d",$mm).sprintf("%02d",$dd);

$lastDay	= date('t', mktime(0, 0, 0, $mm, '01', $yy));
$yymmdd		= date('Y-m-d', mktime(0, 0, 0, $mm, $dd, $yy));
//$conn->debug=true;

if ((isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "0") || 
    (isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "1")) {
	$vendorID = $_SESSION['Session.branchID'] ?? '';
}

//--- BEGIN : VENDOR LIST -------------------------------------------------------------------
	$vendorList = Array();
	$vendorVal  = Array();
	$sSQL = "	SELECT	V.vendorID, V.vendorCode, V.vendorName FROM vendor V  WHERE isDeleted = '0' ";
	$GetN = $conn->Execute($sSQL);
	if ($GetN->RowCount() <> 0){
		while (!$GetN->EOF) {
			array_push ($vendorList, $GetN->fields('vendorName'));
			array_push ($vendorVal, $GetN->fields('vendorID'));
			$GetN->MoveNext();
		}
	}	
//--- END   : VENDOR LIST -------------------------------------------------------------------

if ($delDoc <> '') {
	$sSQL = "UPDATE slip  SET slipAttach = NULL WHERE slipAttach = '".$delDoc."' ";
	$rsU = $conn->Execute($sSQL);
	
	//--- Begin : Create log 
	$user  = $_SESSION['Session.userName'] ?? 'Unknown';
	$event = "Host Receipt: ".$delDoc." - Deleted";
	$type  = $_SESSION['Session.groupName'] ?? 'Unknown';
	createLog($user , $event, $type);
	//--- End   : Create log 	
}

$sSQL = " 
SELECT	Z.instructDate, Z.vendorID, Z.vendorCode, Z.vendorName, 
		SUM(terminalA) AS terminalA, SUM(Z.totalValueA) AS totalValueA,
		SUM(terminalB) AS terminalB, SUM(Z.totalValueB) AS totalValueB,
		SUM(terminalC) AS terminalC, SUM(Z.totalValueC) AS totalValueC, 
		SUM(Z.status) AS statusA 
FROM 
(
SELECT	A.instructDate, B.vendorID, B.vendorCode, B.vendorName, 
		COALESCE(COUNT(*),'0') AS terminalA, COALESCE(SUM(A.totalValue),'0.00') AS totalValueA, 
		CASE WHEN (A.unload100+A.unload50+A.unload20+A.unload10) <> '0' THEN COUNT(*) ELSE 0 END AS terminalB,
		COALESCE(SUM(A.unload100*100 + A.unload50*50 + A.unload20*20 + A.unload10*10), 0.00) AS totalValueB,
		CASE WHEN (A.replenish100+A.replenish50+A.replenish20+A.replenish10) <> '0' THEN COUNT(*) ELSE 0 END AS terminalC,
		COALESCE(SUM(A.replenish100*100 + A.replenish50*50 + A.replenish20*20 + A.replenish10*10), 0.00) AS totalValueC,
		SUM(A.status) AS status 
FROM 	instruct A 
LEFT JOIN	vendor B 
ON		A.vendorID = B.vendorID
WHERE 	A.isDeleted = '0' ";

$sSQL .= "	AND CONVERT(VARCHAR(24),A.createdDate,112)= '".$ymd."' ";

if ($vendorID <> '') {
	$sSQL .= " AND	A.vendorID = ".tosql($vendorID, "Text");
}

if ((isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "0") || 
    (isset($_SESSION['Session.groupID']) && $_SESSION['Session.groupID'] == "1")) {
	$sSQL .= " AND A.status <> '0' ";	
}

$sSQL .= "
GROUP BY A.instructDate, B.vendorID, B.vendorCode, B.vendorName, 
		 A.replenish100, A.replenish50, A.replenish20, A.replenish10, A.unload100, A.unload50, A.unload20, A.unload10,A.status
) Z 
GROUP BY  Z.instructDate, Z.vendorID, Z.vendorCode, Z.vendorName
ORDER BY Z.instructDate DESC, Z.vendorCode, Z.vendorName  ";

$rsD = $conn->Execute($sSQL);	
if ($rsD === false) { 
	print '<p align="center"><hr size="1" color="#999999">'.$sSQL.'<br>
	<b class="textFont">Ralat : '.$conn->ErrorMsg().'<BR>- Sila hubungi Pentadbir Sistem -<br></b>
	<hr size="1" color="#999999"></p>';
	exit; 
}

$PHP_SELF = $_SERVER['PHP_SELF'] ?? '';

print '
<style>
.site-monthly-table {
	width: 98%;
	margin: 0 auto 2rem auto;
	border-collapse: collapse;
	background: #ffffff;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	border-radius: 8px;
	overflow: hidden;
}

.site-monthly-header {
	background: #f8fafc;
	padding: 1rem 1.25rem;
	font-weight: 700;
	font-size: 0.875rem;
	color: #0f172a;
	border-bottom: 2px solid #e2e8f0;
}

.site-monthly-header select {
	padding: 0.375rem 0.75rem;
	border: 1px solid #e2e8f0;
	border-radius: 6px;
	font-size: 0.875rem;
	font-weight: 700;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
	margin: 0 0.25rem;
}

.site-monthly-header select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.site-monthly-note {
	background: #eff6ff;
	padding: 0.75rem 1.25rem;
	font-size: 0.75rem;
	color: #1e40af;
	font-style: italic;
	border-bottom: 1px solid #dbeafe;
}

.site-monthly-subtitle {
	background: #f8fafc;
	padding: 0.75rem 1.25rem;
	font-weight: 700;
	font-size: 0.875rem;
	color: #0f172a;
	border-bottom: 1px solid #e2e8f0;
}

.site-monthly-table thead th {
	padding: 0.75rem 0.5rem;
	text-align: center;
	font-size: 0.7rem;
	font-weight: 700;
	color: #ffffff;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: nowrap;
	line-height: 1.3;
}

.site-monthly-table thead th.header-main {
	background: #8b1538;
}

.site-monthly-table thead th.header-arahan {
	background: #1e293b;
}

.site-monthly-table thead th.header-replenish {
	background: #10b981;
}

.site-monthly-table thead th.header-varian {
	background: #1e293b;
}

.site-monthly-table thead th.header-unload {
	background: #ef4444;
}

.site-monthly-table tbody td {
	padding: 0.75rem 0.5rem;
	font-size: 0.875rem;
	border-bottom: 1px solid #f1f5f9;
}

.site-monthly-table tbody tr.Data {
	background: #ffffff;
}

.site-monthly-table tbody tr.Data1 {
	background: #f8fafc;
}

.site-monthly-table tbody tr:hover {
	background: #eff6ff !important;
}

.site-monthly-table tbody tr.LabelH {
	background: #fef3c7;
	font-weight: 700;
	color: #78350f;
	border-top: 2px solid #f59e0b;
}

.date-link-monthly {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 600;
	transition: color 0.2s;
}

.date-link-monthly:hover {
	color: #2563eb;
	text-decoration: underline;
}

.status-complete-monthly {
	color: #059669;
	font-weight: 700;
}

.status-incomplete-monthly {
	color: #dc2626;
	font-weight: 700;
}

.receipt-icon {
	cursor: pointer;
	transition: transform 0.2s;
	margin: 0 0.25rem;
}

.receipt-icon:hover {
	transform: scale(1.1);
}

.empty-message-monthly {
	padding: 2rem;
	text-align: center;
	color: #64748b;
	font-size: 0.875rem;
	background: #f8fafc;
}

@media (max-width: 1200px) {
	.site-monthly-table {
		width: 100%;
		font-size: 0.75rem;
	}
	
	.site-monthly-table thead th {
		font-size: 0.65rem;
		padding: 0.5rem 0.25rem;
	}
	
	.site-monthly-table tbody td {
		padding: 0.5rem 0.25rem;
	}
}
</style>

<form name="MySD" action="'.$PHP_SELF.'" method="post">
<input type="hidden" name="delDoc">
<table class="site-monthly-table" border="0" cellpadding="5" cellspacing="1">
	<tr>
		<td class="site-monthly-header" colspan="13">
			TARIKH ARAHAN DIKELUARKAN :  
			<select name="dd" class="textFontBold" onchange="document.MySD.submit();">';
for ($d = 1; $d <= $lastDay; $d++) {
	print '		<option value="'.$d.'"';
	if ($dd == $d) print ' selected';
	print 		'>'.$d.'</option>';
}
print '	</select>		
			<select name="mm" class="textFontBold" onchange="document.MySD.submit();">';
for ($m = 0; $m < count($monthList); $m++) {
	print '		<option value="'.$monthVal[$m].'"';
	if ($mm == $monthVal[$m]) print ' selected';
	print 		'>'.strtoupper($monthList[$m]).'</option>';
}
print '	</select>		
			<select name="yy" class="textFontBold" onchange="document.MySD.submit();">';
for ($y = 0; $y < count($yrList); $y++) {
	print '		<option value="'.$yrList[$y].'"';
	if ($yy == $yrList[$y]) print ' selected';
	print 		'>'.$yrList[$y].'</option>';
}
print '	</select>';

$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
if ($sessionGroupID <> '0' && $sessionGroupID <> '1') {	
	print '		&nbsp;&nbsp;Syarikat : 
				<select name="vendorID" class="Data"  onchange="document.MySD.submit();">
					<option value="">- Semua -</option>';
	for ($i = 0; $i < count($vendorList); $i++) {
		print '		<option value="'.$vendorVal[$i].'"';
		if ($vendorID == $vendorVal[$i]) print ' selected';
		print '>'.htmlspecialchars($vendorList[$i]).'</option>';
	}			
	print '		</select>&nbsp;';
}
print '	</td>
	</tr>
	<tr>
		<td class="site-monthly-subtitle" colspan="7"><strong>ARAHAN</strong></td>
		<td class="site-monthly-note" colspan="6">
			<i class="fas fa-info-circle"></i> Note: Host Receipt - to replace new receipt, please remove first existing file.
		</td>
	</tr>
	<thead>
		<tr>
			<th rowspan="2" class="header-main" style="width: 40px;">BIL</th>
			<th rowspan="2" class="header-main" style="width: 110px;">TARIKH<br>PENGISIAN</th>
			<th rowspan="2" class="header-main" style="min-width: 100px;">SYARIKAT</th>
			<th colspan="2" class="header-arahan">ARAHAN</th>
			<th colspan="2" class="header-replenish">REPLENISH</th>
			<th colspan="2" class="header-varian">VARIAN</th>
			<th colspan="2" class="header-unload">UNLOADED</th>
			<th rowspan="2" class="header-main" style="width: 120px;">STATUS</th>
			<th rowspan="2" class="header-main" style="width: 90px;">HOST<br>RECEIPT</th>
		</tr>
		<tr>
			<th class="header-arahan" style="width: 80px;">JUMLAH<br>TERMINAL</th>
			<th class="header-arahan" style="width: 100px;">AMAUN<br>(RM)</th>
			<th class="header-replenish" style="width: 80px;">JUMLAH<br>TERMINAL</th>
			<th class="header-replenish" style="width: 100px;">AMAUN<br>(RM)</th>
			<th class="header-varian" style="width: 80px;">JUMLAH<br>TERMINAL</th>
			<th class="header-varian" style="width: 100px;">AMAUN<br>(RM)</th>
			<th class="header-unload" style="width: 80px;">JUMLAH<br>TERMINAL</th>
			<th class="header-unload" style="width: 100px;">AMAUN<br>(RM)</th>
		</tr>
	</thead>
	<tbody>';

$sd=0;$cnt=0;
$allTerminalA=0; $allValueA=0;
$allTerminalB=0; $allValueB=0;
$allTerminalC=0; $allValueC=0;
$varianQtyAll=0;

if ($rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		$strClass = ($cnt % 2 == 1) ? 'Data' : 'Data1';
		
		$terminalA = $rsD->fields('terminalA') ?? 0;
		$totalValueA = $rsD->fields('totalValueA') ?? 0;
		$terminalB = $rsD->fields('terminalB') ?? 0;
		$totalValueB = $rsD->fields('totalValueB') ?? 0;
		$terminalC = $rsD->fields('terminalC') ?? 0;
		$totalValueC = $rsD->fields('totalValueC') ?? 0;
		$statusA = $rsD->fields('statusA') ?? 0;
		$instructDate = $rsD->fields('instructDate') ?? '';
		$vendorCode = $rsD->fields('vendorCode') ?? '';
		$rsVendorID = $rsD->fields('vendorID') ?? '';
		
		$allTerminalA	+= $terminalA; 
		$allValueA		+= $totalValueA; 
		$allTerminalB	+= $terminalB; 
		$allValueB		+= $totalValueB; 
		$allTerminalC	+= $terminalC; 
		$allValueC		+= $totalValueC; 
		
		if ($terminalA > 0 && ($statusA / $terminalA) == 4) {
			$status = '<span class="status-complete-monthly">** SELESAI **</span>';
		} else {
			$status = '<span class="status-incomplete-monthly">** BELUM SELESAI **</span>';
		}
		
		$varianQty = $terminalC - $terminalA;
		$varianVal = $totalValueC - $totalValueA; 
		$varianQtyAll += $varianQty;
		
		$slipAttach='';
		$sSQL = "SELECT TOP 1 slipAttach FROM slip WHERE createdDate = '".$yymmdd."' AND instructDate = '".$instructDate."' AND vendorID = '".$rsVendorID."' ";
		$rsA = $conn->Execute($sSQL);
		if ($rsA && $rsA->RowCount() <> 0) {
			$slipAttach = $rsA->fields('slipAttach') ?? '';
		}
		
		print '
		<tr class="'.$strClass.'">
			<td align="right" style="font-weight: 600; color: #64748b;">'.$sd.'.</td>
			<td align="center">
				<a href="deno.php?dt='.$instructDate.'&vid='.$rsVendorID.'" class="date-link-monthly">
					'.todate('d/m/Y', $instructDate).'
				</a>
			</td>
			<td align="left" style="font-weight: 500;">'.htmlspecialchars($vendorCode).'</td>
			<td align="right" style="background: #f8fafc; font-weight: 700;">'.number_format($terminalA).'&nbsp;</td>
			<td align="right" style="background: #f8fafc; font-weight: 700;">'.number_format($totalValueA, 2).'&nbsp;</td>
			<td align="right" style="background: #ecfdf5; font-weight: 700;">'.number_format($terminalC).'&nbsp;</td>
			<td align="right" style="background: #ecfdf5; font-weight: 700;">'.number_format($totalValueC, 2).'&nbsp;</td>
			<td align="right" style="background: #f8fafc; font-weight: 700; color: '.($varianQty < 0 ? '#dc2626' : '#0f172a').';">'.number_format($varianQty).'&nbsp;</td>
			<td align="right" style="background: #f8fafc; font-weight: 700; color: '.($varianVal < 0 ? '#dc2626' : '#0f172a').';">'.number_format($varianVal, 2).'&nbsp;</td>
			<td align="right" style="background: #fef2f2; font-weight: 700;">'.number_format($terminalB).'&nbsp;</td>
			<td align="right" style="background: #fef2f2; font-weight: 700;">'.number_format($totalValueB, 2).'&nbsp;</td>
			<td align="center">'.$status.'</td>
			<td align="center">';
		
		if ($slipAttach == '') {
			if ($sessionGroupID == '0' || $sessionGroupID == '1' ) {	
				print '	<img src="images/upload.png" title="Upload Host Receipt" border="0" class="receipt-icon"
						onclick="Javascript:open(\'upload.php?vid='.$rsVendorID.'&dt='.$yymmdd.'&dt1='.$instructDate.'\', \'upload\', \'toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=0,width=350,height=100,left=200,top=200\');">';
			} else {
				print '<span style="color: #94a3b8;">-</span>';
			}
		} else {
			print '	<a href="files/'.htmlspecialchars($slipAttach).'" target="new">
						<img src="images/slip.png" title="View Host Receipt" border="0" class="receipt-icon">
					</a>';
			if ($sessionGroupID == '0' || $sessionGroupID == '1' ) {	
				print '	<img src="images/delete.gif" title="Delete Host Receipt" border="0" class="receipt-icon" onclick="DeleteDoc(\''.addslashes($slipAttach).'\');">';
			}
		}
		
		print '
			</td>
		</tr>';	
		$cnt++;
		$rsD->MoveNext();
	}
	
	$varianValAll = $allValueC - $allValueA;
	print '
		<tr class="LabelH">
			<td colspan="3" align="right">JUMLAH : </td>
			<td align="right">'.number_format($allTerminalA).'&nbsp;</td>
			<td align="right">'.number_format($allValueA, 2).'&nbsp;</td>
			<td align="right">'.number_format($allTerminalC).'&nbsp;</td>
			<td align="right">'.number_format($allValueC, 2).'&nbsp;</td>
			<td align="right" style="color: '.($varianQtyAll < 0 ? '#dc2626' : '#78350f').';">'.number_format($varianQtyAll).'&nbsp;</td>
			<td align="right" style="color: '.($varianValAll < 0 ? '#dc2626' : '#78350f').';">'.number_format($varianValAll, 2).'&nbsp;</td>
			<td align="right">'.number_format($allTerminalB).'&nbsp;</td>
			<td align="right">'.number_format($allValueB, 2).'&nbsp;</td>
			<td colspan="2"></td>
		</tr>';
} else {
	print '
	<tr>
		<td colspan="13" class="empty-message-monthly">
			<strong>- Tiada rekod maklumat arahan -</strong>
		</td>
	</tr>';
}

print'
	</tbody>
</table>
</form>

<script>
	function DeleteDoc(id) {
		e = document.MySD;
		if (confirm(\'Delete Host Receipt \' + id +\'?\')) {
			e.delDoc.value = id;
			e.submit();
		}
	}	
</script>';
?>