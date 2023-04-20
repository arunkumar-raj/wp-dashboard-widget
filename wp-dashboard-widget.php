<?php
/**
 * Main dashboard widget plugin file
 *
 * @package Dashboard-widget.
 */

/**
 * Plugin Name: Rm-Dashboard-widget
 * Description: Dashboard widget to show the graph
 * Version: 1.0.0
 * Author: Arunkumar
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * Text Domain: rankmath
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WP_RM_DASHBOARD_ABSPATH', dirname( __FILE__ ) . '/' );
define( 'WP_RM_URL', plugin_dir_url( __FILE__ ) );
require_once WP_RM_DASHBOARD_ABSPATH . 'includes/class-dashboard-widget.php';

register_activation_hook( __FILE__, 'rankmath_dashboard_widget_create_graph_data' );
if ( ! function_exists( 'rankmath_dashboard_widget_create_graph_data' ) ) {
	/**
	 * Create data for graph on plugin installation.
	 */
	function rankmath_dashboard_widget_create_graph_data() {
		$widget_class = new Rankmath\Admin\Dashboard_Widget();
		$widget_class->create_data_on_installation();
	}
}

