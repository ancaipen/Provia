<?php
 
/*
 
Plugin Name: Provia Dealer Location Sync
 
Plugin URI: https://provia.com/
 
Description: Plugin to automatically sync dealers to store locator plugin
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

add_action('admin_menu', 'provia_locationsync');

function provia_locationsync()
{
    add_menu_page( 'Provia Dealer Location Sync', 'Provia Dealer Location Sync', 'manage_options', 'provia-locationsync', 'provia_locationsync_init' );
}

function provia_locationsync_init(){
    echo '<h1 class="wp-heading-inline">Provia Dealer Location Sync</h1>';
	echo "<p></p>";
}

