=== SoundCloud Sound Competition ===
Contributors: canitb
Tags: soundcloud, sound, remix, competition, comp, host, integration, marketing, socialmarketing
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U7ZQC4QH7MVP2
Requires at least: 3.0
Tested up to: 3.9
Stable tag: 0.9.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Host multiple sound or remix competition all within your wordpress site, integrated with SoundCloud! Get massive social media attention!


== Description ==

[SoundCloud Sound Competition ](http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/) has been created to let you host your own remix competition without thinking about storage space, it's all integrated with SoundCloud so that all remixes are stored there.

Just make your own app in Soundcloud and add the parameters within your settings and you are good to go. You can chose a database slug when a competition has come to an end and you want to start another one, this creates a new competition. The plugin uses [OAuth2](https://api.soundcloud.com/oauth2/) so your Soundcloud account details are not stored for the
plugin to gain access.

Checkout the website - http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/



= Setup =

Once installed, the authorization process is easy:

1. When you first access the plugin’s options page, you will have to fill in setting of you app.

2. To create an app in Soundcloud go here: http://soundcloud.com/you/apps, after logging in you should get the putton "Create new application"

3. After making a name for your app you will get all the information to fill in there. Copy and past all the information into your plugin settings in wordpress in the appropriate fields matching names.

4. In Wordpress create a page for your Remix SoundCloud registration and put in the wordpress code there.

5. The URL of the page you created in 4. type this in your app http://soundcloud.com/you/apps where it says: Redirect URI, this is where SoundCloud redirects after connecting.

6. Test your app to see everything working.




= Minimum Requirements =

1. PHP 5.2 or higher (with [cURL](http://se2.php.net/curl) support)
2. [A Soundcloud account](https://www.soundcloud.com/)



= Errors and Warnings =

Non at the moment, some known configuration setup issues with windows server, see Issues for more information.



= More Information =

For news and updates please visit - http://lightdigitalmedia.com/



= Issues =
* If your website is on a windows server and you get the following error: "The requested URL responded with HTTP code 0" you might have a problem with cURL with your setup, please see the following link to resolve:
http://richardwarrender.com/2007/05/the-secret-to-curl-in-php-on-windows/

If you notice any bugs or want to request a feature please do so on http://lightdigitalmedia.com/contact/


== Installation ==

1. Upload the contents of zip file to the `/wp-content/plugins/` directory or use WordPress' built-in plugin install tool
2. Once installed, you can access the plugins settings page under the new menu
3. The first time you access the settings you will be prompted to authorize it with SoundCloud


== Upgrade Notice ==

Just replace the new plugin files. 


== Frequently Asked Questions ==

1.  Question: What does the star in the admin panel do?
    Answer: The stars has no specific function other than for the admin to mark the potential winners for a competition. 



== Screenshots ==

1. SoundCloud Sound Contest New remixers backend
2. SoundCloud Sound Contest Settings backend
3. SoundCloud Sound Contest SoundCloud Connect button
4. SoundCloud Sound Contest Choosing the track
5. SoundCloud Sound Contest Listening and voting



== Changelog ==

= 0.9.2.5 =
* Added additional viewing option with choice in the admin.


= 0.9.2.4 =
* Added sorting on last uploaded and highest rating on the competition page.


= 0.9.2.3 =
* Updated to new Soundcloud API.


= 0.9.2.1 =
* Usabillity: Better shortcode copyer.

= 0.9.2.0 =
* Added info and upload button


= 0.9.1.0 =
* Added admin delete function for an entry
* Url changes and added screenshots.


= 0.9.0.0 =
* First release.

