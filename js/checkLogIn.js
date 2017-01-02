$(function(){
	        $.ajax({
            type: "POST",
            url: "checkLogInStatus.php",
            data: '',
            cache: false,
            success: function(returned) {
// Returns successful data submission message when the entered information is stored in database.
				if(returned != 'null'){
					alert('Welcome to SearchParty ' + returned);
					//welcome message
				}
				else{
					window.location.assign('login.html');
					alert('You are not logged in, please log in');
				}

            }
		});
});