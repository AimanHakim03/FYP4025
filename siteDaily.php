<?php

include("forms.php");

if (!isset($dd)) 	$dd	= date("j");                   		
if (!isset($mm)) 	$mm	= date("n");                 		
if (!isset($yy))	$yy	= date("Y");  
if (!isset($ty))	$ty	= '';

$dmy = $dd.'/'.$mm.'/'.$yy;
if (ValidDate($dmy) == "N") {
	print '<script>alert("Pilihan tarikh tidak sah - '.$dmy.'");</script>';
	$dd=1;
}
$ymd = $yy.'-'.$mm.'-'.$dd;

//$conn->debug=true;
$sSQL = " 
SELECT	A.atmNo, A.atmLoc, A.type, E.vendorCode, E.vendorName,   
		COALESCE(B.qty100,'0') AS qty100A, COALESCE(B.qty50,'0') AS qty50A, COALESCE(B.qty20,'0') AS qty20A, COALESCE(B.qty10,'0') AS qty10A, COALESCE(B.totalValue,'0.00') AS totalValueA, 
		COALESCE(C.qty100,'0') AS qty100B, COALESCE(C.qty50,'0') AS qty50B, COALESCE(C.qty20,'0') AS qty20B, COALESCE(C.qty10,'0') AS qty10B, COALESCE(C.totalValue,'0.00') AS totalValueB, 
		COALESCE(D.qty100,'0') AS qty100C, COALESCE(D.qty50,'0') AS qty50C, COALESCE(D.qty20,'0') AS qty20C, COALESCE(D.qty10,'0') AS qty10C, COALESCE(D.totalValue,'0.00') AS totalValueC
FROM 	atm A 
INNER JOIN instruct B 
ON		A.atmID = B.atmID AND B.instructDate = '".$ymd."' 
LEFT JOIN recon C 
ON		A.atmID = C.atmID AND C.reconDate = '".$ymd."' 
LEFT JOIN replenish D 
ON		A.atmID = D.atmID AND D.replenishDate = '".$ymd."' 
LEFT JOIN	vendor E 
ON		COALESCE(B.vendorID,A.vendorID) = E.vendorID
WHERE 	A.isDeleted ='0' ";

if ($_SESSION['Session.groupID'] == "0" OR $_SESSION['Session.groupID'] == "1") {
	if ($_SESSION['Session.branchID'] <> "") {
		$sSQL .= " AND COALESCE(B.vendorID,A.vendorID) = '".$_SESSION['Session.branchID']."' ";
	}
}
if ($ty <> '') {
	$sSQL .= " AND A.type = '".$ty."' ";
}

$sSQL .= "
ORDER BY A.atmNo  ";

$rsD = &$conn->Execute($sSQL);	

print '
<style>
.site-daily-table {
	width: 95%;
	margin: 0 auto 2rem auto;
	border-collapse: collapse;
	background: #ffffff;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	border-radius: 8px;
	overflow: hidden;
}

.site-daily-header {
	background: #f8fafc;
	padding: 1rem 1.25rem;
	font-weight: 700;
	font-size: 0.875rem;
	color: #0f172a;
	border-bottom: 2px solid #e2e8f0;
}

