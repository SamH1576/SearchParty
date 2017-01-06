<?php
include 'Database-config.php';	
    //Login to database
	
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>