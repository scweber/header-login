=== Header Login ===
Contributors: scweber, mehle
Tags: login, header, Access Manager, Single Sign-On, SSO
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 2.8.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow a user to be automatically logged into WordPress if their headers are present and valid.

== Description ==
Header Login allows a user to be automatically logged into WordPress if they have previously logged into
an Access Management Tool and have admin-defined headers present.  
The plugin checks for valid headers.  If these headers are present then the user is logged into WordPress.  
If not, then nothing is done and the user remains a guest to the blog.  
If desired, a new user can be created on WordPress automatically if authenticated through Access Management Tool.

== Screenshots ==
1. Settings Page for Single Site
2. Settings Page for Multisite

== Installation ==
= Automatic =
1. Go to your admin area and select Plugins >> Add New from the menu
2. Search for "Header Login"
3. Click Install
4. Click Activate (Network Activate on Multisite)
5. Setup and customize the plugin through the "Settings" Menu (Network Settings Menu on Multisite)

= Manual =
1. Go to https://wordpress.org/plugins/header-login
2. Download latest version of Header Login
3. Unzip file into WordPress plugins directory
4. Activate Plugin (Network Activate on Multisite)
5. Setup and customize the plugin through the "Settings" Menu (Network Settings Menu on Multisite)

== Changelog ==
= 2.8.8 =
* **Enhancements**
  * Added a hook for the wp_login action.

* **General Items**
  * Removed some extraneous logging

= 2.8.7 =
* **Bug Fixes**
  * Usernames with non-lowercase characters were resulting in a redirect loop when trying to access the wp-admin dashboard.

= 2.8.6 =
* **Bug Fixes**
  * Another redirect loop bug was found.

= 2.8.5 =
* **Bug Fixes**
  * If authenticated user did not have a WordPress account and setting was disabled to create new accounts, user was caught in a redirect loop.

= 2.8.4 =
* **Bug Fixes**
  * Add Domain to Path in list of sites when on a MultiSite Install.  This fix is for Domain Based Multi-site installations.

= 2.8.3 =
* **Enhancements**
  * Keep settings on deactivation.  Settings are only removed upon uninstallation.

= 2.8.2 = 
* **Bug Fixes**
  * Minor fix to hide Role dropdown if Create New User is disabled

= 2.8.1 =
* **Bug Fixes**
  * User's role was being overwritten in a single site

= 2.8.0 =
* **Bug Fixes**
  * User's current role on multisite blog was being overridden by user's role on main site.  This did not allow for a user to have varying roles on a multisite install
* **Enhancements**
  * New multisite feature: Allow administrators to choose which subsites will automatically create new users and with what role

= 2.7.3 =
* **Bug Fixes**
  * Bug was overwriting the User-Defined Display Name and Nicename

= 2.7.2 = 
* **Bug Fixes**
  * Another user role issue

= 2.7.1 =
* **Bug Fixes**
  * Issue where the plugin was setting the user's role to null

= 2.7 =
* **Bug Fixes**
  * Issue of not adding new users to all blogs when user already on Network

= 2.6 =
* **Enhancements**
  * If option chosen to add new users and a multi-site, then users are added to each and every blog in network

= 2.5 =
* **Bug Fixes**
  * Blank Admin Bar appearing when not logged in

= 2.4 = 
* **Bug Fixes**
  * Some users not seeing the Admin Bar after logging in

= 2.3 =
* **Security Fixes**

= 2.2 =
* **Bug Fixes**
  * Do not update user info if header is blank

= 2.1 =
* **Enhancements**
  * Multi-Site Support

= 2.0 =
* **MAJOR UPDATE!**
  * New Admin User Interface.
  * Ability to set custom headers to use for creating and authenticating users
  * Can choose to automatically create new users

= 1.0 =
* This is the first release
