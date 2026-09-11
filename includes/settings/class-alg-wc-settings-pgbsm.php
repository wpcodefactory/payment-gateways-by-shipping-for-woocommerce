<?php
/**
 * Payment Gateways by Shipping for WooCommerce - Settings
 *
 * @version 1.6.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Shipping\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Settings_PGBSM' ) ) :

	/**
	 * Alg_WC_Settings_PGBSM class.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	class Alg_WC_Settings_PGBSM extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 1.5.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'alg_wc_pgbsm';
			$this->label = __( 'Payment Gateways by Shipping', 'payment-gateways-by-shipping-for-woocommerce' );

			parent::__construct();

			// Sections.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pgbsm-settings-section.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pgbsm-settings-general.php';
		}

		/**
		 * Get settings.
		 *
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			return array_merge(
				apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				array(
					array(
						'title' => __( 'Reset Settings', 'payment-gateways-by-shipping-for-woocommerce' ),
						'type'  => 'title',
						'id'    => $this->id . '_' . $current_section . '_reset_options',
					),
					array(
						'title'    => __( 'Reset section settings', 'payment-gateways-by-shipping-for-woocommerce' ),
						'desc'     => '<strong>' . __( 'Reset', 'payment-gateways-by-shipping-for-woocommerce' ) . '</strong>',
						'desc_tip' => __( 'Check the box and save changes to reset.', 'payment-gateways-by-shipping-for-woocommerce' ),
						'id'       => $this->id . '_' . $current_section . '_reset',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => $this->id . '_' . $current_section . '_reset_options',
					),
				)
			);
		}

		/**
		 * Maybe reset settings.
		 *
		 * @version 1.1.0
		 * @since   1.0.0
		 */
		public function maybe_reset_settings() {
			global $current_section;
			if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
				foreach ( $this->get_settings() as $value ) {
					if ( isset( $value['id'] ) ) {
						$id = explode( '[', $value['id'] );
						delete_option( $id[0] );
					}
				}
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}

		/**
		 * Admin notice settings reset.
		 *
		 * @version 1.5.0
		 * @since   1.1.0
		 */
		public function admin_notice_settings_reset() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
				esc_html__( 'Your settings have been reset.', 'payment-gateways-by-shipping-for-woocommerce' ) .
			'</strong></p></div>';
		}

		/**
		 * Save settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
		}
	}

endif;

return new Alg_WC_Settings_PGBSM();
