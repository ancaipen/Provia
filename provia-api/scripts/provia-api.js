
jQuery( document ).ready(function() {
    
	//add click event to button 
	jQuery( ".provia-saveall .elementor-button-link, .provia-saveall .elementor-button" ).click(function() {
		proviaAddProductsToWishlist(this);
	});
	
	
});

function proviaAddProductsToWishlist(buttonClicked)
{
	
	//debugger;
	
	var provia_uid = jQuery('#provia_uid').val();
	
	if(provia_uid == null || provia_uid == "")
	{
		return;
	}
	
	provia_uid = parseInt(provia_uid);
	
	if(provia_uid <= 0)
	{
		alert('Please sign in or create an account in order save this palette!');
		return;
	}
	
	//get parent elementor container of the click button
	var elementorContainer = jQuery(buttonClicked).parents('.elementor-section').first();
	var paletteContainer = jQuery(elementorContainer).find(".palette-container").first();
	var product_ids = jQuery(paletteContainer).find('.quick_view');
	
	//loop through all products in save all container	
	if(product_ids != null)
	{
		var product_ids_count = product_ids.length;
		
		for(var i = 0; i < product_ids_count; i++)
		{
			var product_id = jQuery(product_ids[i]).attr('data-product-id');
	
			if(product_id != null && product_id != "")
			{
				product_id = parseInt(product_id);
				if(product_id > 0)
				{
					proviaAddProductToWishlist(provia_uid, product_id);
				}
			}
		}
		
		//refresh page to enforce update
		window.location.reload();
		return;
		
	}
	
}

function proviaAddProductToWishlist(provia_uid, productid)
{
	var urlWishlists = "/wp-json/provia/v1/provia_admin/savewishlistitem?uid=" + provia_uid + "&product_id=" + productid;
	jQuery.get(urlWishlists, function(data, status){
		console.log("Data: " + data + "\nStatus: " + status);
	});
	
}