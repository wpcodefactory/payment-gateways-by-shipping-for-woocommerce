<?php
/**
 * Payment Gateways by Shipping for WooCommerce - General Section Settings
 *
 * @version 1.4.1
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Payment_Gateways_by_Shipping_Settings_General' ) ) :

class Alg_WC_Payment_Gateways_by_Shipping_Settings_General extends Alg_WC_Payment_Gateways_by_Shipping_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'payment-gateways-by-shipping-for-woocommerce' );
		parent::__construct();
		add_action( 'admin_footer', array( $this, 'add_admin_script' ) );
	}

	/**
	 * add_admin_script.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @todo    [next] (dev) move this to a separate js file
	 * @todo    [next] (dev) load on needed pages only
	 */
	function add_admin_script() {
		?><script>
			jQuery( document ).ready( function() {
				jQuery( '.alg-wc-pgbsm-select-all' ).click( function( event ) {
					event.preventDefault();
					jQuery( this ).closest( 'td' ).find( 'select.chosen_select' ).select2( 'destroy' ).find( 'option' ).prop( 'selected', 'selected' ).end().select2();
					return false;
				} );
				jQuery( '.alg-wc-pgbsm-deselect-all' ).click( function( event ) {
					event.preventDefault();
					jQuery( this ).closest( 'td' ).find( 'select.chosen_select' ).val( '' ).change();
					return false;
				} );
			} );
		</script><?php
	}

	/**
	 * get_select_all_buttons.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_select_all_buttons() {
		return
			'<a href="#" class="button alg-wc-pgbsm-select-all">'   . __( 'Select all', 'payment-gateways-by-shipping-for-woocommerce' )   . '</a>' . ' ' .
			'<a href="#" class="button alg-wc-pgbsm-deselect-all">' . __( 'Deselect all', 'payment-gateways-by-shipping-for-woocommerce' ) . '</a>';
	}

	/**
	 * get_shipping_methods.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_shipping_methods() {
		$shipping_methods = array();
		foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
			$shipping_methods[ $method->id ] = $method->get_method_title();
		}
		return $shipping_methods;
	}

	/**
	 * get_shipping_zones.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_shipping_zones( $include_empty_zone = true ) {
		$zones = WC_Shipping_Zones::get_zones();
		if ( $include_empty_zone ) {
			$zone                                                = new WC_Shipping_Zone( 0 );
			$zones[ $zone->get_id() ]                            = $zone->get_data();
			$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
			$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
			$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
		}
		return $zones;
	}

	/**
	 * get_shipping_methods_instances.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_shipping_methods_instances( $full_data = false ) {
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
					$shipping_methods[ $shipping_method->instance_id ] = $zone_data['zone_name'] . ': ' . $shipping_method->title;
				}
			}
		}
		return $shipping_methods;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.4.1
	 * @since   1.0.0
	 *
	 * @todo    [next] (feature) per shipping zone?
	 * @todo    [maybe] (desc) Reset chosen payment method: better desc?
	 * @todo    [maybe] (dev) Reset chosen payment method: remove or at least default to `yes`?
	 * @todo    [maybe] (dev) remove COD (and maybe other payment gateways) that already have `enable_for_methods` option?
	 */
	function get_settings() {
		$main_settings = array(
			array(
				'title'    => __( 'Payment Gateways by Shipping Options', 'payment-gateways-by-shipping-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pgbsm_options',
			),
			array(
				'title'    => __( 'Payment Gateways by Shipping', 'payment-gateways-by-shipping-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'payment-gateways-by-shipping-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Set "enable for shipping methods" for WooCommerce payment gateways.', 'payment-gateways-by-shipping-for-woocommerce' ),
				'id'       => 'alg_wc_pgbsm_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pgbsm_options',
			),
		);
		$use_shipping_instance = ( 'yes' === get_option( 'alg_wc_pgbsm_use_shipping_instance', 'no' ) );
		$shipping_methods      = apply_filters( 'alg_wc_pgbsm_shipping_methods_settings_options',
			( $use_shipping_instance ? $this->get_shipping_methods_instances() : $this->get_shipping_methods() ), $use_shipping_instance );
		$general_settings = array(
			array(
				'title'    => __( 'General Options', 'payment-gateways-by-shipping-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pgbsm_general_options',
			),
			array(
				'title'    => __( 'Use shipping instances', 'payment-gateways-by-shipping-for-woocommerce' ),
				'desc'     => __( 'Enable', 'payment-gateways-by-shipping-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you want to use shipping methods instances (with shipping zones) instead of shipping methods.',
					'payment-gateways-by-shipping-for-woocommerce' ) . ' ' . __( 'Save changes after enabling this option.', 'payment-gateways-by-shipping-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_pgbsm_use_shipping_instance',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pgbsm_general_options',
			),
		);
		$general_settings = array_merge( $general_settings, array(
			array(
				'title' => __( 'Payment Gateways', 'payment-gateways-by-shipping-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'If payment gateway is only available for certain methods, set it up here. Leave blank to enable for all methods.',
					'payment-gateways-by-shipping-for-woocommerce' ),
				'id'    => 'alg_wc_pgbsm_options',
			),
		) );
		$gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $gateways as $key => $gateway ) {
			$general_settings = array_merge( $general_settings, array(
				array(
					'title'             => $gateway->title,
					'desc'              => ( ! in_array( $key, array( 'bacs', 'cod', 'paypal', 'cheque' ) ) ?
						apply_filters( 'alg_wc_pgbsm_settings',
							sprintf( __( 'You will need the "%s" plugin to set the option for the "%s" gateway.', 'payment-gateways-by-shipping-for-woocommerce' ),
								'<a target="_blank" href="https://wpfactory.com/item/payment-gateways-by-shipping-for-woocommerce/">' .
									__( 'Payment Gateways by Shipping for WooCommerce Pro', 'payment-gateways-by-shipping-for-woocommerce' ) .
								'</a>',
								$gateway->title
							), 'buttons', array( 'section' => $this ) ) :
						$this->get_select_all_buttons() ),
					'desc_tip'          => __( 'Enable for shipping methods', 'woocommerce' ),
					'id'                => ( $use_shipping_instance ? 'alg_wc_pgbsm_enable_instance_' . $key : 'alg_wc_pgbsm_enable_' . $key ),
					'default'           => '',
					'type'              => 'multiselect',
					'class'             => 'chosen_select',
					'css'               => 'width: 100%;',
					'options'           => $shipping_methods,
					'custom_attributes' => array_merge( array( 'data-placeholder' => __( 'Select shipping methods', 'woocommerce' ) ),
						( ! in_array( $key, array( 'bacs', 'cod', 'paypal', 'cheque' ) ) ? apply_filters( 'alg_wc_pgbsm_settings', array( 'disabled' => 'disabled' ), 'array' ) : array() ) ),
				),
			) );
		}
		$general_settings = array_merge( $general_settings, array(
			array(
				'type'  => 'sectionend',
				'id'    => 'alg_wc_pgbsm_options',
			),
		) );
		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'payment-gateways-by-shipping-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pgbsm_advanced_options',
			),
			array(
				'title'    => __( 'Reset chosen payment method', 'payment-gateways-by-shipping-for-woocommerce' ),
				'desc'     => __( 'Enable', 'payment-gateways-by-shipping-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if cart fees (e.g. COD fees) are not removed when payment method becomes unavailable.', 'payment-gateways-by-shipping-for-woocommerce' ),
				'id'       => 'alg_wc_pgbsm_reset_payment_method_on_totals',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pgbsm_advanced_options',
			),
		);
		return array_merge( $main_settings, $general_settings, $advanced_settings );
	}

}

endif;

return new Alg_WC_Payment_Gateways_by_Shipping_Settings_General();
