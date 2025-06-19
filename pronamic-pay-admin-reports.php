<?php
/**
 * Plugin Name: Pronamic Pay Admin Reports
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-admin-reports/
 * Description: This plugin adds simple reporting functionality in the WordPress admin dashboard for the Pronamic Pay plugin.
 *
 * Version: 1.0.1
 * Requires at least: 5.9
 * Requires PHP: 8.1
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-pay-admin-reports
 * Domain Path: /languages/
 *
 * License: GPL-2.0-or-later
 *
 * GitHub URI: https://github.com/pronamic/pronamic-pay-admin-reports
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\PronamicPayAdminReports
 */

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'pronamic-pay-admin-reports', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

\Pronamic\WordPress\Pay\Plugin::instance(
	[
		'file'             => __FILE__,
		'action_scheduler' => __DIR__ . '/packages/woocommerce/action-scheduler/action-scheduler.php',
	]
);

\Pronamic\PronamicPayAdminReports\Plugin::instance()->setup();
