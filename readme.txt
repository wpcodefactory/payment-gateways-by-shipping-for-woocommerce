=== Payment Gateways by Shipping for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, omardabbas, kousikmukherjeeli
Tags: woocommerce, payment gateways, payment gateway, shipping, woo commerce
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 1.4.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set "enable for shipping methods" for WooCommerce payment gateways.

== Description ==

**Payment Gateways by Shipping for WooCommerce** is a lightweight plugin that lets you set the **"enable for shipping methods"** option for WooCommerce payment gateways, i.e. gateways will be shown/hidden on the checkout page depending on which shipping method your customer selects.

You can choose if you want to enable/disable payment gateways by shipping methods or by shipping method instances (e.g. by shipping zones).

### &#127942; Premium Version ###

The free version allows you to set shipping methods for all four standard payment gateways: Direct bank transfer, Check payments, Cash on delivery, and PayPal. With [Payment Gateways by Shipping for WooCommerce Pro](https://wpfactory.com/item/payment-gateways-by-shipping-for-woocommerce/), you can set shipping methods for any non-standard payment gateway.

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Visit [plugin site](https://wpfactory.com/item/payment-gateways-by-shipping-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Payment Gateways by Shipping".

== Changelog ==

= 1.4.3 - 18/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 1.4.2 - 03/11/2022 =
* WC tested up to: 7.0.
* Tested up to: 6.1.
* Readme.txt updated.
* Deploy script added.

= 1.4.1 - 01/03/2022 =
* Fix - Admin settings - "Select all" and "Deselect all" buttons added to *all* gateways now.
* Dev - Admin settings restyled.
* Dev - Minor code refactoring.
* WC tested up to: 6.2.
* Tested up to: 5.9.

= 1.4.0 - 11/08/2021 =
* Dev - Admin settings - "Select all" and "Deselect all" buttons added.
* Dev - Plugin is initialized on the `plugins_loaded` action now.
* Dev - Localisation - `load_plugin_textdomain()` function  moved to the `init` action.
* Dev - Code refactoring.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 1.3.0 - 02/12/2020 =
* Dev - Advanced - "Reset chosen payment method" option added.
* Dev - Code refactoring.
* WC tested up to: 4.7.
* Tested up to: 5.5.

= 1.2.1 - 26/05/2020 =
* Dev - Use shipping instances - "WooCommerce Advanced Shipping" plugin compatibility added.
* WC tested up to: 4.1.
* Tested up to: 5.4.

= 1.2.0 - 19/03/2020 =
* Dev - Admin settings - Descriptions updated.
* Dev - Code refactoring.
* Tested up to: 5.3.
* WC tested up to: 4.0.

= 1.1.0 - 27/07/2019 =
* Dev - "PayPal" and "Check payments" payment gateways moved to free version.
* Dev - `alg_wc_pgbsm_shipping_methods_settings_options` filter added.
* Dev - Code refactoring.
* Dev - Admin settings - Descriptions updated; "Your settings have been reset" notice added.
* Plugin URI updated.
* WC tested up to: 3.6.
* Tested up to: 5.2.

= 1.0.0 - 25/04/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
