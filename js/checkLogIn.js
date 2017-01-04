$(function(){
	        $.ajax({
            type: "POST",
            url: "checkLogInStatus.php",
            data: '',
            cache: false,
            success: function(returned) {
// Returns successful data submission message when the entered information is stored in database.
				if(returned != 'null'){
                    //welcome message
                    alert('Welcome to SearchParty ' + returned);
                   document.getElementById('usernameheader').innerHTML = returned;
				}
				else{
					window.location.assign('login.html');
					alert('You are not logged in, please log in');
				}

            }
		});
});