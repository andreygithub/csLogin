login = {
    initialize: function() {

		// Indicator of active ajax request
		ajaxProgress = false;
		$(document)
			.ajaxStart(function() {ajaxProgress = true;})
			.ajaxStop(function() {ajaxProgress = false;});
//		if(!jQuery().ajaxForm) {
//			document.write('<script src="' + login.config.js_url + '/jquery.form.min.js"><\/script>');
//		}
		if(!jQuery().jGrowl) {
			document.write('<script src="' + login.config.js_url + '/jquery.jgrowl.min.js"><\/script>');
		}
		
		$(document).on('submit', 'form#user_login_form', function(e) {
			var params = {
				action: "user_login"
				,ctx: "web"
				,username: $('#input_login').val()
                ,password: $('#input_password').val()
				};
            var login_action_url = $('#login_action_url').val();
			$.post(login_action_url, params, function(response) {
				response = $.parseJSON(response);
				if (response.success) {
					if (response.message) {
						login.message.success(response.message);
					}
					login.user.status();
                    $('#login_main_modal').modal('hide');
                    return false;
				}
				else {
    				login.message.error(response.message);
                    return false;
				}
			});
            return false;
		});
        
        $(document).on('submit', 'form#user_registration_form', function(e) {
			var params = {
				action: "user_registration"
				,ctx: "web"
				,username: $('#input_login_username').val()
                ,email: $('#input_login_email').val()
                ,fullname: $('#input_login_fullname').val()
                ,mobilephone: $('#input_login_mobilephone').val()
                ,country: $('#input_login_country').val()
                ,zip: $('#input_login_zip').val()
                ,state: $('#input_login_state').val()
                ,city: $('#input_login_city').val()
                ,address: $('#input_login_address').val()
				};
            var login_action_url = $('#login_action_url').val();
			$.post(login_action_url, params, function(response) {
				response = $.parseJSON(response);
				if (response.success) {
					if (response.message) {
						login.message.success(response.message);
					}
					login.user.status();
                    $('#login_registration_modal').modal('hide');
                    return false;
				}
				else {
					login.message.error(response.message);
                    return false;
				}
			});                    
			return false;            
        });
        
        $(document).on('click', 'a#login_user_exit', function(e) {
            var params = {
    			action: "user_logout"
				,ctx: "web"
				};
                var login_action_url = $('#login_action_url').val();
				$.post(login_action_url, params, function(response) {
					response = $.parseJSON(response);
					if (response.success) {
						if (response.message) {
							login.message.success(response.message);
						}
						login.user.status();
                        return false;
					}
					else {
						login.message.error(response.message);
                        return false;
					}
				});
            return false;
        });
           
	}
    
};

login.user = {
	status: function(data) {
        var params = {
    		action: "user_status"
			,ctx: "web"
			};
        var login_action_url = $('#login_action_url').val();
	    $.post(login_action_url, params, function(response) {
    		response = $.parseJSON(response);
			if (response.success) {
			   	if (response.message) {
			   	login.message.success(response.message);
		    	}
				$('#login_status').html(response.data);
                return false;
		    }
			else {
				login.message.error(response.message);
                return false;
		  	}
		});
		
	}
};
login.message = {
	success: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'cs-message-success'});
		}
	}
	,error: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'cs-message-error'});
		}
	}
	,info: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'cs-message-info'});
		}
	}
	,close: function() {
		$.jGrowl('close');
	}
};
