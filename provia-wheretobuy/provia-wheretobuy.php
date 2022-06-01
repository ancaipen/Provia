<?php
 
/*
 
Plugin Name: Provia - Where to Buy
 
Plugin URI: https://provia.com/
 
Description: Plugin is used to search for available dealers via Provia's custom web service.
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_wheretobuy_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action('admin_menu', 'provia_wheretobuy_admin');

//located in provia api plugin, sets global var for logged in userid
add_action('init','provia_set_user');

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_wheretobuy', 'provia_wheretobuy_load');

add_shortcode('provia_preferreddealer', 'provia_preferreddealer_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_wheretobuy_admin()
{
    add_menu_page( 'Provia Where to Buy', 'Provia Where to Buy', 'manage_options', 'provia-wheretobuy', 'provia_wheretobuy_admin_load' );
}

function provia_wheretobuy_admin_load(){
    echo '<h1 class="wp-heading-inline">Provia Where to Buy Log</h1>';
	echo "<p></p>";
}

function provia_wheretobuy_load()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	//load form template
	require provia_wheretobuy_path . 'tmpl/default.php';	
}

function provia_preferreddealer_load()
{
			
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}

	//add script to head
	//wp_register_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBBNzHIIdHxWk68i_x0iPmcu3mz-iAu28I', array(), '', true);
		
	//load preferred dealer template
	require provia_wheretobuy_path . 'tmpl/preferred-dealer.php';
	
}

