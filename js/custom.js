jQuery('document').ready(function(){
	jQuery('#mt_add_new_template').on('submit',function(e){
		e.preventDefault();
		jQuery('.mail-templates-loading').show();
		var name 		= jQuery.trim( jQuery('#mt_template_name').val() );
		var subject 	= jQuery.trim( jQuery('#mt_template_subject').val() );
		var header 		= jQuery.trim( jQuery('#mt_template_header').val() );
		var footer 		= jQuery.trim( jQuery('#mt_template_footer').val() );
		var message 	= jQuery.trim( jQuery('#mt_template_message').val() );
		var send_after 	= jQuery.trim( jQuery('#mt_template_send_after').val() );
		var mt_temp_id 	= jQuery.trim( jQuery('#mt_temp_id').val() );
		var error = 0;
		
		if( name == '' ){
			error =1;
			jQuery('.name_error').text('name is mandatory').addClass('error');
			jQuery('#mt_template_name').focus();
		} else {
			jQuery('.name_error').text('').removeClass('error');
		}
		if( subject == '' ){
			jQuery('.subject_error').text('subject is mandatory').addClass('error');
			if( error == 0 ) {
				error =1;
				jQuery('#mt_template_subject').focus();
			}
			
		} else {
			jQuery('.subject_error').text('').removeClass('error');;
		}
		
		if( header == '' ){
			jQuery('.header_error').text('header is mandatory').addClass('error');
			if( error == 0 ) {
				error =1;
				jQuery('#mt_template_header').focus();
			}
		} else {
			jQuery('.header_error').text('').removeClass('error');
		}
		
		if( footer == '' ){
			jQuery('.footer_error').text('footer is mandatory').addClass('error');
			if( error == 0 ) {
				error =1;
				jQuery('#mt_template_footer').focus();
			}
		} else {
			jQuery('.footer_error').text('').removeClass('error');
		}
		
		if( message == '' ){
			jQuery('.message_error').text('message is mandatory').addClass('error');
			if( error == 0 ) {
				error =1;
				jQuery('#mt_template_message').focus();
				
			}
		} else {
			jQuery('.message_error').text('').removeClass('error');
		}
		
		if( send_after == '' ){
			jQuery('.send_after_error').text('send_after is mandatory').addClass('error');
			if( error == 0 ) {
				error =1;
				jQuery('#mt_template_message').focus();
				
			}
		} else {
			jQuery('.send_after_error').text('').removeClass('error');
		}
		
		if( error  == 1 ) {
			jQuery('.mail-templates-loading').hide();
			return;
		}
		
		jQuery('.error-message').text('');
		
		var data = {
			action		:	'mt_add_new_template',
			name		:	name,
			subject		:	subject,
			header		:	header,
			footer		:	footer,
			message		:	message,
			send_after	:	send_after,
			mt_temp_id	: 	mt_temp_id
		};
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(response) {
				response = JSON.parse(response); 
				//console.log(response.content);
				jQuery('.mail-templates-loading').hide();
				if( response.error == true ) { 
					Alert.render(response.content, "alert-fapvoice");
				} else {
					jQuery('#mt_add_new_template').trigger("reset");
					jQuery('.mail-templates-success').show();
					setTimeout(function() {
						jQuery('.mail-templates-success').hide();
					}, 500);
					
					location.reload();
					
				}
			}
		});
		
	});
	
	
	// Activate/deactivate the templates-loading
	
	jQuery(".mt_template_status_change").on("click",function( e ) {
		e.preventDefault();
		var temp_id 	= jQuery(this).attr("template_id");
		var status_to	= jQuery(this).attr("status_to");
		var data = {
			action		:	'mt_activate_deactivate_template',
			temp_id		:	temp_id,
			status_to	:	status_to
		};
		
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(res) {
				response = JSON.parse(res);
				if( response.error == false ) {
					location.reload();
				} else {
					Alert.render(response.responce, "alert-fapvoice");
			 
				}
			}
		});
	});
	
	
	
	// test a template
	
	jQuery("#mt_test_cron_submit").on("click",function(e) {
		e.preventDefault();
		jQuery("span.error").remove();
		var users 	= jQuery.trim(jQuery("#mt_users_test").val());
		var temp_id 	= jQuery.trim(jQuery("#mt_temp_id").val());
		if( '' == users ) {
			jQuery("#mt_test_cron_submit").after("<span class='error'>Please enter user Id's</span>");
			return;
		}
		var data = {
			action		:	'mt_send_a_template_to_users',
			template	:	temp_id,
			users		:	users
		};
		jQuery('.mail-templates-loading').show();
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(res) {
				jQuery('.mail-templates-loading').hide();
				jQuery('.error').remove();
				response = JSON.parse(res);
				if( response.error == false ) {
					jQuery('.mail-templates-success').show();
					setTimeout(function() {
						jQuery('.mail-templates-success').hide();
					}, 500);
				} else {
					jQuery("#mt_test_cron_submit").after("<span class='error'>" + response.message + "</span>");
				}
				
			}
		});
	});
	
	
});