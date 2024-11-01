=== Simple Privacy Helper ===
Contributors: juvodesign
Tags: privacy, gdpr, consent
Requires at least: 4.9.6
Tested up to: 4.9.7
Stable tag: 1.4.3.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: www.gnu.org/licenses/gpl-2.0.html

Simple Privacy Helper provides extra options to manage all your sites privacy settings.

== Description ==
Simple Privacy Helper is a lightweight Plugin that helps you manage many GDPR and privacy related settings.
It is completely free without any limitations.

===Current Features===
* Deactivate the WordPress Cookie Checkbox that came with 4.9.6 
* Add a Privacy Policy Checkbox to the WordPress Registration
* Add a Privacy Policy Checkbox to the WordPress Comments
* Add a Acceptance Box to Contact Form7 Forms and their mail templates
* Synchronise the Contact Form7 labels throughout all forms
* Make all youtube.com links to youtube-nocookie.com links
* Easy one click SSL setup with .htaccess redirect and mixed content fix

== Changelog ==
= 1.4.3.1 =
* Contact Form7 labels now also update when the the privacy policy pages changes
* The translations should now work as supposed


= 1.4.3 =
* The .htaccess redirection now only gets created when the webserver is apache

= 1.4.2 =
* Removed local language Packs to use translations form wordpress.org

= 1.4.1 =
* Fixed Class initialisation order

= 1.4 =
* Added one click SSL setup with .htaccess redirect and mixed content fix
* Reduced plugin queries. The functions now only execute when options are updated

= 1.3.2 =
* Removed WooCommerce Settings, because WooCoomerce added the same functionality
* Removed Options from older Versions
* Fixed a Bug where the Youtube Button did not ReCheck all Posts for youtube Links

= 1.3.1 =
* Fixed an Error of the Contact Form7 Button in the Backend

= 1.3 = 
* Added WooCommerce Privacy Options
* Added an option to deactivate the WordPress Cookie Consent Checkbox
* No more direct writing to the database for contatc form 7

== Frequently Asked Questions ==
= Does this plugin save anything to my database? =
Yes! The comment and registration checkboxes save a small keys in the meta data. Otherwise you can not make sure that the user consented.

= Does the plugin changes youtube iframes embeds and oembeds? =
Yes! In both cases the URL will be output as the nocookie one.

= I locked myself out after activating the SSL option. How can i get back into the Dashboard? =
You need to remove the "Simple Privacy Helper" block in the .htaccess file to remove the redirection. After that you need to get access to your database to change the "home_url"- and "site_url"-options in the "wp_options"-table back to http. Now you should be able to log into WordPress and deactivate Simple Privacy Helper´s SSL option to remove all other changes.