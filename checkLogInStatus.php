<?php
//start session
session_start();

//code to check $_SESSION['loggedIn']
if(isset($_SESSION["loggedIn"]) and $_SESSION["loggedIn"]){
	echo $_SESSION["username"];
}else{
	echo 'null';
}
?>