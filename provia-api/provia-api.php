<?php
 
/*
 
Plugin Name: Provia APIs
 
Plugin URI: https://provia.com/
 
Description: Plugin for needed api calls.  This plugin needs to be enabled to allow operations for external and internal processes.
 
Version: 1.0
 
Author: Aaron Caipen
 
Author URI: https://pilotfishseo.com/
 
License: GPLv2 or later
 
Text Domain: pilotfishseo
 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1', '/saveimage/uid/(?P<id>\d+)', array(
    'methods' => 'POST',
    'callback' => 'provia_saveimage',
  ));
});

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_saveimage($data) {

	$userid = 0;
	
	echo var_dump($data);

	if(!isset($data['id']))
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}

	if(!isset($data["FILES"]))
	{
		return new WP_Error( 'no_image', 'Invalid image, not found', array( 'status' => 404 ) );
	}
	
	$userid = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
	
	if($userid <= 0)
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}
	
	//retrieve user id to make sure it is valid
	$user = get_user_by('id', $userid);
	
	if(!isset($user))
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));		
	}
	
	//validate image
	
	
	//save POSTED image 
	
	//save asssociated user and image
	
	
	//return successful response
	return new WP_REST_Response(null, 200);

}

