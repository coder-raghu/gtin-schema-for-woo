<?php
/*
  Plugin Name: GTIN Schema for Woo
  Description: Display gtin on Google tool for woo products.
  Version: 1.2
  Author: Raghu Prajapati  
  Text Domain: gtin-schema
 */

defined( 'ABSPATH' ) || exit;

define('GTIN_SCHEMA_VERSION', '1.1');

define('GTIN_SCHEMA_FILE', __FILE__ );

define('GTIN_SCHEMA_ASSETS_JS_URL', plugins_url( '/', __FILE__ ). 'assets/js' );

//---------------------------------------------------------------------------
// Create a meta field for GTIN (Global Trade Identification Number)
//---------------------------------------------------------------------------
function gtin_schema_add_field_to_inventory() {
    $public_label = gtin_schema_get_title_label();
    $input   = array(
        'id'          => '_gtin_schema_code',
        'label'       => __( $public_label, 'gtin-schema' ),
        'value'       => get_post_meta( get_the_ID(), '_gtin_schema_code', true ),
        'desc_tip'    => true,
        'description' => __( 'Enter the Global Trade Identification Number (UPC, EAN, ISBN, etc.)', 'gtin-schema' ),
    );
    ?>

    <div id="gtin_attribute" class="gtin_group">
        <?php woocommerce_wp_text_input( $input ); ?>
    </div>

  <?php
}

add_action( 'woocommerce_product_options_inventory_product_data', 'gtin_schema_add_field_to_inventory');

//------------------------------------------------------------------
// Save the product's GTIN (Global Trade Identification Number)
//-------------------------------------------------------------------
function gtin_schema_save_custom_product_field( $product_id ) {
    if (! isset( $_POST['_gtin_schema_code'], $_POST['woocommerce_meta_nonce'] )
        || ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        || ! current_user_can( 'edit_products' )
        || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' )) {
        return;
    }

    $gtin = sanitize_text_field( $_POST['_gtin_schema_code'] );
    update_post_meta( $product_id, '_gtin_schema_code', $gtin );
}
add_action( 'woocommerce_process_product_meta','gtin_schema_save_custom_product_field' );

//--------------------------------------------------
// Set GTIN number to product structured data
//--------------------------------------------------
add_filter( 'woocommerce_structured_data_product','gtin_schema_add_to_structured_data',10,2);
function gtin_schema_add_to_structured_data( $markup ) {
    
    global $product;
    $product_id = $product->get_id();
    $gtin = gtin_schema_get_gtin_code($product_id);
    $gtin_code = get_option('gtin_schema_product_date_structure');
    $markup[$gtin_code] = trim($gtin);
    return $markup;

};

//---------------------------------------------------------
// Check woocommerce plugin active or not
//---------------------------------------------------------
function gtin_schema_get_title_label(){
    $public_label = get_option('wc_gtin_schema_admin_tab_label');
    if(!is_admin()){
        $public_label = __(substr( $public_label, - 1 ) == ':' ? str_replace( ':', '', $public_label ) : $public_label);
    }
    if(empty($public_label)){
        $public_label = __(ucfirst(get_option('gtin_schema_product_date_structure','GTIN')));
    }
    return apply_filters( 'gtin_schema_public_title_label', $public_label );
}

//---------------------------------------------------------
// Get product GTIN code
//---------------------------------------------------------
function gtin_schema_get_gtin_code($product_id){
    $gtin = get_post_meta($product_id, '_gtin_schema_code', true );
    if(empty($gtin) && get_option('wc_gtin_schema_admin_tab_get_get_sku') == 'yes'){
        $gtin = get_post_meta($product_id, '_sku', true );
    }
    return apply_filters( 'gtin_schema_gtin_schema_code', $gtin );
}

//---------------------------------------------------------
// Check woocommerce plugin active or not
//---------------------------------------------------------
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (is_plugin_active('woocommerce/woocommerce.php')) {

    if(is_admin()){

        require 'classes/admin/class-gtin-admin-tab.php';
        new GTIN_Admin_Tab();

    } else {

        require 'classes/class-gtin-schema-front.php';
        new GTIN_Schema_Front();

    }
} else {

    if(is_admin()){
        add_action('admin_notices', function() {
        $message = esc_html__('GTIN Schema needs to run WooCommerce. Please, install and active WooCommerce plugin.', 'gtin-schema');
        printf('<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', $message);
        });
    }

}


