<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

//if ( ! defined( 'ABSPATH' ) ) exit;
//require_once( ABSPATH.'wp-admin/includes/plugin.php' );


//set header 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//header('Content-type: application/json');

if($_SERVER["REQUEST_METHOD"] == 'GET')
{
	//location request
	if(isset($_GET['locations']))
	{
		
		$roofing = 'false';
		
		$zipcode = $_GET['zipcode']; 
		$customertype = ''; 
		$entrydoors = $_GET['entrydoors']; 
		$stormdoors = $_GET['stormdoors']; 
		$windows_vinyl = $_GET['windows_vinyl']; 
		$windows_storm = $_GET['windows_storm']; 
		$vinylpatiodoors = $_GET['vinylpatiodoors']; 
		$siding = $_GET['siding']; 
		$stone = $_GET['stone'];
		
		if(isset($_GET['roofing']))
		{
			$roofing = $_GET['roofing'];
		}
		
		$platiumn = $_GET['platiumn'];
		$certified = $_GET['certified']; 
		$visualization = $_GET['visualization'];
		$displaygroup = "all";
		
		if(isset($_GET['displaygroup']))
		{
			$displaygroup = $_GET['displaygroup'];
		}
		
		$zipcode = filter_var($zipcode, FILTER_SANITIZE_STRING);
		$entrydoors = filter_var($entrydoors, FILTER_VALIDATE_BOOLEAN);
		$stormdoors = filter_var($stormdoors, FILTER_VALIDATE_BOOLEAN);
		$windows_vinyl = filter_var($windows_vinyl, FILTER_VALIDATE_BOOLEAN);
		$windows_storm = filter_var($windows_storm, FILTER_VALIDATE_BOOLEAN);
		$vinylpatiodoors = filter_var($vinylpatiodoors, FILTER_VALIDATE_BOOLEAN);
		$siding = filter_var($siding, FILTER_SANITIZE_STRING);
		$stone = filter_var($stone, FILTER_VALIDATE_BOOLEAN);
		$roofing = filter_var($roofing, FILTER_VALIDATE_BOOLEAN);
		
		$platiumn = filter_var($platiumn, FILTER_VALIDATE_BOOLEAN);
		$certified = filter_var($certified, FILTER_VALIDATE_BOOLEAN);
		$visualization = filter_var($visualization, FILTER_VALIDATE_BOOLEAN);
		
		//convert bool to strings
		$entrydoors = ($entrydoors) ? 'true' : 'false';
		$stormdoors = ($stormdoors) ? 'true' : 'false';
		$windows_vinyl = ($windows_vinyl) ? 'true' : 'false';
		$windows_storm = ($windows_storm) ? 'true' : 'false';
		$vinylpatiodoors = ($vinylpatiodoors) ? 'true' : 'false';
		$stone = ($stone) ? 'true' : 'false';
		$roofing = ($roofing) ? 'true' : 'false';
		
		$platiumn = ($platiumn) ? 'true' : 'false';
		$certified = ($certified) ? 'true' : 'false';
		$visualization = ($visualization) ? 'true' : 'false';
		
		//parse out customer type from siding variable
		$siding_all = explode('-', $siding);
		
		if(isset($siding_all[0]))
		{
			$siding = trim($siding_all[0]);
		}
		
		if(isset($siding_all[1]))
		{
			if(trim($siding_all[1]) != "")
			{
				$customertype = trim($siding_all[1]);
			}
		}
		
		if(trim($zipcode != "")
		&& trim($customertype) != "")
		{
			//write out location html results
			echo get_locations($zipcode, $customertype, $entrydoors, $stormdoors, $windows_vinyl, $windows_storm, $vinylpatiodoors, $siding, $stone, $roofing, $platiumn, $certified, $visualization, $displaygroup);
		}
		
	}
	
	//capture lead
	if(isset($_GET['capturelead']))
	{
				
		//gather values
		$professional = $_GET['professional'];
		$firstname = $_GET['firstname'];
		$lastname = $_GET['lastname'];
		$email = $_GET['email'];
		$companyname = $_GET['companyname'];
		$businessaddress = $_GET['businessaddress'];
		$city = $_GET['city'];
		$state = $_GET['state'];
		$zip = $_GET['zip'];
		$phone = $_GET['phone'];
		$products = $_GET['products'];
		$custno = '';
		$comments = $_GET['comments'];
		
		if(isset($_GET['custno']))
		{
			$custno = $_GET['custno'];
		}
		
		//santize input
		$professional = filter_var($professional, FILTER_SANITIZE_STRING);
		$firstname = filter_var($firstname, FILTER_SANITIZE_STRING);
		$lastname = filter_var($lastname, FILTER_SANITIZE_STRING);
		$email = filter_var($email, FILTER_SANITIZE_STRING);
		$companyname = filter_var($companyname, FILTER_SANITIZE_STRING);
		$businessaddress = filter_var($businessaddress, FILTER_SANITIZE_STRING);
		$city = filter_var($city, FILTER_SANITIZE_STRING);
		$state = filter_var($state, FILTER_SANITIZE_STRING);
		$zip = filter_var($zip, FILTER_SANITIZE_STRING);
		$phone = filter_var($phone, FILTER_SANITIZE_STRING);
		$products = filter_var($products, FILTER_SANITIZE_STRING);
		$custno = filter_var($custno, FILTER_SANITIZE_STRING);
		$comments = filter_var($comments, FILTER_SANITIZE_STRING);
		
		if($phone == "(none)")
		{
			$phone = "";
		}

		$success = false;
		
		//check for required fields
		if(trim($professional) != "" 
		&& trim($firstname) != ""
		&& trim($lastname) != ""
		&& trim($email) != "")
		{
		   $success = capture_lead($professional, $firstname, $lastname, $email, $companyname, $businessaddress, $city, $state, $zip, $phone, $products, $custno, $comments);
		}
		
		echo $success;
		
	}
	
}

