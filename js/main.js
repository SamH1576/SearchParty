function addnewuser() {

    //user variables

//=======
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
    
    // set AJAX datastring to be sent
    var dataString = 'email1=' + email + '&password1=' + new_password + '&FirstName1=' + FirstName + '&LastName1=' + LastName + '&Phone1=' + Phone + '&FirstLine1=' + FirstLine + '&SecondLine1=' + SecondLine + '&City1=' + City + '&County1=' + County + '&PostCode1=' + PostCode;
    // Returns message stating fields are all not filled out
    if (email == '' || new_password == '' || FirstName == '' || LastName == '' || Phone == '' || FirstLine == '' || SecondLine == '' || City == '' || County == '' || PostCode == '')
    {
        alert ("Please Fill All Fields ");
    }
    else
    {
// If passwords do not match, form will not be submitted
    if (Boolean(passwordnotmatch))
        {
            alert ("Passwords must Match!");
        }
        else{
//AJAX code to submit form.
        $.ajax({
            type: "POST",
            url: "user.php/adduser",
            data: dataString,
            dataType: 'json',
            cache: false,
            success: function (result) {
// Returns message whether email already exists or not. when the entered information is stored in database.
                alert(result['window_message']);
//If email did not exist, then successful login and redirection to main.html
                if (result['boolsuccess']){
                    window.location.assign('main.html');
                }
                }
            
            })
        };
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
    var description = $('#description').val();
    var category = $('#desiredeventcategory').val();
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
            },
			error: function()
            {
				alert('failed');
			}
            })
    };
    return false;
}
//Shows hidden items when menu bar button is pressed
function host() {
        $("#addevent").toggle();
        $("#search").hide();
        $("#attending").hide();
}
function search() {
        $("#addevent").hide();
        $("#search").toggle();
        $("#attending").hide();
}
function attending() {
        $("#addevent").hide();
        $("#search").hide();
        $("#attending").toggle();
}
//AJAX call to server to populate table with event details from database
function find(str) {
    if (str=="") {
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
     if (this.readyState == 4 && this.status == 200) { document.getElementById('events_table').innerHTML=this.responseText;
                                                     }
  };
  xmlhttp.open("GET","event.php/showevents/"+ str,true);
  xmlhttp.send();
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
                find($('#desiredeventcategory').val());
                },
            error: function()
            {
				alert('failed');
			}
            })  
}
function check_passwords_match() {
    //check to see if password matches and assign variable passwordnotmatch
    if(document.getElementById('new-password').value === document.getElementById('confirm-password').value) {
        document.getElementById('form-messages').innerHTML = " Passwords are a match";
        passwordnotmatch = null;
    } 
    else {
        document.getElementById('form-messages').innerHTML = " Passwords are not a match!";
        passwordnotmatch = 1;
    }
}
