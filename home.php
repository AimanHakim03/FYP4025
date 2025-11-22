<?php

include("header.php");

// Initialize variables
$continue = $_REQUEST['continue'] ?? '';

$_SESSION['Session.ad'] = 2;
$label_ID = "ID Pengguna";
$img_ID = "images/login.png";

if ($_SESSION['Session.ad'] == '1') {
} elseif ($_SESSION['Session.ad'] == '2') {
}

if ($_SESSION['Session.userName'] == "") {
	print '
<style>
/* Login Page Styles */
.login-container {
	min-height: calc(100vh - 250px);
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 2rem;
}

.login-card {
	background: #ffffff;
	border-radius: 16px;
	box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	max-width: 900px;
	width: 100%;
	display: flex;
	flex-direction: row;
}

.login-left {
	flex: 1;
	background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
	padding: 3rem;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	color: #ffffff;
	position: relative;
	overflow: hidden;
}

.login-left::before {
	content: "";
	position: absolute;
	top: -50%;
	right: -50%;
	width: 200%;
	height: 200%;
	background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
	animation: pulse 15s ease-in-out infinite;
}

@keyframes pulse {
	0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.3; }
	50% { transform: scale(1.2) rotate(180deg); opacity: 0.6; }
}

.login-icon {
	width: 140px;
	height: 140px;
	background: rgba(255, 255, 255, 0.15);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 1.5rem;
	backdrop-filter: blur(10px);
	position: relative;
	z-index: 1;
	border: 3px solid rgba(255, 255, 255, 0.3);
	box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.login-icon i {
	font-size: 4rem;
	color: #ffffff;
	filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.bank-logo {
	width: 100%;
	max-width: 200px;
	margin-bottom: 1.5rem;
	position: relative;
	z-index: 1;
	filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
}

.system-name {
	font-size: 1.5rem;
	font-weight: 700;
	margin-bottom: 1rem;
	text-align: center;
	position: relative;
	z-index: 1;
	text-transform: uppercase;
	letter-spacing: 1px;
	text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.login-welcome {
	font-size: 1.75rem;
	font-weight: 700;
	margin-bottom: 0.5rem;
	text-align: center;
	position: relative;
	z-index: 1;
	text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.login-subtitle {
	font-size: 0.95rem;
	opacity: 0.95;
	text-align: center;
	line-height: 1.6;
	position: relative;
	z-index: 1;
	max-width: 350px;
}

.login-right {
	flex: 1;
	padding: 3rem;
	display: flex;
	flex-direction: column;
	justify-content: center;
}

.login-header {
	margin-bottom: 2rem;
}

.login-title {
	font-size: 1.75rem;
	font-weight: 700;
	color: #0f172a;
	margin-bottom: 0.5rem;
}

.login-desc {
	color: #64748b;
	font-size: 0.875rem;
}

.login-form-group {
	margin-bottom: 1.5rem;
}

.login-label {
	display: block;
	font-size: 0.875rem;
	font-weight: 600;
	color: #475569;
	margin-bottom: 0.5rem;
}

.login-input-wrapper {
	position: relative;
}

.login-input-icon {
	position: absolute;
	left: 1rem;
	top: 50%;
	transform: translateY(-50%);
	color: #94a3b8;
	font-size: 1.1rem;
}

.login-input {
	width: 100%;
	padding: 0.875rem 1rem 0.875rem 3rem;
	border: 2px solid #e2e8f0;
	border-radius: 10px;
	font-size: 0.9375rem;
	transition: all 0.2s;
	background: #f8fafc;
}

.login-input:focus {
	outline: none;
	border-color: #667eea;
	background: #ffffff;
	box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.login-submit {
	width: 100%;
	padding: 1rem;
	background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
	color: #ffffff;
	border: none;
	border-radius: 10px;
	font-size: 1rem;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s;
	margin-top: 0.5rem;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	box-shadow: 0 4px 14px rgba(30, 64, 175, 0.4);
}

.login-submit:hover {
	transform: translateY(-2px);
	box-shadow: 0 8px 24px rgba(30, 64, 175, 0.5);
	background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
}

.login-submit:active {
	transform: translateY(0);
}

.info-message {
	background: #eff6ff;
	border-left: 4px solid #3b82f6;
	padding: 1rem;
	border-radius: 8px;
	margin-top: 2rem;
	color: #1e40af;
	font-size: 0.875rem;
	line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
	.login-card {
		flex-direction: column;
	}
	
	.login-left {
		padding: 2rem;
	}
	
	.login-icon {
		width: 80px;
		height: 80px;
		margin-bottom: 1rem;
	}
	
	.login-icon i {
		font-size: 2.5rem;
	}
	
	.login-welcome {
		font-size: 1.5rem;
	}
	
	.login-subtitle {
		font-size: 0.875rem;
	}
	
	.login-right {
		padding: 2rem;
	}
	
	.login-title {
		font-size: 1.5rem;
	}
}
</style>

<div class="login-container">
	<div class="login-card">
		<div class="login-left">
			<div class="login-icon">
				<i class="fas fa-university"></i>
			</div>
			<div class="system-name">Sistem Pusat Borang ATM</div>
			<h1 class="login-welcome">Bank Rakyat</h1>
			<p class="login-subtitle">Sila log masuk untuk mengakses sistem pengurusan ATM</p>
		</div>
		
		<div class="login-right">
			<div class="login-header">
				<h2 class="login-title">Log Masuk</h2>
				<p class="login-desc">Masukkan ID pengguna dan kata laluan anda</p>
			</div>
			
			<form action="login.php" method="post">
				<input type="hidden" name="continue" value="' . $continue . '">
				
				<div class="login-form-group">
					<label class="login-label" for="username">
						<i class="fas fa-user"></i> ' . $label_ID . '
					</label>
					<div class="login-input-wrapper">
						<i class="fas fa-user login-input-icon"></i>
						<input type="text" id="username" name="username" class="login-input" placeholder="Masukkan ID pengguna" maxlength="10" required autofocus>
					</div>
				</div>
				
				<div class="login-form-group">
					<label class="login-label" for="password">
						<i class="fas fa-lock"></i> Kata Laluan
					</label>
					<div class="login-input-wrapper">
						<i class="fas fa-lock login-input-icon"></i>
						<input type="password" id="password" name="password" class="login-input" placeholder="Masukkan kata laluan" maxlength="10" required>
					</div>
				</div>
				
<div style="text-align: right; margin-bottom: 1rem;">
	<a href="forgotpassword.php" style="color: #3b82f6; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s;">
		<i class="fas fa-key" style="font-size: 0.75rem;"></i> Lupa Kata Laluan?
	</a>
</div>

				<button type="submit" name="action" value="Log Masuk" class="login-submit">
					<i class="fas fa-sign-in-alt"></i>
					Log Masuk ke Sistem
				</button>
			</form>';

	if (isset($registerInfo) && $registerInfo != '') {
		print '
			<div class="info-message">
				<i class="fas fa-info-circle"></i> ' . $registerInfo . '
			</div>';
	}

	print '
		</div>
	</div>
</div>';

	if (isset($introInfo) && $introInfo != '') {
		print '
<div style="max-width: 900px; margin: 2rem auto; padding: 0 2rem;">
	<div style="background: #ffffff; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
		' . $introInfo . '
	</div>
</div>';
	}

} else {
	// Dashboard for logged-in users
	print '
<style>
/* Dashboard Styles */
.dashboard-container {
	padding: 0;
}

.dashboard-welcome {
	background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
	padding: 2rem;
	border-radius: 12px;
	color: #ffffff;
	margin-bottom: 2rem;
	box-shadow: 0 8px 24px rgba(30, 64, 175, 0.3);
	position: relative;
	overflow: hidden;
}

.dashboard-welcome::before {
	content: "";
	position: absolute;
	top: -50%;
	right: -50%;
	width: 200%;
	height: 200%;
	background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
	animation: pulse 20s ease-in-out infinite;
}

.dashboard-welcome h1 {
	font-size: 1.75rem;
	font-weight: 700;
	margin: 0 0 0.5rem 0;
	position: relative;
	z-index: 1;
	text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.dashboard-welcome p {
	margin: 0;
	opacity: 0.95;
	font-size: 1rem;
	position: relative;
	z-index: 1;
}

.dashboard-stats {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 1.5rem;
	margin-bottom: 2rem;
}

.stat-card {
	background: #ffffff;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	display: flex;
	align-items: center;
	gap: 1rem;
	transition: all 0.3s;
}

.stat-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
	width: 60px;
	height: 60px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.75rem;
	color: #ffffff;
}

.stat-icon.blue {
	background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
}

.stat-icon.green {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon.orange {
	background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-icon.red {
	background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-content {
	flex: 1;
}

.stat-label {
	font-size: 0.875rem;
	color: #64748b;
	margin-bottom: 0.25rem;
}

.stat-value {
	font-size: 1.75rem;
	font-weight: 700;
	color: #0f172a;
}

.dashboard-sections {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
	gap: 1.5rem;
}

.dashboard-section {
	background: #ffffff;
	border-radius: 12px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
	padding: 1.5rem;
}

.section-header {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	margin-bottom: 1.5rem;
	padding-bottom: 1rem;
	border-bottom: 2px solid #f1f5f9;
}

.section-header i {
	font-size: 1.5rem;
	color: #1e40af;
}

.section-header h2 {
	font-size: 1.25rem;
	font-weight: 700;
	color: #0f172a;
	margin: 0;
}

.info-box {
	background: #f8fafc;
	padding: 1.25rem;
	border-radius: 8px;
	border-left: 4px solid #3b82f6;
	margin-top: 1rem;
}

@media (max-width: 768px) {
	.dashboard-sections {
		grid-template-columns: 1fr;
	}
}
</style>

<div class="dashboard-container">
	<div class="dashboard-welcome">
		<h1><i class="fas fa-home"></i> Selamat Datang, ' . ($_SESSION['Session.fullName'] ?? $_SESSION['Session.userName']) . '!</h1>
		<p>Sistem Pusat Borang ATM Bank Rakyat - Dashboard Utama</p>
	</div>';

	include("siteMonthly.php");
	include("siteCollect.php");

	if (isset($registerInfo) && $registerInfo != '') {
		print '
	<div class="info-box">
		<i class="fas fa-info-circle" style="color: #3b82f6;"></i> ' . $registerInfo . '
	</div>';
	}

	print '
</div>';
}

include("footer.php");
?>