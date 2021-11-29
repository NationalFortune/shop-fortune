<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}
function chowordpress_wc_custom_get_price_html( $price, $product ) {
    if ( $product->get_price() == 0 ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
 
            $price = wc_format_price_range( $regular_price, __( 'Liên hệ', 'woocommerce' ) );
        } else {
            $price = '<span class="amount">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
        }
    }
    return $price;
}
/**
 * @snippet       Dispatch Date @ WooCommerce Single Product
 * @testedwith    WooCommerce 3.9
 */
add_action( 'woocommerce_after_add_to_cart_form', 'hoangweb_dispatch_info_single_product' );
     
function hoangweb_dispatch_info_single_product() {
   date_default_timezone_set( 'Europe/London' );  
     
   // if FRI/SAT/SUN delivery will be MON
   if ( date( 'N' ) >= 5 ) {
      $del_day = date( "l jS F", strtotime( "next monday" ) );
      $order_by = "Monday";
   } 
     
   // if MON/THU after 4PM delivery will be TOMORROW
   elseif ( date( 'H' ) >= 16 ) {
      $del_day = date( "l jS F", strtotime( "tomorrow" ) );
      $order_by = "tomorrow";
   } 
     
   // if MON/THU before 4PM delivery will be TODAY
   else {
      $del_day = date( "l jS F", strtotime( "today" ) );
      $order_by = "today";
   }
  
   $html = "<br><div class='woocommerce-message' style='clear:both'>Order by 4PM {$order_by} for delivery on {$del_day}</div>";
     
   echo $html;
}
// add_action( 'woocommerce_before_shop_loop', 'woocommerce_product_archive_description', 10 );
// add_filter( 'woocommerce_get_price_html', 'chowordpress_wc_custom_get_price_html', 10, 2 );
// function devvn_wc_custom_get_price_html( $price, $product ) {
//     if ( $product->get_price() == 0 ) {
//         if ( $product->is_on_sale() && $product->get_regular_price() ) {
//             $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
 
//             $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
//         } else {
//             $price = '<span class="amount">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
//         }
//     }
//     return $price;
// }
// add_filter( 'woocommerce_get_price_html', 'devvn_wc_custom_get_price_html', 10, 2 );
// function devvn_oft_custom_get_price_html( $price, $product ) {
//     if ( !is_admin() && !$product->is_in_stock()) {
//        $price = '<span class="amount">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
//     }
//     return $price;
// }
// add_filter( 'woocommerce_get_price_html', 'devvn_oft_custom_get_price_html', 99, 2 );
// function my_custom_upload_mimes($mimes = array()) {

// 	// Add a key and value for the CSV file type
	
// 	$mimes[‘csv’] = “text/csv”;
	
// 	return $mimes;
	
// 	}
	
// 	add_action(‘upload_mimes’, ‘my_custom_upload_mimes’);
	
	
/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
