<?php

get_header(); ?>
	<div class="row" >
		<div class="col-sm-12">
			<?php
				if( function_exists('mt_send_email_to_unconfirmed_users') ) {
					mt_send_email_to_unconfirmed_users();
				} else {
					echo '';
				}
			
			?>
		</div>
	</div>

<?php get_footer(); ?>