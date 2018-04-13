<?php
/*
Plugin Name: Prompt-confirmation
Version: 1.2
Description: Plugin for sending remainder mail for unconfirmed users.
Author: Subair T C
Author URI:
Plugin URI:
Text Domain: prompt-confirmation
Domain Path: /languages
*/


/* Set constant path to the plugin directory. */
define( 'PROMPT_CONFIRMATION_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'PROMPT_CONFIRMATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/* Set the constant path to the plugin's includes directory. */
define( 'PROMPT_CONFIRMATION_INC', PROMPT_CONFIRMATION_PLUGIN_PATH . trailingslashit( 'inc' ), true );
define( 'PROMPT_CONFIRMATION_TEMPLATES', PROMPT_CONFIRMATION_PLUGIN_PATH . trailingslashit( 'templates' ), true );





/*
*	Function to Enqueue required scripts and Style.
*/
function add_prompt_confirmation_script() {
	wp_register_script( 'prompt-confirmation', plugins_url( '/js/prompt-confirmation.js', __FILE__ ), true );
	wp_enqueue_script( 'prompt-confirmation' );
	
	wp_localize_script('prompt-confirmation', 'Ajax', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	));
	wp_register_style( 'prompt-confirmation', plugins_url( '/css/prompt-confirmation.css', __FILE__ ) );
	wp_enqueue_style( 'prompt-confirmation' );
}

//add_action( 'wp_enqueue_scripts', 'add_prompt_confirmation_script' ); **no front end view now!**


/*
	Adding admin styles and scripts
*/
function add_prompt_confirmation_admin_style() {
	
	wp_register_style( 'prompt-confir-css', plugins_url( '/css/custom.css', __FILE__ ) );
	wp_enqueue_style( 'prompt-confir-css' );
	
	
	wp_register_script( 'prompt-confir-js', plugins_url( '/js/custom.js', __FILE__ ), true );
	wp_enqueue_script( 'prompt-confir-js' );
	
	wp_register_script( 'mt_dataTable', plugins_url( '/js/jquery.dataTables.js', __FILE__ ), true );
	wp_enqueue_script( 'mt_dataTable' );
	wp_register_script( 'mt_dataTable_bootstrap', plugins_url( '/js/dataTables.bootstrap.min.js', __FILE__ ), true );
	wp_enqueue_script( 'mt_dataTable_bootstrap' );
	
	wp_localize_script('custom-js', 'Ajax', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	));
}

add_action( 'admin_enqueue_scripts', 'add_prompt_confirmation_admin_style' );



function prompt_confirmation_plugin_activate() {
	
	global $wpdb;
	$table = $wpdb->prefix.'mail_templates';
	$query = "CREATE TABLE IF NOT EXISTS {$table} ( `ID` BIGINT(20) NOT NULL AUTO_INCREMENT , `user_id` BIGINT(20) NOT NULL , `name` LONGTEXT NOT NULL, `subject` LONGTEXT NOT NULL, `header` LONGTEXT NOT NULL,`footer` LONGTEXT NOT NULL,`template` LONGTEXT NOT NULL, `send_after` INT(6) NOT NULL COMMENT 'days after registration', `created_at`  DATETIME NOT NULL,`last_run_at`  DATETIME NOT NULL,  `status` INT(2) NOT NULL DEFAULT '1', PRIMARY KEY (`ID`) )";
	$wpdb->query( $query );
	
	// Creating cron page 
	
	$the_page_title = 'Mail Templates';
    $the_page_name = 'mail-templates';
	 // the menu entry...
    delete_option("prompt_confirmation_plugin_page_title");
    add_option("prompt_confirmation_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("prompt_confirmation_plugin_page_name");
    add_option("prompt_confirmation_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("prompt_confirmation_plugin_page_id");
    add_option("prompt_confirmation_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[mt-send_email-to-unconfirmed-users]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
		// the default 'Uncatrgorised'
        $_p['post_category'] = array(1); 

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'prompt_confirmation_plugin_page_id' );
    add_option( 'prompt_confirmation_plugin_page_id', $the_page_id );
}
register_activation_hook( __FILE__, 'prompt_confirmation_plugin_activate' );

// plugin settings page

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'prompt_confirmation_plugin_add_action_links' );

function prompt_confirmation_plugin_add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'admin.php?page=mail-templates' ) . '">Settings</a>',
 );
return array_merge( $links, $mylinks );
}






// setting templates for cron page!.


//add_action("template_redirect", 'prompt_confirmation_plugin_redirect');

/*function prompt_confirmation_plugin_redirect() {
    global $wp;
    $plugindir = dirname( __FILE__ );

   if ($wp->query_vars["pagename"] == 'mail-templates') {
        $templatefilename = 'mail-templates.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/templates/' . $templatefilename;
        }
        prompt_confirmation_plugin_do_theme_redirect($return_template);
    }
}

function prompt_confirmation_plugin_do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include_once($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}
*/

//creating admin pages

add_action( 'admin_menu', 'prompt_confirmation_add_admin_menu' );
function prompt_confirmation_add_admin_menu(  ) { 
	add_menu_page( 
		'Mail Templates',
		'Mail Templates',
		'manage_options',
		'mail-templates',
		'mt_mail_templates_list',
		PROMPT_CONFIRMATION_PLUGIN_URL.'images/mail-green.png',
		82
	);
	add_submenu_page(
      null, 
      'mail-template-single',
      'Mail Templates Details Page', 
      'manage_options', 
      'mail-template-single', 
      'mt_mail_template_single'
    );
	add_submenu_page(
      'mail-templates', 
      'add-new-mail-template',
      'Add New', 
      'manage_options', 
      'add-new-mail-template', 
      'mt_mail_template_form'
    );

}




include_once( PROMPT_CONFIRMATION_INC . 'general-functions.php' );
include_once( PROMPT_CONFIRMATION_INC . 'get-users-to-send.php' );
include_once( PROMPT_CONFIRMATION_INC . 'send-email.php' );
include_once( PROMPT_CONFIRMATION_INC . 'mail-templates-list.php' );
include_once( PROMPT_CONFIRMATION_INC . 'add-new-template.php' );
include_once( PROMPT_CONFIRMATION_INC . 'mail-template-single.php' );
include_once( PROMPT_CONFIRMATION_INC . 'get-active-templates.php' );




