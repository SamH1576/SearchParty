
	function ValidateEmail(inputText) { 
	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
	var valid = document.getElementById("val1");
	if(inputText.value.match(mailformat)) { 
		valid.style.display = "none";
	}  
	else {  
		valid.style.display = "block";
	}  
}  

function CheckPassword(inputText) {   
	var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;  
	var valid2 = document.getElementById("val2");
	if(inputText.value.match(passw)) {  
		valid2.style.display = "none"; 
	}  
	else {   
		valid2.style.display = "block";
	}  
}  

function ValidateEmail2(inputText) { 
	var mailformat2 = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
	var valid3 = document.getElementById("val3");
	if(inputText.value.match(mailformat2)) { 
		valid3.style.display = "none";
	}  
	else {  
		valid3.style.display = "block";
	}  
} 

function CheckPass(inputText) {   
	var passw2 = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;  
	var valid4 = document.getElementById("val4");
	if(inputText.value.match(passw2)) {  
		valid4.style.display = "none";
	}  
	else {   
		valid4.style.display = "block";
	}  
}  