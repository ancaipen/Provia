<?php

require('/home/proviav2/public_html/provia.com/wp-content/plugins/provia-groutselector/config.php');
require(Config::$home_dir.'/wp-content/plugins/provia-groutselector/data.php');

//check for product id
$product_id = '';
$ver = rand(1, 1000000);
//$user = wp_get_current_user();

if(isset($_GET['product_id']))
{
	$product_id = filter_var($_GET['product_id'], FILTER_SANITIZE_NUMBER_INT);
}

$products = null;
$product_media = null;

if($product_id != null && $product_id != '')
{
	$products = select_product($product_id);	
}

//final check to make sure that data is found
if($products == null || $products[0] == null)
{
	echo '<h1>No products found</h1>';
	exit();
}

$product_title = 'Provia Product Selector '. $products[0]["product_title"];
$product_description = 'ProVia entry doors, replacement windows, vinyl siding and manufactured stone are the best-in-class in home renovation products.';
$product_keywords = 'entry doors, home windows, replacement windows, manufactured stone, vinyl siding, product selector';

$product_enhanced = "";
$product_enhanced_link = "";
if(isset($_GET['product_enhanced']))
{
	$enhanced_bool = filter_var($_GET['product_enhanced'], FILTER_VALIDATE_BOOLEAN);
	if($enhanced_bool)
	{
		$product_enhanced = " product-enhanced";
		$product_enhanced_link = ' onclick="openEnhancedLink();"';
	}
}

$product_enhanced_premium = "";
$product_enhanced_premium_link = "";
if(isset($_GET['product_enhanced_premium']))
{
	$enhanced_premium_bool = filter_var($_GET['product_enhanced_premium'], FILTER_VALIDATE_BOOLEAN);
	if($enhanced_premium_bool)
	{
		$product_enhanced_premium = " product-enhanced-premium";
		$product_enhanced_premium_link = ' onclick="openPremiumEnhancedLink();"';
	}
}

$product_media_count = count($products);
$media_full_default = "";
$description_default = "";
$type_default = "";
$name_default = "";

//echo var_dump($product_media);

