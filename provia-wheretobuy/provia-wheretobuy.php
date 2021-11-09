<?php
 
/*
 
Plugin Name: Provia Where to Buy
 
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

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_wheretobuy', 'provia_wheretobuy_load');

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
	echo '<h1>WHERE TO BUY</h1>';
	
	//load form template
	require_once provia_wheretobuy_path . 'tmpl/default.php';
	
}