function get_locations($zipcode, $customertype, $entrydoors, $stormdoors, $windows_vinyl, $windows_storm, $vinylpatiodoors, $siding, $stone, $roofing, $platiumn, $certified, $visualization, $displaygroup)
{
	$html = "";

	$service_url = "https://entrylink.provia.com/entrylink/dealerlocator.asmx/GetCustomersV10";
	$service_url .= "?password=Hk3L88c";
	$service_url .= "&zipcode=" . $zipcode;
	$service_url .= "&maxradius=100";
	$service_url .= "&maxresults=10";
	$service_url .= "&customertypedr=" . $customertype;
	$service_url .= "&prd_ed=" . $entrydoors;
	$service_url .= "&prd_sd=" . $stormdoors;
	$service_url .= "&prd_wi=" . $windows_vinyl;
	$service_url .= "&prd_vp=" . $vinylpatiodoors;
	$service_url .= "&prd_sw=" . $windows_storm;
	$service_url .= "&prd_si=" . $siding;
	$service_url .= "&prd_st=" . $stone;
	$service_url .= "&prd_mr=" . $roofing;
	$service_url .= "&platinumonly=" . $platiumn;
	$service_url .= "&certifiedonly=" . $certified;
	$service_url .= "&visualizationonly=" . $visualization;
	
	$xml = get_data($service_url);
	//echo '</h1>'.$service_url.'</h1>';
	//echo $xml;
	
	$xml_data = simplexml_load_string($xml) or die("Error: Cannot create object");
	
	if(isset($xml_data))
	{

		//----------------------------
		// get display group = 1
		//----------------------------
		
		$idx_count = 0;
		
		if($displaygroup == "1" || $displaygroup == "all")
		{
			$rowCount = 0;
			$html_1 =  '<table id="cnt-tabbed-content" cellpadding="0" cellspacing="0">';
			$html_1 .= '<tr><th id="col-business">&nbsp;</th><th id="col-contact">&nbsp;</th><th id="col-products">&nbsp;</th><th id="col-certifications">&nbsp;</th></tr>';
			
			foreach($xml_data->children() as $child)
			{
				$display_group = (string)$child->DisplayGroup;
				if($display_group == "1")
				{
					$html_1 .= get_result_html($child, $rowCount, $displaygroup, $idx_count);
					$rowCount++;
					$idx_count++;					
				}			
			}
			
			$html_1 .=  '</table>';
			$html .= $html_1;			
		}		
		
		//----------------------------
		// get display group = 2
		//----------------------------
		
		$html_2 =  '<table id="cnt-tabbed-content" cellpadding="0" cellspacing="0">';
		$html_2 .= '<tr><th id="col-business">&nbsp;</th><th id="col-contact">&nbsp;</th><th id="col-products">&nbsp;</th><th id="col-certifications">&nbsp;</th></tr>';
		
		$rowCount = 0;
		foreach($xml_data->children() as $child)
		{
			$display_group = (string)$child->DisplayGroup; 
			if($display_group == "2")
			{
				$html_2 .= get_result_html($child, $rowCount, '2', $idx_count);
				$rowCount++;	
				$idx_count++;
			}			
		}
		$html_2 .=  '</table>';
		
		if($rowCount > 0)
		{
			$html .= '<div class="authorized-contractors-container">';
			$html .= '<h3><a href="javascript:void(0);" id="authorized-contractors-button"><img src="/wp-content/plugins/provia-wheretobuy/images/down-128.png" width="25" border="0" /> Authorized Retailers</a></h3>';
			$html .= '<p>Authorized Retailers are dealers that buy through an authorized ProVia distributor and have demonstrated a high level of commitment to sell and install ProVia products.</p>';
			$html .= '</div>';
			
			//only display all results if button is clicked
			if($displaygroup == "all")
			{
				$html .= '<div class="authorized-contractors-results">';
				$html .= $html_2;
				$html .= '</div>';
			}
			
		}
		
	}
	
	return $html;
}

