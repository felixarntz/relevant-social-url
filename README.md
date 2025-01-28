[![PHP Unit Testing](https://img.shields.io/github/actions/workflow/status/felixarntz/relevant-social-url/php-test.yml?style=for-the-badge&label=PHP%20Unit%20Testing)](https://github.com/felixarntz/relevant-social-url/actions/workflows/php-test.yml)
[![Codecov](https://img.shields.io/codecov/c/github/felixarntz/relevant-social-url?style=for-the-badge)](https://app.codecov.io/github/felixarntz/relevant-social-url)
[![Packagist version](https://img.shields.io/packagist/v/felixarntz/relevant-social-url?style=for-the-badge)](https://packagist.org/packages/felixarntz/relevant-social-url)
[![Packagist license](https://img.shields.io/packagist/l/felixarntz/relevant-social-url?style=for-the-badge)](https://packagist.org/packages/felixarntz/relevant-social-url)
[![WordPress plugin version](https://img.shields.io/wordpress/plugin/v/relevant-social-url?style=for-the-badge)](https://wordpress.org/plugins/relevant-social-url/)
[![WordPress tested version](https://img.shields.io/wordpress/plugin/tested/relevant-social-url?style=for-the-badge)](https://wordpress.org/plugins/relevant-social-url/)
[![WordPress plugin downloads](https://img.shields.io/wordpress/plugin/dt/relevant-social-url?style=for-the-badge)](https://wordpress.org/plugins/relevant-social-url/)

# Relevant Social URL

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

## Installation and usage

You can download the latest version from the [WordPress plugin repository](https://wordpress.org/plugins/relevant-social-url/).

Please see the [plugin repository installation instructions](https://wordpress.org/plugins/relevant-social-url/#installation) for detailed information on installation and the [plugin repository FAQ](https://wordpress.org/plugins/relevant-social-url/#faq) for additional details on usage and customization.

Alternatively, if you use Composer to manage your WordPress site, you can also [install the plugin from Packagist](https://packagist.org/packages/felixarntz/relevant-social-url):

```
composer require felixarntz/relevant-social-url:^1.0
```

## Contributions

If you have ideas to improve the plugin or to solve a bug, feel free to raise an issue or submit a pull request right here on GitHub. Please refer to the [contributing guidelines](https://github.com/felixarntz/relevant-social-url/blob/main/CONTRIBUTING.md) to learn more and get started.

You can also contribute to the plugin by translating it. Simply visit [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/relevant-social-url) to get started.

## License

The Relevant Social URL plugin is [licensed under the GPLv2 (or later)](https://www.gnu.org/licenses/gpl-2.0.html).
