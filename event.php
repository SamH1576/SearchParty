<?php
//Set connection parameters
include 'Database-config.php';
//start session
session_start();

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
//Adding an event to database with appropriate corresponding data such as event host, event address, event details
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

	return $valid;}

function create_event($eve, $addr){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	//Login to database
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
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
	return True;}

function dbAddEvent($eve, $db){
		$catch = $db->exec("INSERT INTO event(title, capacity, startdate, starttime, enddate, endtime, description, category, ticket_startdate, ticket_enddate)
							VALUES ('$eve[0]', '$eve[1]', '$eve[2]', '$eve[3]', '$eve[4]', '$eve[5]', '$eve[6]', '$eve[7]', '$eve[8]', '$eve[9]') ");
        
		return $db->lastInsertId();}

function dbAddEventAddressRecord($newEvent_ID, $venue_address_id, $db){
		$catch = $db->exec("INSERT INTO fkevent_venue(Event_id, Venue_Address_id)
							VALUES ('$newEvent_ID', '$venue_address_id')");	}

function dbAddVenueAddress($addr, $db){
		$catch = $db->exec("INSERT INTO venue_address(firstline, secondline, city, county, postcode) VALUES ('$addr[0]', '$addr[1]', '$addr[2]', '$addr[3]', '$addr[4]') ");
		return $db->lastInsertId();	}

function dbCheckEventExists($title, $db){
	$result = $db->query("SELECT title FROM event WHERE title = '$title'");
	if($result->rowCount() > 0){
		return True;
	}else{
		return False;
	}	}

function dbAssignEventHost($hosteventID, $db) {
        $catch = $db->exec("INSERT INTO fkhost(User_ID, Event_ID)
							VALUES ('" . $_SESSION['usernameID'] . "', '$hosteventID')");}

function dbCheckVenueAddressExists($firstline, $postcode, $db){
	$result = $db->query("SELECT venue_address_id FROM venue_address WHERE postcode = '$postcode' AND firstline = '$firstline'");
	if($result->rowCount() > 0){
		$rowdata = $result->fetch(PDO::FETCH_ASSOC);
		return $rowdata["venue_address_id"];
	}else{
		return null;
	}}


//Functions to search for event and assign user as participant to event
function display_events($type) {
    global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
    //db connection
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    global $url_pieces;
    if ($type == "bycategory"){
    	$category = $url_pieces[2];
    	//Query to obtain key event details from two seperate tables using fkevent_venue constraint by category
		$sql= "SELECT e.Event_ID AS Event_ID, e.Capacity as Capacity, e.title AS Title, e.startdate AS StartDate, e.StartTime AS StartTime, e.EndDate AS EndDate, e.EndTime AS EndTime, e.Description AS Description, e.category AS Category, e.Ticket_enddate AS stopsaledate, CONCAT(v.firstline ,', ', v.city ,' ', v.postcode) AS Address 
		FROM event AS e
		JOIN fkevent_venue AS fk ON fk.Event_ID = e.Event_ID
		JOIN venue_address AS v ON fk.Venue_Address_ID = v.Venue_Address_ID WHERE e.category = '$category'
		ORDER BY Title";
	}else if ($type == "bydate"){
		$date = explode('.', $url_pieces[2]);
		//Query to obtain key event details from two seperate tables using fkevent_venue constraint by event date
		$sql= "SELECT e.Event_ID AS Event_ID, e.Capacity as Capacity, e.title AS Title, e.startdate AS StartDate, e.StartTime AS StartTime, e.EndDate AS EndDate, e.EndTime AS EndTime, e.Description AS Description, e.category AS Category, e.Ticket_enddate AS stopsaledate, CONCAT(v.firstline ,', ', v.city ,' ', v.postcode) AS Address 
		FROM event AS e
		JOIN fkevent_venue AS fk ON fk.Event_ID = e.Event_ID
		JOIN venue_address AS v ON fk.Venue_Address_ID = v.Venue_Address_ID WHERE e.startdate >= '$date[0]' AND e.startdate <= '$date[1]'
		ORDER BY StartDate ASC";
    }
    $result = $db->query($sql);
    //Generate table of events wrt constraints 
    if($result->rowCount() > 0){    
        echo "<table id='eventtable'>
        <tr>
        <th class='tableheads'>Book Tickets</th>
        <th class='tableheads'>Title</th>
        <th class='tableheads'>Start Date</th>
        <th class='tableheads'>Start Time</th>
        <th class='tableheads'>End Date</th>
        <th class='tableheads'>End Time</th>
        <th class='tableheads'>Description</th>
        <th class='tableheads'>Category</th>
        <th class='tableheads'>Address</th>
        </tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr id='tableinfo'>";
        //Function to echo correct input type depending on user and event. If user is a host/already going, message will show as "User is a host/going". If user can be a participant, button will be displayed with "I am going to this event!"
        echoinputforuser($row, $db); 
        echo "<td class='eventtoattend'>" . $row['Title'] . "</td>";
       	echo "<td class='eventtoattend'>" . $row['StartDate'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['StartTime'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['EndDate'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['EndTime'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['Description'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['Category'] . "</td>";
        echo "<td class='eventtoattend'>" . $row['Address'] . "</td>";
        echo "</tr>";
        }
        echo "</table>";
    }
    else{
    	if ($type == "bycategory"){
        echo "<p id='noevent'>No events of this category.</p>";
    	}
    	if ($type == "bydate"){
    	$displaydate[0] =date("d F Y", strtotime($date[0]));
    	$displaydate[1] =date("d F Y", strtotime($date[1]));
        echo "<p id='noevent'>No events between the $displaydate[0] and the $displaydate[1].</p>";
    	}
    }
	$db = null;}

function echoinputforuser($row, $db){
    $eventtitle =  $row['Title'];
    $eventID = $row['Event_ID'];
    $capacity = $row['Capacity'];
    //Queries to database to check if user is host 
    $sql = "SELECT * FROM fkhost WHERE User_ID = " . $_SESSION['usernameID'] . " AND Event_ID =   $eventID ";
    $result = $db->query($sql);
    //Queries to database to check if user is participant
    $sql1 = "SELECT * FROM fkguest_list WHERE User_ID = " . $_SESSION['usernameID'] . " AND Event_ID =  $eventID ";
    $result1 = $db->query($sql1);
    //Query to database to check if capacity of event has been reached
    $sql2 = "SELECT * FROM fkguest_list WHERE Event_ID = $eventID";
    $result2 = $db->query($sql2);
    
    /*Output correct input message*/
    //Check if event sales has ended.
    if(datepassed($row['stopsaledate'])){
        echo "<td class='eventerror'>The ticket sales for this event has ended</td>";
    }
    //If event is full, echo event full message
    else if($result2->rowCount()==$capacity){
        echo "<td class='eventerror'>Event is full</td>";
    }
    //Check if User is a host. If true, display message saying User is host
    else if($result->rowCount() > 0){
        echo "<td class='eventerror'>You are hosting this event </td>";
    }
    //Check if User is going to event. If true, display message saying User is going
    else if($result1->rowCount() > 0){
        echo "<td id='going'>You are going to this event</td>";
        }
    //Give button input to assign user as a guest
    else if ($result->rowCount() == 0 && $result1->rowCount() == 0) {
        echo "<td id='eventselect'><input type='button' id='submit' onclick= 'attendevent(".$eventID.")' value= 'I am going for $eventtitle'/></td>";
    }
}
function dbAssignUserasGuest($array){
    global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
    //db connection
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $eventID = $array['eventID'];
    $sql = "INSERT INTO fkguest_list (Event_ID, User_ID) VALUES ($eventID, " . $_SESSION['usernameID'] . ")";
    $catch = $db->exec($sql);
    $db = null;}

//Function to show hosted events and guestlist for said events
function dbShowhostedEvents() {
    global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	$userID = $_SESSION['usernameID'];
    //db connection
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT e.Event_ID AS Event_ID, e.StartDate AS StartDate, e.title AS Title, e.Capacity as Capacity
	FROM event AS e
	JOIN fkhost AS fk ON fk.Event_ID = e.Event_ID WHERE fk.User_ID =  $userID ORDER BY Title";
	$result = $db->query($sql);

		if($result->rowCount() > 0){
		echo "<table id='hostedevents'>
        <tr>
        <th class='tablehead'>Event ID</th>
        <th class='tablehead'>Event Title</th>
        <th class='tablehead'>Event Date</th>
        <th class='tablehead'>Capacity</th>
        <th class='tablehead'>Input Type</th>
        </tr>";
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	        echo "<tr>";
	        echo "<td class='roweventID'>" . $row['Event_ID'] . "</td>";
	        echo "<td class='roweventtitle'>" . $row['Title'] . "</td>";

			//Convert date to better format
	        $displaydate = date_format(new DateTime($row['StartDate']),"d F Y");
	        echo "<td id='info' class='hostdate'> $displaydate ";
	        if(datepassed($row['StartDate'])){
	        	echo "-event passed";
	        }
	        	"</td>";
	        //Check number of participants
	        $guests = numberofguestsattending($row, $db);
	        $maxnum = $row['Capacity'];
	        echo "<td id='info' class='guestnum'> $guests / $maxnum";
	        if ($guests==$maxnum){
	        	echo "-sold out";
	 		}
	        echo "</td>";
	        echo "<td id='info'><input type='button' id='submit' class='btndisplayguests' value= 'Display $guests guest(s)'/></td>";
	        echo "</tr>";
        }
    }else{
    	echo "You currently aren't hosting any events";
    }
}
function dbShowEventParticipants(){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
    //db connection
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    global $url_pieces;
    $eventID = $url_pieces[2];
	$sql = "SELECT u.email AS Guest_Email, CONCAT(u.firstname , ' ', u.lastname) AS Name 
	FROM user AS u
	JOIN fkguest_list AS fk ON fk.User_ID = u.User_ID WHERE fk.event_ID =  $eventID ORDER BY Name";
	$result = $db->query($sql);
	if ($result->rowCount() > 0){
		echo "<table id='guesttable'>
        <tr>
        <th class='tableheads'>Guest Email</th>
        <th class='tableheads'>Name</th>
        </tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        	echo "<tr>";
	        echo "<td class='info2'>" . $row['Guest_Email'] . "</td>";
	        echo "<td class='info2'>" . $row['Name'] . "</td>";
	        echo "</tr>";
        }
	}else{
		echo "No guests are attending this event";
	}
}

//Function to show events attending
function dbShowEventsAttending(){
	global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	$userID = $_SESSION['usernameID'];
    //db connection
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT e.Event_ID AS Event_ID, e.StartDate AS StartDate, e.title AS Title, e.description AS Description 
	FROM event AS e
	JOIN fkguest_list AS fk ON fk.Event_ID = e.Event_ID WHERE fk.User_ID =  $userID ORDER BY Title";
	$result = $db->query($sql);

		if($result->rowCount() > 0){
		echo "<table id='eventsattending'>
        <tr>
        <th class='tableheads'>Event ID</th>
        <th class='tableheads'>Event Title</th>
        <th class='tableheads'>Event Date</th>
        <th class='tableheads'>Description</th>
        <th class='tableheads'>Feedback</th>
        </tr>";
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	        echo "<tr>";
	        echo "<td id='info3' class='roweventID'>" . $row['Event_ID'] . "</td>";
	        echo "<td id='info3' class='roweventtitle'>" . $row['Title'] . "</td>";
			//Convert date to better format
	        $displaydate = date_format(new DateTime($row['StartDate']),"d F Y");
	        echo "<td id='info3'> $displaydate </td>";
	        //Check number of participants
	        echo "<td id='info3'>".$row['Description']."</td>";
	        if(datepassed($row['StartDate'])){
	        	echo "<td id='info3'><input type='button' id='submit' class='btngivefeedback' value= 'Give Feedback'/></td>";
	        }
	        echo "</tr>";
        }
    }else{
    	echo "You currently aren't hosting any events";
    }
}
//General functions
function numberofguestsattending($row, $db) {
	$eventID = $row['Event_ID'];
	$sql = "SELECT * FROM fkguest_list WHERE Event_ID = $eventID";
	$result = $db->query($sql);
	$guests = $result->rowCount();
	return $guests;
}
function datepassed($date) {
    //Get date in correct format to compare
    $todaysdate = date("Y-m-d");
    //Check if date has passed ticket sale end date
    if ($date < $todaysdate){
        return True;
    }
    else{
        return False;
    }}
function delete_event($unwantedevent){
    global $dbname;
	global $dbusername;
	global $dbpassword;
	global $dbhost;
	//Login to database
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname", "$dbusername", "$dbpassword");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $eventtodelete= $unwantedevent['unwantedevent'];
    $result = $db->exec("DELETE FROM event WHERE title = '$eventtodelete'");
	if($result == 1){
		return True;
	}else{
		return False;
	}}

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
		/* window output message */
		if($boolSuccess){
            echo "A new event with title $event[0] was added.";
        }
        else {
            echo "An event with title $event[0] already exists.";
        }
	}
}
else if($url_pieces[1]=='showeventsbycategory'){
        display_events("bycategory");
    }
else if($url_pieces[1]=='showeventsbydate'){
        display_events("bydate");
    }

else if($url_pieces[1]=='deleteevent'){
    try{
    $boolSuccess = delete_event($_POST);  
	} catch (UnexpectedValueException $e){
				throw new Exception("Resource does not exist", 404);
			}
}
else if($url_pieces[1]=='addguest'){
       dbAssignUserasGuest($_POST);
    }
else if($url_pieces[1]=='showhostedevents'){
       dbShowhostedEvents();
    }
else if($url_pieces[1]=='showeventsattending'){
       dbShowEventsAttending();
    }
else if($url_pieces[1]=='showparticipants'){
       dbShowEventParticipants();
    }    
else{
	echo 'unknownadsf path';
	}

?>
