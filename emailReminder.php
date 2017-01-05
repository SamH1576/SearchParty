<?php
//Set connection parameters
include 'Database-config.php';
//start session
session_start();

//Login to database
$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//GET HUANY MIN TO WRITE SQL QUERIES FOR GETTING IST OF EMAILZ

require '/PHPMailer/PHPMailerAutoload.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = 'smtp.mail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "searchparty@engineer.com";
//Password to use for SMTP authentication
$mail->Password = "COMP310P";
//Set who the message is to be sent from
$mail->setFrom('searchparty@engineer.com', 'SearchParty');
//Set an alternative reply-to address
$mail->addReplyTo('searchparty@engineer.com', 'SearchParty');
//Set who the message is to be sent to
$mail->addAddress('sam.hiscox@ntlworld.com', 'Sam Hiscox');
//Set the subject line
$mail->Subject = 'PHPMailer GMail SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML('<div> This is some plain content </div>');
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>