function get_result_html($child, $rowCount, $type = 'all', $idx_count = 0)
{
	
	$html = '';
	
	if($child == null)
	{
		return "";
	}
	
	$css_row = "listing odd"; 
	if($rowCount % 2 == 0)
	{
		$css_row = "listing even";
	}

	$html .= '<tr class="'.$css_row.'" id="tr-dealers-'.$child->ID.'" rel="'.$child->ID.'" lang="'.$child->Name.'" connectme="'.$child->ConnectMe.'" lat="'.$child->Latitude.'" long="'.$child->Longitude.'" platclub="'.$child->PlatinumClub.'" displaygroup="'.$child->DisplayGroup.'">';
	$html .= '<td>';
	$html .= '<h2 rel="'.$child->ID.'" id="dealer-name-'.$child->ID.'">'.$child->Name.'</h2>';
	//$html .= '<h3>'.$child->DisplayGroup.'</h2>';
	$html .= '<p><span id="dealer-address-'.$child->ID.'">'.$child->Add1.'<br/>'; 
	
	if(trim($child->Add2) != "")
	{
		$html .= $child->Add2.'<br/>'; 
	}
	
	if(trim($child->Add3) != "")
	{
		$html .= $child->Add3.'<br/>'; 
	}
	
	if(trim($child->City) != "")
	{
		$html .= $child->City.', '; 
	}
	
	$html .= $child->State.' '.$child->ZipCode.'<br/></span>'; 
	
	$distance = intval($child->Distance);
	
	if($distance > 0)
	{
		$html .= $child->Distance.' Miles</p>'; 
	}

	$html .= '<span class="btn" onclick="javascript: getDirections(event);">directions</span>&nbsp;';
	
	$html .= '</td><td><p class="dealer-website">';
	
	if(trim($child->Phone) != "")
	{
		$html .= '<label><a href="tel:'.$child->Phone.'" rel="'.$child->ID.'" id="phone-'.$child->ID.'">' . $child->Phone . '</a></label><br/><br />';
		$html .= '<a href="javascript:void(0);" class="open-info open-info-2" idx="'.$idx_count.'">View on map</a><br/>';
	}
	
	if(trim($child->Website) != "")
	{
		$html .= '<a href="'.$child->Website.'" target="_blank" rel="'.$child->ID.'" id="website-'.$child->ID.'">Visit Website</a><br/>';
	}
	
	if(trim($child->ShowroomURL) != "")
	{
		$html .= '<a href="'.$child->ShowroomURL.'" target="_blank">Visit Showroom</a><br/>';
	}
	
	if(trim($child->ConnectMe) == "true")
	{
		$html .= '<a class="text_connectme" href="javascript:void(0);" rel="'.$child->ID.'" lang="'.$child->Name.'">Connect Me</a><br/>';
	}
	
	$html .= '<a href="javascript:void(0);" class="preferred-dealer" rel="'.$child->ID.'">Set as Preferred Dealer</a><br/>';
	
	//get product oferring
	$product_offering = "";
	if (trim($child->Prd_ED) == "true")
	{
		$product_offering = $product_offering . "Entry Doors, ";
	}
	if (trim($child->Prd_SD) == "true")
	{
		$product_offering = $product_offering . "Storm Doors, ";
	}
	if (trim($child->Prd_VW) == "true")
	{
		$product_offering = $product_offering . "Vinyl Replacement Windows, ";
	}
	if (trim($child->Prd_SW) == "true")
	{
		$product_offering = $product_offering . "Storm Windows, ";
	}
	if (trim($child->Prd_WI) == "true")
	{
		$product_offering = $product_offering . "Windows, ";
	}
	if (trim($child->Prd_SI) == "true")
	{
		$product_offering = $product_offering . "Vinyl Siding, ";
	}
	if (trim($child->Prd_MR) == "true")
	{
		$product_offering = $product_offering . "Metal Roofing, ";
	}
	if (trim($child->Prd_ST) == "true")
	{
		$product_offering = $product_offering . "Stone, ";
	}
	if (trim($child->Prd_VP) == "true")
	{
		$product_offering = $product_offering . "Vinyl Patio Doors, ";
	}
	
	//trim out product offering
	if(strlen($product_offering) > 0)
	{ 
		$product_offering = substr($product_offering, 0, (strlen($product_offering) - 2)); 
	}
	
	$html .= '</p>';
	$html .= '</td><td><p>'.$product_offering.'</p></td><td>';
	
	if(trim($child->Platinum) == "true")
	{
		//$html .= '<img class="certification tooltip_wtb" src="/wp-content/plugins/provia-wheretobuy/images/platinum_dealer_icon.png" alt="Platinum Dealer" title="Platinum Dealer - ProVia' ."'". 's performance-based recognition program for those who have demonstrated the highest level of commitment to selling and installing our products." />';
		
		$tooltip_id = 'tooltipp_wtb_'.$rowCount.'_'.$type;
		$html .= '<img class="certification tooltip_wtb" src="/wp-content/plugins/provia-wheretobuy/images/platinum_dealer_icon.png" alt="Visualization" data-tooltip-content="#'.$tooltip_id.'" />';
		$html .= '<div class="tooltip_templates"><div id="'.$tooltip_id.'" class="tooltip-certified"><strong style="text-decoration: underline;">Platinum Dealer</strong><br />ProVia' ."'". 's performance-based recognition program for those who have demonstrated the highest level of commitment to selling and installing our products.</div></div>';
		
	}
	
	/*
	if(trim($child->Embarq) != "")
	{
		$html .= '<img class="certification tooltip_wtb" src="/images/img/shared/icon-embarq.png" alt="Embarq Dealer" title="Embarq Dealer - Embarq Fiberglass Doors with EnVision Innovation are currently available through select dealers." />';
	}
	*/
	
	$html_certification = '';
	
	if(trim($child->CertInsED) == "true")
	{
		$html_certification .= 'Entry Doors<br /> ';
	}
	
	if(trim($child->CertInsSD) == "true")
	{
		$html_certification .= 'Storm Doors<br /> ';
	}
	
	if(trim($child->CertInsVW) == "true")
	{
		$html_certification .= 'Vinyl Windows<br /> ';
	}
	
	if(trim($child->CertInsVP) == "true")
	{
		$html_certification .= 'Vinyl Patio Doors<br /> ';
	}
	
	if(trim($child->CertInsMR) == "true")
	{
		$html_certification .= 'Metal Roofing<br /> ';
	}

	//add certification html
	if($html_certification != "")
	{
		$html_certification = substr($html_certification, 0, -2);
		$tooltip_id = 'tooltipc_wtb_'.$rowCount.'_'.$type;
		$html .= '<img class="certification tooltip_wtb" src="/wp-content/plugins/provia-wheretobuy/images/certified_installer_icon.png" alt="Certified Installed" data-tooltip-content="#'.$tooltip_id.'" />';
		$html .= '<div class="tooltip_templates">
					<div id="'.$tooltip_id.'" class="tooltip-certified">
						<strong style="text-decoration: underline;">ProVia-Certified Installer</strong><br />This symbol identifies this company has completed specialized training for installing ProVia: 
						<div class="tooltip-highlight-text">'.$html_certification.'</div></div></div></div>';
	}
	
	if(trim($child->UsesVisualizationTools) == "true")
	{
		$tooltip_id = 'tooltipv_wtb_'.$rowCount.'_'.$type;
		$html .= '<img class="certification tooltip_wtb" src="/wp-content/plugins/provia-wheretobuy/images/product_vis_icon.png" alt="Visualization" data-tooltip-content="#'.$tooltip_id.'" />';
		$html .= '<div class="tooltip_templates"><div id="'.$tooltip_id.'" class="tooltip-certified"><strong style="text-decoration: underline;">Product Visualization</strong><br />Dealers who utilize ProVia'."'".'s visualization tools - <span class="tooltip-highlight-text">ProVia iPad App or entryLINK Spec Sheet</span> - to virtually show products before work begins.</div></div>';
	}
	
	/*
	if(trim($child->HasAeris) != "")
	{
		$html .= '<img class="certification tooltip_wtb" src="/images/img/shared/icon-aeris.png" alt="Exclusive Aeris Dealer" title="Aeris Dealer - Aeris Dealers have earned the distinction of having exclusive rights to selling this exceptional collection of windows and patio doors." />';
	}
	*/
	
	$html .=  '</td></tr>';
	
	return $html;
			
	
}

