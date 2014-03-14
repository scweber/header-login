=== Header Login ===
Contributors: scweber, MatthewEhle
Tags: login, header, Access Manager, Single Sign-On, SSO
Requires at least: 3.4.2
Tested up to: 3.8.1
Stable tag: 2.8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow a user to be automatically logged into WordPress if their headers are present and valid.

== Description ==
Header Login allows a user to be automatically logged into WordPress if they have previously logged into
an Access Management Tool and have admin-defined headers present.

The plugin checks for valid headers.  If these headers are present then the user is logged into WordPress.  
If not, then nothing is done and the user remains a guest to the blog.

If desired, a new user can be created on WordPress if authenticated through Access Management Tool.

== Screenshots ==
1. Settings Page for Single Site
2. Settings Page for Multisite

== Installation ==
1. Upload `header-login` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up and customize the plugin through the 'Settings' Menu

== Changelog ==
= 2.8.0 =
Bugfixes 
  -User's current role on multisite blog was being overridden by user's role on main site.  This did not allow for a user to have varying roles on a multisite install.
Enhancement
  -New multisite feature: Allow administrators to choose which subsites will automatically create new users and with what role.

= 2.7.3 =
Fixed a bug that was overwriting the User-Defined Display Name and Nicename

= 2.7.2 = 
Fixed another user role issue

= 2.7.1 =
Fixed an issue where the plugin was setting the user's role to null.

= 2.7 =
Fixed the issue of not adding new users to all blogs when user already on Network.

= 2.6 =
If option chosen to add new users and a multi-site, then users are added to each and every blog in network.

= 2.5 =
Bug Fixes
 - Blank Admin Bar appearing when not logged in.

= 2.4 = 
Bug Fixes
 - Some users not seeing the Admin Bar after logging in.

= 2.3 =
Security Fixes.

= 2.2 =
Do not update user info if header is blank.

= 2.1 =
Multi-Site Support.

= 2.0 =
MAJOR UPDATE!
New Admin User Interface.
Ability to set custom headers to use for creating and authenticating users.
Can choose to automatically create new users.

= 1.0 =
This is the first release.