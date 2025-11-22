<?php

include("header.php");
//$conn->debug=true;
function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

function sendPasswordResetEmail($email, $userName, $newPassword)
{
    require __DIR__ . '/vendor/autoload.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kl2311015233@student.uptm.edu.my'; // CHANGE THIS
        $mail->Password = 'xzqg mfqq thca doxo'; // CHANGE THIS (use App Password, not regular password)
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('kl2311015233@student.uptm.edu.my', 'Sistem BKRM-ATM'); // CHANGE THIS
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Kata Laluan Anda';
        $mail->Body = '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                <h2 style="color: #3b82f6;">Reset Kata Laluan</h2>
                <p>Assalamualaikum,</p>
                <p>Kata laluan anda telah direset oleh pentadbir sistem.</p>
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>ID Pengguna:</strong> ' . $userName . '</p>
                    <p style="margin: 5px 0;"><strong>Kata Laluan Baharu:</strong> <span style="background: #fff; padding: 5px 10px; border-radius: 4px; font-family: monospace; font-size: 16px; color: #ef4444;">' . $newPassword . '</span></p>
                </div>
                <p><strong style="color: #ef4444;">PENTING:</strong> Sila tukar kata laluan anda selepas log masuk untuk keselamatan akaun anda.</p>
                <p style="color: #64748b; font-size: 12px; margin-top: 30px;">Emel ini dijana secara automatik. Sila jangan balas emel ini.</p>
            </div>
        </body>
        </html>
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}


// Initialize ALL request variables
$StartRec = $_REQUEST['StartRec'] ?? 1;
$pg = $_REQUEST['pg'] ?? 50;
$q = $_REQUEST['q'] ?? '';
$action = $_REQUEST['action'] ?? '';
$pk = $_REQUEST['pk'] ?? [];

$sFileName = 'users.php';
$sFileRef = 'usersAddUpdate.php';
$title = 'PENGGUNA';

// Cache session variables
$sessionGroupID = $_SESSION['Session.groupID'] ?? '';
$sessionUserName = $_SESSION['Session.userName'] ?? '';
$sessionGroupName = $_SESSION['Session.groupName'] ?? '';

if ($sessionGroupID <> "3" && $sessionGroupID <> "4" && $sessionGroupID <> "9") {
    print '<script>alert("' . $errPage . '");window.location.href="index.php";</script>';
}

// Fix for QUERY_STRING - use $_SERVER
$QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
$strURL = urlencode($QUERY_STRING);
if ($strURL == '') {
    $strURL = 'q=' . $q;
    $strURL = urlencode($strURL);
}


//--- BEGIN : DELETION BASED ON CHECKED BOX -------------------------------------------------------------------------------------
if ($action == "delete" && is_array($pk) && count($pk) > 0) {
    for ($i = 0; $i < count($pk); $i++) {
        $userName = dlookup("users", "userName", "userID=" . tosql($pk[$i], "Text"));
        //--- Begin : Create log 
        $user = $sessionUserName;
        $event = "ID Pengguna " . $userName . " - Dihapuskan";
        $type = $sessionGroupName;
        createLog($user, $event, $type);
        //--- End   : Create log 	
        $sSQL = "UPDATE users SET isDeleted = '1' WHERE userID =" . tosql($pk[$i], "Text");
        $rs = $conn->Execute($sSQL);
    }
    // Refresh page after delete
    print '<script>
			alert("' . count($pk) . ' pengguna telah dihapuskan.");
			window.location.href = "' . $sFileName . '";
		</script>';
    exit;
}
//--- END   : DELETION BASED ON CHECKED BOX -------------------------------------------------------------------------------------

