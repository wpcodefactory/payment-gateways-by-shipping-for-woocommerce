/**
 * Payment Gateways by Shipping for WooCommerce - Admin JavaScript
 *
 * @version 1.6.0
 * @since   1.6.0
 *
 * @author WPFactory
 */

jQuery(document).ready(function () {
	jQuery(".alg-wc-pgbsm-select-all").click(function (event) {
		event.preventDefault();
		jQuery(this)
			.closest("td")
			.find("select.wc-enhanced-select")
			.select2("destroy")
			.find("option")
			.prop("selected", "selected")
			.end()
			.select2();
		return false;
	});
	jQuery(".alg-wc-pgbsm-deselect-all").click(function (event) {
		event.preventDefault();
		jQuery(this)
			.closest("td")
			.find("select.wc-enhanced-select")
			.val("")
			.change();
		return false;
	});
});
