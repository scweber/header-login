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

//Default Values
define('HL_NEWUSERROLE','subscriber');

//Activation Hook
function hl_activation_hook() {
  
	add_site_option('Activated_Plugin', 'header-login');	
	
	//Set Default Settings
	update_site_option('hl_userLogin_Header');
	update_site_option('hl_userEmail_Header');
	update_site_option('hl_authHeader');
	update_site_option('hl_defaultRole', HL_NEWUSERROLE);
	update_site_option('hl_logoutURL');
	update_site_option('hl_userFirstname_Header');
	update_site_option('hl_userLastname_Header');
	update_site_option('hl_userNicename_Header');
	update_site_option('hl_userDisplayname_Header');
	update_site_option('hl_createNewUser');
	update_site_option('hl_settingsSaved');
}

//Before loading the plugin, run the following redirect
function hl_load_plugin() {
	if(is_admin() && get_site_option('Activated_Plugin') == 'header-login') {
		delete_site_option('Activated_Plugin');
		$redirect_to = admin_url() . "options-general.php?page=headerlogin.php";
        	wp_redirect($redirect_to);
	        exit;
	}
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
				<p><strong><?php _e('Error Saving Settings: Missing value for User Login Header, reset to default.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['user-email-header'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for User Email Header, reset to default.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['auth-header'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for Authentication Header, reset to default.') ?> </strong></p>
			</div>
		<?php
		}
		else if($_POST['logout-url'] == "") {
			
			update_site_option('hl_settingsSaved', 'false');

			?> <div id="message" class="error">
				<p><strong><?php _e('Error Saving Settings: Missing value for Logout URL, reset to default.') ?> </strong></p>
			</div>
		<?php
		}
	}

	//Get the current values out of the database and fill in the view
	$user_login_header = get_site_option('hl_userLogin_Header');
        $user_email_header = get_site_option('hl_userEmail_Header');
        $user_firstname_header = get_site_option('hl_userFirstname_Header');
        $user_lastname_header = get_site_option('hl_userLastname_Header');
        $user_nicename_header = get_site_option('hl_userNicename_Header');
        $user_displayname_header = get_site_option('hl_userDisplayname_Header');
        $auth_header = get_site_option('hl_authHeader');
        $create_new_user = get_site_option('hl_createNewUser', 0);
        $new_user_role = get_site_option('hl_defaultRole', HL_NEWUSERROLE);
        $hl_logout_url = get_site_option('hl_logoutURL');
	
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
				<th scope="row"><label for="user-login-header"><strong><?php _e('user_login*','header-login'); ?></strong></label></th>
				<td>
					<input type="text" name="user-login-header" id="user-login-header" value="<?php echo $user_login_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-email-header"><strong><?php _e('user_email*','header-login'); ?></strong></label></th>
				<td>
					<input type="text" name="user-email-header" id="user-email-header" value="<?php echo $user_email_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-firstname-header"><?php _e('first_name','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-firstname-header" id="user-firstname-header" value="<?php echo $user_firstname_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-lastname-header"><?php _e('last_name','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-lastname-header" id="user-lastname-header" value="<?php echo $user_lastname_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-nicename-header"><?php _e('user_nicename','header-login'); ?></label></th>
				<td>
					<input type="text" name="user-nicename-header" id="user-nicename-header" value="<?php echo $user_nicename_header ?>" size="25" />
					<br/>
				</td>
			</tr>
			<tr valign-"top">
				<th scope="row"><label for="user-displayname-header"><?php _e('display_name','header-login'); ?></label></th>
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

function hl_plugin_menu() {//Set up the plugin menu
	if(is_multisite())
		{add_submenu_page('settings.php',__('Header Login Options','header-login'),__('Header Login','header-login'),'edit_plugins',basename(__FILE__),'hl_menu');}
	else
		{add_submenu_page('options-general.php',__('Header Login Options','header-login'),__('Header Login','header-login'),'edit_plugins',basename(__FILE__),'hl_menu');}
}

//Activation Hook
register_activation_hook(__FILE__, 'hl_activation_hook');

//Deactivation Hook
register_deactivation_hook(__FILE__, 'hl_deactivation_hook');

//Action Hooks
add_action('admin_init', 'hl_load_plugin');
add_action('admin_menu', 'hl_plugin_menu');

?>
