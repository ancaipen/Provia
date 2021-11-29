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

add_action('init','provia_set_user');

add_action( 'rest_api_init', function () {
  register_rest_route( 'provia/v1/saveimage', '/uid/(?P<id>\d+)', array(
    'methods' => 'POST',
    'callback' => 'provia_saveimage',
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

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

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
		  INSERT INTO wp_provia_zipcode_log (zipcode,userid,ip_address,country_code,email_address,name,date_created)
		  VALUES (%s,%d,%s,%s,%s,%s,%s)
		  ",
		  $zipcode,
		  $userid,
		  $ipaddress,
		  $country,
		  $email,
		  $name,
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
	$ipaddress = trim($_SERVER['REMOTE_ADDR']);
	
	if(isset($data['zipcode']))
	{
		$zipcode = filter_var($data['zipcode'], FILTER_SANITIZE_STRING);
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
		  INSERT INTO wp_provia_zipcode_log (zipcode,userid,ip_address,date_created)
		  VALUES (%s,%s,%s,%s)
		  ",
		  $zipcode,
		  $userid,
		  $ipaddress,
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
		$sql = "SELECT preferreddealer_id FROM wp_provia_preferreddealers where ip_address='".$ip_address."'";
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
			  INSERT INTO wp_provia_preferreddealers (dealerid, userid, ip_address, dealer_name, dealer_phone, dealer_website, dealer_address, dealer_lat, dealer_long, dealer_zipcode, date_created)
			  VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s )
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
			  $curr_date
		   )
		);
	}
	
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