.site-daily-header select {
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

.site-daily-header select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.site-daily-table thead th {
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

.site-daily-table thead th.header-main {
	background: #8b1538;
}

.site-daily-table thead th.header-arahan {
	background: #1e293b;
}

.site-daily-table thead th.header-pengeluaran {
	background: #ef4444;
}

.site-daily-table thead th.header-pengisian {
	background: #10b981;
}

.site-daily-table tbody td {
	padding: 0.625rem 0.5rem;
	font-size: 0.8rem;
	border-bottom: 1px solid #f1f5f9;
}

.site-daily-table tbody tr.Data {
	background: #ffffff;
}

.site-daily-table tbody tr.Data1 {
	background: #f8fafc;
}

.site-daily-table tbody tr:hover {
	background: #eff6ff !important;
}

.site-daily-table tbody tr.LabelH {
	background: #fef3c7;
	font-weight: 700;
	color: #78350f;
	border-top: 2px solid #f59e0b;
}

.site-daily-table .col-arahan {
	background: #f8fafc;
}

.site-daily-table .col-pengeluaran {
	background: #fef2f2;
}

.site-daily-table .col-pengisian {
	background: #ecfdf5;
}

.site-daily-table .empty-message {
	padding: 2rem;
	text-align: center;
	color: #64748b;
	font-size: 0.875rem;
	background: #f8fafc;
}

@media (max-width: 1200px) {
	.site-daily-table {
		width: 100%;
		font-size: 0.7rem;
	}
	
	.site-daily-table thead th {
		font-size: 0.65rem;
		padding: 0.5rem 0.25rem;
	}
	
	.site-daily-table tbody td {
		padding: 0.5rem 0.25rem;
		font-size: 0.75rem;
	}
}
</style>

<form name="MySD" action="'.$PHP_SELF.'" method="post">
<table class="site-daily-table" border="0" cellpadding="3" cellspacing="1">
	<tr>
		<td class="site-daily-header" colspan="19">
			AKTIVITI ARAHAN, PENGELUARAN & PENGISIAN DENOMINASI ATM PADA 
			<select name="dd" class="textFontBold" onchange="document.MySD.submit();">';
for ($d = 0; $d < count($dayList); $d++) {
	print '		<option value="'.$dayList[$d].'"';
	if ($dd == $dayList[$d]) print ' selected';
	print 		'>'.$dayList[$d].'</option>';
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
print '	</select>		
			JENIS&nbsp;
			<select name="ty" class="textFontBold" onchange="document.MySD.submit();">
				<option value="">- SEMUA</option>';
for ($s = 0; $s < count($atmList); $s++) {
	print '		<option value="'.$atmList[$s].'"';
	if ($ty == $atmList[$s]) print ' selected';
	print 		'>'.$atmList[$s].'</option>';
}
print '	</select>	
		</td>
	</tr>
	<thead>
		<tr>
			<th rowspan="2" class="header-main" style="width: 40px;">BIL</th>
			<th rowspan="2" class="header-main" style="min-width: 180px;">TERMINAL ATM</th>
			<th rowspan="2" class="header-main" style="width: 60px;">JENIS</th>
			<th rowspan="2" class="header-main" style="width: 80px;">SYARIKAT</th>
			<th colspan="5" class="header-arahan">ARAHAN</th>
			<th colspan="5" class="header-pengeluaran">PENGELUARAN</th>
			<th colspan="5" class="header-pengisian">PENGISIAN</th>
		</tr>
		<tr>
			<th class="header-arahan" style="width: 50px;">RM100</th>
			<th class="header-arahan" style="width: 50px;">RM50</th>
			<th class="header-arahan" style="width: 50px;">RM20</th>
			<th class="header-arahan" style="width: 50px;">RM10</th>
			<th class="header-arahan" style="width: 100px;">JUMLAH</th>
			<th class="header-pengeluaran" style="width: 50px;">RM100</th>
			<th class="header-pengeluaran" style="width: 50px;">RM50</th>
			<th class="header-pengeluaran" style="width: 50px;">RM20</th>
			<th class="header-pengeluaran" style="width: 50px;">RM10</th>
			<th class="header-pengeluaran" style="width: 100px;">JUMLAH</th>
			<th class="header-pengisian" style="width: 50px;">RM100</th>
			<th class="header-pengisian" style="width: 50px;">RM50</th>
			<th class="header-pengisian" style="width: 50px;">RM20</th>
			<th class="header-pengisian" style="width: 50px;">RM10</th>
			<th class="header-pengisian" style="width: 100px;">JUMLAH</th>
		</tr>
	</thead>
	<tbody>';

$sd=0;$cnt=0;
$tqty100A=0; $tqty50A=0; $tqty20A=0; $tqty10A=0; $allValueA=0;
$tqty100B=0; $tqty50B=0; $tqty20B=0; $tqty10B=0; $allValueB=0;
$tqty100C=0; $tqty50C=0; $tqty20C=0; $tqty10C=0; $allValueC=0;

if ($rsD->RowCount() <> 0) {  
	while (!$rsD->EOF) {
		$sd++;
		$strClass = ($cnt % 2 == 1) ? 'Data' : 'Data1';
		
		$tqty100A	+= $rsD->fields('qty100A'); 
		$tqty50A	+= $rsD->fields('qty50A'); 
		$tqty20A	+= $rsD->fields('qty20A'); 
		$tqty10A	+= $rsD->fields('qty10A'); 
		$allValueA	+= $rsD->fields('totalValueA'); 
		$tqty100B	+= $rsD->fields('qty100B'); 
		$tqty50B	+= $rsD->fields('qty50B'); 
		$tqty20B	+= $rsD->fields('qty20B'); 
		$tqty10B	+= $rsD->fields('qty10B'); 
		$allValueB	+= $rsD->fields('totalValueB'); 
		$tqty100C	+= $rsD->fields('qty100C'); 
		$tqty50C	+= $rsD->fields('qty50C'); 
		$tqty20C	+= $rsD->fields('qty20C'); 
		$tqty10C	+= $rsD->fields('qty10C'); 
		$allValueC	+= $rsD->fields('totalValueC'); 
		
		print '
		<tr class="'.$strClass.'">
			<td align="right" style="font-weight: 600; color: #64748b;">'.$sd.'.</td>
			<td align="left" style="font-weight: 500;">'.$rsD->fields('atmNo').' - '.$rsD->fields('atmLoc').'</td>
			<td align="center">'.$rsD->fields('type').'</td>
			<td align="left" style="font-weight: 500;">'.$rsD->fields('vendorCode').'</td>
			<td align="right" class="col-arahan" style="font-weight: 600;">'.$rsD->fields('qty100A').'&nbsp;</td>
			<td align="right" class="col-arahan" style="font-weight: 600;">'.$rsD->fields('qty50A').'&nbsp;</td>
			<td align="right" class="col-arahan" style="font-weight: 600;">'.$rsD->fields('qty20A').'&nbsp;</td>
			<td align="right" class="col-arahan" style="font-weight: 600;">'.$rsD->fields('qty10A').'&nbsp;</td>
			<td align="right" class="col-arahan" style="font-weight: 700;">'.number_format($rsD->fields('totalValueA'),2).'&nbsp;</td>
			<td align="right" class="col-pengeluaran" style="font-weight: 600;">'.$rsD->fields('qty100B').'&nbsp;</td>
			<td align="right" class="col-pengeluaran" style="font-weight: 600;">'.$rsD->fields('qty50B').'&nbsp;</td>
			<td align="right" class="col-pengeluaran" style="font-weight: 600;">'.$rsD->fields('qty20B').'&nbsp;</td>
			<td align="right" class="col-pengeluaran" style="font-weight: 600;">'.$rsD->fields('qty10B').'&nbsp;</td>
			<td align="right" class="col-pengeluaran" style="font-weight: 700;">'.number_format($rsD->fields('totalValueB'),2).'&nbsp;</td>
			<td align="right" class="col-pengisian" style="font-weight: 600;">'.$rsD->fields('qty100C').'&nbsp;</td>
			<td align="right" class="col-pengisian" style="font-weight: 600;">'.$rsD->fields('qty50C').'&nbsp;</td>
			<td align="right" class="col-pengisian" style="font-weight: 600;">'.$rsD->fields('qty20C').'&nbsp;</td>
			<td align="right" class="col-pengisian" style="font-weight: 600;">'.$rsD->fields('qty10C').'&nbsp;</td>
			<td align="right" class="col-pengisian" style="font-weight: 700;">'.number_format($rsD->fields('totalValueC'),2).'&nbsp;</td>
		</tr>';
		$cnt++;
		$rsD->MoveNext();
	}
	
	print '
		<tr class="LabelH">
			<td colspan="4" align="right">Jumlah : </td>
			<td align="right">'.$tqty100A.'&nbsp;</td>
			<td align="right">'.$tqty50A.'&nbsp;</td>
			<td align="right">'.$tqty20A.'&nbsp;</td>
			<td align="right">'.$tqty10A.'&nbsp;</td>
			<td align="right">'.number_format($allValueA,2).'&nbsp;</td>
			<td align="right">'.$tqty100B.'&nbsp;</td>
			<td align="right">'.$tqty50B.'&nbsp;</td>
			<td align="right">'.$tqty20B.'&nbsp;</td>
			<td align="right">'.$tqty10B.'&nbsp;</td>
			<td align="right">'.number_format($allValueB,2).'&nbsp;</td>
			<td align="right">'.$tqty100C.'&nbsp;</td>
			<td align="right">'.$tqty50C.'&nbsp;</td>
			<td align="right">'.$tqty20C.'&nbsp;</td>
			<td align="right">'.$tqty10C.'&nbsp;</td>
			<td align="right">'.number_format($allValueC,2).'&nbsp;</td>
		</tr>';
} else {
	print '
	<tr>
		<td colspan="19" class="empty-message">
			<strong>- Tiada rekod maklumat terminal ATM -</strong>
		</td>
	</tr>';
}

print'
	</tbody>
</table>
</form>';
?>