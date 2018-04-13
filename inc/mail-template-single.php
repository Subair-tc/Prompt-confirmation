<?php

function mt_mail_template_single(){
	
	if ( ! isset( $_GET['temp_id'] ) ) {
		?>
			<script type="text/javascript">
				window.location = <?php echo  "'" .home_url()."'";  ?>;
			
			</script>
		<?php
		exit;
	}
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$temp_id = $_GET['temp_id'];	
	$template =  $wpdb->get_row( $wpdb->prepare( " SELECT * FROM {$table} WHERE ID= %d",$temp_id ) );
	
	echo '<div class="mail-templates-loading"></div><div class="mail-templates-success"></div>';
	echo '<div class="mail-templates"><div class="row"><div class="col-sm-12">';
		
		//var_dump($template);
	?>
		<div class="alert alert-info">
			<strong>Info!</strong> Please use <b><?php echo bloginfo('url') ?>/mail-templates/ </b> as cron URL, should run once everyday.
		</div>
		
		<a href="admin.php?page=mail-templates" class="btn btn-success"> back to all templates</a>
		
		<a href="admin.php?page=add-new-mail-template&temp_id=<?php echo $temp_id; ?>" class="btn btn-warning"> edit this templates</a>
		<div class="row">
			<div class="col-sm-12">
				<h4 class="temp_title"> <?php echo $template->name; ?> </h4>
			</div>
		</div>
		
		<div class="row template_top_block">
			<div class="col-sm-6">
			
				<ul class="properties">
					<li class="Author"> Templated Created By (user ID): <b><?php echo $template->user_id; ?> </b></li>
					<li> Mail Send on  <b><?php echo $template->send_after; ?></b> Days after user registration </li>
					<li> Created on:  <b><?php echo $template->created_at; ?></b> </li>
					<li> Last Send on:  <b><?php echo $template->last_run_at; ?></b> </li>
					<li> Status:  <b><?php echo ($template->status)?'ON':'OFF'; ?></b> </li>
					
				</ul>
			</div>
			
			<div class="col-sm-6">
				<div class="test_this_cron_block">
					<form name="mt_test_cron_form" id="mt_test_cron_form" class="form-inline">
						<div class="form-group">
							<label for="user-id">User To Send</label>
							<input type="text" id="mt_users_test" name= "mt_users_test" placeholder="Users To send" />
							<input type="hidden" name="mt_temp_id" value=" <?php echo $temp_id; ?>" id="mt_temp_id" />
						</div>
						<input type="submit" id="mt_test_cron_submit" class="btn btn-primary" value="Test Now! " />
					
				</div>
			</div>
		</div>
			
		
		
		
		
		
		
		<div class="col-sm-8 col-sm-offset-2">
			<div class ="subject">
				Subject: <b><?php echo $template->subject; ?></b>
			</div>
			<div class ="message-content">
				<span>Mail Body:</span>	
				<?php
				$header_part    = stripslashes_deep($template->header);
				$footer_part	= stripslashes_deep($template->footer);
				$template_part	= stripslashes_deep($template->template);
				
				$header_part    = str_replace( '{siteurl}', site_url(), $header_part );
				$footer_part    = str_replace( '{siteurl}', site_url(), $footer_part );
				$message 		= $header_part . $template_part . $footer_part;
				echo $message;
				?>
			</div>
		</div>
		
	<?php	
	echo '</div></div></div>';
}