<?php
/**
 * Dashboard-widget.
 *
 * Class to manage dashboard widget functions.
 *
 * @package Dashboard-widget.
 * @since 1.0.0
 */

namespace Rankmath\Admin;

if ( ! class_exists( 'Dashboard_Widget' ) ) {
	/**
	 * Dashboard widget settings.
	 *
	 * @since 1.0.0
	 */
	class Dashboard_Widget {

		/**
		 * Constructor initiates the needed functions for dashboard widgets.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp_dashboard_setup', array( $this, 'rankmath_add_dashboard_widget' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'rankmath_enque_scripts' ) );
			add_action( 'rest_api_init', array( $this, 'rankmath_rest_api_register' ) );
		}

		/**
		 * Adds the widget to the WordPress dashboard.
		 *
		 * @since 1.0.0
		 */
		public function rankmath_add_dashboard_widget() {
			wp_add_dashboard_widget( 'rankmath-graph', esc_html__( 'Graph Widget', 'rankmath' ), array( $this, 'rankmath_dashboard_get_graph' ) );
		}

		/**
		 * Adds the view file for the widget.
		 *
		 * @since 1.0.0
		 */
		public function rankmath_dashboard_get_graph() {
			require_once WP_RM_DASHBOARD_ABSPATH . 'views/dashboard-widget.php';
		}

		/**
		 * Adds the Scripts to show the widget.
		 *
		 * @since 1.0.0
		 */
		public function rankmath_enque_scripts() {
			global $pagenow;
			if ( 'index.php' === $pagenow && is_admin() ) {
				wp_enqueue_style( 'rankmath-style', WP_RM_URL . 'build/index.css', '', '1.0.0' );
				wp_enqueue_script( 'rankmath-script', WP_RM_URL . 'build/index.js', array( 'wp-components', 'wp-api-fetch' ), '1.0.0', true );
			}
		}

		/**
		 * Register rest Api routes.
		 *
		 * @since 1.0.0
		 */
		public function rankmath_rest_api_register() {
			register_rest_route(
				'wprm-dashboard/v1',
				'/getchart/(?P<date>\d+)',
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'rankmath_callback_get_chart' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		/**
		 * Callback to get the chart data.
		 *
		 * @since 1.0.0
		 * @param array $request_data Holds the get query parameters.
		 */
		public function rankmath_callback_get_chart( $request_data ) {
			global $wpdb;
			$param      = $request_data->get_params();
			$data_value = array();
			if ( ! empty( $param ) ) {
				$get_date   = $param['date'];
				$where_date = gmdate( 'Y-m-d', strtotime( '-' . $get_date . 'days' ) );

				$data_value = $wpdb->get_results( $wpdb->prepare( 'SELECT name, SUM( uv ) as uv, SUM( pv ) as pv FROM ' . $wpdb->prefix . 'rankmath_widget_graph where DATE(created_date) >= %s GROUP BY `name` ORDER BY `name` ASC', $where_date ) );
			}
			return new \WP_REST_Response( $data_value, 200 );
		}

		/**
		 * Create dashboard widget Graph data on plugin installation.
		 */
		public function create_data_on_installation() {
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$dashboard_graph_sql = 'CREATE TABLE ' . $wpdb->prefix . 'rankmath_widget_graph (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(3) NOT NULL,
				`uv` VARCHAR(5) NOT NULL,
				`pv` VARCHAR(5) NOT NULL,
				`created_date` DATE NOT NULL,
				PRIMARY KEY (`id`)
				)ENGINE=InnoDB';
			maybe_create_table( $wpdb->prefix . 'rankmath_widget_graph', $dashboard_graph_sql );
			$check_values = $wpdb->get_results( 'SELECT id FROM ' . $wpdb->prefix . 'rankmath_widget_graph' );
			if ( empty( $check_values ) ) {
				// Loop mutiple times to add more entries.
				for ( $i = 0; $i < 3; $i++ ) {
					// Insert values to the table.
					$name_string = 'ABCDEFG';
					$name_chars  = str_split( $name_string );
					foreach ( $name_chars as $char ) {
						// Start point of date range.
						$start_date = gmdate( 'Y-m-d', strtotime( '-30 days' ) );
						$start      = strtotime( $start_date );
						// End point of date range.
						$end_date = strtotime( gmdate( 'Y-m-d' ) );
						// custom range.
						$date_val = wp_rand( $start, $end_date );
						$wpdb->insert(
							$wpdb->prefix . 'rankmath_widget_graph',
							array(
								'name'         => $char,
								'uv'           => wp_rand( 100, 600 ),
								'pv'           => wp_rand( 100, 500 ),
								'created_date' => gmdate( 'Y-m-d', $date_val ),
							),
							array( '%s', '%s', '%s', '%s' )
						);
					}
				}
			}
		}
	}
}
return new Dashboard_Widget();
