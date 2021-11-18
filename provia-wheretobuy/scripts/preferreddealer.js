

jQuery( document ).ready(function() {
    
	jQuery('body').on('click', 'a.preferred-dealer', function() {
		
		//save selected dealer id, user context is picked up in server side code
		var dealerid = jQuery(this).attr("rel");
		
		if(dealerid != null && dealerid != "")
		{
			var url = "/wp-json/provia/v1/savepreferreddealer/save/";
			var data = { dealerid: dealerid };
			
			//post to web api to save dealer/user association
			jQuery.post(
				url,
				data,
				function(data, status){
					alert("Data: " + data + "\nStatus: " + status);
				}
			);
						
		}
			
	});
	
});


