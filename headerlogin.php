<?php
/**
 *
 * @package Header_Login
 * @version 2.0
 */
/*
Plugin Name: Header Login
Plugin URI: https://github.com/scweber/Header_Login 
Description: This plugin will automatically log a user into WordPress if they are logged into Access Manager.
This allows for a user to log into Access Manager and then be automatically logged into Wordpress, without having to navigate to the Admin Console.
Author: Scott Weber and Matthew Ehle
Version: 2.0
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
	
//Create a new user with the Header Data
function hl_create_new_user($user_id, $user_login, $email, $fname, $lname, $user_nicename, $user_displayname, $user_role, $setAsSubscriber) {
	//error_log("Creating New User...");
	//Populate the userdata array
	$userdata = array(
		'ID'		=> $user_id,
		'user_login' 	=> $user_login,
		'user_email' 	=> $email,
		'first_name' 	=> $fname,
		'last_name'  	=> $lname,
		'user_nicename' => $user_nicename,
		'display_name' 	=> $user_displayname);
	if($setAsSubscriber)
		{$userdata['role'] = $user_role;}	

	wp_insert_user($userdata);
	return $userdata;
}//End hl_create_new_user

//Update the current user with the Header Data
function hl_update_existing_user($user_id, $user_login, $email, $fname, $lname, $user_nicename, $user_displayname, $user_role, $setAsSubscriber) {
	//error_log("Updating Existing User...");
	//Populate the userdata array
	$userdata = array(
		'ID'            => $user_id,
		'user_login'    => $user_login,
		'user_email'    => $email,
		'first_name'    => $fname,
		'last_name'     => $lname,
		'user_nicename' => $user_nicename,
		'display_name'  => $user_displayname);
	if($setAsSubscriber)
		{$userdata['role'] = $user_role;}

	wp_update_user($userdata);
	return $userdata;
}//End hl_update_existing_user

function hl_authenticate_username($user, $username, $pass) {
	$user = new WP_User($user->ID);
	return $user;
}//End hl_authenticate_username

function hl_user_login() {
	$headers = apache_request_headers(); //Get the headers present

	$user_login_header = get_option('hl_userLogin_Header', HL_USERLOGIN);
        $user_email_header = get_option('hl_userEmail_Header', HL_USEREMAIL);
        $user_firstname_header = get_option('hl_userFirstname_Header');
        $user_lastname_header = get_option('hl_userLastname_Header');
        $user_nicename_header = get_option('hl_userNicename_Header');
        $user_displayname_header = get_option('hl_userDisplayname_Header');
        $auth_header = get_option('hl_authHeader', HL_AUTHHEADER);
        $create_new_user = get_option('hl_createNewUser', 0);
        $new_user_role = get_option('hl_defaultRole', HL_NEWUSERROLE);

	if(!is_user_logged_in() && (isset($headers[$user_login_header]) && ($headers[$user_login_header] != ""))) { //User logged into AM, but not WP
		$errors = "";
		//error_log($headers[$user_login_header] . " is logged into AM, but not WP.  Logging them into WP...");
		
		$user_login	  = $headers[$user_login_header];
		$user_email	  = $headers[$user_email_header];
		$user_firstname	  = $headers[$user_firstname_header];
		$user_lastname	  = $headers[$user_lastname_header];
		$user_nicename	  = $headers[$user_nicename_header];
		$user_displayname = $headers[$user_displayname_header];		
		
		if($create_new_user == 1) { //Create new user accounts? (1 = True, 0 = False)
			if($user_login) {
				$user_id = username_exists($user_login); //Is is a valid, current WP user?
				if(!$user_id) //Not a current WP user
					{$userdata = hl_create_new_user($user_id, $user_login, $user_email, $user_firstname, $user_lastname, $user_nicename, $user_displayname, $new_user_role, true);}
				else //Already a current WP user
					{$userdata = hl_update_existing_user($user_id, $user_login, $user_email, $user_firstname, $user_lastname, $user_nicename, $user_displayname, $new_user_role, false);}
			
				wp_authenticate($userdata->user_login, NULL);	
				wp_set_auth_cookie($user_id, false); //Set the Authorization Cookie
				wp_redirect($_SERVER['REQUEST_URI']); //Redirect back to current location
				exit;
			}
			else if(empty($user_login))
				{$errors->add('empty_username', __('<strong>ERROR</strong>: The username header is empty.'));}
		}
		else { //Don't create new user accounts.
			if($user_login) {
				$user_id = username_exists($user_login); //Valid, current WP user?
				if($user_id) { //Already a WP user
					$userdata = hl_update_existing_user($user_id, $user_login, $user_email, $user_firstname, $user_lastname, $user_nicename, $user_displayname, $new_user_role, false);
					wp_authenticate($userdata->user_login, NULL);
					wp_set_auth_cookie($user_id, false);
					wp_redirect($_SERVER['REQUEST_URI']);
					exit;
				}
				else {
				 	$errors = __('<strong>Error</strong> Unauthorized Account');
					error_log("Unauthorized account -> " . $user_login . ". Cannot create new users.");
				}
			}
		}
	}
	else if(is_user_logged_in() && (!isset($headers[$user_login_header]) || ($headers[$user_login_header] == ""))) { //User logged into WP, but not AM
		//error_log($current_user->user_login . " is logged into WP, but not AM. Logging them out of WP...");
		wp_logout();
		wp_redirect($_SERVER['REQUEST_URI']);
		exit;
	}
	else if(is_user_logged_in() && (isset($headers[$user_login_header]) && ($headers[$user_login_header] != ""))) { //User is logged into WP and AM	
		//error_log($headers[$user_login_header] . " is currently logged into AM and WP.");
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

	$hl_logout_url = get_option('hl_logoutURL');

	$hl_logout_url = $_SERVER['SERVER_NAME'] . '/' . $hl_logout_url;

	$user_id      = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url  = get_edit_profile_url( $user_id );

	if ( ! $user_id )
		{return;}

	$wp_admin_bar->add_group( array(
		'parent' => 'my-account',
		'id'     => 'user-actions',) );

	$user_info  = get_avatar( $user_id, 64 );
	$user_info .= "<span class='display-name'>{$current_user->display_name}</span>";

	if ( $current_user->display_name !== $current_user->user_nicename )
		{$user_info .= "<span class='username'>{$current_user->user_nicename}</span>";}

	$wp_admin_bar->add_menu( array(
		'parent' => 'user-actions',
		'id'     => 'user-info',
		'title'  => $user_info,
		'href'   => $profile_url,
		'meta'   => array('tabindex' => -1,),) );
	$wp_admin_bar->add_menu( array(
		'parent' => 'user-actions',
		'id'     => 'edit-profile',
		'title'  => __( 'Edit My Profile' ),
		'href' => $profile_url,) );
	$wp_admin_bar->add_menu( array(
		'parent' => 'user-actions',
		'id'     => 'logout',
		'title'  => __( 'Log Out' ),
		'href'   => $hl_logout_url,) );		
}//End hl_admin_bar_render

$settingsSaved = get_option('hl_settingsSaved', 'false');
error_log("Settings Saved: " . $settingsSaved);

if($settingsSaved == true) {
	add_action('init', 'hl_user_login', 1);
	add_action('wp_before_admin_bar_render', 'hl_admin_bar_render', 1);

	//Remove Filter
	remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);

	//Add Filter
	add_filter('authenticate', 'hl_authenticate_username', 10, 3);
}
?>