//--- BEGIN : RESET BASED ON CHECKED BOX-----------------------------------------------------------------------------------------
if ($action == "reset") {
    $successCount = 0;
    $failCount = 0;
    $emailsSent = 0;
    $errorMessages = []; // Track errors

    for ($i = 0; $i < count($pk); $i++) {
        // Get user details
        $userName = dlookup("users", "userName", "userID=" . tosql($pk[$i], "Text"));
        $userEmail = dlookup("users", "email", "userID=" . tosql($pk[$i], "Text"));

        // Debug: Check if user data exists
        if (!$userName) {
            $errorMessages[] = "User ID " . $pk[$i] . ": userName not found";
            $failCount++;
            continue;
        }

        if (!$userEmail) {
            $errorMessages[] = "User " . $userName . ": email not found";
            $failCount++;
            continue;
        }

        // Generate random password
        $newPassword = generateRandomPassword(10);

        // Hash password with MD5 (your existing format)
        $hashedPassword = strtoupper(md5($newPassword . strtoupper($userName)));

        // Update password in database
        $sSQL = "UPDATE users SET password = " . tosql($hashedPassword, "Text") .
            " WHERE userID =" . tosql($pk[$i], "Text");

        // Debug: show the actual SQL
        error_log("Attempting SQL: " . $sSQL);

        try {
            $rs = $conn->Execute($sSQL);

            // Check if Execute actually returned false or threw error
            if ($rs === false) {
                $dbError = $conn->ErrorMsg(); // Get SQL Server error message
                $errorMessages[] = "User " . $userName . ": Database error - " . $dbError;
                error_log("SQL Error for " . $userName . ": " . $dbError);
                $failCount++;
                continue;
            }

            // Send email with new password
            $emailSent = sendPasswordResetEmail($userEmail, $userName, $newPassword);

            if ($emailSent) {
                $emailsSent++;

                // Create log
                $user = $sessionUserName;
                $event = 'Kata Laluan untuk ID Pengguna (' . $userName . ') - Direset dan dihantar ke ' . $userEmail;
                $type = $sessionGroupName;
                createLog($user, $event, $type);

                $successCount++;
            } else {
                $errorMessages[] = "User " . $userName . ": Email failed to send to " . $userEmail;
                $failCount++;
            }
        } catch (Exception $e) {
            $errorMessages[] = "User " . $userName . ": " . $e->getMessage();
            $failCount++;
        }
    }

    // Show result message with details
    $message = '';
    if ($successCount > 0) {
        $message .= $successCount . ' kata laluan telah direset dan dihantar ke emel pengguna.';
    }
    if ($failCount > 0) {
        if ($message != '')
            $message .= '\\n\\n';
        $message .= $failCount . ' gagal direset atau dihantar.\\n\\n';
        $message .= 'ERRORS:\\n' . implode('\\n', $errorMessages);
    }

    print '<script>
            alert("' . addslashes($message) . '");
            window.location.href = "' . $sFileName . '";
          </script>';
    exit;
}
//--- END : RESET BASED ON CHECKED BOX-----------------------------------------------------------------------------------------

if ($sessionGroupID == "9") {
    $sSQL = "	SELECT	* FROM users WHERE isDeleted = '0' ";
} elseif ($sessionGroupID == "3") {
    $sSQL = "	SELECT	* FROM users WHERE isDeleted = '0' AND groupID <> '9' ";
} else {
    $sSQL = "	SELECT	* FROM users WHERE isDeleted = '0' AND groupID NOT IN ('9','3') ";
}

if ($q <> '') {
    $sSQL = " SELECT 	A.* FROM (" . $sSQL . ") A " .
        " WHERE		A.userName	 LIKE " . tosql($q . "%", "Text") .
        " OR 		A.fullName   LIKE " . tosql($q . "%", "Text") .
        " ORDER BY A.userName ";
} else {
    $sSQL .= " ORDER BY userName ";
}

$GetList = $conn->Execute($sSQL);
$GetList->Move($StartRec - 1);

//--- BEGIN : CATER FOR NUMBER OF RECORDS AND PAGE TO BE DISPLAYED --------------------------------------------------------------
$TotalRec = $GetList->RowCount();
$TotalPage = ($TotalRec / $pg);

