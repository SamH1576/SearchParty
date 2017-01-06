<?php
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);

include 'Database-config.php';	
    //Login to database
	try{
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	}catch{
		$msg = $db->getMessage();	
		$firephp->info($msg);
	}


?>