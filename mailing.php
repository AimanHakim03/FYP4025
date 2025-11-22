<?php
$subject = "HELPDESK: Perlu Perhatian Pihak Tuan";
$from = "helpdesk@ikram.com.my";
$contents = "test sahaja. Hello! This is a simple email message.";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= 'From: HELPDESK <helpdesk@ikram.com.my>' . "\r\n";
$headers .= 'Reply-To: helpdesk@ikram.com.my' . "\r\n";

$to = "wkfarid@ikram.com.my";
ini_set("sendmail_from", "helpdesk@ikram.com.my");

if (mail ($to, $subject, $contents, $headers,"-f$from")) {
//	echo $to.' - '.$subject.' - '.$contents.' - '.$headers;
echo "Mail Sent.";
}else{      echo('not ok');    }

?>
