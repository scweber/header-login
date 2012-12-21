<?php
/**
 *
 * @package Header_Login
 * @version 1.2
 */
/*
Plugin Name: Header Login
Plugin URI: https://github.com/scweber/Header_Login 
Description: This plugin will automatically log a user into WordPress if they are logged into Access Manager.
This allows for a user to log into Access Manager and then be automatically logged into Wordpress, without having to navigate to the Admin Console.
Author: Scott Weber and Matthew Ehle
Version: 1.2
Author URI: https://github.com/scweber
*/

/*  Copyright 2012  Scott Weber/Matthew Ehle  (email : scweber@novell.com, mehle@novell.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Activation Hook
function hl_activation_hook() {
	update_option('hl_userLogin_Header', $_POST['expired-post-status']);
	update_option('hl_userEmail_Header', $_POST['expired-post-status']);
	update_option('hl_userFirstname_Header', $_POST['expired-post-status']);
	update_option('hl_userLastname_Header', $_POST['expired-post-status']);
	update_option('hl_userNicename_Header', $_POST['expired-post-status']);
	update_option('hl_userDisplayname_Header', $_POST['expired-post-status']);
	update_option('hl_authAttribute', $_POST['expired-post-status']);
	update_option('hl_createNewUser', $_POST['expired-post-status']);
	update_option('hl_defaultRole', $_POST['expired-post-status']);
	update_option('hl_logoutURL', $_POST['expired-post-status']);
}

//Deactivation Hook
function hl_deactivation_hook() {
	delete_option('hl_userLogin_Header', $_POST['expired-post-status']);
	delete_option('hl_userEmail_Header', $_POST['expired-post-status']);
	delete_option('hl_userFirstname_Header', $_POST['expired-post-status']);
	delete_option('hl_userLastname_Header', $_POST['expired-post-status']);
	delete_option('hl_userNicename_Header', $_POST['expired-post-status']);
	delete_option('hl_userDisplayname_Header', $_POST['expired-post-status']);
	delete_option('hl_authAttribute', $_POST['expired-post-status']);
	delete_option('hl_createNewUser', $_POST['expired-post-status']);
	delete_option('hl_defaultRole', $_POST['expired-post-status']);
	delete_option('hl_logoutURL', $_POST['expired-post-status']);
}

function hl_plugin_menu()
	{add_submenu_page('options-general.php',__('Header Login Options','header-login'),__('Header Login','header-login'),'edit_plugins',basename(__FILE__),'hl_menu');}	
	
//Create a new user with the Header Data
function hl_create_new_user($user_id, $user_login, $email, $fname, $lname, $setAsSubscriber) {
	error_log("Creating New User...");
	//Populate the userdata array
	$userdata = array(
		'ID'		=> $user_id,
		'user_login' 	=> $user_login,
		'user_email' 	=> $email,
		'first_name' 	=> $fname,
		'last_name'  	=> $lname,
		'user_nicename' => $user_login,
		'display_name' 	=> $user_login);
	if($setAsSubscriber)
		{$userdata['role'] = "contributor";}	

	wp_insert_user($userdata);
	return $userdata;
}//End hl_create_new_user

//Update the current user with the Header Data
function hl_update_existing_user($user_id, $user_login, $email, $fname, $lname, $setAsSubscriber) {
	error_log("Updating Existing User...");
	//Populate the userdata array
	$userdata = array(
		'ID'            => $user_id,
		'user_login'    => $user_login,
		'user_email'    => $email,
		'first_name'    => $fname,
		'last_name'     => $lname,
		'user_nicename' => $user_login,
		'display_name'  => $user_login);
	if($setAsSubscriber)
		{$userdata['role'] = "contributor";}

	wp_update_user($userdata);
	return $userdata;
}//End hl_update_existing_user

function hl_authenticate_username($user, $username, $pass) {
	$user = new WP_User($user->ID);
	return $user;
}//End hl_authenticate_username

function hl_user_login() {
	$headers = apache_request_headers(); //Get the headers present
	
	if(!is_user_logged_in() && (isset($headers['X-cn']) && ($headers['X-cn'] != ""))) { //User logged into AM, but not WP
		$errors = "";
		//error_log($headers['X-cn'] . " is logged into AM, but not WP.  Logging them into WP...");
		
		$user_login	= $headers['X-cn'];
		$user_email	= $headers['X-email'];
		$user_firstname	= $headers['X-firstname'];
		$user_lastname	= $headers['X-lastname'];
		
		if($user_login) {
			$user_id = username_exists($user_login); //Is is a valid, current WP user?

			if(!$user_id) //Not a current WP user
				{$userdata = hl_create_new_user($user_id, $user_login, $user_email, $user_firstname, $user_lastname, true);}
			else //Already a current WP user
				{$userdata = hl_update_existing_user($user_id, $user_login, $user_email, $user_firstname, $user_lastname, false);}
			
			wp_authenticate($userdata->user_login, NULL);	
			wp_set_auth_cookie($user_id, false); //Set the Authorization Cookie
			wp_redirect($_SERVER['REQUEST_URI']); //Redirect back to current location
			exit;
		}
		else if(empty($user_login))
			{$errors->add('empty_username', __('<strong>ERROR</strong>: The username header is empty.'));}
	}		
	else if(is_user_logged_in() && (!isset($headers['X-cn']) || ($headers['X-cn'] == ""))) { //User logged into WP, but not AM
		//error_log($current_user->user_login . " is logged into WP, but not AM. Logging them out of WP...");
		wp_logout();
		wp_redirect($_SERVER['REQUEST_URI']);
		exit;
	}
	else if(is_user_logged_in() && (isset($headers['X-cn']) && ($headers['X-cn'] != ""))) { //User is logged into WP and AM	
		//error_log($headers['X-cn'] . " is currently logged into AM and WP.");
		if(strpos($_SERVER['REQUEST_URI'], 'wp-login.php')) {
			$redirect_to = str_replace('wp-login.php', '', $_SERVER['REQUEST_URI']);
			wp_redirect($redirect_to);
			exit;
		}
	}
	else {
		//error_log("Nobody is logged into AM or WP");
	}

?>
	<?php
	if($errors) //If error, display their username and e-mail
	{?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>WordPress &rsaquo; Error</title>
			<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
			<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
			<script type="text/javascript">
				function focusit() {
					document.getElementById('log').focus();
				}
				window.onload = focusit;
			</script>
		</head>
		<body>
		<h3>There has been a problem.  Please contact the help desk with the information below.</h3>
		<?php
			if ($errors)
				echo "<br \><strong>User:</strong> $user_login";
				echo "<br \><strong>Email:</strong> $user_email";
		?>
		</body>
		</html>
	<?php
	}
} //End hl_user_login()

function hl_admin_bar_render() {
	global $wp_admin_bar;

	$hl_logout_url = $_SERVER['REQUEST_URI'];
	$hl_logout_url = parse_url($hl_logout_url);	
	$hl_logout_url = $hl_logout_url['HOST'] . "/AGLogout";

	$user_id      = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url  = get_edit_profile_url( $user_id );

	if ( ! $user_id )
        	return;

	$wp_admin_bar->add_group( array(
        	'parent' => 'my-account',
	        'id'     => 'user-actions',
	) );

	$user_info  = get_avatar( $user_id, 64 );
	$user_info .= "<span class='display-name'>{$current_user->display_name}</span>";

	if ( $current_user->display_name !== $current_user->user_nicename )
        	$user_info .= "<span class='username'>{$current_user->user_nicename}</span>";

	$wp_admin_bar->add_menu( array(
        	'parent' => 'user-actions',
	        'id'     => 'user-info',
        	'title'  => $user_info,
	        'href'   => $profile_url,
        	'meta'   => array(
                	'tabindex' => -1,
        	),
	) );
	$wp_admin_bar->add_menu( array(
        	'parent' => 'user-actions',
	        'id'     => 'edit-profile',
        	'title'  => __( 'Edit My Profile' ),
	        'href' => $profile_url,
	) );
	$wp_admin_bar->add_menu( array(
        	'parent' => 'user-actions',
	        'id'     => 'logout',
        	'title'  => __( 'Log Out' ),
	        'href'   => $hl_logout_url,
	) );		
}//End hl_admin_bar_render

//Activation Hook
//register_activation_hook(__FILE__, 'hl_activation_hook');

//Deactivation Hook
//register_deactivation_hook(__FILE__, 'hl_deactivation_hook');

//Action Hooks
add_action('init', 'hl_user_login', 1);
add_action('wp_before_admin_bar_render', 'hl_admin_bar_render', 1);
add_action('admin_menu', 'hl_plugin_menu');

//Remove Filter
remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);

//Add Filter
add_filter('authenticate', 'hl_authenticate_username', 10, 3);

?>