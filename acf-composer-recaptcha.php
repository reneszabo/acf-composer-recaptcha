<?php

/*
Plugin Name: Advanced Custom Fields: reCAPTCHA Field
Plugin URI: https://github.com/reneszabo/acf-recaptcha/
Description: Google reCAPTCHA Field for Advanced Custom Fields. See <a href="https://www.google.com/recaptcha/">https://www.google.com/recaptcha/</a> for an account.
Version: 0.1.0
Author: Rene Ramirez
Author URI: http://ramirez-portfolio.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


load_plugin_textdomain( 'acf-composer-recaptcha', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 



function include_field_types_recaptcha( $version ) {
	
	include_once('acf-composer-recaptcha-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_recaptcha');	

	
?>