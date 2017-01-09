function addnewuser() {
	//user variables
	var email = $('#email').val();
	var new_password = $('#new-password').val();
	var FirstName = $('#FirstName').val();
	var LastName = $('#LastName').val();
	var Phone = $('#Phone').val();
	//user address variables
	var FirstLine = $('#FirstLine').val();
	var SecondLine = $('#SecondLine').val();
	var City = $('#City').val();
	var County = $('#County').val();
	var PostCode = $('#PostCode').val();

	//Variables to authenticate
	//var info = {username:email, password:new_password};
	
	// set AJAX datastring to be sent
	var dataString = 'email1=' + email + '&password1=' + new_password + '&FirstName1=' + FirstName + '&LastName1=' + LastName + '&Phone1=' + Phone + '&FirstLine1=' + FirstLine + '&SecondLine1=' + SecondLine + '&City1=' + City + '&County1=' + County + '&PostCode1=' + PostCode;
	// Returns message stating fields are all not filled out
	if (email == '' || new_password == '' || FirstName == '' || LastName == '' || Phone == '' || FirstLine == '' || SecondLine == '' || City == '' || County == '' || PostCode == '')
	{
		alert ("Please Fill All Fields ");
	}
	// If passwords do not match, form will not be submitted
	else if (Boolean(passwordnotmatch)){
		alert ("Passwords must Match!");
	}
	else if(CheckPass(document.form2.password)==false){
		alert ("Please make sure your password is in the right format");
	}
	else{			//AJAX code to submit form.
		$.ajax({
			type: "POST",
			url: "user.php/adduser",
			data: dataString,
			dataType: 'json',
			cache: false,
			success: function (result) {
			// Returns message whether email already exists or not. when the entered information is stored in database.
				alert(result['window_message']);
				//Input to login form details from create form
				$("#username").val(email);
				$("#password").val(new_password);
				//If email did not exist, then successful login and redirection to main.html
				if (result['login']){
				document.form1.submit();
				}
				}
		})
	}
	return false;	
}
function addnewevent() {
	//event variables
	var title = $('#title').val();
	var capacity = $('#capacity').val();
	var startdate = $('#startdate').val();
	var starttime = $('#starttime').val();
	var enddate = $('#enddate').val();
	var endtime = $('#endtime').val();
	var description = $('#neweventdescription').val();
	var category = $('#neweventcategory').val();
	var ticketstartdate = $('#ticketstartdate').val();
	var ticketenddate = $('#ticketenddate').val();
	//event address variables
	var FirstLine = $('#FirstLine').val();
	var SecondLine = $('#SecondLine').val();
	var City = $('#City').val();
	var County = $('#County').val();
	var PostCode = $('#PostCode').val();
	
	// set AJAX datastring to be sent
	var dataString = 'title=' + title + '&capacity=' + capacity + '&startdate=' + startdate + '&starttime=' + starttime + '&enddate=' + enddate + '&endtime=' + endtime + '&description=' + description + '&category=' + category + '&ticketstartdate=' + ticketstartdate + '&ticketenddate=' + ticketenddate +  '&FirstLine=' + FirstLine + '&SecondLine=' + SecondLine + '&City=' + City + '&County=' + County + '&PostCode=' + PostCode;
	if (title == '' || capacity == '' || startdate == '' || starttime == '' || enddate == '' || endtime == '' || description == '' || category == '' || ticketstartdate == '' || ticketenddate == '' || FirstLine == '' || SecondLine == '' || City == '' || County == '' || PostCode == '')
	{
		alert ("Please Fill All Fields ");
	}else if(isdatelater("EventDate") == false){
		alert ("The event must end later than its start date!")

	}else if(isdatelater("EventTicket")== false){
		alert ("The event's ticket sale end date must be at least 1 day later than its start date!")
	}
	else{
		//AJAX code to submit form.
		$.ajax({
			type: "POST",
			url: "event.php/addevent",
			data: dataString,
			cache: false,
			success: function(html) {
			// Returns successful data submission message when the entered information is stored in database.
				alert(html);
				$('#form').trigger("reset");
			},
			error: function()
			{
				alert('failed');
			}
			})
	};
	return false;
}

