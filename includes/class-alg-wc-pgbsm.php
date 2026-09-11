<?php
/**
 * Payment Gateways by Shipping for WooCommerce - Main Class
 *
 * @version 1.6.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Shipping
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBSM' ) ) :

	/**
	 * Alg_WC_PGBSM class.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	final class Alg_WC_PGBSM {

		/**
		 * Plugin version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_VERSION;

		/**
		 * Core.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 *
		 * @var Alg_WC_PGBSM_Core
		 */
		public $core;

		/**
		 * The single instance of the class.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 *
		 * @var Alg_WC_PGBSM
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_PGBSM Instance.
		 *
		 * Ensures only one instance of Alg_WC_PGBSM is loaded or can be loaded.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_WC_PGBSM
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_PGBSM Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Check for active WooCommerce plugin.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) . 'vendor/autoload.php';
			}

			// Declare compatibility with custom order tables for WooCommerce.
			add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

			// Pro.
			if ( 'payment-gateways-by-shipping-for-woocommerce-pro.php' === basename( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-pgbsm-pro.php';
			}

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * WC declare compatibility.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 *
		 * @see https://developer.woocommerce.com/docs/hpos-extension-recipe-book/
		 */
		public function wc_declare_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				$files = (
					defined( 'ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE_FREE' ) ?
					array( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE, ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE_FREE ) :
					array( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE )
				);
				foreach ( $files as $file ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
						'custom_order_tables',
						$file,
						true
					);
				}
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 */
		public function includes() {
			// Core.
			$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pgbsm-core.php';
		}

		/**
		 * Admin.
		 *
		 * @version 1.5.0
		 * @since   1.1.0
		 */
		public function admin() {
			// Action links.
			add_filter(
				'plugin_action_links_' . plugin_basename( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ),
				array( $this, 'action_links' )
			);

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// WC Settings tab as WPFactory submenu item.
			add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

			// Settings.
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

			// Version update.
			if ( get_option( 'alg_wc_pgbsm_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_update' ) );
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @param mixed $links Action links for the plugin.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_pgbsm' ) . '">' .
				__( 'Settings', 'payment-gateways-by-shipping-for-woocommerce' ) .
			'</a>';

			if ( 'payment-gateways-by-shipping-for-woocommerce.php' === basename( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/payment-gateways-by-shipping-for-woocommerce/">' .
					__( 'Go Pro', 'payment-gateways-by-shipping-for-woocommerce' ) .
				'</a>';
			}

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add cross selling library.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Move WC settings tab to WPFactory menu.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 */
		public function move_wc_settings_tab_to_wpfactory_menu() {
			if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
				return;
			}

			$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu(
				array(
					'wc_settings_tab_id' => 'alg_wc_pgbsm',
					'menu_title'         => __( 'Payment Gateways by Shipping', 'payment-gateways-by-shipping-for-woocommerce' ),
					'page_title'         => __( 'WooCommerce Payment Gateways by Shipping Method', 'payment-gateways-by-shipping-for-woocommerce' ),
					'plugin_icon'        => array(
						'get_url_method'    => 'wporg_plugins_api',
						'wporg_plugin_slug' => 'payment-gateways-by-shipping-for-woocommerce',
					),
				)
			);
		}

		/**
		 * Add Payment Gateways by Shipping settings tab to WooCommerce settings.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 *
		 * @param array $settings WooCommerce settings array.
		 *
		 * @return array Modified WooCommerce settings array.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-settings-pgbsm.php';
			return $settings;
		}

		/**
		 * Version update.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		public function version_update() {
			update_option( 'alg_wc_pgbsm_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_WC_PAYMENT_GATEWAYS_BY_SHIPPING_FILE ) );
		}
	}

endif;
