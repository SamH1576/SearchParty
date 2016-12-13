<?php

$customer = array();
$address = array();
$data = array();

/***********************************************************************/
//  Functions and Exception Handlers
/***********************************************************************/
set_exception_handler(function($e){
	$code = $e->getCode() ?: 400;
	header("Content-Type: application/json", NULL, $code);
	echo json_encode(["error" => $e->getMessage()]);
	exit;
});

function check_parameters($array){
	//need to confirm exactly what is being passed from the form
	global $customer;
	global $address;
	
	$valid = True;
	
	/*array params
	customer array:
		email
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
	
	if(!isset($array(["email"]))
		$valid = False;
	else
		$customer[0] = $array(["email"])

	if(!isset($array(["first_name"]))
		$valid = False;
	else
		$customer[1] = $array(["first_name"])
	
	if(!isset($array(["last_name"]))
		$valid = False;
	else
		$customer[2] = $array(["last_name"])
	
	if(!isset($array(["phone"]))
		$valid = False;
	else
		$customer[3] = $array(["phone"])
	
	if(!isset($array(["firstline"]))
		$valid = False;
	else
		$address[0] = $array(["firstline"])
	
	if(!isset($array(["secondline"]))
		$valid = False;
	else
		$address[1] = $array(["secondline"])
	
	if(!isset($array(["city"]))
		$valid = False;
	else
		$address[2] = $array(["city"])
	
	if(!isset($array(["county"]))
		$valid = False;
	else
		$address[3] = $array(["county"])
	
	if(!isset($array(["postcode"]))
		$valid = False;
	else
		$address[4] = $array(["postcode"])

	return $valid
}

function create_user($cust, $addr){
	//Login to database
	$db = new PDO("mysql:dbname=projectdb", "root", "");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//First check whether address exists
	//using first line of address and post code
	$address_exists = False;
	$result = $db->query("SELECT cust_address_id FROM cust_address WHERE postcode = '$addr[4]' AND firstline = '$addr[0]'");
	if($result->rowCount() > 0){
		$address_exists = True;
		
		//Add user
		$catch = $db->exec("INSERT INTO user(email, firstname, lastname, phone, 
							creation_time, modification_time)
							VALUES ('$cust[0]', '$cust[1]', '$cust[2]', '$cust[3]', 
							NOW(), NOW())");
		$newUser_ID = mysql_insert_id();
		//Update customer_addresses with new User_ID and cust_address_id from previous query
		$catch = $db->exec("INSERT INTO customer_addresses(customer_id, cust_address_id, creation_time, modification_time)
							VALUES ('$newUser_ID', '$result(["cust_address_id"])'")
	}else{
		//create the address
		//add the customer with newly created address
	}
	
	
}

function check_user(){
	
}