<?php
 
/*
 
Plugin Name: Provia - APIs
 
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
include_once( ABSPATH."wp-config.php" );
include_once( ABSPATH."wp-includes/wp-db.php" );

$plugin_data = get_plugin_data( __FILE__ );
define( 'provia_saveimage_path', plugin_dir_path( __FILE__ ) );
define( 'provia_default_url', 'https://provia.proviaserver-v2.com/' );

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_user_firstname', 'provia_user_firstname_load');

add_shortcode('provia_user_avatar', 'provia_user_avatar_load');

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------


add_action( 'wp_footer', 'provia_add_custom_html' );
add_action( 'wp_footer', 'provia_add_custom_js_files' );

add_action('init','provia_set_user');

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/saveimage', '/uid/(?P<id>\d+)', array(
    'methods' => 'POST',
    'callback' => 'provia_saveimage',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/saveimage', '/delete/', array(
    'methods' => 'POST',
    'callback' => 'provia_saveimage_delete',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/savepreferreddealer', '/save/', array(
    'methods' => 'POST',
    'callback' => 'provia_savepreferreddealer',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/savezip', '/full/', array(
    'methods' => 'POST',
    'callback' => 'provia_savezipfull',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/savezip', '/simple/', array(
    'methods' => 'POST',
    'callback' => 'provia_savezip',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_saveproject', '/default/', array(
    'methods' => 'POST',
    'callback' => 'provia_saveproject',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_saveproject', '/image/', array(
    'methods' => 'POST',
    'callback' => 'provia_saveproject_image',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_saveproject', '/delete/', array(
    'methods' => 'POST',
    'callback' => 'provia_saveproject_delete',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_tiwishlist', '/saveimage/', array(
    'methods' => 'POST',
    'callback' => 'provia_savetiwishlist_image',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_tiwishlist', '/getimage/', array(
    'methods' => 'POST',
    'callback' => 'provia_gettiwishlist_image',
  ));
});


add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_getproject', '/getimages/', array(
    'methods' => 'GET',
    'callback' => 'provia_getproject_images',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_admin', '/updatelinks/', array(
    'methods' => 'GET',
    'callback' => 'provia_admin_updatelinks',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_admin', '/savewishlistitem/', array(
    'methods' => 'GET',
    'callback' => 'provia_save_wishlist_item',
  ));
});

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/provia_wheretobuy', '/getpreferreddealer/', array(
    'methods' => 'GET',
    'callback' => 'provia_get_preferred_dealer',
  ));
});


//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_get_preferred_dealer($data)
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
	
	if(isset($data['uid']))
	{
		if($data['uid'] != "")
		{
			$userid = filter_var($data['uid'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//retrieve details by ip or user id
	if($userid > 0)
	{
		$sql = "SELECT * FROM wp_provia_preferreddealers where userid=".$userid;
		$dealers = $GLOBALS['wpdb']->get_results($sql);		
	}
	else
	{
		$sql = "SELECT * FROM wp_provia_preferreddealers where ip_address='".$ip_address."' and userid=0";
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
	
	//create html to return
	$html = '<h2 class="preferred-dealer-heading">'.$dealer_name.'</h2>';
	$html .= '<h3 class="preferred-dealer-contact">'.$dealer_phone;

	if(isset($dealer_website) && $dealer_website != "")
	{
		$html .=  ' | '. '<a href="'.$dealer_website.'" target="_blank">Visit Dealer Website</a>';
	}

	$html .= '</h3>';

	$html .= '<div class="perferred-dealer-hidden" style="display:none;">';
	$html .= '<div id="perferred-dealer-zipcode">'.$user_zipcode.'</div>';
	$html .= '<div id="perferred-dealer-name">'.$dealer_name.'</div>';
	$html .= '<div id="perferred-dealer-phone">'.$dealer_phone.'</div>';
	$html .= '<div id="perferred-dealer-website">'.$dealer_website.'</div>';
	$html .= '<div id="perferred-dealer-address">'.$dealer_address.'</div>';
	$html .= '<div id="perferred-dealer-lat">'.$dealer_lat.'</div>';
	$html .= '<div id="perferred-dealer-long">'.$dealer_long.'</div>';
	$html .= '</div>';
	
	return $html;
	
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

function provia_add_custom_html()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	$userid = get_current_user_id();
	
	//include custom provia user id
	echo '<input type="hidden" name="provia_uid" id="provia_uid" value="'.$userid.'">';
}

function provia_add_custom_js_files()
{
	
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}

	//include custom javascript for any customized, site wide javascript
	echo '<script type="text/javascript" src="/wp-content/plugins/provia-api/scripts/provia-api.js"></script>';
}

function provia_set_user()
{
	$user = wp_get_current_user();
	$userid = 0;

	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	$GLOBALS['provia']['userid'] = $userid;
	
}

function provia_save_wishlist_item($data)
{
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	$product_id = -1;
	$variation_id = 0;
	$consumer_key = "ck_6bf1457a8f56fc354d40ec2af3b12aeee2b11cb2";
	$consumer_secret = "cs_29b86f55e3d0f39ed19fb484d7206fdb33169ed5";
	$domain = "https://provia.proviaserver-v2.com";
	
	if(isset($data['uid']))
	{
		if($data['uid'] != "")
		{
			$userid = filter_var($data['uid'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['product_id']))
	{
		if($data['product_id'] != "")
		{
			$product_id = filter_var($data['product_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['variation_id']))
	{
		if($data['variation_id'] != "")
		{
			$variation_id = filter_var($data['variation_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(!isset($userid))
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}
	
	if(!isset($product_id))
	{
		return new WP_Error( 'no_product', 'Invalid product, not found', array( 'status' => 404 ));
	}
	
	//-------------------------------------
	// get default wishlist by user id
	//-------------------------------------
	
	$url = $domain."/wp-json/wc/v3/wishlist/get_by_user/" . $userid . "/?consumer_key=".$consumer_key."&consumer_secret=".$consumer_secret;
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$resp = curl_exec($curl);
	curl_close($curl);
	
	//echo var_dump($resp);
	
	if(!isset($resp) || trim($resp) == "")
	{
		return new WP_Error( 'no_wishlist_response', 'Invalid wishlist response, no data found', array( 'status' => 404 ));
	}
	
	//$arr = json_decode($resp);
	$arr = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $resp), true );

	$wishlistid = -1;
	$share_key = "";
	
	//echo var_dump($arr);

	foreach($arr as $item) { 
	
		$title = strtolower(trim($item['title'])); 
		
		if($title == "all saved images" || $title == "default folder")
		{
			$wishlistid = filter_var($item['id'], FILTER_SANITIZE_NUMBER_INT);
			$share_key = $item['share_key'];
		}
	}
	
	if($wishlistid < 0)
	{
		return new WP_Error( 'no_wishlist_default', 'Invalid default wishlist, no data found', array( 'status' => 404 ));
	}
	
	if($share_key == "")
	{
		return new WP_Error( 'no_wishlist_default', 'Invalid default wishlist, no data found', array( 'status' => 404 ));
	}
	
	//echo $wishlistid;
	
	//-------------------------------------
	// get wishlist products and make sure product id is not already added
	//-------------------------------------

	$url = $domain."/wp-json/wc/v3/wishlist/".$share_key."/get_products?consumer_key=".$consumer_key."&consumer_secret=".$consumer_secret;
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$resp = curl_exec($curl);
	curl_close($curl);
	
	//echo var_dump($resp);
	
	if(!isset($resp) || trim($resp) == "")
	{
		return new WP_Error( 'no_wishlist_product', 'Invalid wishlist product response, no data found', array( 'status' => 404 ));
	}
	
	$arr = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $resp), true );
	$product_found = false;
		
	foreach($arr as $item) { 
		
		$wishlist_product_id = filter_var($item['product_id'], FILTER_SANITIZE_NUMBER_INT);
		$wishlist_variation_id = filter_var($item['variation_id'], FILTER_SANITIZE_NUMBER_INT);
		
		if($wishlist_product_id == $product_id && $wishlist_variation_id == $variation_id)
		{
			$product_found = true;
		}
		
	}
	
	//-------------------------------------
	// add product to wishlist
	//-------------------------------------
	
	if($product_found == false)
	{
		
		//allow product to be added to wishlist
		$product_data_json = '{
			"product_id": '.$product_id.',
			"variation_id": '.$variation_id.',
			"meta": {
			"saveall": "true"
			}
		}';
		
		$url = $domain."/wp-json/wc/v3/wishlist/".$share_key."/add_product?consumer_key=".$consumer_key."&consumer_secret=".$consumer_secret;
		
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $product_data_json);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($curl);

		curl_close ($curl);
		
		//echo var_dump($server_output);
		
		return new WP_REST_Response($product_id, 200);
	}
	else
	{
		return new WP_REST_Response('product already added', 200);
	}
	
}

function provia_getproject_images($data)
{
	
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	$image_html = '';
	$project_html = '';
	$project_id = 0;
	$list_id = -1;
	$toolset = false;
	$adjust_width = 0;
	$adjust_height = 0;
	
	//check for userid in post 
	if(isset($data['uid']))
	{
		if($data['uid'] != "")
		{
			$userid = filter_var($data['uid'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['project_id']))
	{
		if($data['project_id'] != "")
		{
			$project_id = filter_var($data['project_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['adjust_width']))
	{
		if($data['adjust_width'] != "")
		{
			$adjust_width = filter_var($data['adjust_width'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['adjust_height']))
	{
		if($data['adjust_height'] != "")
		{
			$adjust_height = filter_var($data['adjust_height'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//used to strip out styles for project images
	if(isset($data['toolset']))
	{
		if($data['toolset'] = "true")
		{
			$toolset = true;
		}
	}
	
	if(!isset($userid))
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}
	
	//load images from all projects
	$sql = "SELECT DISTINCT -1 as wishlistitem_id, -1 as wishlist_id, u.ID as user_id, p.ID as product_id, ti_i.variation_id as product_variation_id, p.post_title  ";
	$sql .= "FROM wp_tinvwl_items ti_i ";
	$sql .= "inner join wp_users u on u.ID = ti_i.author ";
	$sql .= "inner join wp_posts p on p.ID=ti_i.product_id ";
	$sql .= "where p.post_status = 'publish' and p.post_type='product' and ti_i.author = ".$userid;
	
	//echo $sql;
	
	$result = $GLOBALS['wpdb']->get_results($sql);

	foreach ( $result as $product )
	{
		
		$product_id = $product->product_id;
		$product_variation_id = intval($product->product_variation_id);
		$wishlist_id = $product->wishlist_id;
		$post_title = $product->post_title;
		$lookup_id = $product_id;
		
		//use variation if found
		if($product_variation_id != null && $product_variation_id > 0)
		{
			$lookup_id = $product_variation_id;
		}
		
		$query_thumb = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_thumbnail_id' AND post_id = ".$lookup_id;
		$result_query = $GLOBALS['wpdb']->get_results($query_thumb);
		$thumb_post_id = $result_query[0]->meta_value;
		$search_terms = "";
		
		//get all search tags and category names
		$terms_sql = "select t.name from wp_term_relationships tr ";
		$terms_sql .= "inner join wp_term_taxonomy tt on tt.term_taxonomy_id=tr.term_taxonomy_id ";
		$terms_sql .= "inner join wp_terms t on t.term_id=tt.term_id ";
		$terms_sql .= "where tt.taxonomy <> 'author' AND tr.object_id = ". $product_id;
		
		$terms_result = $GLOBALS['wpdb']->get_results($terms_sql);

		foreach ( $terms_result as $terms )
		{
			$search_terms .= html_entity_decode($terms->name) . " ";
		}
		
		$search_terms = trim($search_terms);
				
		if($thumb_post_id != "")
		{
			
			$attached_file_path = null;
			$image_style = "";
			$datax = "";
			$datay = "";
			
			if(isset($project_id) && $project_id > 0)
			{
				$query_file = "SELECT pm.meta_value, pi.image_style, pi.datax, pi.datay FROM wp_postmeta pm ";
				$query_file .= "INNER JOIN wp_provia_projects_images pi on pi.project_id=%d and REPLACE(pi.image_name, '/wp-content/uploads/', '')=pm.meta_value and (deleted IS NULL OR deleted = 0) AND pi.user_id=%d ";
				$query_file .= "WHERE pm.meta_key ='_wp_attached_file' AND pm.post_id = %d ";
				$query_file .= "ORDER BY image_id DESC";
				$sql_result = $GLOBALS['wpdb']->prepare($query_file,$project_id,$userid,$thumb_post_id);
				$result_query = $GLOBALS['wpdb']->get_results($sql_result);	
				
				$attached_file_path = str_replace('home/proviav2/public_html/provia.com/wp-content/uploads/', '', $result_query[0]->meta_value);
				$attached_file_path = str_replace('home/oxbow/public_html/proviasandbox/wp-content/uploads/', '', $attached_file_path);
				
				$image_style = $result_query[0]->image_style;
				$datax = $result_query[0]->datax;
				$datay = $result_query[0]->datay;
			}
			else
			{
				$query_file = "SELECT pm.meta_value FROM wp_postmeta pm ";
				$query_file .= "WHERE pm.meta_key ='_wp_attached_file' AND pm.post_id = %d";
				$sql_result = $GLOBALS['wpdb']->prepare($query_file,$thumb_post_id);
				$result_query = $GLOBALS['wpdb']->get_results($sql_result);	
				
				$attached_file_path = str_replace('home/proviav2/public_html/provia.com/wp-content/uploads/', '', $result_query[0]->meta_value);
				$attached_file_path = str_replace('home/oxbow/public_html/proviasandbox/wp-content/uploads/', '', $attached_file_path);
				
			}
			
			//echo 'sql: '.$query_file . "||||";
			
			if($attached_file_path != "")
			{
				if(isset($project_id) && $project_id > 0 && $toolset == false)
				{
					$image_html .= '<div class="drag-drop" product_id="'.$product_id.'" product-variation-id="'.$product_variation_id.'" wishlist-id="'.$wishlist_id.'" style="'.$image_style.'" data-x="'.$datax.'" data-y="'.$datay.'">';
					$image_html .= '<a href="javascript:void(0);" class="myprojects-close-image" product_id="'.$product_id.'" style="display:none;"><img src="/wp-content/plugins/provia-myprojects/images/close.png" width="25" /></a>';
					$image_html .= '<img src="/wp-content/uploads/'.html_entity_decode($attached_file_path).'" class="myprojects-image" />';
					$image_html .= '<div class="product-title">'.$post_title.'</div>';
					$image_html .= '<div class="product-searchterms" style="display:none;">'.$search_terms.'</div>';
					$image_html .= '</div>';
				}
				else
				{
					$image_html .= '<div class="drag-drop toolset-image" product_id="'.$product_id.'">';
					$image_html .= '<img src="/wp-content/uploads/'.$attached_file_path.'" class="myprojects-drag-image" />';
					$image_html .= '<div class="product-title">'.$post_title.'</div>';
					$image_html .= '<div class="product-searchterms" style="display:none;">'.$search_terms.'</div>';
					$image_html .= '</div>';
				}
				
			}
		
		}
		
	}
	
	return $image_html;
	
}

function provia_saveproject_delete($data)
{
	
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	$project_id = -1;
		
	//check for userid in post 
	if(isset($data['user_id']))
	{
		if($data['user_id'] != "")
		{
			$userid = filter_var($data['user_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//check for listid
	if(isset($data['project_id']))
	{
		if($data['project_id'] != "")
		{
			$project_id = filter_var($data['project_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}

	if(!isset($userid))
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	if($userid == "" || $userid == 0)
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	if(!isset($project_id))
	{
		return new WP_Error( 'error_project', 'Project ID '.$project_id.' is Invalid', array( 'status' => 500 ));
	}
	
	if($project_id == "" || $project_id == -1)
	{
		return new WP_Error( 'error_project', 'Project ID '.$project_id.' is Invalid', array( 'status' => 500 ));
	}
	
	//soft delete project
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  UPDATE wp_provia_projects SET deleted=1 WHERE project_id=%d;
		  ",
		  $project_id
	   )
	);
	
	//soft delete project images
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  UPDATE wp_provia_projects_images SET deleted=1 WHERE project_id=%d;
		  ",
		  $project_id
	   )
	);
	
	//return result
	return new WP_REST_Response('success', 200);
	
}

function provia_saveproject_image($data)
{
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	$image_src = '';
	$image_style = '';
	$image_x = -1;
	$image_y = -1;
	$project_id = -1;
	$image_product = -1;
	$list_id = -1;
	$project_id = -1;
	
	//echo var_dump($data);
	
	//check for userid in post 
	if(isset($data['user_id']))
	{
		if($data['user_id'] != "")
		{
			$userid = filter_var(base64_decode($data['user_id']), FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//check for listid
	if(isset($data['project_id']))
	{
		if($data['project_id'] != "")
		{
			$project_id = filter_var($data['project_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}

	if(!isset($userid))
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	if($userid == "")
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	$project_name = filter_var($data['project_name'], FILTER_SANITIZE_STRING);
	$image_src = filter_var($data['image_src'], FILTER_SANITIZE_STRING); 
	$image_style = filter_var($data['image_style'], FILTER_SANITIZE_STRING); 
	$image_x = filter_var($data['image_x'], FILTER_SANITIZE_NUMBER_INT); 
	$image_y = filter_var($data['image_y'], FILTER_SANITIZE_NUMBER_INT); 
	$image_product = filter_var($data['image_product'], FILTER_SANITIZE_NUMBER_INT); 
	$curr_date = date("Y-m-d H:i:s");
	
	if(!isset($image_src) || $image_src == "")
	{
		return new WP_Error( 'error_image', 'Image is not found', array( 'status' => 500 ));
	}
	
	//create image by wishlist and user
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  INSERT INTO wp_provia_projects_images (wishlist_id, image_name, image_style, datax, datay, user_id, project_id, date_created)
		  VALUES (%d,%s,%s,%d,%d,%d,%d,%s);
		  ",
		  $list_id,
		  $image_src,
		  $image_style,
		  $image_x,
		  $image_y,
		  $userid,
		  $project_id,
		  $curr_date
	   )
	);
	
	$sql_statment = "select image_id from wp_provia_projects_images WHERE (deleted IS NULL OR deleted = 0) AND wishlist_id=%d AND user_id=%d AND image_name=%s;";
	$sql = $GLOBALS['wpdb']->prepare($sql_statment,$list_id,$userid,$image_src);
	$images = $GLOBALS['wpdb']->get_results($sql);
	$image_id = -1;
	
	if(isset($images) && count($images) > 0)
	{
		$image_id = $images[0]->image_id;
	}
	
	//return result
	return new WP_REST_Response($image_id, 200);
	
}

function provia_savetiwishlist_image($data)
{
	
	provia_set_user();
	
	$list_id = 5;
	$product_id = 0;
	$userid = 1;
	$curr_date = date("Y-m-d H:i:s");
	
	if(isset($GLOBALS['provia']['userid']))
	{
		$userid = $GLOBALS['provia']['userid'];
	}
	
	//echo 'userid:'.$userid;
	
	if(isset($data['user_id']))
	{
		if($data['user_id'] != "")
		{
			$userid = filter_var(base64_decode($data['user_id']), FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//check for listid
	if(isset($data['list_id']))
	{
		if($data['list_id'] != "")
		{
			$list_id = filter_var($data['list_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['product_id']))
	{
		if($data['product_id'] != "")
		{
			$product_id = filter_var($data['product_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	$sql_statment = "select ID from wp_tinvwl_items WHERE wishlist_id=%d AND author=%d AND product_id=%s;";
	$sql = $GLOBALS['wpdb']->prepare($sql_statment,$list_id,$userid,$product_id);
	$images = $GLOBALS['wpdb']->get_results($sql);
	$image_id = -1;
	
	if(isset($images) && count($images) > 0)
	{
		$image_id = $images[0]->ID;
	}
	
	if($image_id <= 0 && $product_id > 0)
	{
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  INSERT INTO wp_tinvwl_items (wishlist_id,product_id,variation_id,formdata,author,date,quantity,price,in_stock)
			  VALUES (%d,%d,%d,%s,%d,%s,%d,%s,%d)
			  ",
			  $list_id,
			  $product_id,
			  0,
			  '',
			  $userid,
			  $curr_date,
			  1,
			  '',
			  1
		   )
		);
	}
	
	//return result
	return new WP_REST_Response($product_id, 200);
	
}

function provia_gettiwishlist_image($data)
{
	
	provia_set_user();
	
	$list_id = 5;
	$product_id = -1;
	$variation_id = 0;
	$userid = -1;
	$curr_date = date("Y-m-d H:i:s");
	$image_id = -1;
	
	if(isset($GLOBALS['provia']['userid']))
	{
		$userid = $GLOBALS['provia']['userid'];
	}
	
	if(isset($data['user_id']))
	{
		if($data['user_id'] != "")
		{
			$userid = filter_var(base64_decode($data['user_id']), FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//check for listid
	if(isset($data['list_id']))
	{
		if($data['list_id'] != "")
		{
			$list_id = filter_var($data['list_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['product_id']))
	{
		if($data['product_id'] != "")
		{
			$product_id = filter_var($data['product_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['variation_id']))
	{
		if($data['variation_id'] != "")
		{
			$variation_id = filter_var($data['variation_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if($product_id <= 0)
	{
		return new WP_REST_Response($image_id, 200);
	}
	
	if($userid <= 0)
	{
		return new WP_REST_Response($image_id, 200);
	}
	
	$sql_statment = "select ID from wp_tinvwl_items WHERE author=%d AND product_id=%d AND variation_id=%d;";
	$sql = $GLOBALS['wpdb']->prepare($sql_statment,$userid,$product_id,$variation_id);
	$images = $GLOBALS['wpdb']->get_results($sql);
	
	if(isset($images) && count($images) > 0)
	{
		$image_id = $images[0]->ID;
	}
		
	//return result
	return new WP_REST_Response($image_id, 200);
	
}

function provia_saveproject($data)
{
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	$project_name = '';
	$project_id = -1;
	$screen_width = 0;
	$screen_height = 0;
	$canvas_width = 0;
	$canvas_height = 0;
	
	//echo var_dump($data);
	
	//check for userid in post 
	if(isset($data['user_id']))
	{
		if($data['user_id'] != "")
		{
			$userid = filter_var(base64_decode($data['user_id']), FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	//check for project id
	if(isset($data['project_id']))
	{
		if($data['project_id'] != "")
		{
			$project_id = filter_var($data['project_id'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['screen_width']))
	{
		if($data['screen_width'] != "")
		{
			$screen_width = filter_var($data['screen_width'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['screen_height']))
	{
		if($data['screen_height'] != "")
		{
			$screen_height = filter_var($data['screen_height'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['canvas_width']))
	{
		if($data['canvas_width'] != "")
		{
			$canvas_width = filter_var($data['canvas_width'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(isset($data['canvas_height']))
	{
		if($data['canvas_height'] != "")
		{
			$canvas_height = filter_var($data['canvas_height'], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	if(!isset($userid))
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	if($userid == "")
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	if($userid == "0")
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	$image = $data['project_image']; 
	
	if(!isset($image) || $image == "")
	{
		return new WP_Error( 'error_image', 'Image is Invalid', array( 'status' => 500 ));
	}
	
	if(isset($data['project_name']))
	{
		$project_name = filter_var($data['project_name'], FILTER_SANITIZE_STRING);
	}
	
    $image_save = explode('base64,',$image); 
	$image_path = getcwd() . '/wp-content/uploads/provia-myprojects/' . $userid . '/';
	$curr_date = date("YmdHis");
	$image_full = $image_path . $curr_date . '.png';
	
	//create directory if not found
	if (!file_exists($image_path)) 
	{
		mkdir($image_path);
	}
	
	//save images to uploads
    file_put_contents($image_full, base64_decode($image_save[1]));
	
	//save image to database and link to project
	$project_id = provia_saveproject_data($data, $image_full, $userid, $project_id, $screen_width, $screen_height, $canvas_width, $canvas_height);
	
	//return result
	if($project_id >= 0)
	{
		$list_id = provia_getlistid($project_name, $userid, $list_id);
		
		//update image path to be URL
		$image_full = str_replace('/home/proviav2/public_html/provia.com/', provia_default_url, $image_full);
				
		$response_array = array($project_id, $image_full);
		$response = json_encode($response_array);
		
		return new WP_REST_Response($response, 200);
	}
	else
	{
		return new WP_Error( 'error_projectid', 'ERROR: project id did not save', array( 'status' => 500 ));
	}
	
}

function provia_saveproject_data($data, $image_full, $userid, $project_id, $screen_width, $screen_height, $canvas_width, $canvas_height)
{
	
	$project_id = -1;
	$project_name = '';
	$ipaddress = trim($_SERVER['REMOTE_ADDR']);
	
	if(isset($data['project_name']))
	{
		$project_name = filter_var($data['project_name'], FILTER_SANITIZE_STRING);
	}
	
	if(!isset($userid))
	{
		$err_msg = 'UserId '.$userid.' is Invalid';
		$project_id = -1;
		return $project_id;
	}
	
	if($userid == "")
	{
		$err_msg = 'UserId '.$userid.' is Invalid';
		$project_id = -1;
		return $project_id;
	}
		
	if($project_name == null || trim($project_name) == "")
	{
		$err_msg = 'Project Name is Invalid';
		$project_id = -1;
		return $project_id;
	}
			
	if($ipaddress == null || trim($ipaddress) == "")
	{
		$err_msg = 'Ip Address is Invalid';
		$project_id = -1;
		return $project_id;
	}
			
	$curr_date = date("Y-m-d H:i:s");
		
	//make sure project name isn't already created
	if($project_id <= 0)
	{
		$project_id = provia_getprojectid($project_name, $userid);
	}
	
	if($project_id > 0)
	{
		//update provia project
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  UPDATE wp_provia_projects 
			  SET project_name=%s,project_image=%s,screen_width=%d,screen_height=%d,canvas_width=%d,canvas_height=%d   
			  WHERE project_id=%d
			  ",
			  $project_name,
			  $image_full,
			  $screen_width,
			  $screen_height,
			  $canvas_width,
			  $canvas_height,
			  $project_id,
		   )
		);
		
		//delete existing images by wishlist and user
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  DELETE FROM wp_provia_projects_images 
			  WHERE project_id=%d AND user_id=%d; 
			  ",
			  $project_id,
			  $userid
		   )
		);
	}
	else
	{
		//insert to provia project list
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  INSERT INTO wp_provia_projects (project_name,project_image,wishlist_project_id,userid,screen_width,screen_height,canvas_width,canvas_height)
			  VALUES (%s,%s,%s,%d,%d,%d,%d,%d)
			  ",
			  $project_name,
			  $image_full,
			  $list_id,
			  $userid,
			  $screen_width,
			  $screen_height,
			  $canvas_width,
			  $canvas_height
		   )
		);
		
		$project_id = provia_getprojectid($project_name, $userid);
		
	}
	
	//return project id
	return $project_id;
	
}

function provia_getlistid($project_name, $userid, $list_id = -1)
{

	$sql_statment = 'SELECT ID from wp_tinvwl_lists ';
	$lists = null;
	
	if($list_id == -1)
	{
		$sql_statment .= 'where title=%s AND author=%d;';
		$sql = $GLOBALS['wpdb']->prepare($sql_statment,$project_name,$userid);
		$lists = $GLOBALS['wpdb']->get_results($sql);
	}
	else
	{
		$sql_statment .= 'where ID=%d;';
		$sql = $GLOBALS['wpdb']->prepare($sql_statment,$list_id);
		$lists = $GLOBALS['wpdb']->get_results($sql);
	}	
	
	if(isset($lists) && count($lists) > 0)
	{
		$list_id = $lists[0]->ID;
	}
	
	return $list_id;
	
}

function provia_getprojectid($project_name, $userid, $project_id = -1)
{

	$sql_statment = 'SELECT project_id from wp_provia_projects WHERE (deleted IS NULL OR deleted = 0) ';
	$projects = null;
	
	if($project_id == -1)
	{
		$sql_statment .= 'AND project_name=%s AND userid=%d;';
		$sql = $GLOBALS['wpdb']->prepare($sql_statment,$project_name,$userid);
		$projects = $GLOBALS['wpdb']->get_results($sql);
	}
	else
	{
		$sql_statment .= 'AND project_id=%d;';
		$sql = $GLOBALS['wpdb']->prepare($sql_statment,$project_id);
		$projects = $GLOBALS['wpdb']->get_results($sql);
	}	
	
	if(isset($projects) && count($projects) > 0)
	{
		$project_id = $projects[0]->project_id;
	}
	
	return $project_id;
	
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function provia_user_firstname_load()
{
	
	//check for duplicate elementor preview loading
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	$user = wp_get_current_user();
	$user_text = 'User';
	
	if($user)
	{
		if(isset($user->ID))
		{
			$first_name = get_user_meta( $user->ID, 'first_name', true );
		
			//var_dump(get_user_meta($user->ID));
			
			if($first_name != null && $first_name != "")
			{
				$user_text = $first_name;
			}
			else
			{
				$user_text = $user->display_name;
			}
		}
		
	}
	
	if($user_text == "")
	{
		$user_text = 'User';
	}
	
	echo '<h2 class="user-text-title" style="color:#fff">'.$user_text.'</h2>';
	
}

function provia_user_avatar_load()
{
	
	//check for duplicate elementor preview loading
	$request_url = $_SERVER['REQUEST_URI'];
	if (str_contains($request_url, 'elementor-preview')) {
		return;
	}
	
	$user = wp_get_current_user();
	$img_src = '<img src="/wp-content/uploads/2021/12/Male-placeholder.jpeg" />';
	
	if($user)
	{
		if(isset($user->ID))
		{
			$avatar_url = get_avatar_url( $user->ID );
			if($avatar_url)
			{
				$img_src = '<img src="'.$avatar_url.'" />';
			}
		}
	}
	
	echo $img_src;
	
}

function provia_savezipfull($data)
{
	
	$userid = $GLOBALS['provia']['userid'];
	$email = '';
	$name = '';
	$zipcode = '';
	$country = '';
	$state = '';
	$city = '';
	$ipaddress = trim($_SERVER['REMOTE_ADDR']);
	$screen_width = 0;
	$screen_height = 0;
	
	if(isset($data['email']))
	{
		$email = filter_var($data['email'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['name']))
	{
		$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['zipcode']))
	{
		$zipcode = filter_var($data['zipcode'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['country']))
	{
		$country = filter_var($data['country'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['state']))
	{
		$state = filter_var($data['state'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['city']))
	{
		$city = filter_var($data['city'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['screen_width']))
	{
		$screen_width = filter_var($data['screen_width'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['screen_height']))
	{
		$screen_height = filter_var($data['screen_height'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	//make sure values are found
	if($email == null || trim($email) == "")
	{
		return new WP_Error( 'error_email', 'Email not found', array( 'status' => 500 ));
	}
	
	//make sure values are found
	if($name == null || trim($name) == "")
	{
		return new WP_Error( 'error_name', 'Name not found', array( 'status' => 500 ));
	}
	
	if($zipcode == null || trim($zipcode) == "")
	{
		return new WP_Error( 'error_zip', 'Zip Code not found', array( 'status' => 500 ));
	}
			
	if($ipaddress == null || trim($ipaddress) == "")
	{
		return new WP_Error( 'error_ip', 'IP Address not found', array( 'status' => 500 ));
	}
	
	$valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
	
	if($valid_email == false)
	{
		return new WP_Error( 'error_invalid', 'Email is Invalid', array( 'status' => 500 ));
	}
	
	$curr_date = date("Y-m-d H:i:s");

	//insert log
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  INSERT INTO wp_provia_zipcode_log (zipcode,userid,ip_address,country_code,email_address,name,screen_width,screen_height,date_created)
		  VALUES (%s,%d,%s,%s,%s,%s,%d,%d,%s)
		  ",
		  $zipcode,
		  $userid,
		  $ipaddress,
		  $country,
		  $email,
		  $name,
		  $screen_width,
		  $screen_height,
		  $curr_date
	   )
	);
	
	//return successful response
	return new WP_REST_Response('success', 200);

}

function provia_savezip($data)
{
	
	$userid = $GLOBALS['provia']['userid'];
	$zipcode = '';
	$country = '';
	$screen_width = 0;
	$screen_height = 0;
	$ipaddress = trim($_SERVER['REMOTE_ADDR']);
	
	if(isset($data['zipcode']))
	{
		$zipcode = filter_var($data['zipcode'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['screen_width']))
	{
		$screen_width = filter_var($data['screen_width'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['screen_height']))
	{
		$screen_height = filter_var($data['screen_height'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['country']))
	{
		$country = filter_var($data['country'], FILTER_SANITIZE_STRING);
	}
		
	if($zipcode == null || trim($zipcode) == "")
	{
		return new WP_Error( 'error_zipcode', 'Zipcode is Invalid', array( 'status' => 500 ));
	}
			
	if($ipaddress == null || trim($ipaddress) == "")
	{
		return new WP_Error( 'error_aip', 'Ip Address is Invalid', array( 'status' => 500 ));
	}
			
	$curr_date = date("Y-m-d H:i:s");
	
	//insert log
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  INSERT INTO wp_provia_zipcode_log (zipcode,userid,ip_address,screen_width,screen_height,date_created)
		  VALUES (%s,%s,%s,%d,%d,%s);
		  ",
		  $zipcode,
		  $userid,
		  $ipaddress,
		  $screen_width,
		  $screen_height,
		  $curr_date
	   )
	);
	
	//return successful response
	return new WP_REST_Response('success', 200);
	
}

function provia_savepreferreddealer($data) 
{
	
	$userid = $GLOBALS['provia']['userid'];
	$dealerid = 0;
	$dealer_name = "";
	$dealer_phone = "";
	$dealer_website = "";
	$dealer_address = "";
	$dealer_lat = "";
	$dealer_long = "";
	$dealer_zipcode = "";
	
	if(!isset($data['dealerid']))
	{
		return new WP_Error( 'no_dealer', 'Dealer not found', array( 'status' => 404 ));
	}
	
	$dealerid = filter_var($data['dealerid'], FILTER_SANITIZE_STRING);
	$ip_address = trim($_SERVER['REMOTE_ADDR']);
	$curr_date = date('Y-m-d H:i:s');
	$preferreddealer_id = 0;
	$screen_width = 0;
	$screen_height = 0;
	
	if(isset($data['screen_width']))
	{
		$screen_width = filter_var($data['screen_width'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['screen_height']))
	{
		$screen_height = filter_var($data['screen_height'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	//get dealer attributes if found
	if(isset($data['dealer_name']))
	{
		$dealer_name = filter_var($data['dealer_name'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_phone']))
	{
		$dealer_phone = filter_var($data['dealer_phone'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_website']))
	{
		$dealer_website = filter_var($data['dealer_website'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_address']))
	{
		$dealer_address = filter_var($data['dealer_address'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_lat']))
	{
		$dealer_lat = filter_var($data['dealer_lat'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_long']))
	{
		$dealer_long = filter_var($data['dealer_long'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['dealer_zipcode']))
	{
		$dealer_zipcode = filter_var($data['dealer_zipcode'], FILTER_SANITIZE_STRING);
	}
	
	//now lookup if preferred dealer already exists
	if($userid > 0)
	{
		$sql = "SELECT preferreddealer_id FROM wp_provia_preferreddealers where userid=".$userid;
		$dealers = $GLOBALS['wpdb']->get_results($sql);
		$preferreddealer_id = $dealers[0]->preferreddealer_id;
	}
	else
	{
		$sql = "SELECT preferreddealer_id FROM wp_provia_preferreddealers where ip_address='".$ip_address."' and userid=0";
		$dealers = $GLOBALS['wpdb']->get_results($sql);
		$preferreddealer_id = $dealers[0]->preferreddealer_id;
	}
	
	if(isset($preferreddealer_id) && $preferreddealer_id > 0)
	{
		//update association
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  UPDATE wp_provia_preferreddealers 
			  SET userid=%d, 
			  dealerid=%d, 
			  ip_address=%s,
			  dealer_name=%s,
			  dealer_phone=%s,
			  dealer_website=%s,
			  dealer_address=%s,
			  dealer_lat=%s,
			  dealer_long=%s,
			  dealer_zipcode=%s,
			  screen_width=%d,
			  screen_height=%d,
			  date_modified=%s 
			  WHERE preferreddealer_id=%d 
			  ",
			  $userid,
			  $dealerid,
			  $ip_address,
			  $dealer_name,
			  $dealer_phone,
			  $dealer_website,
			  $dealer_address,
			  $dealer_lat,
			  $dealer_long,
			  $dealer_zipcode,
			  $screen_width,
			  $screen_height,
			  $curr_date,
			  $preferreddealer_id
		   )
		);
	}
	else
	{
		//insert association
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  INSERT INTO wp_provia_preferreddealers (dealerid, userid, ip_address, dealer_name, dealer_phone, dealer_website, dealer_address, dealer_lat, dealer_long, dealer_zipcode, screen_width, screen_height, date_created)
			  VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %s )
			  ",
			  $dealerid,
			  $userid,
			  $ip_address,
			  $dealer_name,
			  $dealer_phone,
			  $dealer_website,
			  $dealer_address,
			  $dealer_lat,
			  $dealer_long,
			  $dealer_zipcode,
			  $screen_width,
			  $screen_height,
			  $curr_date
		   )
		);
	}
	
	//return successful response
	return new WP_REST_Response('success', 200);
	
}

function provia_saveimage_delete($data) 
{
	
	$userid = 0;
	$image_id = -1;
	
	if(isset($data['userid']))
	{
		$userid = filter_var($data['userid'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['image_id']))
	{
		$image_id = filter_var($data['image_id'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if($userid <= 0)
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}
	
	if($image_id <= 0)
	{
		return new WP_Error( 'no_image', 'Invalid image, not found', array( 'status' => 404 ));
	}
	
	//soft delete image config
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
		  UPDATE wp_provia_images SET deleted=1 WHERE image_id=%d;
		  ",
		  $image_id
	   )
	);
	
	//return successful response
	return new WP_REST_Response('success', 200);


}

function provia_saveimage($data) {


	$image_name = '';
	$tags = '';
	$source = '';
	$product = '';
	$series = '';
	$style = '';
	$color = '';
	$session = 0;
	$password = '';
	$config_data = '';
	
	//echo var_dump($data['tags']);

	if(!isset($data['id']))
	{
		return new WP_Error( 'no_user', 'Invalid user, not found', array( 'status' => 404 ));
	}
	
	if(!isset($data['source']))
	{
		return new WP_Error( 'no_source', 'Source not found', array( 'status' => 404 ));
	}

	if(!isset($data['image']))
	{
		return new WP_Error( 'no_image', 'Invalid image, not found', array( 'status' => 404 ) );
	}
	
	if(!isset($data['session']))
	{
		return new WP_Error( 'no_session', 'Invalid session, not found', array( 'status' => 404 ) );
	}
	
	if(!isset($data['password']))
	{
		return new WP_Error( 'no_password', 'Invalid password, not found', array( 'status' => 404 ) );
	}
	
	$userid = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
	$image_name = filter_var($data['image'], FILTER_SANITIZE_STRING);
	
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
	
	if(isset($data['tags']))
	{
		$tags = filter_var($data['tags'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['product']))
	{
		$product = filter_var($data['product'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['series']))
	{
		$series = filter_var($data['series'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['style']))
	{
		$style = filter_var($data['style'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['color']))
	{
		$color = filter_var($data['color'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['source']))
	{
		$source = filter_var($data['source'], FILTER_SANITIZE_STRING);
	}

	if(isset($data['session']))
	{
		$session = filter_var($data['session'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if(isset($data['password']))
	{
		$password = filter_var($data['password'], FILTER_SANITIZE_STRING);
	}
	
	if(isset($data['config_data']))
	{
		$config_data = filter_var($data['config_data'], FILTER_SANITIZE_STRING);
	}
	
	//validate & save image
	$image_name = save_image_url($userid, $image_name);
	$curr_date = date('Y-m-d H:i:s');
	$ip_address = $_SERVER['REMOTE_ADDR'];
	
	//save asssociated user and image
	if(isset($image_name))
	{
		//insert into db
		$GLOBALS['wpdb']->query(
		   $GLOBALS['wpdb']->prepare(
			  "
			  INSERT INTO wp_provia_images (userid, image_name, image_tags, source, product, series, style, color, session, password, config_data, ip_address, created_date)
			  VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s )
			  ",
			  $userid,
			  $image_name,
			  $tags,
			  $source,
			  $product, 
			  $series, 
			  $style, 
			  $color, 
			  $session, 
			  $password,
			  $config_data,
			  $ip_address,
			  $curr_date
		   )
		);
	}
	else
	{
		return new WP_Error( 'image_error', 'Image save error: allowed image types jpg, gif or png', array( 'status' => 500 ));
	}
	
	//return successful response
	return new WP_REST_Response('success', 200);

}

function save_image_url($userid, $image_url)
{
	
	$path = provia_saveimage_path.'images/'.$userid.'/';
	$dir_exists = file_exists($path);
	
	if($dir_exists == FALSE)
	{
		mkdir($path);
	}
	
	$image = basename($image_url);
	
	if(isset($image) && $image != "")
	{
		$saveto = $path.$image;	
		$typeok = check_image_type($image_url);
		
		if($typeok)
		{
			$ch = curl_init($image_url);
			$fp = fopen($saveto, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			return $image;
		}
		else
		{
			return null;
		}
	}
	
	return null;
	
}

function check_image_type($image_path)
{
	$typeString = null;
	$typeInt = exif_imagetype($image_path);
	$allow_type = TRUE;
	
	switch($typeInt) {
	  case IMAGETYPE_GIF:
		$typeString = 'image/gif';
		break;
	  case IMAGETYPE_JPEG:
		$typeString = 'image/jpeg';
		break;
	  case IMAGETYPE_PNG:
		$typeString = 'image/png';
		break;
	  default: 
		$typeString = 'unknown';
	}
	
	//echo IMG_PNG.':'.$typeInt.':'.$typeString;
	
	if($typeString == 'unknown')
	{
		$allow_type = FALSE;
	}
	
	return $allow_type;
	
}

function save_image_post($userid, $name)
{
	
	if(!isset($userid))
	{
		return null;		
	}
	
	$ext = pathinfo($name, PATHINFO_EXTENSION);
	
	if (isset($_FILES['image']['name']))
	{
		$path = provia_saveimage_path.'images/'.$userid.'/';
		
		mkdir($path);
		
		$saveto = $path.$name;
		
		$typeok = TRUE;
		switch($_FILES['image']['type'])
		{
			case "image/gif": $src = imagecreatefromgif($saveto); break;
			case "image/jpeg": // Both regular and progressive jpegs
			case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
			case "image/png": $src = imagecreatefrompng($saveto); break;
			default: $typeok = FALSE; break;
		}
		if ($typeok)
		{
			
			move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
			
			list($w, $h) = getimagesize($saveto);
			$max = 250;
			$tw = $w;
			$th = $h;
			if ($w > $h && $max < $w)
			{
				$th = $max / $w * $h;
				$tw = $max;
			}
			elseif ($h > $w && $max < $h)
			{
				$tw = $max / $h * $w;
				$th = $max;
			}
			elseif ($max < $w)
			{
				$tw = $th = $max;
			}

			$tmp = imagecreatetruecolor($tw, $th);      
			imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
			imageconvolution($tmp, array( // Sharpen image
				array(-1, -1, -1),
				array(-1, 16, -1),
				array(-1, -1, -1)      
			), 8, 0);
			imagejpeg($tmp, $saveto);
			imagedestroy($tmp);
			imagedestroy($src);
			return $name;
		}
	}
	return null;
	
}

function provia_admin_updatelinks()
{
	provia_set_user();
		
	$userid = $GLOBALS['provia']['userid'];
	
	if($userid == "")
	{
		return new WP_Error( 'error_user', 'UserId '.$userid.' is Invalid', array( 'status' => 500 ));
	}
	
	//create array of find replace text
	$findText = '/roofing/';
	$replaceText = '/metal-roofing/';
	
	if($findText == "")
	{
		return new WP_Error( 'error_findtext', 'findText is Invalid', array( 'status' => 500 ));
	}
	
	if($replaceText == "")
	{
		return new WP_Error( 'error_replacetext', 'replaceText is Invalid', array( 'status' => 500 ));
	}
	
	//update post content
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
			update wp_posts 
			set post_content=REPLACE(post_content, '".$findText."', '".$replaceText."')
			where post_content like ('%".$findText."%');
		  "
	   )
	);
	
	//update options
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
			update wp_options 
			set option_value=REPLACE(option_value, '".$findText."', '".$replaceText."') 
			where option_value like ('%".$findText."%');
		  "
	   )
	);
	
	//update post meta
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
			update wp_postmeta 
			set meta_value=REPLACE(meta_value, '".$findText."', '".$replaceText."') 
			where meta_value like ('%".$findText."%');
		  "
	   )
	);
	
	//update terms meta
	$GLOBALS['wpdb']->query(
	   $GLOBALS['wpdb']->prepare(
		  "
			update wp_termmeta 
			set meta_value=REPLACE(meta_value, '".$findText."', '".$replaceText."') 
			where meta_value like ('%".$findText."%');
		  "
	   )
	);
	
	
	
	//return result
	return new WP_REST_Response($findText.':'.$replaceText.' SUCCESS', 200);
	
}
