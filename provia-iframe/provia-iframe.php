<?php
 
/*
 
Plugin Name: Provia - iFrame
 
Plugin URI: https://provia.com/
 
Description: Plugin created to inject neccessary javascript/css files for iframe pages.
 
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

add_action( 'wp_head', 'provia_add_iframe_files' );

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_iframe', 'provia_iframe_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_add_iframe_files()
{
	//check for iframe querystring value, if found add neccessary script and css file
	if (isset($_GET['iframe'])) 
	{
		if($_GET['iframe'] == "true")
		{
			echo '<script type="text/javascript" src="/wp-content/plugins/provia-iframe/scripts/iframe/iframeResizer.contentWindow.min.js"></script>';
			echo '<link rel="stylesheet" href="/wp-content/plugins/provia-iframe/scripts/provia-iframe.css" type="text/css" media="all" />';
			echo '<script type="text/javascript" src="/wp-content/plugins/provia-iframe/scripts/provia-iframe.js"></script>';
		}
	}
}

function provia_iframe_load()
{
	
	//load form template
	require_once provia_iframe_path . 'tmpl/default.php';
	
}


