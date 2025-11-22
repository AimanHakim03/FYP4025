<?php
require 'PHPMailer\PHPMailerAutoload.php';
 
$mail = new PHPMailer;
 
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = '';                // SMTP username
$mail->Password = '';               // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->Port = 465;                                    //Set the SMTP port number - 587 for authenticated TLS
$mail->setFrom('wkfarid@gmail.com', 'Wan Kamarulfarid');     //Set who the message is to be sent from
$mail->addAddress('zeidy@gmail.com', 'Zeidy Idros');  // Add a recipient
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->addAttachment('/images/atm-out.png'); // Optional name
$mail->isHTML(true);                                  // Set email format to HTML
 
$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
 
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}
 
echo 'Message has been sent';
?>