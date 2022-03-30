<?php
 
/*
 
Plugin Name: Provia - iFrame Scripts
 
Plugin URI: https://provia.com/
 
Description: Plugin created to inject neccessary javascript file to triggers fancybox iframe popup for grout selector.
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_iframe_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action( 'wp_head', 'provia_add_iframe_fancybox_files' );

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_add_iframe_fancybox_files()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	//add fancybox includes
	echo '<link rel="stylesheet" href="/wp-content/plugins/provia-iframe-scripts/scripts/fancybox/fancybox.css" />';
	echo '<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>';
	
	//include custom javascript to delay load of fancybox pop-up
	echo '<script type="text/javascript" src="/wp-content/plugins/provia-iframe-scripts/scripts/provia-iframe-fancybox.js"></script>';
}

