<?php
/***********************************************************************/
/*  PHP file to add and delete users
/***********************************************************************/
//Set connection parameters
include 'Database-config.php';
//start session
session_start();

$customer = array();
$address = array();
$data = "";

/***********************************************************************/
//  Functions and Exception Handlers
/***********************************************************************/
set_exception_handler(function($e){
	$code = $e->getCode() ?: 400;
	header("Content-Type: application/json", NULL, $code);
	echo json_encode(["error" => $e->getMessage()]);
	exit;
});

function check_parameters_add_user($array){
	global $customer;
	global $address;
	
	$valid = True;
	
	/*array params
	customer array:
		email
		password
		first_name
		last_name
		phone
	address array:
		firstline
		secondline
		city
		county
		postcode	
	*/
	
	if ($array['email1']==null){
		$valid = False;
	}else{
		$customer[0] = $array["email1"];
	}
		
	if ($array["password1"]==null){
		$valid = False;
	}else{
		$customer[1] = $array["password1"];
	}
	
	if ($array["FirstName1"]==null){
		$valid = False;
	}else{
		$customer[2] = $array["FirstName1"];
	}
	
	if (($array["LastName1"]==null)){
		$valid = False;
	}else{
		$customer[3] = $array["LastName1"];
	}

	if (($array["Phone1"]==null)){
		$valid = False;
	}else{
		$customer[4] = $array["Phone1"];
	}
	
	if (($array["FirstLine1"]==null)){
		$valid = False;
	}else{
		$address[0] = $array["FirstLine1"];
	}
	
	if (($array["SecondLine1"]==null)){
		$valid = False;
	}else{
		$address[1] = $array["SecondLine1"];
	}
	
	if (($array["City1"]==null)){
		$valid = False;
	}else{
		$address[2] = $array["City1"];
	}

	if (($array["County1"]==null)){
		$valid = False;
	}else{
		$address[3] = $array["County1"];
	}
	
	if (($array["PostCode1"]==null)){
		$valid = False;
	}else{
		$address[4] = $array["PostCode1"];
	}

	return $valid;
}

function check_parameters_delete_user($array){
	$valid = True;
	$delete_email_password = array();
	
	if ($array['email']==null){
		$valid = False;
	}else{
		$delete_email_password[0] = $array["email"];
	}

	if ($array['password']==null){
		$valid = False;
	}else{
		$delete_email_password[1] = $array["password"];
	}
	
	if($valid){
		return $delete_email_password;
	}else{
		return null;
	}
	
}
/******************************************************************/
/*  Create User
/******************************************************************/
function create_user($cust, $addr){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	//Login to database
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//Check if User already exists - ie check email
	if(dbCheckUserExists($cust[0], $db)){
		//Exit the function, returning False to let the caller know it was unsuccessful
		$db = null;
		return False;
	}
	
	//First check whether address exists using first line of address and post code
	//This returns either null or the cust_address_id
	$cust_address_id = dbCheckCustAddressExists($addr[0],$addr[4],$db);

	if($cust_address_id != null){
		//The address exists, so add user then connect with existing address:
		//dbAddUser returns the new User_ID key it just created
		$newUser_ID = dbAddUser($cust, $db);
		
		//Update customer_addresses with new User_ID and cust_address_id from previous query
		dbAddCustomerAddressRecord($newUser_ID, $cust_address_id, $db);
		
	}else{
		//create the address
		$newCust_Address_ID = dbAddCustAddress($addr, $db);

		//add the user, returning new record id
		$newUser_ID = dbAddUser($cust, $db);
		
		//Update customer_addresses with new User_ID and cust_address_id from previous query
		dbAddCustomerAddressRecord($newUser_ID, $newCust_Address_ID, $db);				
	}
	$db =null;
	//return True to let the calling function know the update was successful
	return True;
}

