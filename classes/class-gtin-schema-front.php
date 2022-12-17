<?php
defined( 'ABSPATH' ) || exit;
/**
 * GTIN schema for front side
 */
class GTIN_Schema_Front
{
	
	public function __construct()
	{
		//Product loop page
		if(get_option('wc_gtin_schema_admin_tab_in_loop', 'no') == 'yes'){
			add_action('woocommerce_after_shop_loop_item_title', [$this, 'gtin_schema_in_loop_product']);
		}

		//Single product page
		if(get_option('wc_gtin_schema_admin_tab_in_single_product', 'no') == 'yes'){
			$this->gtin_schema_in_single_product();
			// Add tab
			add_filter('woocommerce_product_tabs', [$this, 'gtin_schema_product_tab']);
			
            //For variation product
            function gtin_add_scripts_file() {
                $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			    wp_enqueue_script( 'gtin_schema_front', GTIN_SCHEMA_ASSETS_JS_URL.'/gtin_schema_woo_front'.$suffix.'.js',array( 'jquery' ), GTIN_SCHEMA_VERSION, true );
            }
            add_action( 'wp_enqueue_scripts', 'gtin_add_scripts_file' );
        	add_filter( 'woocommerce_available_variation', [$this, 'gtin_schema_add_params_to_variation'], 10, 3 );
		}

		// Cart and checkout page
		if(get_option('wc_gtin_schema_admin_tab_in_cart', 'no') == 'yes'){
			add_action( 'woocommerce_get_item_data', [$this, 'gtin_schema_show_code_on_cart'],10,2);
		}

		// Oder item
		if(get_option('wc_gtin_schema_admin_tab_in_order_item', 'no') == 'yes'){
			add_action( 'woocommerce_checkout_create_order_line_item', [$this, 'gtin_schema_display_in_order_item'],30,3);
		}
	}
	
	//--------------------------------------------------------
	// Show schema in loop product
	//--------------------------------------------------------
	public function gtin_schema_in_loop_product() {
		global $product;
		$product_id = $product->get_id();    	
    	echo '<div class="gtin-schema-in-loop">';
    	echo esc_html(get_post_meta($product_id, '_gtin_schema_code', true ));
    	echo '</div>';
	}

	//--------------------------------------------------------
	// Get the selected position
	//--------------------------------------------------------
	public function gtin_schema_in_single_product() {
		$position = 41;
    	$selected_position = get_option('wc_gtin_schema_admin_tab_single_product_position','no');
    	if (!$selected_position) {
      		update_option('wc_gtin_schema_admin_tab_single_product_position', 'after_meta');
    	}

    	switch ($selected_position) {
    	  case 'before_title':
    	    $position = 4;
    	    break;
    	  case 'after_title':
    	    $position = 6;
    	    break;
    	  case 'after_price':
    	    $position = 11;
    	    break;
    	  case 'after_excerpt':
    	    $position = 21;
    	    break;
    	  case 'after_add_to_cart':
    	    $position = 31;
    	    break;
    	  case 'after_meta':
    	    $position = 41;
    	    break;
    	  case 'after_sharing':
    	    $position = 51;
    	    break;
    	}

    	if ($selected_position == 'meta') {
    	  add_action('woocommerce_product_meta_end', [$this, 'gtin_woocommerce_single_product_summary']);
    	} else {
    	  add_action('woocommerce_single_product_summary', [$this, 'gtin_woocommerce_single_product_summary'], $position);
    	}
	}

	//--------------------------------------------------------
	// Show GTIN schema in single product 
	//--------------------------------------------------------
	public function gtin_woocommerce_single_product_summary() {
    	global $product;
        $product_id = $product->get_id();
    	$show_as = get_option('wc_gtin_schema_admin_tab_in_single_product');
        if ($show_as == 'yes') { ?>
            <div class="schema_wrapper"><?php echo esc_html(gtin_schema_get_title_label().':', 'gtin-schema'); ?>
                <span class="gtin-schema"><?php echo esc_html(gtin_schema_get_gtin_code($product_id)); ?></span>
            </div>
            <?php
        }
	}

    //--------------------------------------------------------
    // Show tab in single product
    //--------------------------------------------------------
    public function gtin_schema_product_tab($tabs) {
        $show_schema_tab = get_option('wc_gtin_schema_admin_tab_single_product_tab');
        if($show_schema_tab == 'yes' && $show_schema_tab){
            $tabs['gtin_schema_tab'] = array(
                'title'     => __( gtin_schema_get_title_label(), 'gtin-schema' ),
                'priority'  => 20,
                'callback'  => [$this, 'gtin_product_tab_content']
            );
        }
        return $tabs;
    }

    //--------------------------------------------------------
    // GTIN Schema tab content
    //--------------------------------------------------------
    function gtin_product_tab_content() {
        global $product;
        $product_id = $product->get_id();
        ob_start();
        ?>
            <h2><?php echo apply_filters('woocommerce_product_schema_heading', esc_html__(gtin_schema_get_title_label(), 'gtin-schema')); ?></h2>
            <div id="tab-gtin_schema">
                <h4><?php echo esc_html(gtin_schema_get_gtin_code($product_id)); ?></h4>
            </div>

        <?php
        echo ob_get_clean();
    }

    //--------------------------------------------------------
    // GTIN Schema add to product variation
    //--------------------------------------------------------
    public function gtin_schema_add_params_to_variation($args, $instance, $variation ){
        $args['_gtin_schema_code'] = $variation->get_meta( '_gtin_schema_code' );
        return $args;
    }

    //--------------------------------------------------------
	// Show schema in cart and checkout page
	//--------------------------------------------------------
	public function gtin_schema_show_code_on_cart($item_data, $cart_item) {		
		if ( ! isset( $cart_item['data'] ) ) {
			return $item_data;
		}
		if(is_cart() && get_option('wc_gtin_schema_admin_tab_in_cart') == 'yes' || is_checkout() && get_option('wc_gtin_schema_admin_tab_in_checkout') == 'yes' ){
			$product = $cart_item['data'];
			$gtin = $product->get_meta( '_gtin_schema_code' );
            if(!empty( $gtin )){
    			$item_data['gtin_schema_code']['name']  = gtin_schema_get_title_label();
    			$item_data['gtin_schema_code']['value'] = $gtin;
            }
		}
    	return $item_data;
	}

	//--------------------------------------------------------
	// Show schema in order item
	//--------------------------------------------------------
	public function gtin_schema_display_in_order_item( $item, $cart_item_key, $values ) {		
        if ( isset( $values['data'] ) ) {
            $product = $values['data'];
            $gtin = $product->get_meta( '_gtin_schema_code' );
            if ( ! empty( $gtin ) ) {                
    		    $item->add_meta_data( gtin_schema_get_title_label(), $gtin );
            }
        }
	}	
}

?>