function get_data($url) {
	$ch = curl_init();
	$timeout = 60;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	//echo $url .'<br />';
	//var_dump($data);
	curl_close($ch);
	return $data;
}

function capture_lead($professional, $firstname, $lastname, $email, $companyname, $businessaddress, $city, $state, $zip, $phone, $products, $custno, $comments)
{
    
    $success = false;
    $leadtype = intval($professional);
    $product_id = 1;
	$product_name = "";
	$arr_product = null;
	$name = $firstname . " " . $lastname;	

	if(trim($products) != "")
	{
		$arr_product = explode(",", $products);
	}
	
	$arr_count = count($arr_product);
	
	if($arr_count == 0)
	{
		return;
	}
	
	foreach ($arr_product as $productname) {
		if(trim($productname) != "R")
		{
			//send each product select as a new lead
			$success = send_lead($professional, $name, $email, $companyname, $businessaddress, $city, $state, $zip, $phone, $productname, $custno, $comments, $leadtype);
		}
	}
	
	return $success;
	
}

function send_lead($professional, $name, $email, $companyname, $businessaddress, $city, $state, $zip, $phone, $productname, $custno, $comments, $leadtype)
{
	
	$campaign_id = 17;
	
	//Entry Doors,Storm Doors,Patio Doors,windows-vinyl,windows-storm,Stone Veneer,Vinyl Siding
	
	//echo $productname.' ';
	
	switch ($productname)
	{
		case "Entry Doors":
			$product_id = 1;
			break;
		case "Storm Doors":
			$product_id = 2;
			break;
		case "Patio Doors":
			$product_id = 3;
			break;
		case "Vinyl Siding":
			$product_id = 5;
			break;
		case "windows-vinyl":
			$product_id = 4;
			break;
		case "windows-storm":
			$product_id = 43;
			break;
		case "Windows":
			$product_id = 4;
			break;
		case "Stone Veneer":
			$product_id = 6;
			break;
		default:
			$product_id = 1;
			break;
	}
	
	//check for comments in cookie
	if($comments == "cookie")
	{
		if(isset($_COOKIE['capturelead_comments']))
		{
			$comments = $_COOKIE['capturelead_comments'];
		}
	}
	
	if($comments == "")
	{
		$comments = "Selected Product Search: " . $products;
	}
	
	//reset customer number if not found
	if($custno == "-1")
	{
		$custno = "";
	}
	
	//clean company name
	if(strtolower(trim($companyname)) == "home owner")
	{
		$companyname = "";
	}
	
	$provia_url = "https://entrylink.provia.com/entrylink/leads.asmx?wsdl";
	
	$xml_data = '<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	  <soap:Body>
		<CaptureLead xmlns="http://provia.com/">
		  <Name>'.$name.'</Name>
		  <CompanyName>'.$companyname.'</CompanyName>
		  <Add1>'.$businessaddress.'</Add1>
		  <Add2></Add2>
		  <Add3></Add3>
		  <City>'.$city.'</City>
		  <State>'.$state.'</State>
		  <Zip>'.$zip.'</Zip>
		  <Country></Country>
		  <Email>'.$email.'</Email>
		  <Phone>'.$phone.'</Phone>
		  <ExtNo></ExtNo>
		  <LeadTypeID>'.$leadtype.'</LeadTypeID>
		  <CampaignID>'.$campaign_id.'</CampaignID>
		  <ProductID>'.$product_id.'</ProductID>
		  <GradeABC></GradeABC>
		  <Comment>'.$comments.'</Comment>
		  <CustNo>'.$custno.'</CustNo>
		</CaptureLead>
	  </soap:Body>
	</soap:Envelope>';
	
	$ch = curl_init($provia_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$provia_output = curl_exec($ch);
	curl_close($ch);
	
	//echo '<h1>XML: ' . $provia_output . '</h1>';
	$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $provia_output);
	$provia_xml = simplexml_load_string($clean_xml);
	
	$lead_result = false;
	
	//echo var_dump($provia_xml);
	
	if(isset($provia_xml))
	{			
		$lead_result = filter_var($provia_xml->Body->CaptureLeadResponse->CaptureLeadResult, FILTER_VALIDATE_BOOLEAN);
	}
	
	//var_dump($params);
	//var_dump($response);
	
    return $lead_result;
}

