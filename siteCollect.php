<?php

//$conn->debug=true;

// Initialize variables if not set
$ymd = $ymd ?? date('Ymd');
$vendorID = $vendorID ?? '';
$ty = $ty ?? '';

$sSQL = " 
SELECT	A.collectDate, B.vendorID, B.vendorCode, B.vendorName, A.status, count(*) AS terminalA, SUM(A.totalValue) AS totalValueA   
FROM 	collect A  LEFT JOIN vendor B  ON		A.vendorID = B.vendorID 
WHERE 	A.isDeleted ='0' ";

$sSQL .= "	AND CONVERT(VARCHAR(24),A.collectDate,112)= '".$ymd."' ";

if ($vendorID <> '') {
	$sSQL .= " AND	A.vendorID = ".tosql($vendorID, "Text");
}
if ($ty <> '') {
	$sSQL .= " AND A.type = '".$ty."' ";
}

$sSQL .= "
GROUP BY A.collectDate, B.vendorID, B.vendorCode, B.vendorName, A.status  ";

$rsD = $conn->Execute($sSQL);	

print '
<style>
.site-collect-table {
	width: 98%;
	margin: 0 auto 2rem auto;
	border-collapse: collapse;
	background: #ffffff;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	border-radius: 8px;
	overflow: hidden;
}

.site-collect-table .table-title {
	background: #f8fafc;
	padding: 1rem 1.25rem;
	font-weight: 700;
	font-size: 0.9rem;
	color: #0f172a;
	border-bottom: 2px solid #e2e8f0;
}

.site-collect-table thead th {
	padding: 0.875rem 1rem;
	text-align: center;
	font-size: 0.75rem;
	font-weight: 700;
	color: #ffffff;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid rgba(255, 255, 255, 0.1);
	white-space: nowrap;
}

.site-collect-table thead th.header-main {
	background: #8b1538;
}

.site-collect-table thead th.header-collection {
	background: #ff9900;
}

.site-collect-table tbody td {
	padding: 0.75rem 1rem;
	font-size: 0.875rem;
	border-bottom: 1px solid #f1f5f9;
}

.site-collect-table tbody tr.Data {
	background: #ffffff;
}

.site-collect-table tbody tr.Data1 {
	background: #f8fafc;
}

.site-collect-table tbody tr:hover {
	background: #eff6ff !important;
}

.site-collect-table tbody tr.LabelH {
	background: #fef3c7;
	font-weight: 700;
	color: #78350f;
	border-top: 2px solid #f59e0b;
}

.site-collect-table .empty-message {
	padding: 2rem;
	text-align: center;
	color: #64748b;
	font-size: 0.875rem;
	background: #f8fafc;
}

@media (max-width: 768px) {
	.site-collect-table {
		width: 100%;
		font-size: 0.75rem;
	}
}
</style>

<table class="site-collect-table" border="0" cellpadding="5" cellspacing="1">
	<tr>
		<td class="table-title" colspan="6">COLLECTION</td>
	</tr>
	<thead>
		<tr>
			<th rowspan="2" class="header-main" style="width: 50px;">BIL</th>
			<th rowspan="2" class="header-main" style="width: 120px;">TARIKH<br>KUTIPAN</th>
			<th rowspan="2" class="header-main">SYARIKAT</th>
			<th colspan="2" class="header-collection">COLLECTION</th>
			<th rowspan="2" class="header-main" style="width: 150px;">STATUS</th>
		</tr>
		<tr>
			<th class="header-collection" style="width: 100px;">JUMLAH<br>TERMINAL</th>
			<th class="header-collection" style="width: 120px;">AMAUN (RM)</th>
		</tr>
	</thead>
	<tbody>';

$sd=0;$cnt=0;
$allTerminalA=0; $allValueA=0;

if ($rsD && $rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		$strClass = ($cnt % 2 == 1) ? 'Data' : 'Data1';
		
		$collectDate = $rsD->fields['collectDate'] ?? '';
		$vendorName = $rsD->fields['vendorName'] ?? '';
		$terminalA = $rsD->fields['terminalA'] ?? 0;
		$totalValueA = $rsD->fields['totalValueA'] ?? 0;
		$status = $rsD->fields['status'] ?? '0';
		
		$allTerminalA += $terminalA;
		$allValueA += $totalValueA;
		
		if ($status == '0') {
			$statusText = '<font class="redText">** BELUM SELESAI **</font>';
		} else {
			$statusText = '<font class="greenText">** SELESAI **</font>';
		}
		
		print '
		<tr class="'.$strClass.'">
			<td align="right" style="font-weight: 600; color: #64748b;">'.$sd.'.</td>
			<td align="center" style="font-family: monospace;">'.todate('d/m/Y', $collectDate).'</td>
			<td align="left" style="font-weight: 500;">'.htmlspecialchars($vendorName).'</td>
			<td align="right" style="font-weight: 700;">'.number_format($terminalA).'&nbsp;</td>
			<td align="right" style="font-weight: 700;">'.number_format($totalValueA, 2).'&nbsp;</td>
			<td align="center">'.$statusText.'</td>
		</tr>';
		$cnt++;
		$rsD->MoveNext();
	}
	
	print '
		<tr class="LabelH">
			<td colspan="3" align="right">Jumlah : </td>
			<td align="right">'.number_format($allTerminalA).'&nbsp;</td>
			<td align="right">'.number_format($allValueA, 2).'&nbsp;</td>
			<td></td>
		</tr>';
} else {
	print '
	<tr>
		<td colspan="6" class="empty-message">
			<strong>- Tiada rekod maklumat kutipan -</strong>
		</td>
	</tr>';
}

print '
	</tbody>
</table>';
?>