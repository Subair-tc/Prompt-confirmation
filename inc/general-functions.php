<?php

// general funcitons


function mt_get_template_object ( $temp_id = '' ) {
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	if( $temp_id ) {
		$templates = $wpdb->get_row( $wpdb->prepare( " SELECT * FROM {$table} WHERE ID= %d",$temp_id ) );
	} else {
		$templates = $wpdb->get_results( " SELECT * FROM {$table} " );
	}
	
	return $templates;
}

// function to deactivate a templates

function mt_activate_deactivate_template( $temp_id = '',$status_to = '' ) {
	if ( isset( $_POST['temp_id'] ) ) {
		$temp_id  	= $_POST['temp_id'];
		$status_to  = $_POST['status_to'];
	}
	if( ! $temp_id || $status_to =='' ) {
		$return['error'] = true;
		$return['responce'] = 'something went wrong, please try later!.';
		echo json_encode($return);
		exit;
	}
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$where = array(
		'ID'	=> $temp_id
	);
	$data = array (
		'status'	=> $status_to
	);
	if( $wpdb->update( $table,$data,$where) ) {
		$return['error'] = false;
		$return['responce'] = 'success';
	} else {
		$return['error'] = true;
		$return['responce'] = 'something went wrong, please try later!.';
	}
	echo json_encode($return);
	exit;
}

add_action('wp_ajax_mt_activate_deactivate_template','mt_activate_deactivate_template');


function mt_send_a_template_to_users( $template,$users ) {
	if ( isset( $_POST['template'] ) ) {
		$template  	= $_POST['template'];
		$users		= $_POST['users'];
	} else{
		$return['error'] = true;
		$return['message'] = 'something went wrong';
	}
	
	$users_list	 = "'".str_replace(',',"','",$users)."'";
	//echo ($users); '<br/>';
	//echo ($users_list); exit;
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$templates =  $wpdb->get_row(  $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d",$template ));
	
	if( $templates ) {
		$user_table 	=  $wpdb->prefix.'users';
		$user_details 	=  $wpdb->get_results( "SELECT ID,user_email,display_name FROM {$user_table} WHERE ID IN ( $users_list )"  );
		//echo  $wpdb->last_query;
		//var_dump($user_details);
		
		if( $user_details ) {
			$header_part    = $templates->header;
			$footer_part	= $templates->footer;
			$template_part	= $templates->template;
			$subject		= $templates->subject;
			$from_address	= ot_get_option( 'notification_from_address' );

			//$activation_key_items = $unconfirmed_user->email . '$' . current_time( 'timestamp' );
			//$encoded = base64_encode( $activation_key_items );
			//$link = get_bloginfo( 'url' ) . '/login/?action=activation&ac_code=' . $encoded;
			
			$header_part    = str_replace( '{siteurl}', site_url(), $header_part );
			$footer_part    = str_replace( '{siteurl}', site_url(), $footer_part );
			$template_part	= str_replace( '{siteurl}', site_url(), $template_part );
			//$template_part	= str_replace( '{activation_link}', $link, $template_part );
			
			
			$headers   = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=UTF-8';
			$headers[] = 'From: ' . $from_address;
			$headers[] = "Subject: {$subject}";
			$headers[] = 'X-Mailer: PHP/' . phpversion();
		
		
			foreach( $user_details as $user_detail ) {
				
				$activation_key_items = $user_detail->user_email . '$' . current_time( 'timestamp' );
				
				
				$encoded = base64_encode( $activation_key_items );
				//update_user_meta( $user_detail->ID, 'activation_key', $encoded );
				
				$link = get_bloginfo( 'url' ) . '/login/?action=activation&ac_code=' . $encoded;
				
				$message_content	= str_replace( '{user_name}', $user_detail->display_name, $template_part );
				$message_content	= str_replace( '{activation_link}', $link, $message_content );
				$message 		= $header_part . $message_content . $footer_part;
				wp_mail( $user_detail->user_email, $subject, $message,implode( "\r\n", $headers ) );
			}
			
			$return['error'] = false;
			$return['message'] = 'success';
		} else {
			$return['error'] = true;
			$return['message'] = 'Enter valid user ID';
		}
		
		
		
	} else {
		
		$return['error'] = true;
		$return['message'] = 'something went wrong';
	}
	
	echo json_encode( $return );
	exit;
	
}
add_action('wp_ajax_mt_send_a_template_to_users','mt_send_a_template_to_users');