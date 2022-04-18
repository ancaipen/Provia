
jQuery( document ).ready(function() {
    
	//add click event to button 
	jQuery( ".elementor-button-link, .elementor-button, .provia-saveall" ).click(function() {
	  proviaAddProductsToWishlist();
	});
	
	
});

function proviaAddProductsToWishlist()
{
	
	debugger;
	
	var provia_uid = jQuery('#provia_uid').val();
	
	if(provia_uid == null || provia_uid == "")
	{
		return;
	}
	
	provia_uid = parseInt(provia_uid);
	
	var product_ids = jQuery(".palette-container").find('.quick_view');
	
	//loop through all products in save all container	
	if(product_ids != null)
	{
		var product_ids_count = product_ids.length - 1;
		
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