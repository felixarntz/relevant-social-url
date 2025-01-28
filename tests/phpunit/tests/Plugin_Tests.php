<?php
/**
 * Tests for the plugin main file.
 *
 * @package RelevantTweet\Tests
 * @author Felix Arntz <hello@felix-arntz.me>
 */

class Relevant_Tweet_Tests extends WP_UnitTestCase {

	public function test_hooks() {
		$this->assertSame( 10, has_action( 'init', 'reltwe_register_meta' ) );
		$this->assertSame( 10, has_action( 'init', 'reltwe_register_editor_script' ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', 'reltwe_enqueue_editor_script' ) );
		$this->assertSame( 10, has_filter( 'the_content', 'reltwe_filter_post_content' ) );
	}

	public function test_reltwe_register_meta() {
		reltwe_register_meta();

		$this->assertTrue( registered_meta_key_exists( 'post', 'reltwe_url' ) );
	}

	public function test_reltwe_register_editor_script() {
		global $wp_scripts;

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		reltwe_register_editor_script();

		$is_registered = wp_script_is( 'relevant-tweet-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-tweet-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered but not enqueued.
		$this->assertTrue( $is_registered );
		$this->assertFalse( $is_enqueued );
	}

	public function test_reltwe_enqueue_editor_script_for_post() {
		global $wp_scripts, $post_type;

		$post_type = 'post';

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		reltwe_register_editor_script();
		reltwe_enqueue_editor_script();

		$is_registered = wp_script_is( 'relevant-tweet-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-tweet-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered and enqueued.
		$this->assertTrue( $is_registered );
		$this->assertTrue( $is_enqueued );
	}

	public function test_reltwe_enqueue_editor_script_for_page() {
		global $wp_scripts, $post_type;

		$post_type = 'page';

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		reltwe_register_editor_script();
		reltwe_enqueue_editor_script();

		$is_registered = wp_script_is( 'relevant-tweet-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-tweet-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered but not enqueued.
		$this->assertTrue( $is_registered );
		$this->assertFalse( $is_enqueued );
	}

	public function test_reltwe_filter_post_content() {
		global $post, $wp_query;

		$post_content = '<p>Test content.</p>';
		$post_id      = self::factory()->post->create(
			array(
				'post_content' => $post_content,
				'post_status'  => 'publish',
			)
		);

		// Ensure the main query and post are set up in the loop.
		$post     = get_post( $post_id );
		$wp_query = new WP_Query( array( 'p' => $post_id ) );

		// Without tweet URL, nothing is changed.
		$expected_content = $post_content;
		$this->assertSame( $expected_content, reltwe_filter_post_content( $post_content ) );

		update_post_meta( $post_id, 'reltwe_url', 'https://twitter.com/test' );

		// With tweet URL, the tweet link is added.
		$expected_content  = $post_content . "\n\n";
		$expected_content .= '<p class="has-small-font-size"><a href="https://twitter.com/test" target="_blank" rel="noopener noreferrer">';
		$expected_content .= 'This post also appeared on Twitter.';
		$expected_content .= '<span class="screen-reader-text"> (link opens in a new tab)</span>';
		$expected_content .= '</a></p>';
		$this->assertSame( $expected_content, reltwe_filter_post_content( $post_content ) );

		add_filter( 'reltwe_frontend_output_enabled', '__return_false' );

		// With frontend output disabled, nothing is changed despite presence of tweet URL.
		$expected_content = $post_content;
		$this->assertSame( $expected_content, reltwe_filter_post_content( $post_content ) );
	}
}
