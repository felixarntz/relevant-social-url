<?php
/**
 * Plugin main file.
 *
 * @package RelevantTweet
 * @author Felix Arntz <hello@felix-arntz.me>
 *
 * @wordpress-plugin
 * Plugin Name: Relevant Tweet
 * Plugin URI: https://wordpress.org/plugins/relevant-tweet/
 * Description: Allows to associate a tweet with each post, and optionally to display a link to it in the frontend.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.2
 * Author: Felix Arntz
 * Author URI: https://felix-arntz.me
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: relevant-tweet
 * Tags: tweet, twitter, post, link, frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the post metadata.
 *
 * @since 1.0.0
 */
function relevant_tweet_register_meta(): void {
	$args = array(
		'type'         => 'string',
		'label'        => __( 'Relevant Tweet URL', 'relevant-tweet' ),
		'description'  => __( 'The URL of a tweet that is associated with this content.', 'relevant-tweet' ),
		'single'       => true,
		'show_in_rest' => true,
	);

	register_meta(
		'post',
		'relevant_tweet_url',
		$args
	);
}
add_action( 'init', 'relevant_tweet_register_meta' );

/**
 * Registers the editor script.
 *
 * @since 1.0.0
 */
function relevant_tweet_register_editor_script(): void {
	$script_metadata = require plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

	wp_register_script(
		'relevant-tweet-ui',
		plugin_dir_url( __FILE__ ) . 'build/index.js',
		$script_metadata['dependencies'],
		$script_metadata['version'],
		array( 'in_footer' => true )
	);
}
add_action( 'init', 'relevant_tweet_register_editor_script' );

/**
 * Conditionally enqueues the editor script.
 *
 * @since 1.0.0
 *
 * @global string $post_type The current post type.
 */
function relevant_tweet_enqueue_editor_script(): void {
	global $post_type;

	$post_types = array( 'post' );

	/**
	 * Filters the post types for which the relevant tweet URL functionality should be enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $post_types Array of post type slugs.
	 */
	$post_types = (array) apply_filters( 'relevant_tweet_post_types', $post_types );

	if ( ! in_array( $post_type, $post_types, true ) ) {
		return;
	}

	wp_enqueue_script( 'relevant-tweet-ui' );
}
add_action( 'enqueue_block_editor_assets', 'relevant_tweet_enqueue_editor_script' );

/**
 * Filters the post content to add the tweet link.
 *
 * @since 1.0.0
 *
 * @param string|mixed $content The post content.
 * @return string|mixed The filtered post content.
 */
function relevant_tweet_filter_post_content( $content ) {
	if ( ! is_string( $content ) || ! is_singular() ) {
		return $content;
	}

	/**
	 * Filters whether tweet links should be displayed in the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $enabled Whether the frontend output is enabled. Default true.
	 */
	$enabled = (bool) apply_filters( 'relevant_tweet_frontend_output_enabled', true );
	if ( ! $enabled ) {
		return $content;
	}

	$tweet_url = get_post_meta( get_the_ID(), 'relevant_tweet_url', true );
	if ( ! $tweet_url ) {
		return $content;
	}

	if ( str_contains( $tweet_url, 'twitter.com' ) ) {
		$link_text = __( 'This post also appeared on Twitter.', 'relevant-tweet' );
	} else {
		$link_text = __( 'This post also appeared on X.', 'relevant-tweet' );
	}

	/**
	 * Filters the link text for displaying the tweet link in the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link_text The link text to use.
	 */
	$link_text = (string) apply_filters( 'relevant_tweet_frontend_link_text', $link_text );

	$content .= "\n\n";
	$content .= '<p class="has-small-font-size">';
	$content .= '<a href="' . esc_url( $tweet_url ) . '" target="_blank" rel="noopener noreferrer">';
	$content .= esc_html( $link_text );
	$content .= '<span class="screen-reader-text"> ' . esc_html__( '(link opens in a new tab)', 'relevant-tweet' ) . '</span>';
	$content .= '</a>';
	$content .= '</p>';

	return $content;
}
add_filter( 'the_content', 'relevant_tweet_filter_post_content' );
