<?php
/*
Plugin Name: Payment Gateways by Shipping for WooCommerce
Plugin URI: https://wpfactory.com/item/payment-gateways-by-shipping-for-woocommerce/
Description: Set "enable for shipping methods" for WooCommerce payment gateways.
Version: 1.5.1
Author: WPFactory
Author URI: https://wpfactory.com
Requires at least: 4.4
Text Domain: payment-gateways-by-shipping-for-woocommerce
Domain Path: /langs
WC tested up to: 10.1
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( 'payment-gateways-by-shipping-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.5.0
	 * @since   1.4.0
	 */
	$plugin = 'payment-gateways-by-shipping-for-woocommerce-pro/payment-gateways-by-shipping-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		(
			is_multisite() &&
			array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
		)
	) {
		defined( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE_FREE' ) || define( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_VERSION' ) || define( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_VERSION', '1.5.1' );

defined( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE' ) || define( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-pgbsm.php';

if ( ! function_exists( 'alg_wc_pgbsm' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Payment_Gateways_by_Shipping to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_pgbsm() {
		return Alg_WC_Payment_Gateways_by_Shipping::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_pgbsm' );
