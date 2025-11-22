<?php
// Include your database connection
include("common.php");

// Initialize variables
$message = '';
$messageType = '';
$action = $_REQUEST['action'] ?? '';
$email = $_REQUEST['email'] ?? '';

// Helper function to generate random password
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Helper function to send password reset email
function sendPasswordResetEmail($email, $userName, $newPassword) {
    require __DIR__ . '/vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = ''; 
        $mail->Password = ''; 
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email settings
        $mail->setFrom('kl2311015233@student.uptm.edu.my', 'Sistem BKRM-ATM'); 
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Kata Laluan Anda';
        $mail->Body = '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                <div style="background: #2947a9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h2 style="margin: 0;">Reset Kata Laluan</h2>
                </div>
                <div style="padding: 20px;">
                    <p>Assalamualaikum,</p>
                    <p>Kata laluan anda telah berjaya direset.</p>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2947a9;">
                        <p style="margin: 5px 0;"><strong>ID Pengguna:</strong> '.$userName.'</p>
                        <p style="margin: 5px 0;"><strong>Kata Laluan Baharu:</strong></p>
                        <p style="background: #fff; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 18px; color: #2947a9; font-weight: bold; text-align: center; margin: 10px 0;">'.$newPassword.'</p>
                    </div>
                    <p><strong style="color: #ef4444;">PENTING:</strong> Sila tukar kata laluan anda selepas log masuk untuk keselamatan akaun anda.</p>
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        <p style="color: #64748b; font-size: 12px; margin: 0;">Emel ini dijana secara automatik. Sila jangan balas emel ini.</p>
                        <p style="color: #64748b; font-size: 12px; margin: 5px 0 0 0;">© Bank Rakyat - Sistem Pusat Borang ATM</p>
                    </div>
                </div>
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

// Process forgot password request
if ($action == 'reset' && $email != '') {
    // Sanitize email
    $email = trim($email);
    
    // Check if email exists in database
    $sSQL = "SELECT userID, userName, email, isActive, isDeleted FROM users WHERE email = " . tosql($email, "Text");
    $rsUser = $conn->Execute($sSQL);
    
    if ($rsUser && !$rsUser->EOF) {
        $userID = $rsUser->fields('userID');
        $userName = $rsUser->fields('userName');
        $isActive = $rsUser->fields('isActive');
        $isDeleted = $rsUser->fields('isDeleted');
        
        // Check if user is active and not deleted
        if ($isDeleted == '1') {
            $message = 'Akaun ini telah dihapuskan. Sila hubungi pentadbir sistem.';
            $messageType = 'error';
        } elseif ($isActive != '1' && $isActive != 'Y') {
            $message = 'Akaun ini tidak aktif. Sila hubungi pentadbir sistem.';
            $messageType = 'error';
        } else {
            
            $newPassword = generateRandomPassword(10);
            
          
            $hashedPassword = strtoupper(md5($newPassword . strtoupper($userName)));
            
            // Update password in database
            $sSQL = "UPDATE users SET password = " . tosql($hashedPassword, "Text") . 
                    ", updatedDate = GETDATE() WHERE userID = " . tosql($userID, "Text");
            $rsUpdate = $conn->Execute($sSQL);
            
            if ($rsUpdate) {
                // Send email with new password
                if (sendPasswordResetEmail($email, $userName, $newPassword)) {
                    $message = 'Kata laluan baharu telah dihantar ke alamat emel anda. Sila semak emel anda.';
                    $messageType = 'success';
                    
                    // Create log
                    createLog('SYSTEM', 'Kata Laluan untuk ID Pengguna (' . $userName . ') - Direset melalui Lupa Kata Laluan', 'Forgot Password');
                } else {
                    $message = 'Gagal menghantar emel. Sila cuba lagi atau hubungi pentadbir sistem.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Gagal mereset kata laluan. Sila cuba lagi.';
                $messageType = 'error';
            }
        }
    } else {
        // Don't reveal if email exists or not (security best practice)
        $message = 'Jika alamat emel wujud dalam sistem, kata laluan baharu akan dihantar ke emel tersebut.';
        $messageType = 'info';
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Laluan - Bank Rakyat ATM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #2947a9 0%, #1e3a8a 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .logo-circle {
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .logo-circle i {
            font-size: 70px;
            color: white;
        }

        .left-panel h1 {
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .left-panel h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .left-panel p {
            font-size: 16px;
            opacity: 0.95;
            line-height: 1.6;
        }

        .right-panel {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #2947a9;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-link:hover {
            gap: 12px;
            color: #1e3a8a;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h3 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert i {
            font-size: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px 14px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #2947a9;
            background: white;
            box-shadow: 0 0 0 4px rgba(41, 71, 169, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2947a9 0%, #1e3a8a 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(41, 71, 169, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .help-text {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }

        .help-text p {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .help-text a {
            color: #2947a9;
            text-decoration: none;
            font-weight: 500;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-panel {
                padding: 40px 30px;
            }

            .right-panel {
                padding: 40px 30px;
            }

            .logo-circle {
                width: 120px;
                height: 120px;
            }

            .logo-circle i {
                font-size: 50px;
            }

            .left-panel h1 {
                font-size: 24px;
            }

            .left-panel h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="logo-circle">
                <i class="fas fa-university"></i>
            </div>
            <h1>SISTEM PUSAT BORANG ATM</h1>
            <h2>Bank Rakyat</h2>
            <p>Reset kata laluan anda dengan mudah dan selamat</p>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <a href="home.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Log Masuk
            </a>

            <div class="form-header">
                <h3>Lupa Kata Laluan?</h3>
                <p>Masukkan alamat emel anda dan kami akan menghantar kata laluan baharu kepada anda.</p>
            </div>

            <?php if ($message != ''): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php if ($messageType == 'success'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($messageType == 'error'): ?>
                        <i class="fas fa-exclamation-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-info-circle"></i>
                    <?php endif; ?>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <form name="forgotPasswordForm" method="post" action="forgotpassword.php">
                <input type="hidden" name="action" value="reset">
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Alamat Emel
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="nama@contoh.com"
                            required
                            value="<?php echo htmlspecialchars($email); ?>"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Hantar Kata Laluan Baharu
                </button>
            </form>

            <div class="help-text">
                <p>Ingat kata laluan anda?</p>
                <a href="home.php">Log masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>