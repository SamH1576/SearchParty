<?php
include 'Database-config.php';	
    //Login to database
	try{
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	}catch{
		echo 'Connection failed: ' . $db->getMessage();	
	}


?>