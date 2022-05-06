<?php
 
/*
 
Plugin Name: Provia - Entry Link Login
 
Plugin URI: https://provia.com/
 
Description: Login Form used to connect EntryLink users to provia.com users.
 
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
define( 'provia_entrylinklogin_path', plugin_dir_path( __FILE__ ) );

//--------------------------------------------------
// ACTIONS
//--------------------------------------------------

//located in provia api plugin, sets global var for logged in userid
add_action('init','provia_set_user');

//--------------------------------------------------
// SHORTCODE
//--------------------------------------------------

add_shortcode('provia_entrylinklogin', 'provia_entrylinklogin_load');

//--------------------------------------------------
// FUNCTIONS
//--------------------------------------------------

function provia_entrylinklogin_load()
{
	
	$user = wp_get_current_user();
	$error_messages = "";
	
	echo '<div class="entrylink-login-header">';
	echo '<img src="/wp-content/uploads/2021/05/ProVia-logo.svg" width="200">';
	echo '<h2>Entry Link Login</h2>';
	echo '</div>';
	
	//echo var_dump($user);
	
	if(isset($user))
	{
		if(isset($user->user_login))
		{
			echo '<div class="entrylink-login-user">Hello '. $user->user_login . '</div>';
			echo '<ul class="um-misc-ul">';
			echo '<li><a href="/my-portfolio/"> Your account</a></li>';
			echo '<li><a href="/logout/?redirect_to=/login/"> Logout</a></li>';
			echo '</ul>';
		}
		else
		{
			//detect potential login
			if ($_SERVER['REQUEST_METHOD'] === 'POST') 
			{
				
				$username = filter_var($_POST['username-6'], FILTER_SANITIZE_STRING);
				$password = filter_var($_POST['user_password-6'], FILTER_SANITIZE_STRING);
				$remember_me = filter_var($_POST['rememberme'],  FILTER_VALIDATE_BOOLEAN);
				
				if($username == null || $username == "")
				{
					$error_messages .= '<div class="alert alert-danger" role="alert">Error: Username is missing!</div>';
				}
				
				if($password == null || $password == "")
				{
					$error_messages .= '<div class="alert alert-danger" role="alert">Error: Password is missing!</div>';
				}
				
				if($error_messages == "")
				{
					//check login against entry link db and wordpress
					$error_messages .= provia_loginuser($username, $password, $remember_me);					
				}
				
			}
			
			//load form template
			require_once provia_entrylinklogin_path . 'tmpl/default.php';
		}	
	}
	
}

function provia_loginuser($username, $password, $remember_me)
{
	
	//double check user isn't already logged in just in case
	$user = wp_get_current_user();
	
	if($user != null)
	{
		if(isset($user->user_login))
		{
			return '<div class="alert alert-danger" role="alert">Error: Username: '.$user->user_login.' is already logged in!</div>';
		}
	}

	//attempt to login to entry link
	$entrylink_success = provia_check_entrylink_login($username, $password);
	
	if($entrylink_success == false)
	{
		return '<div class="alert alert-danger" role="alert">Error: Username or Password is incorrect!</div>';
	}
		
	//attempt to login user to wordpress
	$creds = array();
    $creds['user_login'] = $username;
    $creds['user_password'] = $password;
    $creds['remember'] = $remember_me;
    $user = wp_signon($creds, false);
    
	if (is_wp_error($user))
	{
		
		//if login error happens, check to see if user is valid (password may have changed)
		$user_found = get_user_by('login', $username);
		$userid = -1;
				
		if(isset($user_found->ID))
		{
			$userid = $user_found->ID;
		}
		
		//echo var_dump($userid);
		
		if ($userid > 0)
		{
			
			//var_dump($user);
			
			//we know user exists, but password may have been changed, reset it to make sure it matches entry link
			wp_set_password($password, $userid);
			
			wordpress_login_user($username, $password, false, $remember_me);
			
		}
		else
		{
			//user does not exist, create it
			$email = strtolower(str_replace(" ","",$username)). '_entrylink@provia.com';
			$result = wp_create_user($username, $password, $email);
			
			if(is_wp_error($result))
			{
			  return $result->get_error_message();
			}
			else
			{
				//login user automatically and assign "professional" role
				wordpress_login_user($username, $password, true, $remember_me);
			}
		}
		
	}
	else
	{
		//redirect to homepage
		$redirect_to = get_home_url();
		wp_safe_redirect( $redirect_to );
		exit();
	}
	
}

function wordpress_login_user($username,$password,$setrole = false, $remember_me = true)
{
	$creds = array();
	$creds['user_login'] = $username;
	$creds['user_password'] = $password;
	$creds['remember'] = $remember_me;
	$user = wp_signon($creds, false);
	
	//echo var_dump($user);
	
	if($setrole && isset($user))
	{
		$wp_user_object = new WP_User($user->ID);		
		$wp_user_object->set_role('um_professional');
	}
	
	$user_meta = get_userdata($user->ID);
	var_dump($user_meta);
	
	$redirect_to = get_home_url();
	wp_safe_redirect( $redirect_to );
	exit();
	
}

function provia_check_entrylink_login($username, $password)
{
	$allowed_user = false;
	$username = filter_var($username, FILTER_SANITIZE_STRING);
	$password = filter_var($password, FILTER_SANITIZE_STRING);
		
	$service_url = "https://entrylink.provia.com/entrylink/integrate.aspx?u=" . $username . "&p=" .$password. "&m=ping";
	$service_url = str_replace(" ","%20",$service_url);
	
	$ch = curl_init();
	$timeout = 60;
	curl_setopt($ch, CURLOPT_URL, $service_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$response = curl_exec($ch);
	curl_close($ch);
	
	//echo $service_url . '<br />';
	//echo $username .' - '.$password.' - '.$response;
	
	if(isset($response))
	{
		if($response == "ok")
		{
			$allowed_user = true;
		}
	}
	
	return $allowed_user;
}