//get default values
if($products != null)
{
	//get image
	$media_full_default = select_product_media_by_product($products[0]['product_thumb_id']);	
	$media_full_default = '/wp-content/uploads/'.$media_full_default[0];
	$description_default = $products[0]['product_desc'];
	$type_default = trim(str_replace("Color:","",$products[0]['product_color']));
	$name_default = $products[0]['product_title'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<?php echo $product_keywords; ?>" />
	<meta name="author" content="ProVia" />
	<meta name="description" content="<?php echo $product_description; ?>" />
	<meta name="generator" content="ProVia" />
	<title><?php echo $product_title; ?></title>
	
	<link href="/wp-content/plugins/provia-groutselector/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />

	<script src="/wp-content/plugins/provia-groutselector/js/jquery.min.js?8e295d9a2745a1ae1e87fc3b8ba05e38" type="text/javascript"></script>
	<script src="/wp-content/plugins/provia-groutselector/js/jquery.lazyload.js" type="text/javascript"></script>
			
	<link href="/wp-content/plugins/provia-groutselector/js/bootstrap.css" rel="stylesheet" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="/wp-content/plugins/provia-groutselector/js/magnify.css" />
    <script type="text/javascript" src="/wp-content/plugins/provia-groutselector/js/jquery.magnify.js"></script>

	<link href="/wp-content/plugins/provia-groutselector/product_selector.css?ver=<?php echo $ver; ?>" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/81446b4acc.js" crossorigin="anonymous"></script>
	
</head>
<body>


<div id="main-container">
<div class="product-selector-cont">

<div id="dialog" title="IMPORTANT" style="display: none;">
  <div class="dialog-important" align="center">IMPORTANT</div>
  <p align="center">Stone color representation may be affected by variations in photographic display, indoor/outdoor lighting, printing methods and monitor calibration.  Always refer to actual product samples for accurate color representation.</p>
  <p align="center"><button id="important-btn">OK</button></p>
</div>


<div id="main-header" style="display:none; font-size: 20px;position: absolute; top:0; z-index: 9999; background-color: #ff7096; color:#fff; ">

</div>

<div class="row">
	<div class="col-md-12">			
		<div class="col1-header-mobile">
				<span class="product-name"><?php echo $name_default; ?></span>
				<span class="wishlist-name"><a href="javascript:void(0);" class="add-to-wishlist" product-id="<?php echo $product_id; ?>"><i class="far fa-heart fa-lg fa-fw heart-icon" style="font-size: 25px;padding-bottom:6px;"></i></a></span>
		</div>
	</div>
	<div class="col-md-12">
		<div id="main-body">
			<div class="media-main">
				<img src="<?php echo $media_full_default; ?>" id="main-image" data-magnify-src="<?php echo $media_full_default; ?>" class="lazy" />
			</div>
			<div class="media-desciption">
				<?php  echo $description_default; ?>
			</div>
		</div>
	</div>
	
	
<div class="col-md-12">	
	
</div>
		
	
</div>
<div class="grout-color-container">
<div class="row color-info<?php if($product_media_count <= 1) { echo ' no-selection'; } ?>">
	
	
	<div class="col-sm-6 grout-color-<?php echo $product_media_count; ?><?php echo $product_enhanced; ?><?php echo $product_enhanced_premium; ?>"<?php echo $product_enhanced_link; ?><?php echo $product_enhanced_premium_link; ?>>	
		<div class="color-text-container">
			<div class="col1-header">
				<span class="product-name"><?php echo $name_default; ?></span>
				<span class="wishlist-name"><a href="javascript:void(0);" product-id="<?php echo $product_id; ?>" class="add-to-wishlist"><i class="far fa-heart fa-lg fa-fw heart-icon" style="font-size: 25px;padding-bottom:6px;"></i></a></span>
			</div>
			<div class="col2-header">
				<span class="align-text-bottom media-title">Color: </span> <span id="media-type"><?php echo $type_default; ?></span>
			</div>
		</div>
	</div>
	
	<div class="col-sm-6">

		<div id="main-menu">

			<ul class="product-type">
			<?php 
			
			$media_count = 0;
			
			if($products != null && $product_media_count > 1)
			{
				foreach($products as $media)
				{
					
					//get image
					$media_image = select_product_media_by_product($media['product_thumb_id']);	
					$media_image = '/wp-content/uploads/'.$media_image[0];
					
					$product_color = trim(str_replace("Color:","",$media['product_color']));
					
					echo '<li class="media-item">';
					echo '<a href="javascript:void(0);" ';
					echo 'id="media-'.$media['product_variation_id'].'" ';
					echo 'class="media-full';
					
					if($product_color != "")
					{
						echo ' media-'.strtolower(trim($product_color));
					}
					
					//make default option selected
					if($media_count == 0)
					{
						echo ' selected" ';
					}
					else
					{
						echo '" ';
					}
					
					echo 'rel="'.$media_image.'" ';
					echo 'shown="'.$product_color.'" ';
					echo 'onclick="changeProduct('.$media['product_variation_id'].')"></a>';
					echo '</li>'; 
					$media_count++;
				}
			}

			?>
			</ul>
		</div>
	</div>
	</div>
</div>
</div>

<div class="preload-images" style="display:none;"></div>

<style type="text/css">
<?php 

if($products != null)
{
	foreach($products as $media)
	{
		echo '#media-'.$media['product_variation_id']. ' { '. "\r\n";
		echo 'background-color: '.$media['product_hex'].';'."\r\n";
		echo ' } '."\r\n"; 
	}
}

?>

@media (min-width: 268px) {	
.ui-dialog{
width:100% !important;
left:50% !important;
top:0 !important;
margin:0 -50%;
min-height:70%;
	z-index:99999;
	}
	
.ui-dialog p{
	 font-size: 13px;
		line-height:20px;
	}
	}
	
	@media (min-width: 516px) {
		
		.ui-dialog{
width:80% !important;
left:60% !important;
top:20% !important;
margin:0 -50%;
min-height:0%;
	}
	
.ui-dialog p{
	 font-size: 13px;
		line-height:20px;
	}
		
	}	
	
	@media (min-width: 768px) {
		.ui-dialog{
top:40% !important;
	}
		
	}	
	

.fancybox-type-iframe .fancybox-inner {
	overflow:hidden !important ;
	height:500px !important;
}

.ui-button
{
	display:none;
}

.ui-dialog-titlebar
{
	display: none;
}
.dialog-important
{
	font-size: 18px;
	font-weight: bold;
	padding: 5px;
}
#important-btn
{
	font-size: 18px;
	font-weight: bold;
	padding: 8px;
}
</style>

</div>
</div>
<script language="javascript" type="text/javascript">
	
	var mediaImageDir = 'https://provia.proviaserver-v2.com/';

	jQuery(document).ready(function() {
		
		//used to determine height/width within the iframe
		/*
		jQuery(window).resize(function() {
		  var height = jQuery(window).height(); 
		  var width = jQuery(window).width();
		  var displaySize = 'HEIGHT: ' + height + ", WIDTH: " + width; 		  
		  jQuery('#main-header').html(displaySize);
		});
		*/
		
		//lazy load image(s)
		//jQuery("img.lazy").lazyload();
		
		var product_id = jQuery(".add-to-wishlist").attr('product-id');
		getWishlistImage(product_id);
		
		//message has been hidden permentally (for now)
		/*
		var dialogCookie = getCookie("productSelector");
				
		if(dialogCookie == null)
		{
			//show dialog
			jQuery("#dialog").dialog();

			jQuery(window).resize(function() {
				jQuery("#dialog").dialog("option", "position", {my: "center", at: "center", of: window});
			});
			
			//set cookie for pop-up
			setCookie("productSelector","1",30); 
		}
		*/
		
		jQuery("#important-btn").click(function () {
			jQuery("#dialog").dialog();
            jQuery("#dialog").dialog('close');
        });
		
		jQuery(".add-to-wishlist").click(function () {
			var product_id = jQuery(this).attr('product-id');
			saveWishlistImage(product_id);
        });
			
		<?php
	
		$images = '';
		foreach($products as $media)
		{
			$media_image = select_product_media_by_product($media['product_thumb_id']);	
			$media_image = '/wp-content/uploads/'.$media_image[0];
			$images .= '"'.$media_image.'",'; 
		}
		$images = trim($images, ',');
		echo 'var preloadImgs = ['.$images.'];';	
		
		?>
		
		//preload all images
		preloadImages(preloadImgs);
		
		//set magnify on default image
		jQuery('#main-image').magnify();
		
		
	});
	
	function getWishlistImage(product_id)
	{
		
		//debugger;
		
		if(product_id == null || product_id == "")
		{
			return;
		}

		var url = '/wp-json/provia/v1/provia_tiwishlist/getimage/';
			
		var data = 
		{
			product_id : product_id
		};
		
		try
		{
			jQuery.post( url, data, function(result) {
				var imageId = result;
				
				if(imageId > 0)
				{
					jQuery('.heart-icon').attr('class', 'fa-solid fa-heart heart-icon');
				}
				
				//show icon
				jQuery('.add-to-wishlist').attr('style', '');
				
			}).fail(function(xhr, status, error) {
				console.log(status);
			});
		}
		catch(e)
		{
			alert('saveWishlistImage() ERROR: ' + e);
		}
		
	}
	
	function saveWishlistImage(product_id)
	{
		
		debugger;
		
		if(product_id == null || product_id == "")
		{
			return;
		}

		var url = '/wp-json/provia/v1/provia_tiwishlist/saveimage/';
			
		var data = 
		{
				product_id : product_id
		};
		
		try
		{
			jQuery.post( url, data, function(result) {
				var errMsg = result;
				jQuery('.heart-icon').attr('class', 'fa-solid fa-heart heart-icon');
			}).fail(function(xhr, status, error) {
				console.log(status);
			});
		}
		catch(e)
		{
			alert('saveWishlistImage() ERROR: ' + e);
		}
		
	}
	
	function openPremiumEnhancedLink()
	{
		var url = "/manufactured-stone/stone-color-reference#premium";
		var win = window.open(url, '_blank');
		win.focus();
	}
	
	function openEnhancedLink()
	{
		var url = "/manufactured-stone/stone-color-reference#enhance";
		var win = window.open(url, '_blank');
		win.focus();
	}
	
	function preloadImages(preloadImgs)
	{
		for (var i = 0; i < preloadImgs.length; i++) 
		{
			var img = '<img src="' + mediaImageDir + preloadImgs[i] + '" />';
			jQuery(".preload-images").append(img);
		}
	}
	
	function changeProduct(productId)
	{
		var mediaId = '#media-' + productId;
		var mediaImage = mediaImageDir + jQuery(mediaId).attr('rel');
		var nameType = jQuery(mediaId).attr('shown');
		
		//swap image
		jQuery('#main-image').attr('src', mediaImage);
		
		//update source and add magnify
		jQuery('#main-image').attr('data-magnify-src', mediaImage);
		
		jQuery('#main-image').magnify();
		
		//update name/type
		jQuery('#media-type').html(nameType);
		
		//update selected link with class
		var listItems = jQuery("ul.product-type a");
		
		listItems.each(function(idx, li) {		
			var linkId = '#' + jQuery(li).attr('id');
			var className = jQuery(li).attr('class');
			
			//removed selected
			className = className.replace(" selected", "");
			
			if(linkId == mediaId)
			{
				jQuery(li).attr('class', className + ' selected');
			}
			else
			{
				jQuery(li).attr('class', className);
			}
		});
		
	}
	
	function setCookie(name,value,days) 
	{
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}
	
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	function eraseCookie(name) {   
		document.cookie = name+'=; Max-Age=-99999999;';  
	}

</script>

</body>
</html>