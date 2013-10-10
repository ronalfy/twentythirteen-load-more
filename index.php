<?php
/*
Plugin Name: Twenty Thirteen Load More
Plugin URI: http://wordpress.org/extend/plugins/twentythirteen-load-more/
Description: Add a "Load More" button to the bottom of your Twenty Thirteen blog homepage on WordPress.
Author: ronalfy
Version: 1.0.0
Requires at least: 3.5
Author URI: http://www.ronalfy.com
Contributors: ronalfy
*/ 

add_action( 'plugins_loaded', 'ronalfy_load_more_textdomain' );
function ronalfy_load_more_textdomain() {
	load_plugin_textdomain( 'twentythirteen-load-more', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/* Load More */
add_action( 'wp_enqueue_scripts', 'ronalfy_load_more_scripts' );
function ronalfy_load_more_scripts() {
	global $wp_query;
	if ( !is_home() ) return;
	
	//Detect TwentyThirteen
	$theme = wp_get_theme();
	$template = $theme->get_template();
	if ( 'twentythirteen' != $template ) return;
	
	$max = $wp_query->max_num_pages;
		$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
	wp_enqueue_script( 'ronalfy-load-more', plugins_url( '/js/loadmore.js', __FILE__ ), array( 'jquery' ), '20131010', true );
	wp_localize_script( 'ronalfy-load-more', 'ronalfy_load_more', array(
		'current_page' => esc_js( $paged ),
		'max_pages' => esc_js( $max ),
		'ajaxurl' => esc_js( admin_url( 'admin-ajax.php' ) ),
		'main_text' => esc_js( __( 'Load more', 'twentythirteen-load-more' ) ),
		'loading_img' => esc_js( plugins_url( '/images/ajax-loader.gif', __FILE__ ) )
	) );
}
/**
 * Ajax for loading more posts
 * 
 * @author Ronald Huereca <ronald@metronet.no> 
 */
 add_action( 'wp_ajax_load_posts', 'ronalfy_ajax_load_posts' );
 add_action( 'wp_ajax_nopriv_load_posts', 'ronalfy_ajax_load_posts' );
 function ronalfy_ajax_load_posts() {
 	$next_page = absint( $_POST[ 'next_page' ] );
 	
 	global $wp_query;
 	$wp_query = new WP_Query( array(
 		'paged' => $next_page,
 		'post_status' => 'publish'
 	) );
 	ob_start();
 	global $post;
 	while( $wp_query->have_posts() ) {
 		$wp_query->the_post();
 		get_template_part( 'content', get_post_format() );
 	};
 	$html = ob_get_clean();
 	
 	$return_array = array(
 		'next_page' => $next_page + 1,
 		'max_pages' => $wp_query->max_num_pages,
 		'html' => $html
 	);
 	//return
 	die( json_encode( $return_array ) );
 } //end ronalfy_ajax_load_posts

?>