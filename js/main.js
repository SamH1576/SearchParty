function addnewuser() {
    var email = $('#email').val();
    var password = $('#password').val();
    var FirstName = $('#Firstname').val();
    var LastName = $('#LastName').val();
    var Phone = $('#Phone').val();
    var FirstLine = $('#FirstLine').val();
    var SecondLine = $('#SecondLine').val();
    var City = $('#City').val();
    var County = $('#County').val();
    var PostCode = $('#PostCode').val();
    
// Returns message stating fields are all not filled out
    var dataString = 'email1=' + email + '&password1=' + password + '&FirstName1=' + FirstName + '&LastName1=' + LastName + '&Phone1=' + Phone + '&FirstLine1=' + FirstLine + '&SecondLine1=' + SecondLine + '&City1=' + City + '&County1=' + County + '&PostCode1=' + PostCode;
    if (email == '' || password == '' || FirstName == '' || LastName == '' || Phone == '' || FirstLine == '' || SecondLine == '' || City == '' || County == '' || PostCode == '')
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
            cache: false,
            success: function(html) {
// Returns successful data submission message when the entered information is stored in database.
				window.location.assign('main.html');
                alert(html);
            }
            }
        )};
    }
    return false;
}
function host() {
	var form = document.getElementById("addevent");
	if (form.style.display == "none"){
		form.style.display = "block";
	}
	else{
		form.style.display = "none";
	}
	var search = document.getElementById("search");
	if (search.style.display == "block"){
		search.style.display = "none";
	}
	var attending = document.getElementById("attending");
	if (attending.style.display == "block"){
		attending.style.display = "none";
	}
    alert ("boo");
}
function search() {
	var search = document.getElementById("search");
	if (search.style.display == "none"){
		search.style.display = "block";
	}
	else {
		search.style.display = "none";
	}
	var form = document.getElementById("addevent");
	if (form.style.display == "block"){
		form.style.display = "none";
	}
	var attending = document.getElementById("attending");
	if (attending.style.display == "block"){
		attending.style.display = "none";
	}
}
function attending() {
	var attending = document.getElementById("attending");
	if (attending.style.display == "none"){
		attending.style.display = "block";
	}
	else{
		attending.style.display = "none";
	}
	var form = document.getElementById("addevent");
	if (form.style.display == "block"){
		form.style.display = "none";
	}
	var search = document.getElementById("search");
	if (search.style.display == "block"){
		search.style.display = "none";
	}
}
function check_passwords_match(){
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