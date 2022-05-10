
jQuery( document ).ready(function() {
    
	
	
	jQuery( "a.quick_view" ).click(function(e) {
		
		debugger;
		
		var iframeParam = getParameterByName('iframe');
		var productId = jQuery(this).attr('data-product-id');
		
		if(iframeParam == "true" && productId != null)
		{
			e.preventDefault();
			e.stopPropagation();
			
			productId = parseInt(productId);
			var url = '/provia-grout-selector-iframe/?product_id=' + productId;
			window.open(url, '_blank', 'location=yes,height=600,width=800,scrollbars=yes,status=yes');
			
		}
		
	});
	
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
				
				var iframeParam = getParameterByName('iframe');
				
				//set product url
				var url = '/provia-grout-selector-iframe/?product_id=' + productId;
				
				if(iframeParam == "true")
				{
					window.open(url, '_blank', 'location=yes,height=600,width=800,scrollbars=yes,status=yes');
				}
				else
				{
					//set url and display
					Fancybox.show([
					  {
						src: url,
						type: "iframe",
						preload: false
					  },
					]);
				}			
				
				//jQuery('.fancybox__content').attr('style', 'margin: 50% auto;');
				
			}
		}
		
	});
	
});

function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}


