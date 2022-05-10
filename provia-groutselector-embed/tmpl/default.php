<?php


//product vars and id
$product_id = -1;
$ver = rand(1, 1000000);
$user = wp_get_current_user();
$userid = -1;
$products = null;
$product_media = null;
$product_media_count = 0;

//product title placeholders
$product_title = 'Provia Product Selector '. $products[0]->product_title;
$product_description = 'ProVia entry doors, replacement windows, vinyl siding and manufactured stone are the best-in-class in home renovation products.';
$product_keywords = 'entry doors, home windows, replacement windows, manufactured stone, vinyl siding, product selector';
$product_enhanced = "";
$product_enhanced_link = "";
$product_enhanced_premium = "";
$product_enhanced_premium_link = "";

//media placeholders
$media_full_default = "";
$media_custom_style = "";
$description_default = "";
$type_default = "";
$name_default = "";

if(isset($_GET['product_id']))
{
	$product_id = filter_var($_GET['product_id'], FILTER_SANITIZE_NUMBER_INT);
}

if(isset($user))
{
	$userid = $user->ID;
}

//if product id is not found, do not load
if($product_id <= 0)
{
	echo '<h1>No products found</h1>';
	return;
}

$query = "SELECT p1.ID as product_id, p1.post_title as product_title, p1.post_content as product_desc, p2.post_excerpt as product_color, p2.ID as product_variation_id, ";
$query .= "(select meta_value from wp_postmeta where meta_key='_variation_description' and post_id=p2.ID limit 1) as product_hex, ";
$query .= "(select meta_value from wp_postmeta where meta_key='_thumbnail_id' and post_id=p2.ID limit 1) as product_thumb_id ";
$query .= "from wp_posts p1 ";
$query .= "inner join wp_posts p2 on p1.ID=p2.post_parent ";
$query .= "where p1.post_status = 'publish' and p1.post_type='product' ";
$query .= "and p2.post_status = 'publish' and p2.post_type='product_variation' ";
$query .= "and p1.ID = ". $product_id;
$query .= " ORDER BY p2.menu_order";	
$products = $GLOBALS['wpdb']->get_results($query);

if($products != null)
{
	$product_media_count = count($products);
}

//check for single image
if($products == null || $product_media_count == 0)
{
	$query = "SELECT p.ID as product_id, p.post_title as product_title, p.post_content as product_desc, '' as product_color, -1 as product_variation_id, '#fff' as product_hex, ";
	$query .= "( ";
	$query .= "select pm2.meta_value  FROM wp_postmeta pm, wp_postmeta pm2 ";
	$query .= "WHERE pm.meta_key = '_thumbnail_id' and pm.post_id = ".$product_id." and pm2.post_id=pm.meta_value and pm2.meta_key='_wp_attached_file' ";
	$query .= "LIMIT 0, 1 ";
	$query .= ") as thumbnail_image ";
	$query .= "from wp_posts p  ";
	$query .= "where p.post_status = 'publish' and p.post_type='product' ";
	$query .= "and p.ID = ". $product_id;
	$products = $GLOBALS['wpdb']->get_results($query);
	
	if($products != null)
	{
		$product_media_count = count($products);
	}
	
	if($product_media_count > 0)
	{
		$description_default = $products[0]->product_desc;
		$type_default = trim(str_replace("Color:","",$products[0]->product_color));
		$name_default = $products[0]->product_title;
		$media_full_default = '/wp-content/uploads/'.$products[0]->thumbnail_image;
		$media_custom_style = 'style="display:none;"';
	}
}

//final check to make sure that data is found
if($products == null || $product_media_count == 0)
{
	echo '<h1>No products found</h1>';
	return;
}

if(isset($_GET['product_enhanced']))
{
	$enhanced_bool = filter_var($_GET['product_enhanced'], FILTER_VALIDATE_BOOLEAN);
	if($enhanced_bool)
	{
		$product_enhanced = " product-enhanced";
		$product_enhanced_link = ' onclick="openEnhancedLink();"';
	}
}

if(isset($_GET['product_enhanced_premium']))
{
	$enhanced_premium_bool = filter_var($_GET['product_enhanced_premium'], FILTER_VALIDATE_BOOLEAN);
	if($enhanced_premium_bool)
	{
		$product_enhanced_premium = " product-enhanced-premium";
		$product_enhanced_premium_link = ' onclick="openPremiumEnhancedLink();"';
	}
}

//get default values
if($products != null && $media_full_default == "")
{
	//get image
	$query = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_wp_attached_file' AND post_id='".$products[0]->product_thumb_id."'";
	$media_full_default = $GLOBALS['wpdb']->get_results($query);
	$media_full_default = '/wp-content/uploads/'.$media_full_default[0]->meta_value;
	$description_default = $products[0]->product_desc;
	$type_default = trim(str_replace("Color:","",$products[0]->product_color));
	$name_default = $products[0]->product_title;
}

?>

<link rel="stylesheet" type="text/css" href="/wp-content/plugins/provia-groutselector-embed/scripts/magnify.css" />
<script type="text/javascript" src="/wp-content/plugins/provia-groutselector-embed/scripts/jquery.magnify.js"></script>
<link href="/wp-content/plugins/provia-groutselector-embed/css/product_selector.css?ver=<?php echo $ver; ?>" rel="stylesheet" type="text/css">
<script src="https://kit.fontawesome.com/81446b4acc.js" crossorigin="anonymous"></script>

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
				<img src="<?php echo $media_full_default; ?>" id="main-image" data-magnify-src="<?php echo $media_full_default; ?>" class="lazy" <?php echo $media_custom_style; ?>/>
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
			<?php if($type_default != "") { ?>
			<div class="col2-header">
				<span class="align-text-bottom media-title">Color: </span> <span id="media-type"><?php echo $type_default; ?></span>
			</div>
			<?php } ?>
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
					$sql = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_wp_attached_file' AND post_id='".$media->product_thumb_id."'";
					$media_image = $GLOBALS['wpdb']->get_results($sql);
					$media_image = '/wp-content/uploads/'.$media_image[0]->meta_value;
					
					$product_color = trim(str_replace("Color:","",$media->product_color));
					
					echo '<li class="media-item">';
					echo '<a href="javascript:void(0);" ';
					echo 'id="media-'.$media->product_variation_id.'" ';
					echo 'class="media-full';
					
					if($product_color != "")
					{
						echo ' media-'.strtolower(trim(str_replace(" ", "", $product_color)));
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
					echo 'onclick="changeProduct('.$media->product_variation_id.')"></a>';
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
		echo '#media-'.$media->product_variation_id. ' { '. "\r\n";
		echo 'background-color: '.$media->product_hex.';'."\r\n";
		echo ' } '."\r\n"; 
	}
}
?>
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
			$sql = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_wp_attached_file' AND post_id='".$media->product_thumb_id."'";
			$media_image = $GLOBALS['wpdb']->get_results($sql);
			$media_image = '/wp-content/uploads/'.$media_image[0]->meta_value;
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
			product_id : product_id,
			user_id : '<?php echo base64_encode($userid); ?>'
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
		
		//debugger;
		
		if(product_id == null || product_id == "")
		{
			return;
		}

		var url = '/wp-json/provia/v1/provia_admin/savewishlistitem/';
			
		var data = 
		{
			product_id : product_id,
			uid : <?php echo $userid; ?>
		};
		
		try
		{
			jQuery.get( url, data, function(result) {
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