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
	//load form template
	require_once provia_wheretobuy_path . 'tmpl/default.php';	
}

function provia_preferreddealer_load()
{

	//get preferred dealer
	$userid = $GLOBALS['provia']['userid'];
	$user_zipcode = "44681";
	$ip_address = trim($_SERVER['REMOTE_ADDR']);
	$dealers = null;
	
	//retrieve details by userid or ip address (provia set by default)
	$dealer_name = "ProVia";
	$dealer_phone = "(877) 389-0835";
	$dealer_website = "https://www.provia.com";
	$dealer_address = "";
	$dealer_lat = "40.516380";
	$dealer_long = "-81.700790";
	
	//retrieve details by ip or user id
	if($userid > 0)
	{
		$sql = "SELECT * FROM wp_provia_preferreddealers where userid=".$userid;
		$dealers = $GLOBALS['wpdb']->get_results($sql);		
	}
	else
	{
		$sql = "SELECT * FROM wp_provia_preferreddealers where ip_address='".$ip_address."'";
		$dealers = $GLOBALS['wpdb']->get_results($sql);
	}
	
	if(isset($dealers) && count($dealers) > 0)
	{
		$dealer_name = $dealers[0]->dealer_name;
		$dealer_phone = $dealers[0]->dealer_phone;
		$dealer_website = $dealers[0]->dealer_website;
		$dealer_address = $dealers[0]->dealer_address;
		$dealer_lat = $dealers[0]->dealer_lat;
		$dealer_long = $dealers[0]->dealer_long;
		$user_zipcode = $dealers[0]->dealer_zipcode;
		
		//use user ip address to find zip code
		if($user_zipcode == "")
		{
			$user_zipcode = provia_getzipcode();
		}
	}
	
	//load preferred dealer template
	require_once provia_wheretobuy_path . 'tmpl/preferred-dealer.php';
	
}

function provia_getzipcode()
{
	
	$ip_address = trim($_SERVER['REMOTE_ADDR']);
	$ips = [$ip_address];

	// ip-api endpoint URL
	// see http://ip-api.com/docs/api:batch for documentation
	$endpoint = 'http://ip-api.com/batch';

	$options = [
		'http' => [
			'method' => 'POST',
			'user_agent' => 'Batch-Example/1.0',
			'header' => 'Content-Type: application/json',
			'content' => json_encode($ips)
		]
	];
	
	$response = file_get_contents($endpoint, false, stream_context_create($options));

	// Decode the response and print it
	$array = json_decode($response, true);
	$results = $array[0];
	$zipcode = "";
	
	if(isset($results))
	{
		//print_r($results);
		$zipcode = $results['zip'];
	}
	
	return $zipcode;
}

