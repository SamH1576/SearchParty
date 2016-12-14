<?php
//Set connection parameters
$dbname = "projectdb";
$dbusername = "hyminsa";
$dbpassword = "hyminsa";

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
	
	if ($array['email']==null){
		$valid = False;
	}else{
		$customer[0] = $array["email"];
	}
		
	if ($array["password"]==null){
		$valid = False;
	}else{
		$customer[1] = $array["password"];
	}
	
	if ($array["first_name"]==null){
		$valid = False;
	}else{
		$customer[2] = $array["first_name"];
	}
	
	if (($array["last_name"]==null)){
		$valid = False;
	}else{
		$customer[3] = $array["last_name"];
	}

	if (($array["phone"]==null)){
		$valid = False;
	}else{
		$customer[4] = $array["phone"];
	}
	
	if (($array["firstline"]==null)){
		$valid = False;
	}else{
		$address[0] = $array["firstline"];
	}
	
	if (($array["secondline"]==null)){
		$valid = False;
	}else{
		$address[1] = $array["secondline"];
	}
	
	if (($array["city"]==null)){
		$valid = False;
	}else{
		$address[2] = $array["city"];
	}

	if (($array["county"]==null)){
		$valid = False;
	}else{
		$address[3] = $array["county"];
	}
	
	if (($array["postcode"]==null)){
		$valid = False;
	}else{
		$address[4] = $array["postcode"];
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
	
	//Login to database
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
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
	$result = $db->query("SELECT cust_address_id FROM fkcust_address WHERE postcode = '$postcode' AND firstline = '$firstline'");
	if($result->rowCount() > 0){
		$rowdata = $result->fetch(PDO::FETCH_ASSOC);
		return $rowdata["cust_address_id"];
	}else{
		return null;
	}
}

/******************************************************************/
/*  Delete User
/******************************************************************/
function delete_user($cust_info){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	
	//Login to database
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if(dbCheckUserExists($cust_info[0], $db)){
		//SQL to detete user
		return True;
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
		display_result($boolSuccess, 'adduser');
		}else{
			display_result(False, 'adduser');
		}
	
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
		display_result($boolSuccess, 'delete_user');
		}else{
		display_result(False, 'delete_user');			
		}
	}
}else{
	echo 'unknown path';
}

function display_result($success, $action){
?>
<!-- Generate some HTML to indicate success or failure -->
<!DOCTYPE html>
<html>
<head>
	<title>Add User</title>
</head>
<body>
	
<?php	
	if($action == 'adduser'){
		if($success){
		?>
			<h1>Success</h1>
			<p>A new user with email address <?php echo $customer[0] ?> was added.</p>
		<?php }else{ ?>
			<h1>Failure</h1>
			<p>A user with email address <?php echo $customer[0] ?> already exists.</p>	
		<?php } 
	}else if($action == 'deleteuser'){
		if($success){
		?>
			<h1>Success</h1>
			<p>The user with email address <?php echo $customer[0] ?> was deleted.</p>
		<?php }else{ ?>
			<h1>Failure</h1>
			<p>The user with email address <?php echo $customer[0] ?> was not deleted.</p>	
		<?php }	
	}else{
		
	}
?>
</body>	
<? } ?>

