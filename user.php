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
	$result = 
	
}

function check_user(){
	
}