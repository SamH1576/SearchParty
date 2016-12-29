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
    if(document.getElementById('new-password').value === document.getElementById('confirm-password').value) {
        document.getElementById('message').innerHTML = " match";
    } else {
        document.getElementById('message').innerHTML = " no match";
    }
}