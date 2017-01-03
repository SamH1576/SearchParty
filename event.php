<?php
//Set connection parameters
include 'Database-config.php';
//start session
session_start();

$event = array();
$address = array();
$data = "";

/***********************************************************************/
//  Exception Handlers
/***********************************************************************/
set_exception_handler(function($e){
	$code = $e->getCode() ?: 400;
	header("Content-Type: application/json", NULL, $code);
	echo json_encode(["error" => $e->getMessage()]);
	exit;
});
/***********************************************************************/
//  Functions 
/***********************************************************************/
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
    
	if (($array["FirstLine"]==null)){
		$valid = False;
	}else{
		$address[0] = $array["FirstLine"];
	}
	
	if (($array["SecondLine"]==null)){
		$valid = False;
	}else{
		$address[1] = $array["SecondLine"];
	}
	
	if (($array["City"]==null)){
		$valid = False;
	}else{
		$address[2] = $array["City"];
	}

	if (($array["County"]==null)){
		$valid = False;
	}else{
		$address[3] = $array["County"];
	}
	
	if (($array["PostCode"]==null)){
		$valid = False;
	}else{
		$address[4] = $array["PostCode"];
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
    //Update fkhost with new Event_ID and session variable
    dbAssignEventHost($newEvent_ID, $db);
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

function dbAssignEventHost($hosteventID, $db) {
        $catch = $db->exec("INSERT INTO fkhost(User_ID, Event_ID)
							VALUES ('" . $_SESSION['usernameID'] . "', '$hosteventID')");
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
    global $url_pieces;
    $category = $url_pieces[2];
    //query to obtain key event details from two seperate tables using fkevent_venue constraint
$sql= "SELECT e.title AS Title, e.startdate AS StartDate, e.StartTime AS StartTime, e.EndDate AS EndDate, e.EndTime AS EndTime, e.Description AS Description, e.category AS Category, CONCAT(v.firstline ,', ', v.city ,' ', v.postcode) AS Address 
FROM event AS e
JOIN fkevent_venue AS fk ON fk.Event_ID = e.Event_ID
JOIN venue_address AS v ON fk.Venue_Address_ID = v.Venue_Address_ID WHERE e.category = '$category'
ORDER BY Title";
$result = $db->query($sql);
    if($result->rowCount() > 0){
//Generate table with details of events and a going button
        /*Table headers*/
echo "<table>
<tr>
<th> </th>
<th>Title</th>
<th>Start Date</th>
<th>Start Time</th>
<th>End Date</th>
<th>End Time</th>
<th>Description</th>
<th>Category</th>
<th>Address</th>
</tr>";
        /*Table rows*/
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
     //Function to echo correct input type depending on user and event. If user is a host / already going, message will show as "User is a host/going". If user can be a participant, button will be displayed with "I am going to this event!"
    echoinputforuser($category, $db);    
    echo "<td><input type='button' id='goingtoevent' onclick='attendevent()' value='I am going to this event' /></td>";
    echo "<td>" . $row['Title'] . "</td>";
    echo "<td>" . $row['StartDate'] . "</td>";
    echo "<td>" . $row['StartTime'] . "</td>";
    echo "<td>" . $row['EndDate'] . "</td>";
    echo "<td>" . $row['EndTime'] . "</td>";
    echo "<td>" . $row['Description'] . "</td>";
    echo "<td>" . $row['Category'] . "</td>";
    echo "<td>" . $row['Address'] . "</td>";
    echo "</tr>";
    }
echo "</table>";
    }
// If no events of the category exist, display message
    else{
        echo "No events of this category.";
    }
    $db = null;
}
function echoinputforuser($category, $db) {
    
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
        display_events();
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
/* window output message */
if($url_pieces[1] == 'addevent'){
		if($boolSuccess){
            echo "A new event with title $event[0] was added.";
        }
        else {
            echo "An event with title $event[0] already exists.";
        }
}

?>

<?php /*
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
<? } ?>*/
