<?php
/**
 * Tests for the plugin main file.
 *
 * @package RelevantSocialURL\Tests
 * @author Felix Arntz <hello@felix-arntz.me>
 */

class Relevant_Social_URL_Tests extends WP_UnitTestCase {

	public function test_hooks() {
		$this->assertSame( 10, has_action( 'init', 'relsoc_register_meta' ) );
		$this->assertSame( 10, has_action( 'init', 'relsoc_register_editor_script' ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', 'relsoc_enqueue_editor_script' ) );
		$this->assertSame( 10, has_filter( 'the_content', 'relsoc_filter_post_content' ) );
	}

	/**
	 * @covers ::relsoc_get_providers
	 * @dataProvider data_relsoc_get_providers
	 */
	public function test_relsoc_get_providers( string $social_url, string $expected_provider_name ) {
		$matched_provider_name = '';
		foreach ( relsoc_get_providers() as $provider_slug => $provider_data ) {
			foreach ( $provider_data['regexes'] as $regex ) {
				if ( preg_match( $regex, $social_url ) ) {
					$matched_provider_name = $provider_data['name'];
					break 2;
				}
			}
		}

		$this->assertSame( $expected_provider_name, $matched_provider_name );
	}

	public static function data_relsoc_get_providers(): array {
		return array(
			array( 'https://bsky.app/test', 'Bluesky' ),
			array( 'http://bsky.app/test', 'Bluesky' ),
			array( 'https://www.bsky.app/test', 'Bluesky' ),
			array( 'https://facebook.com/test', 'Facebook' ),
			array( 'http://facebook.com/test', 'Facebook' ),
			array( 'https://www.facebook.com/test', 'Facebook' ),
			array( 'https://github.com/test', 'GitHub' ),
			array( 'http://github.com/test', 'GitHub' ),
			array( 'http://gist.github.com/test', 'GitHub' ),
			array( 'https://instagram.com/test', 'Instagram' ),
			array( 'http://instagram.com/test', 'Instagram' ),
			array( 'https://www.instagram.com/test', 'Instagram' ),
			array( 'http://instagr.am/test', 'Instagram' ),
			array( 'https://linkedin.com/test', 'LinkedIn' ),
			array( 'http://linkedin.com/test', 'LinkedIn' ),
			array( 'https://www.linkedin.com/test', 'LinkedIn' ),
			array( 'https://soundcloud.com/test', 'SoundCloud' ),
			array( 'http://soundcloud.com/test', 'SoundCloud' ),
			array( 'https://www.soundcloud.com/test', 'SoundCloud' ),
			array( 'https://open.spotify.com/test', 'Spotify' ),
			array( 'http://open.spotify.com/test', 'Spotify' ),
			array( 'https://play.spotify.com/test', 'Spotify' ),
			array( 'https://threads.net/test', 'Threads' ),
			array( 'http://threads.net/test', 'Threads' ),
			array( 'https://www.threads.net/test', 'Threads' ),
			array( 'https://tiktok.com/test', 'TikTok' ),
			array( 'http://tiktok.com/test', 'TikTok' ),
			array( 'https://www.tiktok.com/test', 'TikTok' ),
			array( 'https://twitter.com/test', 'Twitter' ),
			array( 'http://twitter.com/test', 'Twitter' ),
			array( 'https://www.twitter.com/test', 'Twitter' ),
			array( 'https://tumblr.com/test', 'Tumblr' ),
			array( 'http://tumblr.com/test', 'Tumblr' ),
			array( 'https://www.tumblr.com/test', 'Tumblr' ),
			array( 'https://wordpress.org/test', 'WordPress' ),
			array( 'http://wordpress.org/test', 'WordPress' ),
			array( 'https://make.wordpress.org/test', 'WordPress' ),
			array( 'https://events.wordpress.org/test', 'WordPress' ),
			array( 'https://wordcamp.org/test', 'WordPress' ),
			array( 'https://central.wordcamp.org/test', 'WordPress' ),
			array( 'https://wordpress.com/test', 'WordPress.com' ),
			array( 'http://wordpress.com/test', 'WordPress.com' ),
			array( 'https://www.wordpress.com/test', 'WordPress.com' ),
			array( 'https://x.com/test', 'X' ),
			array( 'http://x.com/test', 'X' ),
			array( 'https://www.x.com/test', 'X' ),
			array( 'https://youtube.com/test', 'YouTube' ),
			array( 'http://youtube.com/test', 'YouTube' ),
			array( 'https://www.youtube.com/test', 'YouTube' ),
			array( 'http://youtu.be/test', 'YouTube' ),
		);
	}

	/**
	 * @covers ::relsoc_register_meta
	 */
	public function test_relsoc_register_meta() {
		relsoc_register_meta();

		$this->assertTrue( registered_meta_key_exists( 'post', 'relsoc_url' ) );
	}

	/**
	 * @covers ::relsoc_register_editor_script
	 */
	public function test_relsoc_register_editor_script() {
		global $wp_scripts;

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		relsoc_register_editor_script();

		$is_registered = wp_script_is( 'relevant-social-url-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-social-url-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered but not enqueued.
		$this->assertTrue( $is_registered );
		$this->assertFalse( $is_enqueued );
	}

	/**
	 * @covers ::relsoc_enqueue_editor_script
	 */
	public function test_relsoc_enqueue_editor_script_for_post() {
		global $wp_scripts, $post_type;

		$post_type = 'post';

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		relsoc_register_editor_script();
		relsoc_enqueue_editor_script();

		$is_registered = wp_script_is( 'relevant-social-url-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-social-url-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered and enqueued.
		$this->assertTrue( $is_registered );
		$this->assertTrue( $is_enqueued );
	}

	/**
	 * @covers ::relsoc_enqueue_editor_script
	 */
	public function test_relsoc_enqueue_editor_script_for_page() {
		global $wp_scripts, $post_type;

		$post_type = 'page';

		// Store original `$wp_scripts`, then reset it.
		$orig_wp_scripts = wp_scripts();
		$wp_scripts      = null;

		relsoc_register_editor_script();
		relsoc_enqueue_editor_script();

		$is_registered = wp_script_is( 'relevant-social-url-ui', 'registered' );
		$is_enqueued = wp_script_is( 'relevant-social-url-ui', 'enqueued' );

		// Restore original `$wp_scripts`.
		$wp_scripts = $orig_wp_scripts;

		// Ensure that the script has been registered but not enqueued.
		$this->assertTrue( $is_registered );
		$this->assertFalse( $is_enqueued );
	}

	/**
	 * @covers ::relsoc_filter_post_content
	 */
	public function test_relsoc_filter_post_content() {
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

		// Without social URL, nothing is changed.
		$expected_content = $post_content;
		$this->assertSame( $expected_content, relsoc_filter_post_content( $post_content ) );

		update_post_meta( $post_id, 'relsoc_url', 'https://twitter.com/test' );

		// With social URL, the link is added.
		$expected_content  = $post_content . "\n\n";
		$expected_content .= '<p class="has-small-font-size"><a href="https://twitter.com/test" target="_blank" rel="noopener noreferrer">';
		$expected_content .= 'This post also appeared on Twitter.';
		$expected_content .= '<span class="screen-reader-text"> (link opens in a new tab)</span>';
		$expected_content .= '</a></p>';
		$this->assertSame( $expected_content, relsoc_filter_post_content( $post_content ) );

		add_filter( 'relsoc_frontend_output_enabled', '__return_false' );

		// With frontend output disabled, nothing is changed despite presence of social URL.
		$expected_content = $post_content;
		$this->assertSame( $expected_content, relsoc_filter_post_content( $post_content ) );
	}
}
