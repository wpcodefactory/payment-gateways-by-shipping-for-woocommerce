<?php
/**
 * Payment Gateways by Shipping for WooCommerce - Core Class
 *
 * @version 1.3.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Payment_Gateways_by_Shipping_Core' ) ) :

class Alg_WC_Payment_Gateways_by_Shipping_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_pgbsm_plugin_enabled', 'yes' ) ) {
			$this->use_shipping_instance = ( 'yes' === get_option( 'alg_wc_pgbsm_use_shipping_instance', 'no' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), PHP_INT_MAX, 1 );
			if ( 'yes' === get_option( 'alg_wc_pgbsm_reset_payment_method_on_totals', 'no' ) ) {
				add_action( 'woocommerce_before_calculate_totals', array( $this, 'maybe_reset_chosen_payment_method' ), 1, 0 );
			}
		}
	}

	/**
	 * maybe_reset_chosen_payment_method.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 *
	 * @todo    [next] (dev) test with https://wpfactory.com/blog/how-to-add-cart-fees-in-woocommerce-with-php/
	 * @todo    [maybe] (dev) `woocommerce_cart_calculate_fees` instead of `woocommerce_before_calculate_totals`?
	 * @todo    [maybe] (dev) `wc_smart_cod_fee`: `return ( ! in_array( 'cod', array_keys( WC()->payment_gateways->get_available_payment_gateways() ) ) ? 0 : $fee )`?
	 */
	function maybe_reset_chosen_payment_method() {
		if ( '' != ( $chosen_payment_method = WC()->session->get( 'chosen_payment_method' ) ) || isset( $_POST['payment_method'] ) ) {
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			$available_gateways = array_keys( $available_gateways );
			if ( isset( $_POST['payment_method'] ) && ! in_array( $_POST['payment_method'], $available_gateways ) ) {
				$_POST['payment_method'] = '';
			}
			if ( '' != $chosen_payment_method && ! in_array( $chosen_payment_method, $available_gateways ) ) {
				WC()->session->set( 'chosen_payment_method', '' );
			}
		}
	}

	/**
	 * available_payment_gateways.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) optionally `maybe_reset_chosen_payment_method()` right after `unset()`?
	 */
	function available_payment_gateways( $_available_gateways ) {
		foreach ( $_available_gateways as $key => $gateway ) {
			$enable_for_methods = get_option( 'alg_wc_pgbsm_enable_' . ( $this->use_shipping_instance ? 'instance_' : '' ) . $key, '' );
			if ( ! empty( $enable_for_methods ) && ! $this->check_if_enabled_for_methods( $key, $enable_for_methods ) ) {
				unset( $_available_gateways[ $key ] );
				continue;
			}
		}
		return $_available_gateways;
	}

	/**
	 * check_if_enabled_for_methods.
	 *
	 * @version 1.2.1
	 * @since   1.0.0
	 *
	 * @see     https://woocommerce.github.io/code-reference/classes/WC-Gateway-COD.html#method_is_available
	 *
	 * @todo    [next] (dev) do we really need to check all the standard stuff like `WC()->cart->needs_shipping()` etc.?
	 * @todo    [next] (dev) cache result?
	 */
	function check_if_enabled_for_methods( $gateway_key, $enable_for_methods ) {

		$order          = null;
		$needs_shipping = false;

		// Test if shipping is needed first
		if ( WC()->cart && WC()->cart->needs_shipping() ) {
			$needs_shipping = true;
		} elseif ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
			$order_id = absint( get_query_var( 'order-pay' ) );
			$order    = wc_get_order( $order_id );

			// Test if order needs shipping.
			if ( 0 < sizeof( $order->get_items() ) ) {
				foreach ( $order->get_items() as $item ) {
					$_product = $order->get_product_from_item( $item );
					if ( $_product && $_product->needs_shipping() ) {
						$needs_shipping = true;
						break;
					}
				}
			}
		}

		$needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

		// Check methods
		if ( ! empty( $enable_for_methods ) && $needs_shipping ) {

			// Only apply if all packages are being shipped via chosen methods, or order is virtual
			$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

			if ( isset( $chosen_shipping_methods_session ) ) {
				$chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
			} else {
				$chosen_shipping_methods = array();
			}

			$check_method = false;

			if ( is_object( $order ) ) {
				if ( $order->shipping_method ) {
					$check_method = $order->shipping_method;
				}

			} elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
				$check_method = false;
			} elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
				$check_method = $chosen_shipping_methods[0];
			}

			if ( ! $check_method ) {
				return false;
			}

			$found = false;

			// Shipping method instance
			if ( $this->use_shipping_instance ) {
				if ( 'jem_table_rate_' === substr( $check_method, 0, 15 ) ) {
					// https://wordpress.org/plugins/woocommerce-easy-table-rate-shipping/
					$check_method = explode( '_', $check_method );
					if ( ! isset( $check_method[3] ) || ! is_numeric( $check_method[3] ) ) {
						return false;
					} else {
						$check_method = $check_method[3];
					}
				} else {
					$check_method = explode( ':', $check_method, 2 );
					if ( ! isset( $check_method[1] ) || ! is_numeric( $check_method[1] ) ) {
						return false;
					} else {
						$check_method = $check_method[1];
					}
				}
			}

			// Final check
			foreach ( $enable_for_methods as $method_id ) {
				if ( $this->use_shipping_instance ) {
					if ( $check_method == $method_id ) {
						return true;
					}
				} else {
					if ( strpos( $check_method, $method_id ) === 0 ) {
						return true;
					}
				}
			}

			if ( ! $found ) {
				return false;
			}
		}

		return true;
	}

}

endif;

return new Alg_WC_Payment_Gateways_by_Shipping_Core();