function dbAddUser($cust, $db){
		$catch = $db->exec("INSERT INTO user(email, password, firstname, lastname, phone, 
							creation_time, modification_time)
							VALUES ('$cust[0]', '$cust[1]', '$cust[2]', '$cust[3]', '$cust[4]',
							NOW(), NOW())");
		return $db->lastInsertId();
}

function dbAddCustomerAddressRecord($newUser_ID, $cust_address_id, $db){
		$catch = $db->exec("INSERT INTO fkcust_address(customer_id, cust_address_id, 
							creation_time, modification_time)
							VALUES ('$newUser_ID', '$cust_address_id', NOW(), NOW())");	
}

function dbAddCustAddress($addr, $db){
		$catch = $db->exec("INSERT INTO cust_address(firstline, secondline, city, county, postcode, 
							creation_time, modification_time)
							VALUES ('$addr[0]','$addr[1]','$addr[2]','$addr[3]','$addr[4]', NOW(), NOW())");
		return $db->lastInsertId();	
}

function dbCheckUserExists($email, $db){
	$result = $db->query("SELECT email FROM user WHERE email = '$email'");
	if($result->rowCount() > 0){
		return True;
	}else{
		return False;
	}	
}

function dbCheckCustAddressExists($firstline, $postcode, $db){
	$result = $db->query("SELECT cust_address_id FROM cust_address WHERE postcode = '$postcode' AND firstline = '$firstline'");
	if($result->rowCount() > 0){
		$rowdata = $result->fetch(PDO::FETCH_ASSOC);
		return $rowdata["cust_address_id"];
	}else{
		return null;
	}
}

function dbDeleteUserbyEmail($email, $password, $db){
	$result = $db->exec("DELETE FROM user WHERE email = '$email' AND password = '$password'");
	if($result == 1){
		return True;
	}else{
		return False;
	}
}

/******************************************************************/
/*  Delete User
/******************************************************************/
function delete_user($cust_info){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	//Login to database
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if(dbCheckUserExists($cust_info[0], $db)){
		if(dbDeleteUserbyEmail($cust_info[0], $cust_info[1], $db)){
			return True;
		}else{
			return False;
		}	
	}else{
		return False;
	}
}

/*****************************************************/
// main
/*****************************************************/
//handle requestions by verb and path
$verb = $_SERVER['REQUEST_METHOD'];
$url_pieces= explode('/', $_SERVER['PATH_INFO']);

if($url_pieces[1] == 'adduser'){
	if($verb == 'POST'){
		if(check_parameters_add_user($_POST)){
			try{
				$boolSuccess = create_user($customer, $address);
			} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
		}else{
			$boolSuccess = False;
		}
        $registeruserajaxvars['boolsuccess']= $boolSuccess;
	}
}else if($url_pieces[1]=='deleteuser'){
	if($verb == 'POST'){
		$cust_info = check_parameters_delete_user($_POST);
		if($cust_info != null){
			try{
				$boolSuccess = delete_user($cust_info);
			} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
		}else{
		$boolSuccess = False;			
		}
	}
}else{
	echo 'unknown path';
}
/* window output message */
if($url_pieces[1] == 'adduser'){
		if($boolSuccess){
			$_SESSION["loggedIn"] = True;
			$_SESSION["username"] = $customer[0];	
			$registeruserajaxvars['login'] = True;
            $registeruserajaxvars['window_message'] = "Success, a new user with email address $customer[0] was added.";
        }
        else {
        	$_SESSION["loggedIn"] = False;
        	$registeruserajaxvars['login'] = False;
            $registeruserajaxvars['window_message'] = "Sorry, a user with the address $customer[0] already exists. Please use another email.";
        }
    echo json_encode($registeruserajaxvars);
}
else if($url_pieces[1] == 'deleteuser'){
		if($boolSuccess){
            echo "success the user was deleted.";
        }
        else{
            echo "Failure, the user was not deleted.";
        }
}
?>