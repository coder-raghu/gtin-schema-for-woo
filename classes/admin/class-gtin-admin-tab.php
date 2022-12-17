<?php
defined( 'ABSPATH' ) || exit;
/**
 * Set admin tab in woocommerce settings
 */
class GTIN_Admin_Tab
{
	
	public function __construct()
	{
		$this->id = "gtin_schema";
		$this->label = __('GTIN Schema', 'gtin-schema');

		add_filter('woocommerce_settings_tabs_array', [$this, 'gtin_schema_add_tab'], 200);
		add_action('woocommerce_settings_' . $this->id, [$this, 'gtin_schema_tab_settings']);
		add_action('woocommerce_update_options_' . $this->id, [$this, 'gtin_schema_save_settings']);
		add_action( 'woocommerce_variation_options_pricing', [$this,'bbloomer_add_custom_field_to_variations'], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [$this,'bbloomer_save_custom_field_variations'], 10, 2 );
		add_filter( 'woocommerce_available_variation', [$this,'bbloomer_add_custom_field_variation_data'] );
		add_filter( 'woocommerce_available_variation', array( $this, 'add_params_to_available_variation' ), 10, 3 );
		
		// Product list
		add_filter('manage_product_posts_columns', [$this, 'gtin_schema_table_head']);
		add_action( 'manage_product_posts_custom_column', [$this,'gtin_schema_table_content'], 10,2);
		add_filter( 'manage_edit-product_sortable_columns', [$this,'gtin_schema_table_sorting'] );
		add_filter( 'request', [$this,'gtin_schema_column_orderby'] );
		// Extend product search
		add_filter( 'request', [$this,'gtin_schema_extend_product_search_query'], 20 );
	}
	
	//--------------------------------------------------------
	// Add tab to woocommerce setting
	//--------------------------------------------------------
	public function gtin_schema_add_tab( $settings_tabs ) {
		$settings_tabs[$this->id] = $this->label;
  		return $settings_tabs;
	}

	//--------------------------------------------------------
	// Get settings
	//--------------------------------------------------------
	public function gtin_schema_tab_settings() {
    	
    	woocommerce_admin_fields( $this->gtin_schema_get_settings() );
	}

	//--------------------------------------------------------
	// fields settings
	//--------------------------------------------------------
	public function gtin_schema_get_settings() {
		$options = apply_filters('gtin_schema_data_structure_options',array(
					'gtin'   => 'gtin',
					'gtin8'  => 'gtin8',
					'gtin12' => 'gtin12',
					'gtin13' => 'gtin13',
					'gtin14' => 'gtin14',
					'isbn'   => 'isbn',
					'mpn'    => 'mpn',
				)
			);
	    $settings = array(
	        'section_title' => array(
	            'name'     => __( 'Product GTIN Settings', 'gtin-schema' ),
	            'type'     => 'title',
	            'desc'     => '',
	            'id'       => 'wc_settings_tab_section_title'
	        ),	        
	        'label_of_gtin_schema_code' => array(
	            'name' => __( 'Public label of GTIN Code', 'gtin-schema' ),
	            'type' => 'text',
	            'desc' => __( 'Label of GTIN Code', 'gtin-schema' ),
	            'id'   => 'wc_gtin_schema_admin_tab_label',
	            'desc_tip' => true
	        ),	        
       		'gtin_schema_product_date_structure' => array(
	          	'name' => __('Structured data product', 'gtin-schema'),
	          	'type' => 'select',
	          	'class' => 'gtin_schema-admin-tab-field',
	          	'desc' => __('Choose product structure data property to set.', 'gtin-schema'),
	          	'id' => 'gtin_schema_product_date_structure',
	          	'options'  => $options,
				'desc_tip' => true
       		),
	        'show_schema_in_loop' => array(
		            'name' => __( 'Show Code GTIN', 'gtin-schema' ),
		            'type' => 'checkbox',
					'default' => 'no',
		            'desc' => __( 'Show GTIN in loop product page', 'gtin-schema' ),
		            'id'   => 'wc_gtin_schema_admin_tab_in_loop',
		            'checkboxgroup' => 'start',
		        ),
	        	array(
					'desc'          => __( 'Show GTIN in single product', 'gtin-schema' ),
					'id'            => 'wc_gtin_schema_admin_tab_in_single_product',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				),
	        	array(
					'desc'          => __( 'Show the GTIN in cart.', 'gtin-schema' ),
					'id'            => 'wc_gtin_schema_admin_tab_in_cart',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				),

				array(
					'desc'          => __( 'Show the GTIN in checkout page.', 'gtin-schema' ),
					'id'            => 'wc_gtin_schema_admin_tab_in_checkout',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				),

				array(
					'desc'          => __( 'Show the GTIN as order item meta.', 'gtin-schema' ),
					'id'            => 'wc_gtin_schema_admin_tab_in_order_item',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
				),

	        'schema_single_position' => array(
	          	'name' => __('Schema position', 'gtin-schema'),
	          	'type' => 'select',
	          	'class' => 'gtin_schema-admin-tab-field',
	          	'desc' => __('For single product page', 'gtin-schema'),
	          	'id' => 'wc_gtin_schema_admin_tab_single_product_position',
	          	'options' => array(
		            'before_title' => __('Before title', 'gtin-schema'),
		            'after_title' => __('After title', 'gtin-schema'),
		            'after_price' => __('After price', 'gtin-schema'),
		            'after_excerpt' => __('After excerpt', 'gtin-schema'),
		            'after_add_to_cart' => __('After add to cart', 'gtin-schema'),
		            'meta' => __('In meta', 'gtin-schema'),
		            'after_meta' => __('After meta', 'gtin-schema'),		            
	          	)
       		),       		
	        'gtin_schema_auto_product_sku' => array(
				'title' => __('Product SKU as GTIN', 'gtin-schema'),
				'type' => 'checkbox',
				'default' => 'no',
				'desc' => __('GTIN empty then SKU as GTIN code', 'gtin-schema'),
				'id' => 'wc_gtin_schema_admin_tab_get_get_sku'
			),
			'schema_single_product_tab' => array(
				'title' => __('Products tab', 'gtin-schema'),
				'type' => 'checkbox',
				'default' => 'no',
				'desc' => __('Show GTIN tab in single product page', 'gtin-schema'),
				'id' => 'wc_gtin_schema_admin_tab_single_product_tab'
			),
	        'section_end' => array(
	             'type' => 'sectionend',
	             'id' => 'wc_settings_tab_section_end'
	        )
	    );
	    return apply_filters( 'woocommerce_settings_tabs_gtin_schema', $settings );
	}

