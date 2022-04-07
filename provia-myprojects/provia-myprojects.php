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
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_myprojects', 'provia_myprojects_load');

add_shortcode('provia_myprojects_boards', 'provia_myprojects_boards_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_myprojects_load()
{
	//prevents duplicate previews in admin
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}

	//load form template
	require_once provia_myprojects_path . 'tmpl/default.php';
	
}

function provia_myprojects_boards_load()
{
	//prevents duplicate previews in admin
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}

	//get vision board based on current logged in user
	$user = wp_get_current_user();
	$userid = 0;

	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	if($userid <= 0)
	{
		return;
	}
	
	$sql = "SELECT * FROM wp_provia_projects  ";
	$sql .= "where userid = ".$userid;
	
	//echo $sql;
	
	$result = $GLOBALS['wpdb']->get_results($sql);
	
	echo '<div class="visionboards-container">';
	
	//add default create item
	echo '<div class="visionboards-item-container">';
	echo '<a href="/design-center/#project-builder" class="elementor-button-link elementor-button elementor-size-sm" role="button">';
	echo '<span class="elementor-button-content-wrapper">';
	echo '<span class="elementor-button-icon elementor-align-icon-left">';
	echo '<i aria-hidden="true" class="fas fa-plus"></i></span>';
	echo '<span class="elementor-button-text">Create a New <br>Vision Board</span>';
	echo '</span>';
	echo '</a>';
	echo '</div>';
	
	foreach ( $result as $project )
	{
		
		$img = str_replace('/home/proviav2/public_html/provia.com', '', $project->project_image);
		
		echo '<div class="visionboards-item-container">';
		echo '<a href="/design-center/?myprojects_projectid='.$project->project_id.'#project-builder" class="elementor-button-link elementor-button elementor-size-sm" role="button">';
		echo '<img src="'.$img.'" class="visionboards-image" />';
		echo '</a>';
		echo '<div class="visionboards-item-text">'.$project->project_name.'</div>';
		echo '</div>';
	}
	
	echo '</div>';
	
}

