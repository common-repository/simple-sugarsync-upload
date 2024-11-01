=== Simple Sugarsync Upload ===
Contributors: hiphopsmurf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DV48838KHA4QU
Tags: simple, sugarsync, upload, integration, api, form, file, photos, shortcode, widget
Requires at least: 3.2.1
Tested up to: 3.3
Stable tag: 1.2.0

Inserts an upload form for visitors to upload files to you SugarSync account without the need of a SugarSync developer account.

== Description ==

This plugin lets you insert an upload form in a page, post or widget so visitors can upload files to your SugarSync account. No need to signup for a developer account.

== Installation ==

1. Upload `simple-sugarsync-upload` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress admin.
1. Goto Simple SugarSync in the 'Settings' menu in WordPress admin.
1. Enter your SugarSync username, password and file extensions of files you wish to be allowed for upload.
1. Save settings.
1. Place `[simple-wp-sugarsync]` in a page, post or widget.

== Requirements ==

* WordPress 3.2.1 or higher
* PHP 5.0 or higher
* CURL must be enabled
* The wp-content/uploads directory needs to be writable by the plugin.  This is likely already the case as WordPress stores your media and various other uploads here.

== Usage ==

1. Go to Site Admin > Settings > Simple SugarSync
1. (Optional)Enter the folder path you would like to save the files to on SugarSync.
1. (Optional) Change the temporary path for files uploaded to your server before being uploaded to SugarSync.
1. (Required) Enter the file extensions without periods for the files you want to allow users to upload separated by one space.
1. (Optional) Enter a message you want displayed after the user uploads a file.
1. Choose whether or not to display upload form again after the first file has been uploaded to SugarSync.
1. Choose whether or not to delete the file located on your server after it has been uploaded to SugarSync.
1. Click Save options.
1. Create a Page, Post or Widget to insert the shortcode into.
1. Insert **[simple-wp-sugarsync]** where you would like the form to display.
1. Click Save or Publish.
1. Visit the location to confirm everything is working properly.

== To-do list ==

* Add ability to append uploaders username to file name/folder path
* Add ability to control file upload size
* Add ability to limit the number of submissions per user/day
* Restyle admin interface

== Frequently Asked Questions ==

= Is this your first Wordpress plugin =

No but I make no promises that it will work for you.

= I am getting a message saying "This plugin will not work without CURL enabled" =

Ask your hosting provider to enable CURL.

= I am on a VPS or Dedicated server and this plugin still won't play nice =

You have a few options:

1. Check your phpinfo to ensure PHP was compiled with CURL

1. Check disabled_functions to ensure it doesn't contain curl_init

== Screenshots ==

1. Admin Panel.

2. Before a file is uploaded by user.

3. After a file is uploaded by user.

== Changelog ==

= 1.2.0 =
* Fixed bug with form showing after upload.

= 1.1.0 =
* Bug fixes

= 1.0.1 =
* Typo fixes

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
Major bug fixes in this update.