if ($StartRec < $TotalRec) {
    if (($TotalRec - ($StartRec + $pg)) < 0) {
        $NextRec = $StartRec;
    } else {
        $NextRec = $StartRec + $pg;
    }
} else {
    $NextRec = $StartRec;
}
if ($StartRec != 1) {
    $PrevRec = $StartRec - $pg;
} else {
    $PrevRec = 1;
}
if ($TotalRec != 0) {
    if (($TotalRec % $pg) == 0) {
        $LastRec = ($TotalPage * $pg) - $pg + 1;
    } else {
        $LastRec = ($TotalPage * $pg) + 1;
    }
} else {
    $LastRec = 1;
}
//--- END   : CATER FOR NUMBER OF RECORDS AND PAGE TO BE DISPLAYED --------------------------------------------------------------

print '
<style>
.page-header {
	background: #ffffff;
	padding: 2rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.page-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 0.5rem 0;
}

.page-subtitle {
	color: #64748b;
	font-size: 0.875rem;
	margin: 0;
}

.filter-section {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	margin-bottom: 1.5rem;
}

.search-bar {
	display: flex;
	gap: 1rem;
	align-items: center;
	flex-wrap: wrap;
}

.search-input-group {
	flex: 1;
	min-width: 250px;
	position: relative;
}

.search-input-group i {
	position: absolute;
	left: 1rem;
	top: 50%;
	transform: translateY(-50%);
	color: #94a3b8;
}

.search-input {
	width: 100%;
	padding: 0.75rem 1rem 0.75rem 2.75rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	transition: all 0.2s;
}

.search-input:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-group {
	display: flex;
	gap: 0.75rem;
	align-items: center;
}

.filter-select {
	padding: 0.625rem 1rem;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	font-size: 0.875rem;
	background: #ffffff;
	cursor: pointer;
	transition: all 0.2s;
}

.filter-select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
	padding: 0.625rem 1.25rem;
	border: none;
	border-radius: 8px;
	font-size: 0.875rem;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.2s;
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
}

.btn-primary {
	background: #3b82f6;
	color: #ffffff;
}

