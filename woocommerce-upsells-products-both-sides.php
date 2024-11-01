<?php
/*
Plugin Name: WOOCOMMERCE RELATED PRODUCTS BOTH SIDES
Plugin URI: http://www.devila.net
Description: Devila Plugin, Related Products Both Sides
Version: 1.1
Author: Devila
Author URI: http://www.devila.net
*/

function devila_wupbs_idsProducts($posts) {
    $ids = array();
    foreach ($posts as $post) {
        $ids[] = $post->ID;
    }
    return $ids;
}

function devila_wupbs_returnProductsRelated($posts,$productId) {    
    $ids = array();
    $pf = new WC_Product_Factory();
    foreach ($posts as $postId) {
        $post = $pf->get_product($postId);
        remove_filter( 'woocommerce_product_upsell_ids', 'devila_wupbs_filter_woocommerce_product_upsell_ids', 10, 2 ); 
        $upsells = $post->get_upsells();
        if($upsells!=null){
            for( $i = 0; $i<count( $upsells ); $i++){
                if($productId === $upsells[$i]){
                    $ids[] = $post->id;
                }
            }
        }
    }
    return $ids;
}

function devila_wupbs_filter_woocommerce_product_upsell_ids( $array_maybe_unserialize_this_upsell_ids, $instance ) { 

    $args = array(
        'post_type' => 'product',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => 1,
        'orderby' => $orderby,
        'posts_per_page' => -1, 
        'post__not_in'        => array( $instance->id ),
        'post_status' => 'publish'
    );

    $wp_query = new WP_Query($args);

    wp_reset_query(); 
    
    /* Products IDs (array) */
    $idAllProducts = devila_wupbs_idsProducts($wp_query->posts);
    
    /* upsell Products IDs relateds */ 
    $all_related_products = devila_wupbs_returnProductsRelated($idAllProducts, $instance->id);
    
    /* Products IDs merge */
    $array_maybe_unserialize_this_upsell_ids = array_merge($all_related_products, $array_maybe_unserialize_this_upsell_ids);

    return $array_maybe_unserialize_this_upsell_ids; 
    
}; 
         
add_filter( 'woocommerce_product_upsell_ids', 'devila_wupbs_filter_woocommerce_product_upsell_ids', 10, 2 ); 

?>

