<?php
/**
 * This is just a very lightweight child theme of the very famous Blocksy WordPress theme. Since I am not really a UI
 * expert I created this child theme because of some Google research. Maybe it is a good approach, maybe not. Not sure
 * if I am missing here something in terms of licensing or anything else. Please bear with me. :-)
 *
 * @author GFW Pachler
 * @since 1.0.0
*/
if (! defined('WP_DEBUG')) {
	die( 'Direct access forbidden.' );
}
function enqueue_gfw_blocksy_theme_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri().'/style.css');
    wp_enqueue_style('child-theme-css', get_stylesheet_directory_uri().'/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'enqueue_gfw_blocksy_theme_styles');
function gfw_add_navigation_bar_background_on_single_prodcut_page() {
    if(function_exists('is_product') && is_product()) {
        echo '
            <section class="gfw-single-project-page-header">
			    <div class="gfw-single-project-page-header-background-overflow"></div>
            </section>    
        ';
    }
    $prefix = blocksy_manager()->screen->get_prefix();
		if ($prefix === 'woo_categories') {
            echo '
            <section class="gfw-single-project-page-header">
			    <div class="gfw-single-project-page-header-background-overflow"></div>
            </section>    
        ';
        }
}
add_action('woocommerce_before_main_content', 'gfw_add_navigation_bar_background_on_single_prodcut_page', 10, 2);
function woocommerce_check_cart_quantities() {
    $multiples = 6;
    $total_products = 0;
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
        $product = get_product( $values['product_id']);
        
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        $product_categories = array();
        foreach ($terms  as $term  ) {       
            array_push($product_categories, $term->name);         
        }
  
        if (in_array ( '12er Pakete', $product_categories )) {
            $total_products += 12;
        } elseif (in_array ( '6er Pakete', $product_categories )) {
            $total_products += 6;    
        } else {
            $total_products += $values['quantity'];
        }
    }
    if ($total_products < 12) {
        wc_add_notice( sprintf( __('Die Mindestbestellmenge liegt bei 12 Flaschen. Sie haben aktuell %s Flaschen im Einkaufswagen. Bitte beachten Sie auch, dass der Versand entweder in 6er oder 12er Kartons erfolgt. Entsprechend dessen muss die Anzahl der bestellten Flaschen durch 6 teilbar sein. Sie können aber natürlich gerne verschiedene Weine bestellen.', 'woocommerce'), $total_products ), 'error' );
    } elseif (($total_products % $multiples) > 0) {
        wc_add_notice( sprintf( __('Der Versand erfolgt entweder in 6er oder 12er Kartons. Entsprechend dessen muss die Anzahl der bestellten Flaschen durch 6 teilbar sein. Sie können aber natürlich gerne verschiedene Weine bestellen. Sie haben aktuell %s Flaschen im Einkaufswagen.', 'woocommerce'), $total_products ), 'error' );
    }
}
add_action('woocommerce_check_cart_items', 'woocommerce_check_cart_quantities');

function disable_checkout_button_wrong_quantity() {
    $multiples = 6;
    $total_products = 0;
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
        $product = get_product( $values['product_id']);
        
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        $product_categories = array();
        foreach ($terms  as $term  ) {       
            array_push($product_categories, $term->name);         
        }
  
        if (in_array ( '12er Pakete', $product_categories )) {
            $total_products += 12;
        } elseif (in_array ('6er Pakete', $product_categories)) {
            $total_products += 6;
        } else {
            $total_products += $values['quantity'];
        }
    }
    if ($total_products < 12) {
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
    } elseif (($total_products % $multiples) > 0) {
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
    }
}
add_action( 'woocommerce_proceed_to_checkout', 'disable_checkout_button_wrong_quantity', 1 );