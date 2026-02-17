=== PostSimple Integration ===
Contributors: PostSimple
Tags: social media, automation, publisher
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send your WordPress posts to PostSimple with one click to automatically generate professional social media content.

== Description ==

[PostSimple](https://postsimple.app) is the AI social media tool for automatic social media posts and smart content scheduling. PostSimple creates and publishes content in your brand's style, tailored to each social media channel.

With this WordPress plugin, you can easily share your WordPress content with PostSimple, where it gets transformed into engaging social media posts that perfectly match your brand identity and target audience.

= Requirements =

* WordPress 5.0 or higher
* PHP 7.2 or higher
* PostSimple Pro subscription with API access
* PostSimple API key (request via api@postsimple.nl)

Note: The API key is only available with a Pro subscription. Don't have a Pro subscription yet? Upgrade your account at [PostSimple](https://postsimple.app) first.

= Features =

* Easy integration - Install and configure in minutes
* Works with all content - Posts, pages, and custom post types
* One-click sending - Directly from the WordPress editor
* Automatic redirect - Straight to your PostSimple content overview
* Secure and reliable - Secure API connection with error handling
* No technical knowledge required - User-friendly interface

== Installation ==

= Via WordPress Admin (recommended) =

1. Download the ZIP file of this plugin
2. Go to Plugins > Add New > Upload Plugin
3. Upload the ZIP file and click "Install Now"
4. Click "Activate Plugin"

= Manual installation =

1. Download and extract the ZIP file
2. Upload the `postsimple` folder to `/wp-content/plugins/`
3. Go to Plugins in WordPress
4. Activate "PostSimple Integration"

= Configuration =

1. Make sure you have a PostSimple Pro subscription
2. Send an email to api@postsimple.nl with your company name, PostSimple account email, and WordPress website URL
3. You will receive your personal API key within 1-2 business days
4. Go to Settings > PostSimple in WordPress
5. Paste your API key in the field and click "Save Settings"

== Usage ==

1. Create or edit a post, page, or other content type in WordPress
2. Publish your post - the plugin requires a publicly accessible URL
3. Find the PostSimple meta box in the sidebar (right side of the screen)
4. Click the "Send to PostSimple" button
5. You will be automatically redirected to PostSimple where you can view and edit the generated social media content

Note: Only published posts can be sent to PostSimple. Draft, scheduled, or pending posts cannot be sent because PostSimple needs a publicly accessible URL to analyze your content.

== Frequently Asked Questions ==

= How do I get an API key? =

The API key is available for PostSimple Pro customers. Send an email to api@postsimple.nl to request your API key.

= Does the plugin work with Gutenberg and Classic Editor? =

Yes! The plugin works with both the Gutenberg block editor and the Classic Editor.

= Can I send multiple posts at once? =

Currently, the plugin supports sending one post at a time. Bulk sending will be available in a future update.

= What happens to my post after sending? =

Your WordPress post is not modified. The title and URL are sent to PostSimple, where AI automatically generates social media content based on your settings.

= Can I send drafts or scheduled posts? =

No, only published posts can be sent to PostSimple. This is because PostSimple needs to access the public URL of your post to analyze its content. Make sure your post is published before sending it to PostSimple.

== External services ==

This plugin connects to the PostSimple API to send your WordPress post data for automatic social media content generation.

= What data is sent and when =

When you click the "Send to PostSimple" button in the post editor, the plugin sends the following data to the PostSimple API:

* The post title
* The public URL (permalink) of the post

This data is only sent when the user explicitly clicks the "Send to PostSimple" button. No data is sent automatically or in the background.

= Service details =

This service is provided by PostSimple.

* Service website: [https://postsimple.app](https://postsimple.app)
* API endpoint: `https://postsimple.link/api/plugins/create-post`
* Terms of use: [https://postsimple.app/gebruikersvoorwaarden](https://postsimple.app/gebruikersvoorwaarden)
* Privacy policy: [https://postsimple.app/privacyverklaring](https://postsimple.app/privacyverklaring)

After sending, the plugin redirects the user to PostSimple at `https://my.postsimple.app/lab` to view the generated social media content.

== Changelog ==

= 1.0.2 =
* Fixed missing version parameter in wp_register_style() call
* Set resource versions for proper browser cache busting

= 1.0.1 =
* Use wp_add_inline_style() instead of inline style tag
* Added external services documentation to readme

= 1.0.0 =
* Initial release
* API key configuration
* Send posts to PostSimple from editor
* Automatic redirect to PostSimple batch overview
* Support for all post types
* Meta box in post editor sidebar
* User-friendly error messages
* Published posts only (requires publicly accessible URL)
