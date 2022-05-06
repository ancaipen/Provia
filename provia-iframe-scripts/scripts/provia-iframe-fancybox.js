
jQuery( document ).ready(function() {
    
	jQuery( ".eael-lc-logo a" ).click(function(e) {
		
		//debugger;
				
		//get href value
		var productId = jQuery(this).attr('href');
		
		if(productId != null)
		{
			
			productId = productId.replace("?iframe=true", "");
			productId = productId.replace("#", "");
			productId = parseInt(productId);
			
			if(productId > 0)
			{
				e.preventDefault();
				
				//set product url
				var url = '/provia-grout-selector-iframe/?product_id=' + productId;
				
				//set url and display
				Fancybox.show([
				  {
					src: url,
					type: "iframe",
					preload: false
				  },
				]);
				
				jQuery('.fancybox__content').attr('style', 'margin: 50% auto;');
				
			}
		}
		
	});
	
});