//*Functions in top bar of HTML page*//
//Shows hidden items when menu bar button is pressed
function search() {
        $("#Image").hide();
		$("#addevent").hide();
        $("#hosting").hide();
		$("#search").toggle();
		$("#attending").hide();
		$('#searchbtn').css({"background-color":"green"});
		$('#hostbtn').css({"background-color":"#483d8b"});
		$('#attendingbtn').css({"background-color":"#483d8b"});
}
function host() {
        $("#Image").hide();
		$("#addevent").toggle();
        $("#hosting").toggle();
		$("#search").hide();
		$("#attending").hide();
		hostedevents();
		$('#searchbtn').css({"background-color": "#483d8b"});
		$('#hostbtn').css({"background-color":"green"});
		$('#attendingbtn').css({"background-color":"#483d8b"});
}
function attending() {
        $("#Image").hide();
		$("#addevent").hide();
        $("#hosting").hide();
		$("#search").hide();
		$("#attending").toggle();
		eventsattending();
		$('#searchbtn').css({"background-color":"#483d8b"});
		$('#hostbtn').css({"background-color":"#483d8b"});
		$('#attendingbtn').css({"background-color":"green"});
}
// Logout Functiions
function logout() {
	$("#logout").toggle();
}
function logoutconfirm(){
	$.ajax({
				type: "POST",
				url: "logout.php",
				data: '',
				cache: false,
				success: function() {
					window.location.assign('login.html');
				},
				error: function()
				{
					alert('failed');
				}
				})  
}
//*Search Events Functions*//
//*Search events by date or by category					*//
//*AJAX GET to server to populate table with event details from database	*//
function find(str) {
	var dataA = "showevents" + str;
	if (str == "bydate") {
		var data1 = $('#searchdate1').val();
		var data2 = $('#searchdate2').val();
		var dataB = data1 + "." + data2;
	}
	if (str == "bycategory") {
		var dataB = $('#desiredeventcategory').val();
	}
	if (str =="" || dataB=="." ) {
		$('#events_table').innerHTML="";
		return;
  	} 
  	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
  	} else { // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
  	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) { 
			document.getElementById('events_table').innerHTML=this.responseText;
	}};
  	xmlhttp.open("GET","event.php/" + dataA + "/"+ dataB,true);
  	xmlhttp.send();
}
// Function returns true if date 2 is later than date 1
function isdatelater(str){
	if (str == "EventDate"){
		var eventstartdate = $('#startdate').val();
		var eventstarttime = $('#starttime').val();
		var date1 = eventstartdate + eventstarttime;

		var eventenddate = $('#enddate').val();
		var eventendtime = $('#endtime').val();
		var date2 = eventenddate + eventendtime;
	}

	if (str == "EventTicket"){
		var date1 = $('#ticketstartdate').val();
		var date2 = $('#ticketenddate').val();
		if (date2 > date1){
		//Insert response to date 2 being later than date 1
        	$("#dateerror1").hide();
			return true;
		}else{
		//Insert response to date 2 being earlier than date 1
        	$("#dateerror1").show();
        	return false;
		}
	}
	if (str == "SearchEvent"){
		var date1 = $('#searchdate1').val();
		var date2 = $('#searchdate2').val();
		
	}
	if (date2 >= date1){
		//Insert response to date 2 being later than date 1
        $("#dateerror").hide();
		return true;
	}else{
		//Insert response to date 2 being earlier than date 1
        $("#dateerror").show();
        return false;
	}
}
//AJAX to server to assign user as participant to event
function attendevent(eventID) {
	var dataString = 'eventID=' + eventID;
	//AJAX code to submit form.
	$.ajax({
			type: "POST",
			url: "event.php/addguest",
			data: dataString,
			cache: false,
			success: function() {
				//Reload table with 
				find("bycategory");
				},
			error: function()
			{
				alert('failed');
			}
			})  
}
//*Display past events with option to see reviews*//
//*AJAX GET to server to populate table with past event details from database	*//
function displaypastevents() {
	//AJAX
  	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
  	} else { // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
  	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) { 
			document.getElementById('events_table').innerHTML=this.responseText;
	}};
  	xmlhttp.open("GET","event.php/showpastevents",true);
  	xmlhttp.send();
}
function showfeedback(eventID){
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
  	} else { // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
  	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) { 
			document.getElementById('userfeedback').innerHTML=this.responseText;
	}};
  	xmlhttp.open("GET","event.php/showeventfeedback/" + eventID,true);
  	xmlhttp.send();
}

