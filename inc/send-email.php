<?php

function mt_send_email_to_unconfirmed_users( ) {
	$templates = mt_get_active_templates(); // get all active templates that not run today.
	//var_dump($templates );
	foreach( $templates as $template ) {
		$unconfirmed_users = mt_get_un_confirmed_users( $template->send_after ); // get unconfirmed users
		echo $template->name.'<br/><pre>';var_dump( $unconfirmed_users );echo '</pre><br/>';//exit;
		foreach( $unconfirmed_users as $unconfirmed_user ) {
			
			$header_part    = $template->header;
			$footer_part	= $template->footer;
			$template_part	= $template->template;
			$subject		= $template->subject;
			$from_address	= ot_get_option( 'notification_from_address' );

			$activation_key_items = $unconfirmed_user->user_email . '$' . current_time( 'timestamp' );
			$encoded = base64_encode( $activation_key_items );
			update_user_meta( $unconfirmed_user->ID, 'activation_key', $encoded );
			$link = get_bloginfo( 'url' ) . '/login/?action=activation&ac_code=' . $encoded;
			
			$header_part    = str_replace( '{siteurl}', site_url(), $header_part );
			$footer_part    = str_replace( '{siteurl}', site_url(), $footer_part );
			$template_part	= str_replace( '{siteurl}', site_url(), $template_part );
			$template_part	= str_replace( '{activation_link}', $link, $template_part );
			
			$message_content	= str_replace( '{user_name}', $unconfirmed_user->display_name,$template_part  );
			$message 		= $header_part . $message_content . $footer_part;
			
			$headers   = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=UTF-8';
			$headers[] = 'From: ' . $from_address;
			$headers[] = "Subject: {$subject}";
			$headers[] = 'X-Mailer: PHP/' . phpversion();
			wp_mail( $unconfirmed_user->user_email, $subject, $message,implode( "\r\n", $headers ) );
			
			
		}
		
		global $wpdb;
		$table = $wpdb->prefix.'mail_templates';
		$data = array(
			'last_run_at'	=> current_time( 'Y-m-d H:i:s' )
		);
		$where = array(
			'ID'	=> $template->ID
		);
		$wpdb->update($table,$data,$where);
	}
	
}


add_shortcode( 'mt-send_email-to-unconfirmed-users', 'mt_send_email_to_unconfirmed_users_shortcode' );
function mt_send_email_to_unconfirmed_users_shortcode() {
	return mt_send_email_to_unconfirmed_users();
}