<?php
/**
 * Plugin
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\PronamicPayAdminReports
 */

namespace Pronamic\PronamicPayAdminReports;

use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Plugin class
 */
class Plugin {
	/**
	 * Instance.
	 *
	 * @var self|null
	 */
	private static $instance;

	/**
	 * Return an instance of this class.
	 *
	 * @return self A single instance of this class.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'admin_print_styles', [ $this, 'admin_css' ] );

		\add_filter( 'pronamic_pay_modules', [ $this, 'modules' ] );

		\add_action( 'admin_menu', [ $this, 'admin_menu' ], 100 );
	}

	/**
	 * Modules.
	 * 
	 * @link https://github.com/pronamic/wp-pay-core/blob/bd197f4b1d3ddd2947c8d0a210171c2e7482bac7/src/Admin/AdminModule.php#L741
	 * @param string[] $modules Modules.
	 * @return string[]
	 */
	public function modules( $modules ) {
		if ( ! \in_array( 'reports', $modules, true ) ) {
			$modules[] = 'reports';
		}

		return $modules;
	}

	/**
	 * Create the admin menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		\add_submenu_page(
			'pronamic_ideal',
			\__( 'Reports', 'pronamic_ideal' ),
			\__( 'Reports', 'pronamic_ideal' ),
			'edit_payments',
			'pronamic_pay_reports',
			function() {
				$this->page_reports();
			}
		);
	}

	/**
	 * Page reports.
	 *
	 * @return void
	 */
	public function page_reports() {
		$admin_reports = $this;

		include __DIR__ . '/../views/page-reports.php';
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function admin_css() {
		// Check if this is the reports page.
		/* phpcs:ignore WordPress.Security.NonceVerification.Recommended */
		if ( ! \array_key_exists( 'page', $_GET ) || 'pronamic_pay_reports' !== $_GET['page'] ) {
			return;
		}

		$min = \SCRIPT_DEBUG ? '' : '.min';

		// Flot - http://www.flotcharts.org/.
		$flot_version = '0.8.0-alpha';

		$file = __DIR__ . '/../../assets/flot/jquery.flot' . $min . '.js';

		\wp_register_script(
			'flot',
			\plugins_url( \basename( $file ), $file ),
			[ 'jquery' ],
			$flot_version,
			true
		);

		$file = __DIR__ . '/../../assets/flot/jquery.flot.time' . $min . '.js';

		\wp_register_script(
			'flot-time',
			\plugins_url( \basename( $file ), $file ),
			[ 'flot' ],
			$flot_version,
			true
		);

		$file = __DIR__ . '/../../assets/flot/jquery.flot.resize' . $min . '.js';

		\wp_register_script(
			'flot-resize',
			\plugins_url( \basename( $file ), $file ),
			[ 'flot' ],
			$flot_version,
			true
		);

		$file = __DIR__ . '/../../assets/accounting/accounting' . $min . '.js';

		// Accounting.js - http://openexchangerates.github.io/accounting.js.
		\wp_register_script(
			'accounting',
			\plugins_url( \basename( $file ), $file ),
			[ 'jquery' ],
			'0.4.1',
			true
		);

		// Reports.
		$file = __DIR__ . '/../../js/dist/admin-reports' . $min . '.js';

		\wp_register_script(
			'pronamic-pay-admin-reports',
			\plugins_url( \basename( $file ), $file ),
			[
				'jquery',
				'flot',
				'flot-time',
				'flot-resize',
				'accounting',
			],
			\hash_file( 'crc32b', $file ),
			true
		);

		global $wp_locale;

		\wp_localize_script(
			'pronamic-pay-admin-reports',
			'pronamicPayAdminReports',
			[
				'data'       => $this->get_reports(),
				'monthNames' => \array_values( $wp_locale->month_abbrev ),
			]
		);

		\wp_enqueue_script( 'pronamic-pay-admin-reports' );
	}

	/**
	 * Get reports.
	 *
	 * @return array
	 */
	public function get_reports() {
		$start = new \DateTime( 'First day of January' );
		$end   = new \DateTime( 'Last day of December' );

		$data = [
			(object) [
				'label'      => __( 'Number successful payments', 'pronamic_ideal' ),
				'data'       => $this->get_report( 'payment_completed', 'COUNT', $start, $end ),
				'color'      => '#dbe1e3',
				'bars'       => (object) [
					'fillColor' => '#dbe1e3',
					'fill'      => true,
					'show'      => true,
					'lineWidth' => 0,
					'barWidth'  => 2419200000 * 0.5,
					'align'     => 'center',
				],
				'shadowSize' => 0,
				'hoverable'  => false,
				'class'      => 'completed-count',
			],
			(object) [
				'label'            => __( 'Open payments', 'pronamic_ideal' ),
				'data'             => $this->get_report( 'payment_pending', 'SUM', $start, $end ),
				'yaxis'            => 2,
				'color'            => '#b1d4ea',
				'points'           => (object) [
					'show'      => true,
					'radius'    => 5,
					'lineWidth' => 2,
					'fillColor' => '#FFF',
					'fill'      => true,
				],
				'lines'            => (object) [
					'show'      => true,
					'lineWidth' => 2,
					'fill'      => false,
				],
				'shadowSize'       => 0,
				'tooltipFormatter' => 'money',
				'class'            => 'pending-sum',
			],
			(object) [
				'label'            => __( 'Successful payments', 'pronamic_ideal' ),
				'data'             => $this->get_report( 'payment_completed', 'SUM', $start, $end ),
				'yaxis'            => 2,
				'color'            => '#3498db',
				'points'           => (object) [
					'show'      => true,
					'radius'    => 6,
					'lineWidth' => 4,
					'fillColor' => '#FFF',
					'fill'      => true,
				],
				'lines'            => (object) [
					'show'      => true,
					'lineWidth' => 5,
					'fill'      => false,
				],
				'shadowSize'       => 0,
				'prepend_tooltip'  => '&euro;&nbsp;',
				'tooltipFormatter' => 'money',
				'class'            => 'completed-sum',
			],
			(object) [
				'label'            => __( 'Cancelled payments', 'pronamic_ideal' ),
				'data'             => $this->get_report( 'payment_cancelled', 'SUM', $start, $end ),
				'yaxis'            => 2,
				'color'            => '#F1C40F',
				'points'           => (object) [
					'show'      => true,
					'radius'    => 5,
					'lineWidth' => 2,
					'fillColor' => '#FFF',
					'fill'      => true,
				],
				'lines'            => (object) [
					'show'      => true,
					'lineWidth' => 2,
					'fill'      => false,
				],
				'shadowSize'       => 0,
				'prepend_tooltip'  => '&euro;&nbsp;',
				'tooltipFormatter' => 'money',
				'class'            => 'cancelled-sum',
			],
			(object) [
				'label'            => __( 'Expired payments', 'pronamic_ideal' ),
				'data'             => $this->get_report( 'payment_expired', 'SUM', $start, $end ),
				'yaxis'            => 2,
				'color'            => '#DBE1E3',
				'points'           => (object) [
					'show'      => true,
					'radius'    => 5,
					'lineWidth' => 2,
					'fillColor' => '#FFF',
					'fill'      => true,
				],
				'lines'            => (object) [
					'show'      => true,
					'lineWidth' => 2,
					'fill'      => false,
				],
				'shadowSize'       => 0,
				'prepend_tooltip'  => '&euro;&nbsp;',
				'tooltipFormatter' => 'money',
				'class'            => 'expired-sum',
			],
			(object) [
				'label'            => __( 'Failed payments', 'pronamic_ideal' ),
				'data'             => $this->get_report( 'payment_failed', 'SUM', $start, $end ),
				'yaxis'            => 2,
				'color'            => '#E74C3C',
				'points'           => (object) [
					'show'      => true,
					'radius'    => 5,
					'lineWidth' => 2,
					'fillColor' => '#FFF',
					'fill'      => true,
				],
				'lines'            => (object) [
					'show'      => true,
					'lineWidth' => 2,
					'fill'      => false,
				],
				'shadowSize'       => 0,
				'prepend_tooltip'  => '&euro;&nbsp;',
				'tooltipFormatter' => 'money',
				'class'            => 'failed-sum',
			],
		];

		foreach ( $data as $serie ) {
			// @codingStandardsIgnoreStart
			$serie->legendValue = \array_sum( \wp_list_pluck( $serie->data, 1 ) );
			// @codingStandardsIgnoreEnd
		}

		return $data;
	}

	/**
	 * Get report.
	 *
	 * @link https://github.com/woothemes/woocommerce/blob/2.3.11/assets/js/admin/reports.js
	 * @link https://github.com/woothemes/woocommerce/blob/master/includes/admin/reports/class-wc-report-sales-by-date.php
	 *
	 * @param string    $status    Status.
	 * @param string    $aggregate Aggregate function.
	 * @param \DateTime $start     Start date.
	 * @param \DateTime $end       End date.
	 *
	 * @return array
	 *
	 * @throws \Exception Throws exception on date interval error.
	 */
	private function get_report( $status, $aggregate, $start, $end ) {
		global $wpdb;

		$interval = new \DateInterval( 'P1M' );
		$period   = new \DatePeriod( $start, $interval, $end );

		$date_format = '%Y-%m';

		/* phpcs:ignore WordPress.DB.DirectDatabaseQuery */
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT
					DATE_FORMAT( post.post_date, %s ) AS month,
			  		post.ID
				FROM
					$wpdb->posts AS post
				WHERE
					post.post_type = 'pronamic_payment'
						AND
					post.post_date BETWEEN %s AND %s
						AND
					post.post_status = %s
				ORDER BY
					post_date
				;
				",
				$date_format,
				$start->format( 'Y-m-d' ),
				$end->format( 'Y-m-d' ),
				$status
			)
		);

		$months = \wp_list_pluck( $results, 'month' );

		switch ( $aggregate ) {
			case 'COUNT':
				$data = \array_count_values( $months );

				break;
			case 'SUM':
				$data = \array_fill_keys(
					$months,
					0
				);

				foreach ( $results as $post ) {
					$payment = new Payment( $post->ID );

					$data[ $post->month ] += $payment->get_total_amount()->get_value();
				}

				break;
		}

		$report = [];

		foreach ( $period as $date ) {
			$key = $date->format( 'Y-m' );

			$value = 0;

			if ( isset( $data[ $key ] ) ) {
				$value = (float) $data[ $key ];
			}

			$report[] = [
				// Flot requires milliseconds so multiply with 1000.
				$date->getTimestamp() * 1000,
				$value,
			];
		}

		return $report;
	}
}
