<?php
	
	//get current user and asspociated images
	$user = wp_get_current_user();
	$userid = 0;
	
	if(isset($user))
	{
		$userid = $user->ID;
	}
	
?>
<link href="/wp-content/plugins/provia-wheretobuy/css/preferred-dealer.css" rel="stylesheet" type="text/css" />

<div class="perferred-dealer-container">

	<div class="perferred-dealer-text">
		<h1 class="preferred-dealer-label">Preferred Dealer</h1>
   
		<a href="/where-to-buy">
			<img src="https://assets.website-files.com/6129234d13126814a210bb20/612cdfa3e2a6ba1a7897b8d0_002-pencil.png" loading="lazy" alt="">
		</a>
		
		<div id="provia-preferred-dealer-html"></div>
		
	</div>

	<div class="perferred-dealer-map-container">
		<div id="perferred-dealer-map"></div>
	</div>
	
</div> 


<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBBNzHIIdHxWk68i_x0iPmcu3mz-iAu28I" type="text/javascript"></script>
<script type="text/javascript">


jQuery(document).ready(function () {
	
	//load preferred dealer and map
	loadPreferredDealer();

});

function loadPreferredDealer()
{
	
	
	var url = "/wp-json/provia/v1/provia_wheretobuy/getpreferreddealer/?uid=<?php echo $userid; ?>";
	
	//load preferred dealer
	jQuery.get(url, function(result) {
		
		//debugger;
		
		//clear existing html
		jQuery('#provia-preferred-dealer-html').html('');
		
		if(result != null && result != "")
		{
			
			//set html
			jQuery('#provia-preferred-dealer-html').html(result);
			
			//load map 
			loadPreferredMap('perferred-dealer-map');
	
		}
		else
		{
			//hide preferred dealer code
			jQuery('#perferred-dealer-container').attr('style', 'display:none;');
		}
		
	});
}

function loadPreferredMap(id) 
{
	
	var zipCode = jQuery('#perferred-dealer-zipcode').html();
	
	if(zipCode == null || zipCode == "")
	{
		return;
	}
	
	if(id == null || id == "")
	{
		return;		
	}
	
	var geocoder = new google.maps.Geocoder();
	
	geocoder.geocode({ 'address': zipCode }, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			
			//reset markers
			markers = [];
			
			//default map location
			var latitude = jQuery('#perferred-dealer-lat').html();
			var longitude = jQuery('#perferred-dealer-long').html();
			
			//load map into id div
			var map = new google.maps.Map(
			document.getElementById(id), {
			  center: new google.maps.LatLng(latitude, longitude),
			  zoom: 8,
			  mapTypeId: google.maps.MapTypeId.ROADMAP,
			  mapTypeControl: false,
			  streetViewControl: false,
			  draggable: false,
			  fullscreenControl: false
			});
			
			//create marker
			var markerLat = parseFloat(latitude);
			var markerLong = parseFloat(longitude);
			var title = jQuery('#perferred-dealer-name').html();		
			var iconPath = 'https://www.provia.com/images/p-icon-large.png';
			var address = jQuery('#perferred-dealer-address').html();
			
			var dealer_content = '<div class="googleMapInfoWindow">';
			dealer_content = dealer_content + '<div>' + title + '</div>';
			dealer_content = dealer_content + '<div class="google-address">' + address + '</div>';
			dealer_content = dealer_content + '</div>';
			
			var infowindow = new google.maps.InfoWindow({
			  content: dealer_content
			});

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(markerLat, markerLong),
				icon: iconPath,
				map: map
			});
			
			marker.customIndex = 1;
			
			marker.addListener('click', function() {
			  infowindow.open(map, marker);
			});
			
			markers.push(marker);
	
		}
		else
		{
			alert('Invalid Zipcode!');
		}
	});
}


</script>