.btn-primary:hover {
	background: #2563eb;
	box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-success {
	background: #10b981;
	color: #ffffff;
}

.btn-success:hover {
	background: #059669;
	box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-danger {
	background: #ef4444;
	color: #ffffff;
}

.btn-danger:hover {
	background: #dc2626;
	box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-warning {
	background: #f59e0b;
	color: #ffffff;
}

.btn-warning:hover {
	background: #d97706;
	box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.action-buttons {
	display: flex;
	gap: 0.75rem;
	flex-wrap: wrap;
}

.table-container {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	overflow-x: auto;
}

.modern-table {
	width: 100%;
	border-collapse: collapse;
}

.modern-table thead {
	background: #f8fafc;
	border-bottom: 2px solid #e2e8f0;
}

.modern-table thead th {
	padding: 1rem;
	text-align: left;
	font-size: 0.75rem;
	font-weight: 600;
	color: #475569;
	text-transform: uppercase;
	letter-spacing: 0.05em;
}

.modern-table tbody tr {
	border-bottom: 1px solid #f1f5f9;
	transition: background 0.15s;
}

.modern-table tbody tr:hover {
	background: #f8fafc;
}

.modern-table tbody td {
	padding: 1rem;
	font-size: 0.875rem;
	color: #1e293b;
}

.user-id-cell {
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.user-link {
	color: #3b82f6;
	text-decoration: none;
	font-weight: 500;
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.user-link:hover {
	color: #2563eb;
	text-decoration: underline;
}

.profile-badge {
	display: inline-block;
	padding: 0.25rem 0.75rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
}

.badge-pentadbir {
	background: #e0e7ff;
	color: #4338ca;
}

.badge-pengguna {
	background: #d1fae5;
	color: #065f46;
}

.status-badge {
	display: inline-flex;
	align-items: center;
	gap: 0.375rem;
	padding: 0.25rem 0.75rem;
	border-radius: 12px;
	font-size: 0.75rem;
	font-weight: 600;
}

.status-active {
	background: #d1fae5;
	color: #065f46;
}

.status-inactive {
	background: #fee2e2;
	color: #991b1b;
}

.status-dot {
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: currentColor;
}

.pagination-container {
	padding: 1.5rem;
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: #ffffff;
	border-top: 1px solid #e2e8f0;
	flex-wrap: wrap;
	gap: 1rem;
}

.pagination-info {
	color: #64748b;
	font-size: 0.875rem;
}

.pagination-info strong {
	color: #0f172a;
	font-weight: 600;
}

.pagination-buttons {
	display: flex;
	gap: 0.5rem;
	flex-wrap: wrap;
}

.page-btn {
	padding: 0.5rem 0.75rem;
	border: 1px solid #e2e8f0;
	background: #ffffff;
	color: #475569;
	border-radius: 6px;
	font-size: 0.875rem;
	cursor: pointer;
	transition: all 0.15s;
	text-decoration: none;
	display: inline-block;
}

.page-btn:hover {
	border-color: #3b82f6;
	color: #3b82f6;
	background: #eff6ff;
}

.page-btn.active {
	background: #3b82f6;
	color: #ffffff;
	border-color: #3b82f6;
}

.empty-state {
	padding: 4rem 2rem;
	text-align: center;
	color: #64748b;
}

.empty-state i {
	font-size: 3rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.empty-state h3 {
	color: #0f172a;
	margin-bottom: 0.5rem;
}

.checkbox-custom {
	width: 18px;
	height: 18px;
	cursor: pointer;
	accent-color: #3b82f6;
}

@media (max-width: 768px) {
	.search-bar {
		flex-direction: column;
	}
	
	.search-input-group {
		width: 100%;
	}
	
	.action-buttons {
		width: 100%;
	}
	
	.btn {
		flex: 1;
		justify-content: center;
	}
	
	.modern-table {
		font-size: 0.75rem;
	}
	
	.modern-table thead th,
	.modern-table tbody td {
		padding: 0.75rem 0.5rem;
	}
}
</style>

<form name="ITRViewResults" action="' . $sFileName . '" method="post">
<input type="hidden" name="action">
<input type="hidden" name="q" value="' . $q . '">

<!-- Page Header -->
<div class="page-header">
	<h1 class="page-title">Pengurusan Pengguna</h1>
	<p class="page-subtitle">Urus pengguna sistem dan tetapan akaun</p>
</div>

<!-- Filter Section -->
<div class="filter-section">
	<div class="search-bar">
		<div class="search-input-group">
			<i class="fas fa-search"></i>
			<input type="text" name="q" value="' . $q . '" placeholder="Cari ID Pengguna/Nama..." class="search-input" maxlength="100">
		</div>
		
		<div class="filter-group">
			<label style="font-size: 0.875rem; color: #64748b;">Profil:</label>
			<select class="filter-select" style="min-width: 150px;">
				<option>Semua Profil</option>
				<option>Pentadbir Sistem</option>
				<option>Pengguna</option>
			</select>
		</div>
		
		<div class="filter-group">
			<label style="font-size: 0.875rem; color: #64748b;">Syarikat:</label>
			<select class="filter-select" style="min-width: 150px;">
				<option>Semua Syarikat</option>
			</select>
		</div>
	</div>
	
	<div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
		<div class="action-buttons">
			<button type="button" class="btn btn-danger" onclick="ITRActionButtonClick(\'delete\');">
				<i class="fas fa-trash"></i>
				Hapus Pengguna Terpilih
			</button>
			<button type="button" class="btn btn-warning" onclick="ITRActionResetClick(\'reset\');">
				<i class="fas fa-key"></i>
				Reset Kata Laluan Terpilih
			</button>
		</div>
		
		<button type="button" class="btn btn-success" onclick="winOpen(\'' . $sFileRef . '?action=add\');">
			<i class="fas fa-plus"></i>
			Daftar Pengguna Baru
		</button>
	</div>
</div>

<!-- Table Container -->
<div class="table-container">';

if ($GetList->RowCount() <> 0) {
    $bil = $StartRec;
    $cnt = 1;

    print '
	<table class="modern-table">
		<thead>
			<tr>
				<th style="width: 3%;"></th>
				<th style="width: 5%;">NO.</th>
				<th>ID PENGGUNA</th>
				<th>NAMA</th>
				<th>EMEL & NO DIHUBUNGI</th>
				<th>ID NO</th>
				<th>PROFIL</th>
				<th>SYARIKAT</th>
				<th style="text-align: center;">AKTIF</th>
				<th style="width: 5%;"></th>
			</tr>
		</thead>
		<tbody>';

    while (!$GetList->EOF && $cnt <= $pg) {
        // Safe array search
        $groupIndex = array_search($GetList->fields('groupID'), $groupVal);
        $group = ($groupIndex !== false) ? $groupList[$groupIndex] : 'Unknown';

        // Determine badge class
        $badgeClass = 'profile-badge ';
        if (stripos($group, 'PENTADBIR') !== false || stripos($group, 'ADMIN') !== false) {
            $badgeClass .= 'badge-pentadbir';
        } else {
            $badgeClass .= 'badge-pengguna';
        }

        $company = '';
        $call = '';

        if ($GetList->fields('branchID') <> '') {
            $vendorName = dlookup("vendor", "vendorName", "vendorID=" . tosql($GetList->fields('branchID'), "Text"));
            $vendorCode = dlookup("vendor", "vendorCode", "vendorID=" . tosql($GetList->fields('branchID'), "Text"));

            if ($vendorName) {
                $company = $vendorName;
                if ($vendorCode) {
                    $company .= ' (' . $vendorCode . ')';
                }
            }
        }

        if ($GetList->fields('contactNo') <> '')
            $call .= $GetList->fields('contactNo') . ' ';
        if ($GetList->fields('mobileNo') <> '')
            $call .= $GetList->fields('mobileNo');

        $isActive = $GetList->fields('isActive');
        $statusClass = ($isActive == 'Y' || $isActive == '1' || $isActive == 1) ? 'status-active' : 'status-inactive';
        $statusText = ($isActive == 'Y' || $isActive == '1' || $isActive == 1) ? 'Ya' : 'Tidak';

        print '
			<tr>
				<td>
					<input type="checkbox" name="pk[]" value="' . $GetList->fields('userID') . '" class="checkbox-custom">
				</td>
				<td style="font-weight: 600; color: #64748b;">' . $bil . '</td>
				<td>
					<div class="user-id-cell">
						<a href="#" onclick="winOpen(\'' . $sFileRef . '?action=update&amp;pk=' . $GetList->fields('userID') . '\'); return false;" class="user-link">
							' . $GetList->fields('userName') . '
							<i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
						</a>
					</div>
				</td>
				<td style="font-weight: 500;">' . $GetList->fields('fullName') . '</td>
				<td>
					<div style="display: flex; flex-direction: column; gap: 0.25rem;">
						<span style="color: #3b82f6;">' . $GetList->fields('email') . '</span>
						<span style="color: #64748b; font-size: 0.8rem;">' . $call . '</span>
					</div>
				</td>
				<td>' . $GetList->fields('icNo') . '</td>
				<td><span class="' . $badgeClass . '">' . $group . '</span></td>
				<td style="color: #64748b;">' . $company . '</td>
				<td style="text-align: center;">
					<span class="status-badge ' . $statusClass . '">
						<span class="status-dot"></span>
						' . $statusText . '
					</span>
				</td>
				<td>
					<a href="#" onclick="winOpen(\'' . $sFileRef . '?action=update&amp;pk=' . $GetList->fields('userID') . '\'); return false;" title="Lihat/Edit">
						<i class="fas fa-eye" style="color: #3b82f6;"></i>
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
	<div class="pagination-container">
		<div class="pagination-info">
			Menunjukkan <strong>1-2</strong> daripada <strong>5 Rekod</strong>
		</div>
		
		<div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
			<div style="display: flex; align-items: center; gap: 0.5rem;">
				<label style="font-size: 0.875rem; color: #64748b;">Paparan</label>
				<select name="pg" class="filter-select" onchange="doListAll();" style="width: 80px;">';

    if ($pg == 50)
        print '<option value="50"  selected>50</option>';
    else
        print '<option value="50">50</option>';
    if ($pg == 100)
        print '<option value="100" selected>100</option>';
    else
        print '<option value="100">100</option>';
    if ($pg == 150)
        print '<option value="150" selected>150</option>';
    else
        print '<option value="150">150</option>';
    if ($pg == 200)
        print '<option value="200" selected>200</option>';
    else
        print '<option value="200">200</option>';
    if ($pg == 250)
        print '<option value="250" selected>250</option>';
    else
        print '<option value="250">250</option>';
    if ($pg == 300)
        print '<option value="300" selected>300</option>';
    else
        print '<option value="300">300</option>';

    print '		</select>
				<span style="font-size: 0.875rem; color: #64748b;">setiap mukasurat</span>
			</div>
			
			<div class="pagination-buttons">';

    if ($TotalRec > $pg) {
        if ($TotalRec % $pg == 0) {
            $numPage = $TotalPage;
        } else {
            $numPage = $TotalPage + 1;
        }

        print '<a href="#" class="page-btn"><i class="fas fa-chevron-left"></i> Sebelum</a>';

        for ($i = 1; $i <= min($numPage, 5); $i++) {
            $linkRef = $sFileName . '?StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q;
            $activeClass = ($i == 1) ? ' active' : '';
            print '<a href="' . $linkRef . '" class="page-btn' . $activeClass . '">' . $i . '</a>';
        }

        if ($numPage > 5) {
            print '<span class="page-btn" style="border: none; cursor: default;">...</span>';
        }

        print '<a href="#" class="page-btn">Seterus <i class="fas fa-chevron-right"></i></a>';
    }

    print '
			</div>
		</div>
	</div>';

} else {
    if ($q <> '') {
        print '
		<div class="empty-state">
			<i class="fas fa-search"></i>
			<h3>Tiada Rekod Dijumpai</h3>
			<p>Tiada pengguna yang sepadan dengan carian "' . $q . '"</p>
		</div>';
    } else {
        print '
		<div class="empty-state">
			<i class="fas fa-users"></i>
			<h3>Tiada Pengguna</h3>
			<p>Belum ada pengguna didaftarkan dalam sistem</p>
		</div>';
    }
}

print '
</div>

</form>';

include("footer.php");


print '
<script>
	var allChecked=false;
	var popupWindow=null;
	
	function ITRViewSelectAll() {
	    e = document.ITRViewResults.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}
	
	function ITRActionButtonClick(v) {
	     e = document.ITRViewResults;
	     if(e==null) {
	       alert(\'Confirm the form name.!\');
	     } else {
	       count=0;
	       for(c=0; c<e.elements.length; c++) {
	         if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	           count++;
	         }
	       }
	       
	       if(count==0) {
	          alert(\'Sila pilih rekod yang hendak dihapuskan.\');
	       } else {
	          if(confirm(\'Hapuskan \' + count + \' rekod ?\')) {
	           e.action.value = v;
	           e.submit();
	         }
	       }
	     }
	   }

	function ITRActionResetClick(v) {
	     e = document.ITRViewResults;
	     if(e==null) {
	       alert(\'Confirm the form name.!\');
	     } else {
	       count=0;
	       for(c=0; c<e.elements.length; c++) {
	         if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	           count++;
	         }
	       }
	       
	       if(count==0) {
	          alert(\'Sila pilih rekod yang hendak direset kata laluan.\');
	       } else {
	          if(confirm(\'Reset kata laluan untuk \' + count + \' rekod ?\')) {
	           e.action.value = v;
	           e.submit();
	         }
	       }
	     }
	   }
	   
	 function doListAll() {
		c = document.forms[\'ITRViewResults\'].pg;
		document.location = "' . $sFileName . '?StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function selGroup() {
		c = document.forms[\'ITRViewResults\'].q;
		document.location = "' . $sFileName . '?StartRec=1&q=" + c.options[c.selectedIndex].value;
	}
	
	function winOpen(url) {
		e = document.ITRViewResults;
		popupWindow = window.open(url,"sort","top=50,left=50,width=700,height=550,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
       	popupWindow.focus();
    }	   
</script>';
?>