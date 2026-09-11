<?php
/**
 * Payment Gateways by Shipping for WooCommerce - General Section Settings
 *
 * @version 1.6.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Shipping\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBSM_Settings_General' ) ) :

	/**
	 * Alg_WC_PGBSM_Settings_General class.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	class Alg_WC_PGBSM_Settings_General extends Alg_WC_PGBSM_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = '';
			$this->desc = __( 'General', 'payment-gateways-by-shipping-for-woocommerce' );

			parent::__construct();

			add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_script' ) );
		}

		/**
		 * Add admin script.
		 *
		 * @version 1.6.0
		 * @since   1.4.0
		 *
		 * @param string $hook The current admin page.
		 */
		public function add_admin_script( $hook ) {
			if (
				'woocommerce_page_wc-settings' !== $hook ||
				! isset( $_GET['tab'] ) || // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'alg_wc_pgbsm' !== sanitize_text_field( wp_unslash( $_GET['tab'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				return;
			}

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				'alg-wc-pgbsm-admin',
				alg_wc_pgbsm()->plugin_url() . '/assets/js/alg-wc-pgbsm-admin' . $min . '.js',
				array( 'jquery', 'select2' ),
				alg_wc_pgbsm()->version,
				true
			);
		}

		/**
		 * Get select all / deselect all buttons.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		public function get_select_all_buttons() {
			return (
				'<a href="#" class="button alg-wc-pgbsm-select-all">' .
					__( 'Select all', 'payment-gateways-by-shipping-for-woocommerce' ) .
				'</a>' .
				' ' .
				'<a href="#" class="button alg-wc-pgbsm-deselect-all">' .
					__( 'Deselect all', 'payment-gateways-by-shipping-for-woocommerce' ) .
				'</a>'
			);
		}

		/**
		 * Get shipping methods.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_shipping_methods() {
			$shipping_methods = array();
			foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
				$shipping_methods[ $method->id ] = $method->get_method_title();
			}
			return $shipping_methods;
		}

		/**
		 * Get shipping zones.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param bool $include_empty_zone Whether to include the empty shipping zone (ID 0). Default true.
		 */
		public function get_shipping_zones( $include_empty_zone = true ) {
			$zones = WC_Shipping_Zones::get_zones();
			if ( $include_empty_zone ) {
				$zone = new WC_Shipping_Zone( 0 );

				$zones[ $zone->get_id() ] = $zone->get_data();

				$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
				$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
				$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
			}
			return $zones;
		}

		/**
		 * Get shipping methods instances.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param bool $full_data Whether to return full data for each shipping method instance. Default false.
		 */
		public function get_shipping_methods_instances( $full_data = false ) {
			$shipping_methods = array();
			foreach ( $this->get_shipping_zones() as $zone_id => $zone_data ) {
				foreach ( $zone_data['shipping_methods'] as $shipping_method ) {
					if ( $full_data ) {
						$shipping_methods[ $shipping_method->instance_id ] = array(
							'zone_id'                     => $zone_id,
							'zone_name'                   => $zone_data['zone_name'],
							'formatted_zone_location'     => $zone_data['formatted_zone_location'],
							'shipping_method_title'       => $shipping_method->title,
							'shipping_method_id'          => $shipping_method->id,
							'shipping_method_instance_id' => $shipping_method->instance_id,
						);
					} else {
						$shipping_methods[ $shipping_method->instance_id ] = (
							$zone_data['zone_name'] . ': ' .
							$shipping_method->title
						);
					}
				}
			}
			return $shipping_methods;
		}

		/**
		 * Get settings.
		 *
		 * @version 1.6.0
		 * @since   1.0.0
		 *
		 * @todo (feature) Per shipping zone?
		 * @todo (desc) Reset chosen payment method: better desc?
		 * @todo (dev) Reset chosen payment method: remove or at least default to `yes`?
		 * @todo (dev) Remove COD (and maybe other payment gateways) that already have `enable_for_methods` option?
		 */
		public function get_settings() {

			$use_shipping_instance = ( 'yes' === get_option( 'alg_wc_pgbsm_use_shipping_instance', 'no' ) );
			$shipping_methods      = apply_filters(
				'alg_wc_pgbsm_shipping_methods_settings_options',
				(
					$use_shipping_instance ?
					$this->get_shipping_methods_instances() :
					$this->get_shipping_methods()
				),
				$use_shipping_instance
			);

			$general_settings = array(
				array(
					'title'    => __( 'General Options', 'payment-gateways-by-shipping-for-woocommerce' ),
					'desc_tip' => __( 'Set "enable for shipping methods" for WooCommerce payment gateways.', 'payment-gateways-by-shipping-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_pgbsm_general_options',
				),
				array(
					'title'    => __( 'Use shipping instances', 'payment-gateways-by-shipping-for-woocommerce' ),
					'desc'     => __( 'Enable', 'payment-gateways-by-shipping-for-woocommerce' ),
					'desc_tip' => (
						__( 'Enable this if you want to use shipping methods instances (with shipping zones) instead of shipping methods.', 'payment-gateways-by-shipping-for-woocommerce' ) . ' ' .
						__( 'Save changes after enabling this option.', 'payment-gateways-by-shipping-for-woocommerce' )
					),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_pgbsm_use_shipping_instance',
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_pgbsm_general_options',
				),
			);

			$general_settings = array_merge(
				$general_settings,
				array(
					array(
						'title' => __( 'Payment Gateways', 'payment-gateways-by-shipping-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => __( 'If payment gateway is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'payment-gateways-by-shipping-for-woocommerce' ),
						'id'    => 'alg_wc_pgbsm_options',
					),
				)
			);

			$gateways = WC()->payment_gateways->payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$general_settings = array_merge(
					$general_settings,
					array(
						array(
							'title'             => $gateway->title,
							'desc'              => $this->get_select_all_buttons(),
							'desc_tip'          => __( 'Enable for shipping methods', 'payment-gateways-by-shipping-for-woocommerce' ),
							'id'                => ( $use_shipping_instance ? 'alg_wc_pgbsm_enable_instance_' . $key : 'alg_wc_pgbsm_enable_' . $key ),
							'default'           => '',
							'type'              => 'multiselect',
							'class'             => 'wc-enhanced-select',
							'css'               => 'width: 100%;',
							'options'           => $shipping_methods,
							'custom_attributes' => array(
								'data-placeholder' => __( 'Select shipping methods', 'payment-gateways-by-shipping-for-woocommerce' ),
							),
						),
					)
				);
			}

			$general_settings = array_merge(
				$general_settings,
				array(
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_pgbsm_options',
					),
				)
			);

			$advanced_settings = array(
				array(
					'title' => __( 'Advanced Options', 'payment-gateways-by-shipping-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_pgbsm_advanced_options',
				),
				array(
					'title'    => __( 'Reset chosen payment method', 'payment-gateways-by-shipping-for-woocommerce' ),
					'desc'     => __( 'Enable', 'payment-gateways-by-shipping-for-woocommerce' ),
					'desc_tip' => __( 'Enable this if cart fees (e.g., COD fees) are not removed when payment method becomes unavailable.', 'payment-gateways-by-shipping-for-woocommerce' ),
					'id'       => 'alg_wc_pgbsm_reset_payment_method_on_totals',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_pgbsm_advanced_options',
				),
			);

			return array_merge(
				$general_settings,
				$advanced_settings
			);
		}
	}

endif;

return new Alg_WC_PGBSM_Settings_General();
