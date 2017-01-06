<?php
//start session
session_start();

//code to check $_SESSION['loggedIn']
if(isset($_SESSION["loggedIn"]) and $_SESSION["loggedIn"]){
	// remove all session variables
	session_unset(); 
	// destroy the session 
	session_destroy(); 
}else{
	echo 'error';
}
?>