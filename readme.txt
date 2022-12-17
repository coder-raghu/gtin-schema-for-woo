=== GTIN Schema for WooCommerce ===
Contributors: prajapatiraghu
Tags: seo, gtin, schema, woo
Tested up to: 6.0.1
Stable tag: 1.2
Requires at least: 5.0
Requires PHP: 7.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add GTIN number to inventory tabs of woocommerce product.

== Description ==

Added GTIN number to inventory tabs of woo product.

* UPC: this is the primary GTIN in North America.
* EAN/UCC: the major GTIN used outside of North America
* JAN: the Japanese GTIN
* ISBN: a GTIN for books

It's very simple.

= Main features of Product GTIN (EAN, UPC, ISBN) for WooCommerce: =
* Option to show GTIN code in WooCommerce shop page.
* Option to show GTIN code in WooCommerce product detail page.
* Option to show GTIN code in WooCommerce cart page.
* Option to show GTIN code in WooCommerce checkout page.
* Option to show GTIN code in WooCommerce Order Items.
* Option to choose the position of GTIN code inside the single product page
* Option to add new GTIN tab in product details page

= Add new code in GTIM schema: =

`add_filter('gtin_schema_data_structure_options', 'add_extra_gtin_code');
 function add_extra_gtin_code($codes){
	$codes['volume'] = "Volume";
	return $codes;
 }`

This function paste in your functions.php file

== Installation ==

Install GTIN Schema for Woo either via the WordPress.org plugin directory, or by uploading the files to your server.

Activate GTIN Schema for Woo through the Plugins menu in WordPress.


In the search field type the plugin name and click Search Plugins. Once you’ve found our plugin you can install it by simply clicking “Install Now”.


== Frequently Asked Questions ==

= Why is Google showing GTIN warning? =

Maybe your product doesn’t have GTIN

= Is there any setup need =

No setup need. Just install the plugin and add GTN number on product page.

== Changelog ==
= 1.2 - Released: Jan 12, 2021 =
* Add GTIN Schema structure options

= 1.1 - Released: Oct 26, 2020 =
* Added setting options

= 1.0 =
* Initial release.


== Upgrade Notice ==
= 1.1 =
 Added setting options
= 1.0 =
1.0 is Initial release. Its will not disataurn you site.

== Screenshots ==
1. wp-admin setting