//* Host Events Functions					*//
//*AJAX GET to server to populate table with event details from database	*//
function hostedevents() {
	if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange = function() {
	 if (this.readyState == 4 && this.status == 200) { document.getElementById('hostedevents').innerHTML=this.responseText;
													 }
  };
  xmlhttp.open("GET","event.php/showhostedevents",true);
  xmlhttp.send();
  }
//Listen for button click in hosted events table to pass eventID to showguests()
$(document).ready(function(){
 $("#hostedevents").on('click','.btndisplayguests',function(){
         // get the current row
        var currentRow=$(this).closest("tr"); 
        var eventdata = getrowdata(currentRow);
        showguests(eventdata['ID'], eventdata['title']);
        $("#eventguests").toggle();
    });
 });

function givefeedback(eventID){
        // show feedback form
        var html = '<h3 id="eventname"></h3>' + 
    	'<input type="textarea" id="comments"/>' +
    	'<input type="number" id="ratings" min="0" max="5">' +
    	'<input type="box" class= "submit" id="submit" onclick="submitfeedback(' + eventID + ')" value="Submit Feedback!"/>'	
    	$("div#feedbacktext").empty();
        $("#feedbacktext").append(html);
        $("#feedbacktext").show();
}
function getrowdata(currentRow){
    var eventID= currentRow.find(".roweventID").html(); // get current row table cell TD class= 'hostedeventID' value
    var eventtitle= currentRow.find(".roweventtitle").html(); // get current row table cell TD class= 'hostedeventtitle' value
    var eventdata = [];
    eventdata['ID'] = eventID;
    eventdata['title'] = eventtitle;
	return eventdata;
}
function showguests(eventID, eventtitle) {
	var data1 = "<p>"+eventtitle+"</p>";
	if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange = function() {
	 if (this.readyState == 4 && this.status == 200) { 
		var data2=this.responseText;
		var data = data1 + data2;
	 	document.getElementById('eventguests').innerHTML=data;
													 }
  };
  xmlhttp.open("GET","event.php/showparticipants/"+ eventID,true);
  xmlhttp.send();
}

//* Events I'm Attending Functions */
//*AJAX GET to server to populate table with event details from database *//
function eventsattending(){
	if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange = function() {
	 if (this.readyState == 4 && this.status == 200) { document.getElementById('eventsattending').innerHTML=this.responseText;
													 }
  };
  xmlhttp.open("GET","event.php/showeventsattending",true);
  xmlhttp.send();
}
function submitfeedback(eventID){
	var comments = $('#comments').val();
	var ratings = $('#ratings').val();
	if(comments == '' || ratings == ''){
		alert ("Please fill in all fields");
	}
	else{
		var dataString = 'comments=' + comments + '&ratings=' + ratings +'&eventID=' + eventID;
		//AJAX code to submit form.
		$.ajax({
			type: "POST",
			url: "event.php/submitfeedback",
			data: dataString,
			cache: false,
			success: function() {
				eventsattending();
			}
		})
	}
}

//*Form Validation*/
function check_passwords_match() {
	var valid5 = document.getElementById("val5");
	//check to see if password matches and assign variable passwordnotmatch
	if(document.getElementById('new-password').value === document.getElementById('confirm-password').value) {
		passwordnotmatch = null;
		valid5.style.display = "none";
	} 
	else {
		passwordnotmatch = 1;
		valid5.style.display = "block";
	}
}

function CheckPass(inputText) {   
	var passw2 = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;  
	var valid4 = document.getElementById("val4");
	if(inputText.value.match(passw2)) {  
		valid4.style.display = "none";
		return true;
	}  
	else {   
		valid4.style.display = "block";
		return false;
	}  
}  
