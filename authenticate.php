<?php
include 'Database-config.php';
function check_username_password_match($array){
    global $dbname;
	global $dbusername;
	global $dbpassword;
	
    //Login to database
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $username = $array['username'];
    $password = $array['password'];
    $result=$db->query("SELECT User_ID FROM user WHERE email= '$username' and password= '$password'");
    if($result->rowCount() > 0){
        $db = null;
        return True;
    }
    else{
        $db=null;
        return False;
    }
}
/*****************************************************/
// main
/*****************************************************/
$verb = $_SERVER['REQUEST_METHOD'];
    if($verb == 'POST'){
		if(check_username_password_match($_POST)){
            echo "Login successful";
			include 'main.html';
    }
        else{
            echo "Username and password not a match <br><br>";
            include 'login.html';
        } 
    } 
?>