<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//defined( '_JEXEC' ) or die( 'Restricted access' );

//set header 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//header('Content-type: application/json');

if($_SERVER["REQUEST_METHOD"] == 'GET')
{
	$urlreferrer = $_GET['urlreferrer'];
	$urlreferrer = filter_var($urlreferrer, FILTER_SANITIZE_STRING);
	$ispartner = setPartnerCookie($urlreferrer);
	//echo $urlreferrer;
	echo $ispartner;
}

function setPartnerCookie($urlreferrer)
{
	
	$is_partner = false;
	
	if(!isset($urlreferrer))
	{
		return;			
	}
	
	$domain_referer = parse_url($urlreferrer, PHP_URL_HOST);
		
	if(trim($domain_referer) == "")
	{
		return;
	}
	
	if(trim($domain_referer) == "www.provia.com")
	{
		return;
	}
	
	if(trim($domain_referer) == "provia.com")
	{
		return;
	}
		
	$url = "https://entrylink.provia.com/utils/wwwAPI.asmx";
	
	$xml_data = '<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	  <soap:Body>
		<IsPartnerWebsite xmlns="http://provia.com/">
		  <domain>' . $domain_referer . '</domain>
		</IsPartnerWebsite>
	  </soap:Body>
	</soap:Envelope>';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$provia_output = curl_exec($ch);
	curl_close($ch);
		
	//$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $provia_output);
	$provia_xml = simplexml_load_string($provia_output);
	
	//echo var_dump($provia_xml->children('soap', true)->Body);
	//echo $provia_output;
	
	$provia_xml->registerXPathNamespace("soap", "http://www.w3.org/2003/05/soap-envelope");
	$provia_vars = $provia_xml->xpath('//soap:Body');
	
	if(isset($provia_vars))
	{			
		$is_partner = filter_var($provia_vars[0]->IsPartnerWebsiteResponse[0]->IsPartnerWebsiteResult);
		// set cookie object				
		$cookie_name = "IsPartner";
		$cookie_value = $is_partner;
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); 
	}

	return $is_partner;

}

?>