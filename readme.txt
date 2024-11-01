=== WP Quote Tweets ===
Contributors: 0xTC
Tags: cite, tweets, tweet, twitter, shortcode, quotes, plugin, quote, blockquote
Requires at least: 2.0.0
Tested up to: 2.8
Stable tag: trunk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6331357

Allows authors to quote Twitter updates in posts or pages using a simple shortcode. [quotetweet tweetid=123456789].

== Description ==

**Quote tweets from Twitter** directly to a Wordpress page or post using a simple shortcode.

Basic example: `[quotetweet tweetid=1234567890]` (See the FAQ section)

Depending on the template you select, an **"in reply to"** link will be created if the tweet is a reply to another tweet. All generated links have rel="external".

**notes:** 

* This plugin caches the information it gets from Twitter.com. Please make sure the `/xmlcache/` sub folder is writable!
* You can create your own tweet templates and save them in your templates folder under `{CurrentTheme}/wpqt/`.
* This plugin uses the SimpleXML extension. It requires PHP 5!
* If you wish to preserve your cached XML files due to Twitter limitations, create the following writable folder: `/wp-content/cache/xmlcache/`. All the requess will be cached there if it exists and is writable.

== Installation ==

1. Upload/extract the `wp-quote-tweets` folder to your `/wp-content/plugins/` directory.
3. **Make sure the `/xmlcache/` sub folder is writable! (Permissions set to 766 seem to work best)**
4. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. Post with rendered Twitter tweets.

2. Post with rendered Twitter tweet quoted in alternate template.

3. Post with twitter.tidy template

== Frequently Asked Questions ==

= So, how do I use it? =

Example: `[quotetweet tweetid=1234567890]`

The `tweetid` can be found in the URL of the tweet that is to be quoted. 

For example, you can quote the following tweet:

http://twitter.com/TCorp/status/2208284083

Its tweetid is `2208284083`; making the shortcode look like this:

`[quotetweet tweetid=2208284083]`

Alternative notations are [quotetweet xxx] or [qtweet xxx] where xxx is the tweetid.

If the tweet isn't showing up, something went wrong. Also keep in mind that this only works with public tweet. Private tweets can't be made public with this plugin.

= How can I change the style/theme of the tweets? =

The current version includes a number of templates to select from. Go to your admin panel and select "WP Quote Tweets" under Settings to select a template from the list.

If you've created your own custom templates put them in a "**`wpqt`**" folder in your theme's folder (example: `/wp-content/themes/yourCurrentTheme/wpqt/MyCustomTweetTheme/`) and you'll be able to select them from the settings screen.

To learn more about how to create your own template read the [templating documentation](http://0xtc.com/2009/07/09/creating-a-wp-quote-tweets-template.xhtml "Creating a WP Quote Tweets template").

== Changelog ==

* Added support for templates

= 2.0.1 =

* Fixed some CSS issues in the Twitter template.

= 2.1.0 =

* Added some simple templates.

= 2.1.1 =

* caching routine will no longer show an error when /xmlcache/ directory is not writable. The plug-in will continue to run but will not cache.

= 2.2.0 =

* Added screenshots to settings.
* Fixed filenames in templates.

= 2.2.2 =

* Added twitter.tidy template

= 2.2.3 =

* Addressed security restriction issue

= 2.2.5 =

* Better ReplyTo link generation code.

= 3.0.0 =

* Improved settings screen
* Custom cache configuration
* Automatic custom template for RSS readers
* Better error handling for missing tweets

= 4.0.0 =

* Custom Quote Tweet templates can be stored under `{CurrentTheme}/wpqt/` folder (/wp-content/themes/CurrentTheme/**wpqt**/myOwnTweetTemplate/)
* Caching has be overhauled. With this version the Twitter XML files will be cached in the "xmlcache" folder and the post generation using the user selected template is completely dynamic. This makes switching between themes a much quicker and much safer experience.
* The twitter.tidy template uses the new %NICE\_TIMESTAMP\_LINK% variable to show the date as "x minutes ago", "x hours ago", "x days ago" etc

= 4.0.1 =

* More error handling in this version.
* [quotetweet tweetid=1234567890] can now also be written as [quotetweet 1234567890] or [qtweet 1234567890]
* Fixed feed template when using custom template in theme subfolder folder.
* Adding additional caching option.

= 4.0.2 =
* Fixed meta writeonce bug.

= 4.1.0 =
* Cleaner date outputs (server timezone).
* Added tweetimag.es support.

= 4.1.1 =
* Additional error handling.

= 4.1.2 =
* Accommodating for "Twitter is Over Capacity" error.