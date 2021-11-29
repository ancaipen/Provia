

jQuery( document ).ready(function() {
    
	jQuery('body').on('click', 'a.preferred-dealer', function() {
		
		//save selected dealer id, user context is picked up in server side code
		var dealerid = jQuery(this).attr("rel");

		if(dealerid != null && dealerid != "")
		{
			
			var dealer_name = jQuery("#dealer-name-" + dealerid).html();
			var dealer_phone = jQuery("#phone-" + dealerid).html();
			var dealer_website = jQuery("#website-" + dealerid).attr("href");
			var dealer_address = jQuery("#dealer-address-" + dealerid).html();
			var dealer_lat = jQuery("#tr-dealers-" + dealerid).attr('lat');
			var dealer_long = jQuery("#tr-dealers-" + dealerid).attr('long');
			var dealer_zipcode = jQuery("#fld-zip").val();
			
			var url = "/wp-json/provia/v1/savepreferreddealer/save/";
			var data = { 
				dealerid: dealerid,
				dealer_name: dealer_name,
				dealer_phone: dealer_phone,
				dealer_website: dealer_website,
				dealer_address: dealer_address,
				dealer_lat: dealer_lat,
				dealer_long: dealer_long,
				dealer_zipcode: dealer_zipcode
			};
			
			//post to web api to save dealer/user association
			jQuery.post(
				url,
				data,
				function(data, status){
					jQuery( "#preferred-dealer-dialog" ).dialog();
				}
			);
						
		}
			
	});
	
});


