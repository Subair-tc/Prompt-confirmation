<?php


function mt_mail_templates_list(){
	?>
	<div class="mail-templates"><div class="row"><div class="col-sm-12">
		<div class="alert alert-info">
			<strong>Info!</strong> Please use <b><?php echo bloginfo('url') ?>/mail-templates/ </b> as cron URL, should run once everyday.
		</div>
		<div class="buttons"><a href="admin.php?page=add-new-mail-template" class="btn btn-success"> Add new</a></div>
	
	<?php
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$templates =  $wpdb->get_results(  " SELECT * FROM {$table}" );
	?>
	

	
	<?php
	//var_dump($template);
	if( $templates ) { ?>
		<table border="0" class="form t-style table table-resposive" id="dataTable1" width="100%" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>ID</th>
					<th>User ID</th>
					<th>Name</th>
					<th>Subject</th>
					<th>Send after<span class="info">Send after registration ( In Days )</span></th>
					<th>Created at</th>
					<th>Last run at</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>ID</th>
					<th>User ID</th>
					<th>Name</th>
					<th>Subject</th>
					<th>Send after</th>
					<th>Created at</th>
					<th>Last run at</th>
					<th>Actions</th>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach( $templates as $template ) { ?>
						<tr>
							<td><?php echo $template->ID; ?></td>
							<td><?php echo $template->user_id; ?></td>
							<td><?php echo $template->name; ?></td>
							<td><?php echo $template->subject; ?></td>
							<td><?php echo $template->send_after; ?></td>
							<td><?php echo $template->created_at; ?></td>
							<td><?php echo $template->last_run_at; ?></td>
							<td> 
								<?php 
									if ( $template->status ) { ?>
											<a class="mt_table_links" href="admin.php?page=add-new-mail-template&temp_id=<?php echo $template->ID; ?>" title="Edit template" >
												<i class="fa fa-pencil" aria-hidden="true"></i>
											</a>
											<a class="mt_table_links mt_delete_template mt_template_status_change" template_id = "<?php echo $template->ID; ?>" status_to = "0" title="Disable template" >
												<i class="fa fa-toggle-off" aria-hidden="true"></i>
											</a>
											
											
										
									<?php
									} else{ ?>
										<a class=" mt_table_links mt_activate_template mt_template_status_change" template_id = "<?php echo $template->ID; ?>" status_to = "1" title="Enable template" >
											<i class="fa fa-toggle-on" aria-hidden="true"></i>
										</a>
									<?php
									}
									
								?>
								<a class="mt_table_links" href="admin.php?page=mail-template-single&temp_id=<?php echo $template->ID; ?>" title="View template" >
									<i class="fa fa-eye" aria-hidden="true"></i>
								</a>
							</td>
						</tr>
				<?php
					}
				
				?>
			
			</tbody>
		</table>
		
	
		
		<!--<iframe width="800" height="600" src="https://app.powerbi.com/view?r=eyJrIjoiMTlhYWQ1MGQtNTIzMi00YmFhLTg2MzQtOGYxMWQzZmU4MmY0IiwidCI6IjExYzQ4ZTc4LTYxMzEtNDZmNy1hNjdkLTA3MDc0ZWRmMWE4OSIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe> -->
	<?php
	}
	
	echo '</div></div></div>';
}