<?php
 
/*
 
Plugin Name: Provia - My Projects
 
Plugin URI: https://provia.com/
 
Description: Plugin is used to display 'My Projects' shortcode for use in design center.  My projects allow users to drag and drop images to a canvas to save a create pin boards.
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_myprojects_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action('admin_menu', 'provia_myprojects_admin');

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_myprojects', 'provia_myprojects_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------


function provia_myprojects_admin()
{
    add_menu_page( 'Provia Where to Buy', 'Provia Where to Buy', 'manage_options', 'provia-wheretobuy', 'provia_wheretobuy_admin_load' );
}

function provia_myprojects_load()
{
	//load form template
	require_once provia_myprojects_path . 'tmpl/default.php';
	
}

