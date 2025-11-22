<?php

include("header.php");	

// Initialize variables
$action = $_REQUEST['action'] ?? '';
$password = $_REQUEST['password'] ?? '';
$newpassword = $_REQUEST['newpassword'] ?? '';
$newpassword1 = $_REQUEST['newpassword1'] ?? '';

// Session check
if (!isset($_SESSION['Session.userID']) || $_SESSION['Session.userID'] == "") {
	$errPage = $errPage ?? 'Unauthorized access';
	print '<script>alert("'.addslashes($errPage).'");window.location.href="index.php";</script>';
	exit;
}

$un = $_SESSION['Session.userName'] ?? '';
$un = strtoupper($un);

if ($action == 'Kemaskini') {
	$msg = '';
	if ($password <> '') {
		if ($newpassword <> $newpassword1 || strlen($newpassword) < 6) {
			$msg = "Sila pastikan pengesahan kata laluan sama dengan kata laluan baru.";
		} else {
			if ($password == $newpassword) {
				$msg = "Kata laluan sekarang tidak boleh sama dengan kata laluan baru";
			} else {
				$encryptpwd = strtoupper(md5($password.$un));
				$GetUser = ctVerifyUser($_SESSION['Session.userName'],$encryptpwd);
				if ($GetUser->RowCount() == 1) {
					$sWhere = ' userID = ' . tosql($_SESSION['Session.userID'],"Text");
					$sSQL	= ' UPDATE users SET ' .
				          	  ' password=' . tosql(strtoupper(md5($newpassword.$un)), "Text") ;
					$sSQL .=  ' WHERE ' . $sWhere;
					$rs = $conn->Execute($sSQL);
					
					//--- Begin : Create log 
					$user  = $_SESSION['Session.userName'];
					$event = "Kata Laluan telah dikemaskinikan - ".$newpassword;
					$type  = $_SESSION['Session.groupName'];
					createLog($user , $event, $type);
					//--- End   : Create log 
				} else {
					$msg = "Kata laluan tidak sah";
				}
			}
		}
	} else {
		$msg = "Kata laluan mesti diisi";
	}
	
	if ($msg <> ''){
		print 	' 	<script>
						alert("' . $msg . '");
					</script>';
	} else {
		print '		<script>	
						alert("Kata laluan baru telah dikemaskinikan");
						window.location.href = "index.php";
					</script>';
	}
}

