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

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/saveimage', '/uid/(?P<id>\d+)', array(
    'methods' => 'POST',
    'callback' => 'provia_saveimage',
  ));
});

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_saveimage($data) {

	$userid = 0;
	$image_name = '';
	$tags = '';
	$source = '';
	$product = '';
	$series = '';
	$style = '';
	$color = '';
	$session = 0;
	$password = '';
	
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
			  INSERT INTO wp_provia_images (userid, image_name, image_tags, source, product, series, style, color, session, password, ip_address, created_date)
			  VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )
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


