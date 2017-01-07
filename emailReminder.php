<?php
//Set connection parameters
include 'Database-config.php';
//start session
session_start();

date_default_timezone_set('Etc/UTC');
require 'PHPMailer/PHPMailerAutoload.php';


$mail = new PHPMailer;
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = 'smtp.mail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "searchparty@engineer.com";
//Password to use for SMTP authentication
$mail->Password = "COMP310P";
//Set who the message is to be sent from
$mail->setFrom('searchparty@engineer.com', 'SearchParty');
//Set an alternative reply-to address
$mail->addReplyTo('searchparty@engineer.com', 'SearchParty');
$mail->Subject = "Event Reminder!";

//Login to database
$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//Get list of events where a reminder hasn't been sent and it is the day before the startdate
$result=$db->query("SELECT Event_ID, Title FROM event WHERE Reminder_sent= 0 AND CURDATE() = StartDate - INTERVAL 1 DAY");
//LOOP OVER EVENTS
foreach ($result as $row) {
	//echo $row['Event_ID'];
	//GET LIST OF EMAILS
	$result2=$db->query("SELECT u.Email, CONCAT(u.firstname , ' ', u.lastname) AS Name FROM user as u
		JOIN fkguest_list AS fk ON fk.User_ID = u.User_ID 
		WHERE fk.Event_ID = '".$row['Event_ID']."' ");
	foreach ($result2 as $row2) {
		// echo $row2['Email'];
		// echo $row2['Name'];
		//Set address
		$mail->addAddress($row2['Email'], $row2['Name']);
		$body = "<p>Dear '".$row2['Name']."',</p>
		<p>This is a reminder that your event '".$row['Title']."' is tomorrow!</p>
		<p>From SearchParty</p>";
		$mail->msgHTML($body);
		if(!$mail->send()){
			echo "Sending email to '".$row2['Email']."' failed";
			echo $mail->ErrorInfo;
		}else{
			$sql = "UPDATE event SET Reminder_sent=1 WHERE Event_ID = '".$row['Event_ID']."'";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			//echo "Sending email to '".$row2['Email']."' Success";
		}
		$mail->clearAddresses();
	}
}
$db = null;
$mail = null;
?>