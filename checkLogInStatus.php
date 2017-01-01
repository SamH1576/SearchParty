<?php
include 'Database-config.php';
//start session
session_start();

//code to check $_SESSION['loggedIn']
if($_SESSION['loggedIn']){
	return True;
}
else{
	return False;
}
?>