<?php
//Set connection parameters
$dbname = "projectdb";
$dbusername = "hyminsa";
$dbpassword = "hyminsa";

$event = array();
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

function check_parameters_add_event($array){
	global $event;
	global $address;
	
	$valid = True;
	
	/*array params
	event array:
		title
		capacity
		startdate
		starttime
        enddate
        endtime
        description
        category
        ticketstartdate
        ticketenddate
	address array:
		firstline
		secondline
		city
		county
		postcode	
	*/
	
	if ($array['title']==null){
		$valid = False;
	}else{
		$event[0] = $array["title"];
	}
		
	if ($array["capacity"]==null){
		$valid = False;
	}else{
		$event[1] = $array["capacity"];
	}
	
	if ($array["startdate"]==null){
		$valid = False;
	}else{
		$event[2] = $array["startdate"];
	}
	
	if (($array["starttime"]==null)){
		$valid = False;
	}else{
		$event[3] = $array["starttime"];
	}

	if (($array["enddate"]==null)){
		$valid = False;
	}else{
		$event[4] = $array["enddate"];
	}
    
    if (($array["endtime"]==null)){
		$valid = False;
	}
    else{
		$event[5] = $array["endtime"];
	}
    
    if (($array["description"]==null)){
		$valid = False;
	}
    else{
		$event[6] = $array["description"];
	}
    
    if (($array["category"]==null)){
		$valid = False;
	}
    else{
		$event[7] = $array["category"];
	}
    
    if (($array["ticketstartdate"]==null)){
		$valid = False;
	}
    else{
		$event[8] = $array["ticketstartdate"];
	}
    
    if (($array["ticketenddate"]==null)){
		$valid = False;
	}
    else{
		$event[9] = $array["ticketenddate"];
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

function create_event($eve, $addr){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	
	//Login to database
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//Check if Event already exists - ie check title
	if(dbCheckEventExists($eve[0], $db)){
		//Exit the function, returning False to let the caller know it was unsuccessful
		$db = null;
		return False;
	}
	
	//First check whether address exists using first line of address and post code
	//This returns either null or the venue_address_id
	$venue_address_id = dbCheckVenueAddressExists($addr[0], $addr[4], $db);
    
	if($venue_address_id != null){
		//The address exists, so add event then connect with existing address:
		//dbAddEvent returns the new Event_ID key it just created
		$newEvent_ID = dbAddEvent($eve, $db);
        
		//Update FKEvent_Venue with new Event_ID and Venue_Address_ID from previous query
		dbAddEventAddressRecord($newEvent_ID, $venue_address_id, $db);
		
	}
    else{
		//create the address
		$newVenue_Address_ID = dbAddVenueAddress($addr, $db);

		//add the event, returning new record id
		$newEvent_ID = dbAddEvent($eve, $db);
		
		//Update FKEvent_Venue with new Event_ID and Venue_address_ID from previous query
		dbAddEventAddressRecord($newEvent_ID, $newVenue_Address_ID, $db);				
	}
	$db =null;
	//return True to let the calling function know the update was successful
	return True;
}

function dbAddEvent($eve, $db){
		$catch = $db->exec("INSERT INTO event(title, capacity, startdate, starttime, enddate, endtime, description, category, ticket_startdate, ticket_enddate)
							VALUES ('$eve[0]', '$eve[1]', '$eve[2]', '$eve[3]', '$eve[4]', '$eve[5]', '$eve[6]', '$eve[7]', '$eve[8]', '$eve[9]') ");
        
		return $db->lastInsertId();
}

function dbAddEventAddressRecord($newEvent_ID, $venue_address_id, $db){
		$catch = $db->exec("INSERT INTO fkevent_venue(Event_id, Venue_Address_id)
							VALUES ('$newEvent_ID', '$venue_address_id')");	
}

function dbAddVenueAddress($addr, $db){
		$catch = $db->exec("INSERT INTO venue_address(firstline, secondline, city, county, postcode) VALUES ('$addr[0]', '$addr[1]', '$addr[2]', '$addr[3]', '$addr[4]') ");
		return $db->lastInsertId();	
}

function dbCheckEventExists($title, $db){
	$result = $db->query("SELECT title FROM event WHERE title = '$title'");
	if($result->rowCount() > 0){
		return True;
	}else{
		return False;
	}	
}

function dbCheckVenueAddressExists($firstline, $postcode, $db){
	$result = $db->query("SELECT venue_address_id FROM venue_address WHERE postcode = '$postcode' AND firstline = '$firstline'");
	if($result->rowCount() > 0){
		$rowdata = $result->fetch(PDO::FETCH_ASSOC);
		return $rowdata["venue_address_id"];
	}else{
		return null;
	}
}

function display_events(){
    global $dbname;
	global $dbusername;
	global $dbpassword;
    //db connection
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //query
$sql= "SELECT title FROM event";
$result = $db->query($sql);
//generate dropdown
echo '<select name="unwantedevent">';
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo '<option value="'.$row['title'].'">'.$row['title'].'</option>';
}
echo '</select>';
    return True;
}

function delete_event($unwantedevent){
    global $dbname;
	global $dbusername;
	global $dbpassword;
	
	//Login to database
	$db = new PDO("mysql:dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $eventtodelete= $unwantedevent['unwantedevent'];
    $result = $db->exec("DELETE FROM event WHERE title = '$eventtodelete'");
	if($result == 1){
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

if($url_pieces[1] == 'addevent'){
	if($verb == 'POST'){
		if(check_parameters_add_event($_POST)){
			try{
				$boolSuccess = create_event($event, $address);
			} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
		}else{
			$boolSuccess = False;
		}
	}
}
else if($url_pieces[1]=='showevents'){
try{
    $boolSuccess= True;
} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
}
else if($url_pieces[1]=='deleteevent'){
    try{
    $boolSuccess = delete_event($_POST);  
} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
}
else{
	echo 'unknown path';
}
?>

<!-- Generate some HTML to indicate success or failure -->
<!DOCTYPE html>
<html>
<head>
	<title>Add User</title>
</head>
<body>
	
<?php //HTML for event add success/fail
	if($url_pieces[1] == 'addevent'){
		if($boolSuccess){
		?>
			<h1>Success</h1>
			<p>A new event with title <?php echo $event[0] ?> was added.</p>
		<?php }else{ ?>
			<h1>Failure</h1>
			<p>An event with title <?php echo $event[0] ?> already exists.</p>	
		<?php } 
	}
    
    //HTML for showevents
    else if($url_pieces[1] == 'showevents'){  
		if($boolSuccess){
		?>
    <form action="deleteevent" method="post">
        <?php if($url_pieces[1]=='showevents'){
    try{
    $boolSuccess=display_events();  
} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
}?>
    <input type="submit" value="Delete event" />
      </form>
			<h1>Success</h1>
			<p>Events are shown in dropdown menu</p>
		<?php }else{ ?>
			<h1>Failure</h1>
			<p>The events are not shown in dropdown menu</p>	
		<?php }	
	}
    
    //HTML for deleting events success/fail
    else if($url_pieces[1] == 'deleteevent'){  
		if($boolSuccess){
		?>
<form action="deleteevent" method="post">
        <?php if($url_pieces[1]=='deleteevent'){
    try{
    $boolSuccess=display_events();  
} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
}?>
    <input type="submit" value="Delete event" />
      </form>
			<h1>Success</h1>
			<p>Event has been deleted</p>
		<?php }else{ ?>
			<h1>Failure</h1>
			<p>Event has not been deleted</p>	
		<?php }	
	}
    else{
		
	}
?>
</body>	
<? } ?>
