<?php
 
/*
 
Plugin Name: Provia - EntryLink Configurator
 
Plugin URI: https://provia.com/
 
Description: Plugin created to handle entry link door configurator integration.
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_entrylink_configurator_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action( 'wp_head', 'provia_add_iframe_configurator_script' );

add_action( 'wp_body_open', 'provia_add_iframe_configurator_input' );

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_entrylink_gallery', 'provia_entrylink_gallery_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_add_iframe_configurator_script()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	//include custom javascript to delay load of fancybox pop-up
	echo '<script type="text/javascript" src="/wp-content/plugins/provia-entrylink-configurator/scripts/provia-entrylink-configurator.js"></script>';
	
}

function provia_add_iframe_configurator_input()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	$user = wp_get_current_user();
	$userid = 0;

	if(isset($user))
	{
		$userid = $user->ID;
	}

	//include custom javascript to delay load of fancybox pop-up
	echo '<input type="hidden" name="provia-wp-id" id="provia-wp-id" value="'.$userid.'" />';
	
}

function provia_entrylink_gallery_load()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	$user = wp_get_current_user();
	$userid = 0;

	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	if($userid == 0 || $userid == "")
	{
		echo '<div id="provia-entrylink-configurator-overlay">To begin using the Door & Window Configurator <a href="/login">Sign-in or Register for an account here</a></div>';
		return;
	}

	//$userid = 17;
	
	//get saved images
	$sql = "SELECT * FROM wp_provia_images ";
	$sql .= "WHERE userid = ".$userid;
	$sql .= " ORDER BY created_date DESC ";
			
	//echo $sql;
	
	$result = $GLOBALS['wpdb']->get_results($sql);
	
	echo '<div class="entrylink-configurator-gallery">';
	
	foreach ( $result as $image )
	{
		
		$image_id = $image->image_id;
		$image_name = $image->image_name;
		$tags = $image->image_tags;
		$source = $image->source;
		$product = $image->product;
		$series = $image->series;
		$style = $image->style;
		$color = $image->color;
		$session = $image->session;
		$password = $image->password;
		
		$entrylink_href = 'https://entrylink.provia.com/entryLINK/design.aspx?s='.$session.'&p='.$password . '&userid='.$userid;
		$image_name = '/wp-content/plugins/provia-api/images/'.$userid.'/'.$image_name;
		
		//write out gallery images
		echo '<div class="entrylink-configurator-gallery-image-item">';
		echo '<a href="'.$entrylink_href.'" target="new">';
		echo '<img src="'.$image_name.'" class="entrylink-configurator-gallery-image" />';
		echo '</a>';
		echo '</div>';
		
	}
	
	echo '</div>';
	
	
}