/*
function sendRoofingEmail($name, $ipaddress, $zipcode, $email, $state, $city)
{
	
	//echo realpath();
	$mail = new PHPMailer();

	// Settings
	$mail->IsSMTP();
	$mail->CharSet = 'UTF-8';

	$mail->Host       = "mail.smtp2go.com"; 
	$mail->SMTPDebug  = 0;                     
	$mail->SMTPAuth   = true;                  
	$mail->Port       = 80;                    
	$mail->Username   = "ncaipen@pilotfishseo.com"; 
	$mail->Password   = "Vq5D4DTxUR7E";        

    $mail->setFrom('ncaipen@pilotfishseo.com', 'ProVia Roofing Request');
    $mail->addAddress('metalroofing@provia.com', 'ProVia Roofing'); 
	//$mail->addAddress('aaron.caipen@gmail.com', 'ProVia Roofing');
	
	$mail_message = '<h3>The following homeowner would like to be notified once we have a roofing dealer in their area.</h3>';
    $mail_message .= '<p>Name: ' .$name. ', IP Address: '.$ipaddress.', City: '.$city.', State: '.$state.', Zip: '.$zipcode.', Email Address: '.$email.'</p>';
	
	// Content
	$mail->isHTML(true);                                  
	$mail->Subject = 'Homeowner Roofing Lead';
	$mail->Body    = $mail_message;
	$mail->AltBody = 'The following homeowner would like to be notified once we have a roofing dealer in their area: Name: ' .$name. ', IP Address: '.$ipaddress.', Zip: '.$zipcode.', Email Address: '.$email;

	try {
		$mail->send();
		//echo "Message has been sent successfully";
	} catch (Exception $e) {
		//echo "Mailer Error: " . $mail->ErrorInfo;
	}
	
}
*/

?>