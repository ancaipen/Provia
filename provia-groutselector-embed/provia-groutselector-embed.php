<?php
 
/*
 
Plugin Name: Provia - Grout Selector
 
Plugin URI: https://provia.com/
 
Description: Plugin created to display grout selector details and html via iframe requests
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH."wp-config.php" );
include_once( ABSPATH."wp-includes/wp-db.php" );
require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_groutselector_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_groutselector', 'provia_groutselector_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_groutselector_load()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	
	
	//load form template
	require_once provia_groutselector_path . 'tmpl/default.php';
	
}

