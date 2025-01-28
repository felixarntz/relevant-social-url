<?php
/**
 * Plugin main file.
 *
 * @package RelevantSocialURL
 * @author Felix Arntz <hello@felix-arntz.me>
 *
 * @wordpress-plugin
 * Plugin Name: Relevant Social URL
 * Plugin URI: https://wordpress.org/plugins/relevant-social-url/
 * Description: Allows to associate a social media post URL with each post, and optionally to display a link to it in the frontend.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.2
 * Author: Felix Arntz
 * Author URI: https://felix-arntz.me
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: relevant-social-url
 * Tags: social media, twitter, post, link, frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Returns data for the supported social media providers.
 *
 * @since 1.0.0
 *
 * @return array<string, array<string, mixed>> Associative array of provider data, keyed by provider slug.
 */
function relsoc_get_providers(): array {
	return array(
		'bluesky'      => array(
			'name'    => 'Bluesky',
			'regexes' => array( '#https?://(www\.)?bsky\.app#i' ),
		),
		'facebook'     => array(
			'name'    => 'Facebook',
			'regexes' => array( '#https?://(www\.)?facebook\.com#i' ),
		),
		'github'       => array(
			'name'    => 'GitHub',
			'regexes' => array( '#https?://(gist\.)?github\.com#i' ),
		),
		'instagram'    => array(
			'name'    => 'Instagram',
			'regexes' => array( '#https?://(www\.)?instagram\.com#i', '#https?://instagr\.am#i' ),
		),
		'linkedin'     => array(
			'name'    => 'LinkedIn',
			'regexes' => array( '#https?://(www\.)?linkedin\.com#i' ),
		),
		'soundcloud'   => array(
			'name'    => 'SoundCloud',
			'regexes' => array( '#https?://(www\.)?soundcloud\.com#i' ),
		),
		'spotify'      => array(
			'name'    => 'Spotify',
			'regexes' => array( '#https?://(open|play)\.spotify\.com#i' ),
		),
		'threads'      => array(
			'name'    => 'Threads',
			'regexes' => array( '#https?://(www\.)?threads\.net#i' ),
		),
		'tiktok'       => array(
			'name'    => 'TikTok',
			'regexes' => array( '#https?://(www\.)?tiktok\.com#i' ),
		),
		'twitter'      => array(
			'name'    => 'Twitter',
			'regexes' => array( '#https?://(www\.)?twitter\.com#i' ),
		),
		'tumblr'       => array(
			'name'    => 'Tumblr',
			'regexes' => array( '#https?://(www\.)?tumblr\.com#i' ),
		),
		'wordpress'    => array(
			'name'    => 'WordPress',
			'regexes' => array( '#https?://([a-z0-9]+\.)?wordpress\.org#i', '#https?://([a-z0-9]+\.)?wordcamp\.org#i' ),
		),
		'wordpresscom' => array(
			'name'    => 'WordPress.com',
			'regexes' => array( '#https?://(www\.)?wordpress\.com#i' ),
		),
		'x'            => array(
			'name'    => 'X',
			'regexes' => array( '#https?://(www\.)?x\.com#i' ),
		),
		'youtube'      => array(
			'name'    => 'YouTube',
			'regexes' => array( '#https?://(www\.)?youtube\.com#i', '#https?://youtu\.be#i' ),
		),
	);
}

/**
 * Registers the post metadata.
 *
 * @since 1.0.0
 */
function relsoc_register_meta(): void {
	$args = array(
		'type'              => 'string',
		'label'             => __( 'Relevant Social URL', 'relevant-social-url' ),
		'description'       => __( 'The URL of a social media post that is associated with this content.', 'relevant-social-url' ),
		'sanitize_callback' => static function ( $value ) {
			if ( ! is_string( $value ) ) {
				return '';
			}
			if ( ! preg_match( '#http(s?)://(.+)#i', $value ) ) {
				return '';
			}
			return sanitize_url( $value );
		},
		'single'            => true,
		'show_in_rest'      => true,
	);

	register_meta(
		'post',
		'relsoc_url',
		$args
	);
}
add_action( 'init', 'relsoc_register_meta' );

/**
 * Registers the editor script.
 *
 * @since 1.0.0
 */
function relsoc_register_editor_script(): void {
	$script_metadata = require plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

	wp_register_script(
		'relevant-social-url-ui',
		plugin_dir_url( __FILE__ ) . 'build/index.js',
		$script_metadata['dependencies'],
		$script_metadata['version'],
		array( 'in_footer' => true )
	);
}
add_action( 'init', 'relsoc_register_editor_script' );

/**
 * Conditionally enqueues the editor script.
 *
 * @since 1.0.0
 *
 * @global string $post_type The current post type.
 */
function relsoc_enqueue_editor_script(): void {
	global $post_type;

	$post_types = array( 'post' );

	/**
	 * Filters the post types for which the relevant social media post URL functionality should be enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $post_types Array of post type slugs.
	 */
	$post_types = (array) apply_filters( 'relsoc_post_types', $post_types );

	if ( ! in_array( $post_type, $post_types, true ) ) {
		return;
	}

	wp_enqueue_script( 'relevant-social-url-ui' );
}
add_action( 'enqueue_block_editor_assets', 'relsoc_enqueue_editor_script' );

/**
 * Filters the post content to add the social media post link.
 *
 * @since 1.0.0
 *
 * @param string|mixed $content The post content.
 * @return string|mixed The filtered post content.
 */
function relsoc_filter_post_content( $content ) {
	if ( ! is_string( $content ) || ! is_singular() ) {
		return $content;
	}

	/**
	 * Filters whether social media post links should be displayed in the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $enabled Whether the frontend output is enabled. Default true.
	 */
	$enabled = (bool) apply_filters( 'relsoc_frontend_output_enabled', true );
	if ( ! $enabled ) {
		return $content;
	}

	$social_url = get_post_meta( get_the_ID(), 'relsoc_url', true );
	if ( ! $social_url ) {
		return $content;
	}

	$matched_provider_name = '';
	foreach ( relsoc_get_providers() as $provider_slug => $provider_data ) {
		foreach ( $provider_data['regexes'] as $regex ) {
			if ( preg_match( $regex, $social_url ) ) {
				$matched_provider_name = $provider_data['name'];
				break 2;
			}
		}
	}

	if ( '' !== $matched_provider_name ) {
		$link_text = sprintf(
			/* translators: %s: name of social media platform */
			__( 'This post also appeared on %s.', 'relevant-social-url' ),
			$matched_provider_name
		);
	} else {
		$link_text = __( 'This post also appeared on social media.', 'relevant-social-url' );
	}

	/**
	 * Filters the link text for displaying the social media post link in the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link_text The link text to use.
	 */
	$link_text = (string) apply_filters( 'relsoc_frontend_link_text', $link_text );

	$content .= "\n\n";
	$content .= '<p class="has-small-font-size">';
	$content .= '<a href="' . esc_url( $social_url ) . '" target="_blank" rel="noopener noreferrer">';
	$content .= esc_html( $link_text );
	$content .= '<span class="screen-reader-text"> ' . esc_html__( '(link opens in a new tab)', 'relevant-social-url' ) . '</span>';
	$content .= '</a>';
	$content .= '</p>';

	return $content;
}
add_filter( 'the_content', 'relsoc_filter_post_content' );