	//--------------------------------------------------------
	// Save fields settings
	//--------------------------------------------------------
	public function gtin_schema_save_settings() {
		woocommerce_update_options( $this->gtin_schema_get_settings() );
	}
	
	//--------------------------------------------------------
	// Add field to variation
	//--------------------------------------------------------
	function bbloomer_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
		woocommerce_wp_text_input( array(
			'id' => '_gtin_schema_code[' . $loop . ']',
			'class' => 'short',
			'label' => __( gtin_schema_get_title_label(), 'gtin-schema' ),
			'value' => get_post_meta( $variation->ID, '_gtin_schema_code', true )
			)
		);
	}
	
	//--------------------------------------------------------
	// Save custom field on product variation save
	//--------------------------------------------------------
	function bbloomer_save_custom_field_variations( $variation_id, $i ) {
		$_gtin = $_POST['_gtin_schema_code'][$i];
		if ( isset( $_gtin ) ) update_post_meta( $variation_id, '_gtin_schema_code', esc_attr( $_gtin ) );
	}

 	//--------------------------------------------------------
	// Store custom field value into variation data
	//--------------------------------------------------------
	function bbloomer_add_custom_field_variation_data( $variations ) {
		$variations['_gtin_schema_code'] = '<div class="woocommerce_gtin_field">'.gtin_schema_get_title_label().' <span>' . get_post_meta( $variations[ 'variation_id' ], '_gtin_schema_code', true ) . '</span></div>';
		return $variations;
	}

	//--------------------------------------------------------
	// Add fields
	//--------------------------------------------------------
	public function add_params_to_available_variation( $args, $product, $variation ) {
		$args['_gtin_schema_code'] = $variation->get_meta( '_gtin_schema_code' );
		return $args;
	}

	//--------------------------------------------------------
	// Assign table label
	//--------------------------------------------------------
	public function gtin_schema_table_head( $defaults ) {
	    $defaults['gtin_schema']  = gtin_schema_get_title_label();   
	    return $defaults;
	}

	//--------------------------------------------------------
	// Assign value to field
	//--------------------------------------------------------
	public function gtin_schema_table_content( $column_name, $post_id ) {
	    if ($column_name == 'gtin_schema') {
	    	echo gtin_schema_get_gtin_code($post_id);    	
	    }    
	}

	//--------------------------------------------------------
	// Table sorting
	//--------------------------------------------------------
	public function gtin_schema_table_sorting( $columns ) {
	  	$columns['gtin_schema'] = 'gtin_schema';
	  	return $columns;
	}

	//--------------------------------------------------------
	// field order by
	//--------------------------------------------------------
	public function gtin_schema_column_orderby( $vars ) {
	    if ( isset( $vars['orderby'] ) && 'gtin_schema' == $vars['orderby'] ) {
	        $vars = array_merge( $vars, array(
	            'meta_key' => '_gtin_schema_code',
	            'orderby' => 'meta_value'
	        ) );
	    }    
	    return $vars;
	}

	function gtin_schema_extend_product_search_query( $query_vars ) {
		
		global $typenow;
		global $wpdb;
		global $pagenow;

		if ( 'product' === $typenow && isset( $_GET['s'] ) && 'edit.php' === $pagenow ) {
			$search_term            = esc_sql( sanitize_text_field( $_GET['s'] ) );
			$meta_key               = '_gtin_schema_code';
			$post_types             = array( 'product', 'product_variation' );
			$search_results         = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DISTINCT posts.ID as product_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id WHERE postmeta.meta_key = '{$meta_key}' AND postmeta.meta_value LIKE %s AND posts.post_type IN ('" . implode( "','", $post_types ) . "') ORDER BY posts.post_parent ASC, posts.post_title ASC",
					'%' . $wpdb->esc_like( $search_term ) . '%'
				)
			);
			$product_ids            = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'product_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );
			$query_vars['post__in'] = array_merge( $product_ids, $query_vars['post__in'] );
		}

		return $query_vars;
	}
}

?>