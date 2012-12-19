<?php
/**
 *
 * @package Auto_Login
 * @version 1.1
 */
/*
Plugin Name: Auto Login
Plugin URI: https://github.com/scweber/Auto-Login
Description: This plugin will automatically log a user into WordPress if they are logged into Access Manager.
This allows for a user to log into Access Manager and then be automatically logged into Wordpress, without having to navigate to the Admin Console.
Author: Scott Weber and Matthew Ehle
Version: 1.1
Author URI: https://github.com/scweber/
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

// Create a new user with the Header Data
function al_create_new_user($user_id, $user_login, $email, $fname, $lname, $setAsSubscriber) {
  error_log("Creating New User...");
	// Populate the userdata array
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
}

// Update the current user with the Header Data
function al_update_existing_user($user_id, $user_login, $email, $fname, $lname, $setAsSubscriber) {
	error_log("Updating Existing User...");
	// Populate the userdata array
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
}

function al_authenticate($userdata) {
	$username = $userdata->user_login;

	$user = apply_filters('authenticate', null, $username);

	if($user == null)
		{$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username.'));}

	$ignore_codes = array('empty_username');

	if(is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes)) 
		{do_action('wp_login_failed', $username);}
	
	return $user;
}

//Hooks
add_action('init', 'al_user_login', 1);

// Add Filter
//add_filter('authenticate', 'al_authenticate', 1);

function al_user_login() {
	$headers = apache_request_headers(); // Get the headers present

	$current_user = wp_get_current_user();
	
	if(!is_user_logged_in() && (isset($headers['X-cn']) && ($headers['X-cn'] != ""))) { // Is the user already logged into WP?
		$errors = "";
		error_log($headers['X-cn'] . " is logged into AM, but not WP.  Logging them into WP...");
		
		if(isset($headers['X-cn']) && ($headers['X-cn'] != "")) // Set the user_login if X-cn header is present
			{$user_login = $headers['X-cn'];}
		
		if(isset($headers['X-email']) && ($headers['X-email'] != "")) // Set the user_login if X-cn header is present
			{$user_email = $headers['X-email'];}
		
		if($user_login) {
			$user_id = username_exists($user_login); // Is is a valid, current WP user?
			remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3); // Remove filter
			
			if(!$user_id) // Not a current WP user
				{$userdata = al_create_new_user($user_id, $user_login, $headers['X-email'], $headers['X-firstname'], $headers['X-lastname'], true);} // Create new WP User
			else // Already a current WP user
				{$userdata = al_update_existing_user($user_id, $user_login, $headers['X-email'], $headers['X-firstname'], $headers['X-lastname'], false);} // Update existing WP User
			
			al_authenticate($userdata); //Authenticate the user
			wp_set_auth_cookie($user_id, false); //Set the Authorization Cookie
			wp_safe_redirect(admin_url());//Redirect to wp-login.php and then back to current location
		}
		else if(empty($user_login))
			{$errors->add('empty_username', __('<strong>ERROR</strong>: The username header is empty.'));}
	}		
	else if(is_user_logged_in() && (!isset($headers['X-cn']) || ($headers['X-cn'] == ""))) { // User logged into WP, but not AM
		error_log($current_user->user_login . " is logged into WP, but not AM. Logging them out of WP...");
		wp_logout();
		$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'communities/';
	}
	else if(is_user_logged_in() && (isset($headers['X-cn']) && ($headers['X-cn'] != ""))) { // User is logged into WP and AM	
		error_log($headers['X-cn'] . " is currently logged into AM and WP.");
	}
	else {
		error_log("Nobody is logged into AM or WP");
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
} //End al_user_login()

?>
