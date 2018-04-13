<?php


function mt_mail_template_form() {
	if( isset( $_GET['temp_id'] ) ) {
		global $wpdb;
		
		$form_id	='mt_add_new_template';
		$form_name	='add_new_template';
		$table 		= $wpdb->prefix.'mail_templates';
		$temp_id 	= $_GET['temp_id'];
		
		$template =  $wpdb->get_row( $wpdb->prepare( " SELECT * FROM {$table} WHERE ID= %d",$temp_id ) );
		
		$name 		= stripslashes_deep($template->name);
		$subject 	= stripslashes_deep($template->subject);
		$header 	= stripslashes_deep($template->header);
		$footer 	= stripslashes_deep($template->footer);
		$message 	= stripslashes_deep($template->template);
		$send_after	= stripslashes_deep($template->send_after);
		$page_title = "Edit Template";
		$submit_button_label = 'Update!';
		
	} else {
		$temp_id	= 0;
		$name 		= '';
		$subject 	= '';
		$header 	= '';
		$footer 	= '';
		$message 	= '';
		$send_after	= '';
		$form_id	='mt_add_new_template';
		$form_name	='add_new_template';
		
		
		$page_title = "Add New Mail Templates";
		$submit_button_label = 'Create Now!';
	}
	
	?>
	<div class="mail-templates-loading"></div>
	<div class="mail-templates-success"></div>
	<div class="mail-templates">
		<div class="alert alert-info">
			<strong>Info!</strong> Please use <b><?php echo bloginfo('url') ?>/mail-templates/ </b> as cron URL, should run once everyday.
		</div>
		<a href="admin.php?page=mail-templates" class="btn btn-success"> back to all templates</a>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<h4 class="text-center temp_title"> <?php echo $page_title; ?></h4>
				
				<div class="new_tmeplate_form">
					<form name="<?php echo $form_name; ?>" id="<?php echo $form_id; ?>" action="" method="POST" >
						<input type="hidden" name="temp_id" id="mt_temp_id" value="<?php echo $temp_id; ?>" />
						<div class="form-group">
							<label for="TemplateName">Name</label>
							<input name="TemplateName" type="text" class="form-control" id="mt_template_name" placeholder="Template Name" value="<?php echo $name; ?>" />
							<div class="name_error" ></div>
						</div>
						
						<div class="form-group">
							<label for="TemplateSubject">Subject</label>
							<input name="TemplateSubject" type="text" class="form-control" id="mt_template_subject" placeholder="Template Subject" value="<?php echo $subject; ?>"/>
							<div class="subject_error" ></div>
						</div>
						
						<div class="form-group">
							<label for="TemplateDaysAfter">No of days to send this mail</label>
							<input name="TemplateDaysAfter" type="number" class="form-control" id="mt_template_send_after" placeholder="Template Send After User Registration (In days)" value="<?php echo $send_after; ?>"/>
							<div class="send_after_error" ></div>
						</div>
						
						<div class="form-group">
							<label for="TemplateSubject">Header</label>
							<?php //wp_editor( $header , 'mt_template_header', $settings = array('textarea_name'=>'TemplateHeader') ); ?> 
							
							<textarea class="form-control" rows="15" id="mt_template_header" name="TemplateHeader">
								<?php echo $header; ?>
							</textarea>
							<div class="header_error" ></div>
						</div>
						
						<div class="form-group">
							<label for="TemplateSubject">Message</label>
							<?php //wp_editor( $message , 'mt_template_message', $settings = array('textarea_name'=>'TemplateMessage') ); ?>
							
							<textarea class="form-control" rows="15" id="mt_template_message" name="TemplateMessage">
								<?php echo $message; ?>
							</textarea>
							<div class="message_error" ></div>							
						</div>
						
						<div class="form-group">
							<label for="TemplateSubject">Footer</label>
							<?php //wp_editor( $footer , 'mt_template_footer', $settings = array('textarea_name'=>'TemplateFooter') ); ?> 
							<textarea class="form-control" rows="15" id="mt_template_footer" name="TemplateFooter">
								<?php echo $footer; ?>
							</textarea>
							<div class="footer_error" ></div>
						</div>
						
						<input type="submit" id="add-new-template" class="btn btn-primary" value=" <?php echo $submit_button_label; ?>" />
						
					</form>
				</div>
			</div>
		
		</div>
	</div>
	<?php
	
	
}

function mt_add_new_template(){
		$name 		= stripslashes_deep($_POST['name']);
		$subject 	= stripslashes_deep($_POST['subject']);
		$header		= stripslashes_deep($_POST['header']);
		$footer		= stripslashes_deep($_POST['footer']);
		$message 	= stripslashes_deep($_POST['message']);
		$send_after	= stripslashes_deep($_POST['send_after']);
		$temp_id	= stripslashes_deep($_POST['mt_temp_id']);
		
	if( !$name || !$subject  || !$header || !$footer || !$message || ! $send_after ) {
		$return['error'] 	= true;
		$return['content'] 	= 'please fill all requuired fields';
		return json_encode($return);
		exit;
	}
	
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$data = array(
		'name'			=> $name,
		'user_id'		=> get_current_user_id(),
		'subject'		=> $subject,
		'header'		=> $header,
		'footer'		=> $footer,
		'template'		=> $message,
		'send_after'	=> $send_after,
		'created_at'	=> current_time( 'Y-m-d H:i:s' ),
		'last_run_at'	=> date('Y-m-d H:i:s',strtotime("-1 days"))
	);
	
	if( $temp_id ) {
		$where = array(
			'ID'	=> $temp_id
		);
		if( $wpdb->update( $table, $data,$where ) ){
			$return['error'] 	= false;
			$return['content'] 	= 'success';
		} else {
			$return['error'] 	= true;
			$return['content'] 	= 'something went wrong, please try again';
		}
	} else {
		if( $wpdb->insert( $table, $data ) ){
			$return['error'] 	= false;
			$return['content'] 	= 'success';
		} else {
			$return['error'] 	= true;
			$return['content'] 	= 'something went wrong, please try again';
		}
	}
	
	echo json_encode($return);
	exit;
}
add_action('wp_ajax_mt_add_new_template','mt_add_new_template');