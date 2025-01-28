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
 * Registers the post metadata.
 *
 * @since 1.0.0
 */
function relsoc_register_meta(): void {
	$args = array(
		'type'         => 'string',
		'label'        => __( 'Relevant Social URL URL', 'relevant-social-url' ),
		'description'  => __( 'The URL of a social media post that is associated with this content.', 'relevant-social-url' ),
		'single'       => true,
		'show_in_rest' => true,
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

	if ( str_contains( $social_url, 'twitter.com' ) ) {
		$link_text = __( 'This post also appeared on Twitter.', 'relevant-social-url' );
	} else {
		$link_text = __( 'This post also appeared on X.', 'relevant-social-url' );
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
