<?php
/**
 * Plugin Name: Notification bar
 * Description: Easily update a Notification Bar on your website through REST API. Do not use in production demo only. Tested with Twenty Twenty Theme.
 * Version:     0.1.0
 * Author:      Thrifty Developer
 * Author URI:  https://thriftydeveloper.com
 * License:     GPL2
 * Textdomain:  ConnectedServices
 *
 * @since       0.1.0
 */


class Notification_Bar {


	/**
	 * Construct
	 *
	 * @param string $plugin_file_path Plugin file path.
	 * @since  0.1.0
	 * @author Scott Anderson <scott@church.agency>
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author Scott Anderson <scott@church.agency>
	 */
	public function register_hooks() : void {

		// 1. First we register our endpoint to receive requests from our SAS.
		add_action( 'rest_api_init', function () {
			register_rest_route( 'site-updater', '/banner-bar/', array(
				'methods'  => 'POST',
				'callback' => [ $this, 'banner_bar' ],
			) );
		} );

		// 2. Next we enquie our scrips to inject the status bar at the top of the page.
		add_action( 'wp_enqueue_scripts', [ $this, 'add_status_bar_styles' ] );

	}

	/**
	 * Register javascript for status bar.
	 *
	 * @author Scott Anderson <scott@church.agency>
	 * @since  NEXT
	 */
	public function add_status_bar_styles() {

		if ( get_option( 'cc_status_bar_active', false ) ) {
			wp_enqueue_script( 'topbar_frontjs', plugins_url( '/assets/js/status-bar.js', __FILE__ ), array( 'jquery' ) );

			$status_bar_settings = [
				'message' => get_option( 'cc_status_bar_message', '' ),
			];

			// Sending settings to the status bar.
			wp_localize_script( 'topbar_frontjs', 'status_bar_settings', $status_bar_settings );
		}

	}

	/**
	 * Updates status bar message and display flag.
	 *
	 * @param object $args Options for the function.
	 * @author Scott Anderson <scott@church.agency>
	 * @return array
	 */
	public function banner_bar( \WP_REST_Request $request ) : array {

		if ( 'true' === $request['active'] ) {
			update_option( 'cc_status_bar_active', true );
		} else {
			update_option( 'cc_status_bar_active', false );
		}

		update_option( 'cc_status_bar_message', $request['message'] );

		return [
			'success' => true,
		];
	}
}

new Notification_Bar();
