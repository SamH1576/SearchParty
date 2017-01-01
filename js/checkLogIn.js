$(function(){
	        $.ajax({
            type: "POST",
            url: "checkLogInStatus.php",
            data: '',
            cache: false,
            success: function(bool) {
// Returns successful data submission message when the entered information is stored in database.
				if(bool){
					alert('Welcome to SearchParty');
					//welcome message
				}
				else{
					window.location.assign('login.html');
				}

            }
		});
});