=== Relevant Social URL ===

Plugin Name:  Relevant Social URL
Plugin URI:   https://wordpress.org/plugins/relevant-social-url/
Author:       Felix Arntz
Author URI:   https://felix-arntz.me
Contributors: flixos90
Donate link:  https://felix-arntz.me/wordpress-plugins/
Tested up to: 6.7
Stable tag:   1.0.0
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Tags:         social media, twitter, post, link, frontend

Allows to associate a social media post URL with each post, and optionally to display a link to it in the frontend.

== Description ==

This plugin adds a field to the WordPress editor sidebar which allows you to paste the URL to a social media post that is associated with the post on your WordPress site. The URL is then stored in post meta and can be used to e.g. display the relevant social media post URL in the frontend.

By default, the plugin will append the link to the post content in the frontend. This can be disabled via filter though in favor of manual output elsewhere.

You can paste any URL into the field, regardless of social media platform. That said, the plugin will automatically recognize certain providers and adjust the message displayed in the frontend accordingly, referencing the specific provider when possible. The following providers are explicitly supported (in alphabetical order):

* Bluesky
* Facebook
* GitHub
* Instagram
* LinkedIn
* SoundCloud
* Spotify
* Threads
* TikTok
* Twitter
* Tumblr
* WordPress
* WordPress.com
* X
* YouTube

No third-party APIs are called by this plugin and no data is sent to any of these providers by the plugin.

== Installation ==

1. Upload the entire `relevant-social-url` folder to the `/wp-content/plugins/` directory or download it through the WordPress backend.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Where can I configure the plugin? =

This plugin doesn't come with a settings screen or options of any kind. As soon as you install it, you should see the input field in the sidebar when editing a post.

= How can I enable the functionality on other post types? =

This is very straightforward using the built-in filter `relsoc_post_types`.

For example, the following snippet would additionally show the input field for pages and output the social media post link in the frontend on pages too:

`
add_filter(
	'relsoc_post_types',
	function ( $post_types ) {
		$post_types[] = 'page';
		return $post_types;
	}
);
`

= How can I disable frontend output of the social media post link? =

Simply use the built-in filter `relsoc_frontend_output_enabled`. For example, to disable output:

`
add_filter( 'relsoc_frontend_output_enabled', '__return_false' );
`

= How can I customize the frontend output of the social media post link? =

You can modify the text that is displayed for the link, replacing the default of "This post also appeared on {social media platform}.".

You can do so by providing your own text via the built-in `relsoc_frontend_link_text` filter.

= Where should I submit my support request? =

For regular support requests, please use the [wordpress.org support forums](https://wordpress.org/support/plugin/relevant-social-url). If you have a technical issue with the plugin where you already have more insight on how to fix it, you can also [open an issue on GitHub instead](https://github.com/felixarntz/relevant-social-url/issues).

= How can I contribute to the plugin? =

If you have ideas to improve the plugin or to solve a bug, feel free to raise an issue or submit a pull request in the [GitHub repository for the plugin](https://github.com/felixarntz/relevant-social-url). Please stick to the [contributing guidelines](https://github.com/felixarntz/relevant-social-url/blob/main/CONTRIBUTING.md).

You can also contribute to the plugin by translating it. Simply visit [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/relevant-social-url) to get started.

== Changelog ==

= 1.0.0 =

* Initial release.
