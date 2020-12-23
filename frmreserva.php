<?php
/*
Plugin Name: Frmreserva
Description: Formulario Webcheckout para generación de reserva
Author: Snayder Acero
Author URI: helacer3.yahoo.es
Version: 1.0
License: GPLv2
*/
defined( 'ABSPATH' ) or die( '¡sin trampas!' );

// create Constants
const PAY_APILOGIN   = "pRRXKOl8ikMmt9u";
const PAY_APIKEY     = "4Vj8eK4rloUd272L48hsrarnUA";
const PAY_MERCHANT   = "508029";
const PAY_ACCOUNT    = "512321";
const VALUE_PAYMENT  = 100000;
const VALUE_CURRENCY = "COP";
const URL_GATEWAY    = "https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/"; 
const URL_RESPONSE   = ""; 
const URL_CONFIRM    = ""; 

/*
* generate Form Order
*/
function generateFormOrder() {
	global $wp;
	// url Response
	$urlResponse  = home_url(add_query_arg(array('type' => 'response'), $wp->request));
	// url Confirm
	$urlConfirm   = home_url(add_query_arg(array('type' => 'confirm'), $wp->request));
	//echo $current_url;die;
	// generate Reference
	$strReference = "rsv_".strtotime(date('Ymd His'));
	// “ApiKey~merchantId~referenceCode~amount~currency”
	$strSignature = MD5(PAY_APIKEY."~".PAY_MERCHANT."~".$strReference."~".VALUE_PAYMENT."~".VALUE_CURRENCY);
	include_once('pages/formularioReserva.php');
}

// add Short Form Order
add_shortcode( 'formOrderShortCode', function ($atts, $content, $tag) {
	// JS
	wp_enqueue_script( 'scriptCustomer', plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery' ) );
	// CSS
	wp_enqueue_style( 'cssCustomer', plugin_dir_url( __FILE__ ) . 'css/style.css');
	// show form
	ob_start();
	generateFormOrder();
	return ob_get_clean();
});

?>