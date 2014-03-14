=== Header Login ===
Contributors: scweber, MatthewEhle
Tags: login, header
Requires at least: 3.4.2
Tested up to: 3.5
Stable tag: 2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow a user to be automatically logged into WordPress if their headers are present and valid.

== Description ==
Header Login allows a user to be automatically logged into WordPress if they have previously logged into
an Access Management Tool and have admin-defined headers present.

The plugin checks for valid headers.  If these headers are present then the user is logged into WordPress.  
If not, then nothing is done and the user remains a guest to the blog.

== Installation ==
1. Upload `header-login` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up and customize the plugin through the 'Settings' Menu

== Changelog ==
= 2.3 =
Security Fixes

= 2.2 =
Do not update user info if header is blank

= 2.1 =
Multi-Site Support

= 2.0 =
MAJOR UPDATE!
New Admin User Interface
Ability to set custom headers to use for creating and authenticating users
Can choose to automatically create new users

= 1.0 =
This is the first release
