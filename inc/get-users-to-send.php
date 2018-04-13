<?php
function mt_get_un_confirmed_users( $period = '' ){
	global $wpdb;
	$user_table = $wpdb->prefix.'users';
	$user_meta_table = $wpdb->prefix.'usermeta';
	$current_time = current_time( 'Y-m-d H:i:s' );
	
	if( $period ) {
		
		$return = $wpdb->get_results( "SELECT `ID`,`user_email`,`display_name`,`user_registered` ,TIMESTAMPDIFF(DAY,`user_registered`,'{$current_time}' ) AS days FROM {$user_table} users INNER JOIN {$user_meta_table} meta ON users.ID = meta.user_id WHERE meta.`meta_key` = 'activation_key' AND meta.`meta_value` != 'confirmed' AND TIMESTAMPDIFF(DAY,`user_registered`,'{$current_time}') = $period" );
		
		//var_dump($wpdb->last_query ) ;
		
	} else{
	$return = $wpdb->get_results( "SELECT `ID`,`user_email`,`display_name`,`user_registered`,meta.`meta_value`,TIMESTAMPDIFF(DAY,`user_registered`,'{$current_time}' ) AS days FROM {$user_table} users INNER JOIN {$user_meta_table} meta ON users.ID = meta.user_id WHERE meta.`meta_key` = 'activation_key' AND meta.`meta_value` != 'confirmed'" );
	}
	
	return $return;
}