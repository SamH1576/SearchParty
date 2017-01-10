<?php
include 'Database-config.php';

//start session
session_start();

function check_username_password_match($array){
    global $dbname;
	global $dbusername;
	global $dbpassword;
    global $dbhost;
    global $returndata;
	
    //Login to database
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $username = $array['username']; 
    $password = $array['password'];
    $result=$db->query("SELECT User_ID FROM user WHERE email= '$username' and BINARY password= '$password' ");
    $result1=$db->query("SELECT User_ID FROM user WHERE email= '$username' ");
    if($result->rowCount() > 0){
        $rowdata = $result->fetch(PDO::FETCH_ASSOC);
        $_SESSION["usernameID"] = $rowdata['User_ID'];
        $db = null;
        $returndata = "LogInSuccess";
        return $returndata;
    }
    else if($result1->rowCount() > 0){
        $db = null;
        $returndata = "EmailMatch";
        return $returndata;
    }

    else{
        $db=null;
        $returndata = "LogInFail";
        return $returndata;
    }
}
/*****************************************************/
// main
/*****************************************************/
$verb = $_SERVER['REQUEST_METHOD'];
    if($verb == 'POST'){
		if(check_username_password_match($_POST)== "LogInSuccess"){
			$_SESSION["loggedIn"] = True;
			$_SESSION["username"] = $_POST['username'];	
			include 'main.html';
        }
        else if(check_username_password_match($_POST)== "EmailMatch"){
            echo "<div id = 'alertbox'> Username exists, but that password was incorrect. Please enter the correct password! </div>";
            $_SESSION["loggedIn"] = False;
            include 'login.html';
        }
        else if(check_username_password_match($_POST)== "LogInFail"){
            echo "<div id = 'alertbox'> Username or password incorrect, if you have not signed up please sign up below</div>";
			$_SESSION["loggedIn"] = False;
            include 'login.html';
        } 
    } 
?>