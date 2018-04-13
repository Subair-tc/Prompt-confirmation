<?php
	function mt_get_active_templates() {
		global $wpdb;
		$table = $wpdb->prefix.'mail_templates';
		$current_time = current_time( 'Y-m-d H:i:s' );
		$templates =  $wpdb->get_results(  " SELECT * FROM {$table} WHERE status =1 AND TIMESTAMPDIFF(DAY,`last_run_at`, '$current_time' ) > 0" );
		
		return $templates;
	}

	