print '
<style>
	* { box-sizing: border-box; }
	
	.profile-container {
		max-width: 900px;
		margin: 0 auto;
		padding: 24px;
		background: #f5f7fa;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
	}
	
	.profile-header {
		background: white;
		padding: 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
	}
	
	.profile-title {
		font-size: 24px;
		font-weight: 600;
		color: #1a202c;
		margin: 0 0 12px 0;
		display: flex;
		align-items: center;
		gap: 12px;
	}
	
	.profile-description {
		font-size: 14px;
		color: #718096;
		line-height: 1.6;
		margin: 0;
	}
	
	.profile-card {
		background: white;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0,0,0,0.06);
		overflow: hidden;
		margin-bottom: 20px;
	}
	
	.card-header {
		background: #475569;
		color: white;
		padding: 16px 24px;
		font-size: 16px;
		font-weight: 600;
		text-align: center;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.card-body {
		padding: 32px 24px;
	}
	
	.info-section {
		background: #f7fafc;
		border-radius: 8px;
		padding: 24px;
		margin-bottom: 32px;
	}
	
	.info-row {
		display: flex;
		padding: 12px 0;
		border-bottom: 1px solid #e2e8f0;
	}
	
	.info-row:last-child {
		border-bottom: none;
	}
	
	.info-label {
		width: 200px;
		font-size: 14px;
		font-weight: 500;
		color: #4a5568;
		display: flex;
		align-items: center;
	}
	
	.info-value {
		flex: 1;
		font-size: 14px;
		font-weight: 600;
		color: #1a202c;
		display: flex;
		align-items: center;
	}
	
	.form-section {
		padding: 0;
	}
	
	.form-group {
		margin-bottom: 24px;
	}
	
	.form-label {
		display: block;
		font-size: 14px;
		font-weight: 500;
		color: #4a5568;
		margin-bottom: 8px;
	}
	
	.form-label-required::after {
		content: " *";
		color: #f56565;
	}
	
	.form-input {
		width: 100%;
		padding: 10px 14px;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		font-size: 14px;
		color: #2d3748;
		background: white;
		transition: all 0.2s;
	}
	
	.form-input:focus {
		outline: none;
		border-color: #4299e1;
		box-shadow: 0 0 0 3px rgba(66,153,225,0.1);
	}
	
	.form-help {
		font-size: 13px;
		color: #718096;
		margin-top: 6px;
	}
	
	.form-actions {
		display: flex;
		gap: 12px;
		justify-content: flex-end;
		padding-top: 24px;
		border-top: 1px solid #e2e8f0;
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
	
	.password-strength {
		margin-top: 8px;
		font-size: 13px;
	}
	
	.strength-bar {
		height: 4px;
		background: #e2e8f0;
		border-radius: 2px;
		margin-top: 6px;
		overflow: hidden;
	}
	
	.strength-fill {
		height: 100%;
		width: 0%;
		transition: all 0.3s;
	}
	
	.strength-weak {
		background: #f56565;
		width: 33%;
	}
	
	.strength-medium {
		background: #f59e0b;
		width: 66%;
	}
	
	.strength-strong {
		background: #48bb78;
		width: 100%;
	}
	
	@media (max-width: 768px) {
		.profile-container {
			padding: 12px;
		}
		
		.info-row {
			flex-direction: column;
			gap: 8px;
		}
		
		.info-label {
			width: 100%;
		}
		
		.form-actions {
			flex-direction: column;
		}
		
		.btn-modern {
			width: 100%;
		}
	}
</style>

<div class="profile-container">
	<div class="profile-header">
		<h1 class="profile-title">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
				<circle cx="12" cy="7" r="4"/>
			</svg>
			PROFIL PENGGUNA
		</h1>
		<p class="profile-description">
			Anda boleh menukar kata laluan pada bila-bila masa. Penukaran kata laluan akan berlaku dengan serta merta.
		</p>
	</div>

	<form name="profile" action="profile.php" method="post">
		<div class="profile-card">
			<div class="card-header">
				MAKLUMAT PENGGUNA
			</div>
			<div class="card-body">
				<div class="info-section">
					<div class="info-row">
						<div class="info-label">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
								<circle cx="12" cy="7" r="4"/>
							</svg>
							ID Pengguna
						</div>
						<div class="info-value">' . htmlspecialchars($_SESSION['Session.userName']) . '</div>
					</div>
					
					<div class="info-row">
						<div class="info-label">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
								<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
								<circle cx="9" cy="7" r="4"/>
								<path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
								<path d="M16 3.13a4 4 0 0 1 0 7.75"/>
							</svg>
							Nama Penuh
						</div>
						<div class="info-value">' . htmlspecialchars($_SESSION['Session.fullName']) . '</div>
					</div>
					
					<div class="info-row">
						<div class="info-label">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
								<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
								<polyline points="22,6 12,13 2,6"/>
							</svg>
							Email
						</div>
						<div class="info-value">' . htmlspecialchars($_SESSION['Session.email']) . '</div>
					</div>
					
					<div class="info-row">
						<div class="info-label">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
								<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
								<circle cx="9" cy="7" r="4"/>
								<path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
								<path d="M16 3.13a4 4 0 0 1 0 7.75"/>
							</svg>
							Kumpulan
						</div>
						<div class="info-value">' . htmlspecialchars($_SESSION['Session.groupName']) . '</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="profile-card">
			<div class="card-header">
				TUKAR KATA LALUAN
			</div>
			<div class="card-body">
				<div class="form-section">
					<div class="form-group">
						<label class="form-label form-label-required">Kata Laluan Semasa</label>
						<input type="password" name="password" class="form-input" maxlength="10" placeholder="Masukkan kata laluan semasa" required>
						<p class="form-help">Masukkan kata laluan semasa anda untuk pengesahan</p>
					</div>
					
					<div class="form-group">
						<label class="form-label form-label-required">Kata Laluan Baru</label>
						<input type="password" name="newpassword" id="newpassword" class="form-input" maxlength="10" placeholder="Masukkan kata laluan baru" required>
						<p class="form-help">Kata laluan mestilah sekurang-kurangnya 6 aksara</p>
						<div class="password-strength" id="strength-indicator" style="display: none;">
							<div class="strength-bar">
								<div class="strength-fill" id="strength-fill"></div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="form-label form-label-required">Pengesahan Kata Laluan</label>
						<input type="password" name="newpassword1" id="newpassword1" class="form-input" maxlength="10" placeholder="Masukkan semula kata laluan baru" required>
						<p class="form-help">Masukkan semula kata laluan baru untuk pengesahan</p>
					</div>
					
					<div class="form-actions">
						<button type="button" class="btn-modern btn-secondary" onclick="window.location.href=\'index.php\'">Batal</button>
						<button type="submit" name="action" value="Kemaskini" class="btn-modern btn-primary">Kemaskini Kata Laluan</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	// Password strength indicator
	const newPasswordInput = document.getElementById("newpassword");
	const strengthIndicator = document.getElementById("strength-indicator");
	const strengthFill = document.getElementById("strength-fill");
	
	if (newPasswordInput) {
		newPasswordInput.addEventListener("input", function() {
			const password = this.value;
			
			if (password.length === 0) {
				strengthIndicator.style.display = "none";
				return;
			}
			
			strengthIndicator.style.display = "block";
			
			// Remove existing strength classes
			strengthFill.classList.remove("strength-weak", "strength-medium", "strength-strong");
			
			if (password.length < 6) {
				strengthFill.classList.add("strength-weak");
			} else if (password.length < 8) {
				strengthFill.classList.add("strength-medium");
			} else {
				strengthFill.classList.add("strength-strong");
			}
		});
	}
	
	// Form validation
	const profileForm = document.querySelector("form[name=\'profile\']");
	if (profileForm) {
		profileForm.addEventListener("submit", function(e) {
			const password = document.querySelector("input[name=\'password\']").value;
			const newPassword = document.querySelector("input[name=\'newpassword\']").value;
			const confirmPassword = document.querySelector("input[name=\'newpassword1\']").value;
			
			if (!password) {
				e.preventDefault();
				alert("Sila masukkan kata laluan semasa");
				return false;
			}
			
			if (!newPassword || newPassword.length < 6) {
				e.preventDefault();
				alert("Kata laluan baru mestilah sekurang-kurangnya 6 aksara");
				return false;
			}
			
			if (newPassword !== confirmPassword) {
				e.preventDefault();
				alert("Pengesahan kata laluan tidak sama dengan kata laluan baru");
				return false;
			}
			
			if (password === newPassword) {
				e.preventDefault();
				alert("Kata laluan baru tidak boleh sama dengan kata laluan semasa");
				return false;
			}
			
			return true;
		});
	}
</script>';

include("footer.php");	
?>