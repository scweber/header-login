<?php
/**
 *
 * @package Header_Login
 * @version 2.6
 */
/*
Plugin Name: Header Login
Plugin URI: https://github.com/scweber/header-login 
Description: This plugin will automatically log a user into WordPress if they are logged into Access Manager.
This allows for a user to log into Access Manager and then be automatically logged into Wordpress, without having to navigate to the Admin Console.
Author: Scott Weber and Matthew Ehle
Version: 2.6
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
//Default Values
define('HL_NEWUSERROLE','subscriber');

//Activation Hook
function hl_activation_hook() {
	//Set Default Settings
	update_site_option('hl_userLogin_Header', '');
	update_site_option('hl_userEmail_Header', '');
	update_site_option('hl_userFirstname_Header', '');
	update_site_option('hl_userLastname_Header', '');
	update_site_option('hl_userNicename_Header', '');
	update_site_option('hl_userDisplayname_Header', '');
	update_site_option('hl_authHeader', '');
	update_site_option('hl_createNewUser', '');
	update_site_option('hl_defaultRole', HL_NEWUSERROLE);
	update_site_option('hl_logoutURL', '');
	update_site_option('hl_settingsSaved', '');
}

//Deactivation Hook
function hl_deactivation_hook() {
	delete_site_option('hl_userLogin_Header');
	delete_site_option('hl_userEmail_Header');
	delete_site_option('hl_userFirstname_Header');
	delete_site_option('hl_userLastname_Header');
	delete_site_option('hl_userNicename_Header');
	delete_site_option('hl_userDisplayname_Header');
	delete_site_option('hl_authHeader');
	delete_site_option('hl_createNewUser');
	delete_site_option('hl_defaultRole');
	delete_site_option('hl_logoutURL');
	delete_site_option('hl_settingsSaved');
}

function hl_menu() {
	//Update the values in the database, send error message if all required fields not filled in.
	if(isset($_POST['header-login-save']) && $_POST['header-login-save']) {
		if($_POST['user-login-header'] != "" && $_POST['user-email-header'] != "" && $_POST['auth-header'] != "" && $_POST['logout-url'] != "") {
			update_site_option('hl_userLogin_Header', $_POST['user-login-header']);
			update_site_option('hl_userEmail_Header', $_POST['user-email-header']);
			update_site_option('hl_userFirstname_Header', $_POST['user-firstname-header']);
			update_site_option('hl_userLastname_Header', $_POST['user-lastname-header']);
			update_site_option('hl_userNicename_Header', $_POST['user-nicename-header']);
			update_site_option('hl_userDisplayname_Header', $_POST['user-displayname-header']);
			update_site_option('hl_authHeader', $_POST['auth-header']);
			update_site_option('hl_createNewUser', $_POST['create-new-user']);
			update_site_option('hl_defaultRole', $_POST['new-user-role']);
			update_site_option('hl_logoutURL', $_POST['logout-url']);
			update_site_option('hl_settingsSaved', 'true');
			
			?><div id="message" class="updated">
				<p><strong><?php _e('Settings Saved') ?></strong></p>
			</div>
		<?php
		}
		else if($_POST['user-login-header'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for User Login Header, reset to previously saved value.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['user-email-header'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for User Email Header, reset to previously saved value.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['auth-header'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for Authentication Header, reset to previously saved value.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['logout-url'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for Logout URL, reset to previously saved value.') ?> </strong></p>
			</div>
		<?php
		}
	}

	//Get the current values out of the database and fill in the view
	$user_login_header 	 = get_site_option('hl_userLogin_Header');
	$user_email_header 	 = get_site_option('hl_userEmail_Header');
	$user_firstname_header 	 = get_site_option('hl_userFirstname_Header');
	$user_lastname_header 	 = get_site_option('hl_userLastname_Header');
	$user_nicename_header 	 = get_site_option('hl_userNicename_Header');
	$user_displayname_header = get_site_option('hl_userDisplayname_Header');
	$auth_header 		 = get_site_option('hl_authHeader');
	$create_new_user 	 = get_site_option('hl_createNewUser', 0);
	$new_user_role 		 = get_site_option('hl_defaultRole', HL_NEWUSERROLE);
	$hl_logout_url 		 = get_site_option('hl_logoutURL');
	
	$create_new_user_true = '';
	$create_new_user_false = '';
	if($create_new_user == 0)
		$create_new_user_false = 'checked="checked"';
	else if($create_new_user == 1)
		$create_new_user_true = 'checked="checked"';
	
	echo '<div class="wrap">';
        echo '<h2>' . __('Header Login Options','header-login') . '</h2>';
	echo '</div>';
?>
	<p>
		<?php _e('The Header Login plugin sets the WordPress attributes equal the headers passed from Access Manager.'); ?>
	</p>

	<form method="post" id="header_login_save_options">
		<h3><?php _e('User Attributes','header-login'); ?></h3>
		<p>
			<?php _e('The plugin will overwrite any current attributes, so if you wish to maintain the current value for a WordPress attribute, then leave the respective header field blank.'); ?>
		</p>
		<table class="form-table">
			<tr valign-"top">
				<th>
					<label for="wp-attribute"><strong><?php _e('WordPress Attribute','header-login'); ?></strong></label>                       
					<td>
						<label for="header"><strong><?php _e('Header','header-login'); ?></strong></label>
                        <br/>
					</td>
				</th>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-login-header"><strong><?php _e('Username*','header-login'); ?></strong></label></th>
				<td>
					<input type="text" name="user-login-header" id="user-login-header" value="<?php echo $user_login_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-email-header"><strong><?php _e('E-mail*','header-login'); ?></strong></label></th>
				<td>
					<input type="text" name="user-email-header" id="user-email-header" value="<?php echo $user_email_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-firstname-header"><?php _e('First Name','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-firstname-header" id="user-firstname-header" value="<?php echo $user_firstname_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-lastname-header"><?php _e('Last Name','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-lastname-header" id="user-lastname-header" value="<?php echo $user_lastname_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-nicename-header"><?php _e('Nickname','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-nicename-header" id="user-nicename-header" value="<?php echo $user_nicename_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-displayname-header"><?php _e('Displayname','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-displayname-header" id="user-displayname-header" value="<?php echo $user_displayname_header ?>" size="25" />
					<br/>
				</td>
			</tr>
		</table>
		<h3><?php _e('Header Login Settings','header-login'); ?></h3>
		<table class="form-table">
			<tr valign-"top">
				<th scope="row"><label for="auth-header"><strong><?php _e('Authenticating Header*','header-login'); ?></strong></label></th>
				<td>
					<input type="text" name="auth-header" id="auth-header" value="<?php echo $auth_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="create-new-user"><?php _e('Automatically Create New Users','header-login'); ?></label></th>
				<td>
					<input type="radio" name="create-new-user" id="create-new-user-true" value="1" <?php echo $create_new_user_true ?> /> <label for="create-new-user-true"><?php _e('Yes','header-login'); ?></label>
					<input type="radio" name="create-new-user" id="create-new-user-false" value="0" <?php echo $create_new_user_false ?> /> <label for="create-new-user-false"><?php _e('No','header-login'); ?></label>
					<br/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="new-user-role"><?php _e('New Users Role','header-login'); ?></label></th>
				<td>
					<select name="new-user-role" id="new-user-role">
						<option <?php if($new_user_role == 'administrator') {echo 'selected="selected"';} ?> value="administrator"><?php _e('Administrator','header-login'); ?></option>
						<option <?php if($new_user_role == 'editor') {echo 'selected="selected"';} ?> value="editor"><?php _e('Editor','header-login'); ?></option>
						<option <?php if($new_user_role == 'author') {echo 'selected="selected"';} ?> value="author"><?php _e('Author','header-login'); ?></option>
						<option <?php if($new_user_role == 'contributor') {echo 'selected="selected"';} ?> value="contributor"><?php _e('Contributor','header-login'); ?></option>
						<option <?php if($new_user_role == 'subscriber') {echo 'selected="selected"';} ?> value="subscriber"><?php _e('Subscriber','header-login'); ?></option>
					</select>
					<br/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="logout-url"><strong><?php _e('Logout URL*','header-login'); ?></strong></label></th>
				<td>
					<?php echo $_SERVER['SERVER_NAME'] . "/" ?><input type="text" name="logout-url" id="logout-url" value="<?php echo $hl_logout_url ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<strong>* Required</strong>
				</th>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="header-login-save" class="button-primary" value="<?php _e('Save Changes','header-login'); ?>" />
		</p>
	</form>
<?php
}

//Set up Plugin Menu
function hl_plugin_menu() {//Set up the plugin menu
	if(is_multisite()) //Is multisite?
		{add_submenu_page('settings.php',__('Header Login Options','header-login'),__('Header Login','header-login'),'manage_options','header-login','hl_menu');}
	else
		{add_submenu_page('options-general.php',__('Header Login Options','header-login'),__('Header Login','header-login'),'edit_plugins',basename(__FILE__),'hl_menu');}
}

function hl_add_to_all_blogs($userdata) {
	global $wpdb;
	
	$blogList = $wpdb->get_results("SELECT blog_id FROM " . $wpdb->blogs);
	foreach($blogList as $blog)
		{add_user_to_blog($blog->blog_id, $userdata['ID'], $userdata['role']);}
}

//Create a new user with the Header Data
function hl_create_new_user($user_id, $user_login, $email, $fname, $lname, $user_nicename, $user_displayname, $user_role, $updateRole) {
	error_log("Creating New User...");
	//Populate the userdata array
	$userdata = array(
		'ID'		=> $user_id,
		'user_login' 	=> $user_login,
		'user_email' 	=> $email);
		
	if($fname != "")
		{$userdata['first_name'] = $fname;}
	if($lname != "")
		{$userdata['last_name'] = $lname;}
	if($user_nicename != "")
		{$userdata['user_nicename'] = $user_nicename;}
	if($user_displayname != "")
		{$userdata['display_name'] = $user_displayname;}
	if($updateRole)
		{$userdata['role'] = $user_role;}	

	wp_insert_user($userdata);
	
	if(is_multisite())  //If multi-site add new user to each blog
		{hl_add_to_all_blogs($userdata);}
	
	return $userdata;
}//End hl_create_new_user

//Update the current user with the Header Data
function hl_update_existing_user($user_id, $user_login, $email, $fname, $lname, $user_nicename, $user_displayname, $user_role, $updateRole) {
	error_log("Updating Existing User...");
	//Populate the userdata array
	$userdata = array(
		'ID'		=> $user_id,
		'user_login' 	=> $user_login,
		'user_email' 	=> $email);
		
	if($fname != "")
		{$userdata['first_name'] = $fname;}
	if($lname != "")
		{$userdata['last_name'] = $lname;}
	if($user_nicename != "")
		{$userdata['user_nicename'] = $user_nicename;}
	if($user_displayname != "")
		{$userdata['display_name'] = $user_displayname;}
	if($updateRole)
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

	$user_login_header 	 = get_site_option('hl_userLogin_Header');
        $user_email_header  	 = get_site_option('hl_userEmail_Header');
        $user_firstname_header 	 = get_site_option('hl_userFirstname_Header');
        $user_lastname_header 	 = get_site_option('hl_userLastname_Header');
        $user_nicename_header 	 = get_site_option('hl_userNicename_Header');
        $user_displayname_header = get_site_option('hl_userDisplayname_Header');
        $auth_header 		 = get_site_option('hl_authHeader');
        $create_new_user 	 = get_site_option('hl_createNewUser', 0);
        $new_user_role 		 = get_site_option('hl_defaultRole', HL_NEWUSERROLE);

	$current_user = wp_get_current_user();
	if($current_user->user_login != $headers[$user_login_header])
		{wp_logout();}

	if(is_user_logged_in())
		{show_admin_bar(true);}
	
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

	$hl_logout_url = get_site_option('hl_logoutURL');

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
		'href' 	 => $profile_url,) );
	$wp_admin_bar->add_menu( array(
		'parent' => 'user-actions',
		'id'     => 'logout',
		'title'  => __( 'Log Out' ),
		'href'   => $hl_logout_url,) );		
}//End hl_admin_bar_render

//Activation Hook
register_activation_hook(__FILE__, 'hl_activation_hook');

//Deactivation Hook
register_deactivation_hook(__FILE__, 'hl_deactivation_hook');

//Action Hooks
add_action('admin_menu', 'hl_plugin_menu');
add_action('network_admin_menu', 'hl_plugin_menu');

$settingsSaved = get_site_option('hl_settingsSaved', 'false');

if($settingsSaved == true) {
	add_action('init', 'hl_user_login', 1);
	add_action('wp_before_admin_bar_render', 'hl_admin_bar_render', 1);

	//Remove Filter
	remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);

	//Add Filter
	add_filter('authenticate', 'hl_authenticate_username', 10, 3);
}
?>
