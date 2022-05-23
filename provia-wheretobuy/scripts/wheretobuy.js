
	var markers = [];
	
	jQuery(document).delegate('a.open-info', "click", function (event) {
		
		jQuery('html, body').animate({
				scrollTop: jQuery("#map-canvas").offset().top
		}, 1000);
		
		var idx = jQuery(this).attr("idx");
		
		if(idx != "")
		{
			if(markers != null && markers.length > 0)
			{
				var index = parseInt(idx);
				mapItemClick(index);
			}
		}
		
	});
	
	jQuery('body').on('click', 'a.preferred', function() {
		
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
	
	function loadGoogleMap() 
	{
		
		var zipCode = jQuery('#fld-zip').val();
		
		if(zipCode == null || zipCode == "")
		{
			//alert('Please enter a zipcode to continue!');
			return;
		}
		
		var geocoder = new google.maps.Geocoder();
		
		geocoder.geocode({ 'address': zipCode }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				//reset markers
				markers = [];
				
				//default map location
				var latitude = results[0].geometry.location.lat();
				var longitude = results[0].geometry.location.lng();
				
				//load map
				var map = new google.maps.Map(
				document.getElementById('map-canvas'), {
				  center: new google.maps.LatLng(latitude, longitude),
				  zoom: 8,
				  mapTypeId: google.maps.MapTypeId.ROADMAP,
				  mapTypeControl: false,
				  streetViewControl: false
				});
				
				//add markers
				jQuery("tr.listing").each(function (idx) { 

					var markerLat = parseFloat(jQuery(this).attr("lat"));
					var markerLong = parseFloat(jQuery(this).attr("long"));
					var platClub = jQuery(this).attr("platclub");
					var title = jQuery(this).find("h2").text();
					
					var iconPath = 'https://www.provia.com/images/pin-grey.png';
					if(platClub == "true")
					{
						iconPath = 'https://www.provia.com/images/p-icon-large.png';
					}
					
					var infowindow = new google.maps.InfoWindow({
					  content: getInfoWindowContent(this)
					});

					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(markerLat, markerLong),
						icon: iconPath,
						map: map
					});
					
					marker.customIndex = idx;
					
					marker.addListener('click', function() {
					  infowindow.open(map, marker);
					});
					
					markers.push(marker);
					
				});
		
			}
			else
			{
				alert('Invalid Zipcode!');
			}
		});
	}
	
	function mapItemClick(id){
        google.maps.event.trigger(markers[id], 'click');
    }
	
	function getInfoWindowContent(tr)
	{
		var html = '<div class="googleMapInfoWindow">';
        html = html + '<h4>' + jQuery(tr).find("h2").text() + '</h4>';
        html = html + '<div class="add">' + jQuery(tr).find("span").html() + '</div>';
        html = html + '<div>' + jQuery(tr).find("label").text() + '</div>';
        html = html + '<div class="link">';
        html = html + '<p><a href="javascript:void(0)" onclick="javascript:getDirections(event)">Get Directions</a>';
        if (jQuery(tr).attr("connectme") == "true") {
            var _cust_no = "'" + jQuery(tr).attr('rel') + "'";
            var _cust_name = "'" + jQuery(tr).attr('lang') + "'";
            html = html + '&nbsp;&nbsp;<a href="javascript:void(0)" class="google_connectme" onclick="javascript:showLeadCapture_google(' + _cust_no + ',' + _cust_name + ')">Connect Me</a>';
        }
        html = html + '</p></div>';
        html = html + '</div>';
		return html;
	}
	
	function changeAudience() { 
		
        jQuery("tr.listing").remove(); 
		jQuery("#cnt-tabbed-content").append(getEmptyRow()); 
		jQuery(".listing td").html("Enter your search criteria to view locations in your area"); 
		if(jQuery("#fs-audience input:checked").val() == "R") { 
		    jQuery(".homeOwner").attr("style", "display:inline;margin-top: 10px;");
			jQuery(".professional").attr("style", "display: none;");
			
			jQuery("#fs-action").removeClass('shiftLeft'); 
			//jQuery("#instructions").html('Please fill out all required information. Dealer Results will be displayed below. <span style="color:#000; font-style:italic; font-size: 13px;">All fields required</span>'); 
		} 
		 else {
		    jQuery(".homeOwner").css("display", "none");
			jQuery(".professional").css("display","inline"); 
			/*jQuery("#fs-action").addClass('shiftLeft');*/
			//jQuery("#instructions").html('Please complete the information below to be connected with an Account Manager who will help you find a Distributor. <span style="color:#000; font-style:italic; font-size: 13px;">All fields required</span>');
		}  
	}
	
	var urlParams = {};
		(function () {
			var match,
				pl     = /\+/g,  // Regex for replacing addition symbol with a space
				search = /([^&=]+)=?([^&]*)/g,
				decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
				query  = window.location.search.substring(1);

			while (match = search.exec(query))
			   urlParams[decode(match[1])] = decode(match[2]);
		})();
		
	jQuery(function() {
		jQuery("#fld-type-"+urlParams["audience"]).attr("checked", "checked");
		changeAudience();
	});

	 function getAddress(target) {
		 var address = jQuery(target).parents(".listing").find("span").text().replace("<br/>", "");
		 if (address.replace("   ", "") == "") {
			 if (jQuery(target).parents(".listing").length > 0)
				 address = jQuery(target).parents(".listing").attr("lat") + ", " + jQuery(target).parents(".listing").attr("long");
			 else
				 address = jQuery(target).parents(".googleMapInfoWindow").find(".add").text();
		 }

		 address = address.toString().replace('directions', '');

		 return address;
	 }
	 
	 function getDirections(e) {
		 var target = e.target;
		 var mapUrl = "https://maps.google.com/maps?saddr=" + jQuery("#fld-zip").val() + "&daddr=" + getAddress(target);
		 window.open(mapUrl);
	 }
	 
	 function getEmptyRow() {
		 var tr = document.createElement("tr");
		 var td = document.createElement("td");
		 jQuery(tr).attr("class", "listing list-def");
		 jQuery(td).attr("colspan", "4");
		 jQuery(td).text("There are no locations available based on your criteria.");
		 jQuery(tr).append(td);
		 return tr;
	 }
	 
	 function replaceAll(find, replace, str) {
		 return str.replace(new RegExp(find, 'g'), replace);
	 }
	 
	 function getLocations(tab, displaygroup, allowscroll) {
		 if (validateLocationRequest()) {
				
				displaygroup = displaygroup || "all";
				
				if(allowscroll == null)
				{
					allowscroll = true;
				}
				
				if (jQuery("#fs-audience input:checked").val() == "D") 
				{
					
					var products = jQuery('.selectproducts input:checked').map(function () {
					return jQuery(this).val();
					}).get().join(',');

					var _company_name = jQuery('#fld-company-name').val();
					_company_name = replaceAll('&', 'and', _company_name);
					_company_name = escape(_company_name.replace(/\./g, ""));

					var _address = jQuery('#fld-address').val();
					_address = replaceAll('&', 'and', _address);
					_address = escape(_address.replace(/\./g, ""));

					var _city = jQuery('#fld-city').val();
					_city = replaceAll('&', 'and', _city);
					_city = escape(_city.replace(/\./g, ""));
					
					var postURL = self.location.protocol + '//' + document.domain + '/wp-content/plugins/provia-wheretobuy/ajax/wtb.php?capturelead=true&professional=' + jQuery('#fld-profession option:selected').val() + '&firstname=' + jQuery('#fld-first-name').val() + '&lastname=' + jQuery('#fld-last-name').val() + '&email=' + jQuery('#fld-email').val() + '&companyname=' + _company_name + '&businessaddress=' + _address + '&city=' + _city + '&state=' + jQuery('#fld-state').val() + '&zip=' + jQuery('#fld-form-zip').val() + '&phone=' + (jQuery('#fld-phone').val() == "" ? "(none)" : jQuery('#fld-phone').val()) + '&products=' + products + '&custno=-1&comments=-1&displaygroup=' + displaygroup;
					
					//alert(postURL);

					jQuery.get(postURL,
					  function (data) {
						  if (data == '1') {
							  
							  if (tab == "tab-map") {
								  jQuery("#map-canvas").fadeIn(100);
								  jQuery(".mapkey").show();
							  }
							  else {
								  jQuery("#map-canvas").fadeOut(100);
								  jQuery(".mapkey").hide();
							  }

							  //hide or show map upon successful submission
							  if (jQuery("#fs-audience input:checked").val() == "D") {
								  var result_html = '<div align="center"><h2>Your Request Has Been Received</h2>';
								  result_html = result_html + "<p>Thank you for your interest in ProVia's products. An Account Manager will be in touch with you soon.</p></div>";
								  jQuery("#results_message").html(result_html);
								  jQuery("#map_container").attr("style", "display:none;");
								  jQuery("#sec-listings").attr("style", "display:none;");
								  jQuery("#sec-form").attr("style", "display:none;");
								  jQuery("#instructions").attr("style", "display:none;");
								  
							  }
							  else {
								  jQuery("#map_container").attr("style", "background-color:#fff;");
								  jQuery("#sec-listings").attr("style", "background-color:#fff;");
								  jQuery("#sec-form").attr("style", "background-color:#fff;");
								  jQuery("#instructions").attr("style", "background-color:#fff;");
								  loadLocations(displaygroup, allowscroll);
							  }

						  }
						  else {
							  jQuery(".error-msg").hide();
							  jQuery(".error-msg ul").remove();
							  jQuery("#map-canvas").fadeOut(100);
							  jQuery(".mapkey").hide();
							  var ul = document.createElement("ul");
							  var li1 = document.createElement("li");
							  jQuery(li1).text(data.Message);
							  jQuery(ul).append(li1);
							  jQuery(".error-msg").append(ul);
							  jQuery(".error-msg").fadeIn(200, function () { jQuery(this).css('filter', ''); jQuery(this).css('opacity', ''); });
						  }
					  }).error(function (error, errorText, ex) { alert(ex); });
				}
				  else 
				  {
					  if (tab == "tab-map") {
						  jQuery("#map-canvas").fadeIn(100);
						  jQuery(".mapkey").show();
					  }
					  else {
						  jQuery("#map-canvas").fadeOut(100);
						  jQuery(".mapkey").hide();
						  
					  }
					loadLocations(displaygroup, allowscroll);
				}
		 }
	}

	function postConnectMeLeadCapture() {
		
		if (validateConnectRequest()) {

			//get products from selection
			var products = jQuery('.selectproducts input:checked').map(function () {
				return jQuery(this).val();
			}).get().join(',');

			var _leadtype = '1'; //homeowner

			//clean data
			var _comments = jQuery('#connect-comments').val();
			_comments = replaceAll('&', 'and', _comments);

			//use cookie to store comments
			jQuery.cookie("capturelead_comments", _comments);

			_comments = "cookie";

			var _address = jQuery('#connect-address').val();
			_address = replaceAll('&', 'and', _address);
			_address = escape(_address.replace(/\./g, ""));

			var _city = jQuery('#connect-city').val();
			_city = replaceAll('&', 'and', _city);
			_city = escape(_city.replace(/\./g, ""));

			//if professional, update professional type
			if (jQuery("#fs-audience input:checked").val() == "D") {
				_leadtype = jQuery('#fld-profession option:selected').val();
			}

			var postURL = self.location.protocol + '//' + document.domain + '/wp-content/plugins/provia-wheretobuy/ajax/wtb.php?capturelead=true'
			+ '&professional=' + _leadtype 
			+ '&firstname=' + jQuery('#connect-firstname').val() 
			+ '&lastname=' + jQuery('#connect-lastname').val()
			+ '&email=' + jQuery('#connect-email').val() 
			+ '&companyname=home owner'
			+ '&businessaddress=' + _address 
			+ '&city=' + _city
			+ '&state=' + jQuery('#connect-state').val() 
			+ '&zip=' + jQuery('#connect-form-zip').val() 
			+ '&phone=' + (jQuery('#connect-phone').val() == "" ? "(none)" : jQuery('#connect-phone').val()) 
			+ '&products='  + products + jQuery('#connect-custno').val() 
			+ '&comments=' + _comments;
			
			//alert(postURL);

			jQuery.get(postURL,
				  function (data) {
					  if (data == '1') {
						  //close dialog
						  jQuery('.dialog').dialog('close');
					  }
					  else {
						  jQuery(".connect-error-msg").hide();
						  jQuery(".connect-error-msg ul").remove();
						  var ul = document.createElement("ul");
						  var li1 = document.createElement("li");
						  jQuery(li1).text(data.Message);
						  jQuery(ul).append(li1);
						  jQuery(".connect-error-msg").append(ul);
						  jQuery(".connect-error-msg").fadeIn(200, function () { jQuery(this).css('filter', ''); jQuery(this).css('opacity', ''); });
					  }
				  }).error(function (error, errorText, ex) { alert(postURL + ' ' + ex); });

		}
	}

	function loadLocations(displaygroup, allowscroll) {
		
		displaygroup = displaygroup || "all";
		
		if(allowscroll == null)
		{
			allowscroll = true;
		}
		
		var container = document.createElement("div");
		jQuery(".list-def td").html("&nbsp;");
		jQuery(".loading").show();
		
		if(allowscroll)
		{
			jQuery('html, body').animate({
				scrollTop: jQuery("#map-canvas").offset().top
			}, 1000);
		}
		
		//clear result message
		jQuery("#results_message").html('');
		
		jQuery(container).load(getProviaApiUrl(displaygroup), function (responseText, textStatus, XMLHttpRequest) {
			
			if (responseText == "Error: Cannot create object") {
				jQuery("#results_message").html('<h2>No Results Found</h2>'); 
			}

			jQuery(".loading").hide();
			jQuery("tr.listing").fadeOut(500);
			jQuery("tr.listing").remove();
			
			//alert(jQuery(container).html());
			
			if (jQuery(container).find("tr").length == 0) {
				jQuery("#cnt-tabbed-content").append(getEmptyRow());

				jQuery("#map-canvas").fadeOut(100);
				jQuery(".mapkey").hide();
			}
			else {
				
				//jQuery(container).find("tr").hide();
				//jQuery("#cnt-tabbed-content").append(jQuery(container).find("tr"));
				
				jQuery('#cnt-search-results').html(jQuery(container).html());
				
				if (jQuery("#map-canvas").css("display") != "none") {
					loadGoogleMap();
					jQuery(".open-info").show();
				}
			}
			
			/*
			jQuery(".tooltip_wtb").tooltip({ track: true,
				delay: 0,
				showURL: false,
				showBody: " - ",
				extraClass: "pretty",
				fixPNG: true,
				opacity: 0.95,
				left: -120,
				content: function () {
					return $(this).attr('title').replace(/\[br\]/g,"<br />");
				}
			});
			*/
			
			jQuery('.tooltip_wtb').tooltipster({
				maxWidth: 200
			});
			
			jQuery("#cnt-tabbed-content .listing").fadeIn(500);
			
			//show authorized table results
			if(allowscroll == false)
			{
				jQuery("div.authorized-contractors-results").slideDown( "slow", function() { 
					//complete
				});
			}
			
		});
		
	}
	
	function getOrigin() {
		var origin = new Array();
		if (jQuery("tr.listing").length > 0) {
			origin[0] = parseFloat(jQuery(jQuery("tr.listing").get(0)).attr("lat"));
			origin[1] = parseFloat(jQuery(jQuery("tr.listing").get(0)).attr("long"));
		}
		return origin;
	}
	
	function getProviaApiUrl(displaygroup) {
		
		displaygroup = displaygroup || "all";
		
		var zip = (jQuery("#fs-audience input:checked").val() == "D" ? jQuery("#fld-form-zip").val() : jQuery("#fld-zip").val());
		var form_type = jQuery("#fs-audience input:checked").val();

		var apiUrl = "/wp-content/plugins/provia-wheretobuy/ajax/wtb.php?locations=true" +
			"&zipcode=" + zip + 
			"&customertype=" + form_type +
			"&entrydoors=" + getCheckBoxStatus("#fld-prod-entry") +
			"&stormdoors=" + getCheckBoxStatus("#fld-prod-storm") +
			"&windows_vinyl=" + getCheckBoxStatus("#fld-prod-windows-vinyl") +
			"&windows_storm=" + getCheckBoxStatus("#fld-prod-windows-storm") +
			"&vinylpatiodoors=" + getCheckBoxStatus("#fld-prod-patio") +
			"&siding=" + getCheckBoxStatus("#fld-prod-siding-option") +
			"&stone=" + getCheckBoxStatus("#fld-prod-stone") +
			"&roofing=" + getCheckBoxStatus("#fld-prod-roofing") +
			"&platiumn=" + getCheckBoxStatus("#recognition-type-platiumn") +
			"&certified=" + getCheckBoxStatus("#recognition-type-certified") +
			"&visualization=" +  getCheckBoxStatus("#recognition-type-studio") + 
			"&displaygroup=" +  displaygroup;
		
		return apiUrl;

	}
	function getCheckBoxStatus(checkbox) {
		var _results = jQuery(checkbox).prop("checked");
		
		//override customer type if found
		if (jQuery(checkbox).val() == "Vinyl Siding") {
			if (jQuery('#fld-prod-siding-contractors').is(':checked')) {
				_results = _results + "-" + jQuery('#fld-prod-siding-contractors').val();
			} else if (jQuery('#fld-prod-siding-distributors').is(':checked')) {
				_results = _results + "-" + jQuery('#fld-prod-siding-distributors').val();
			}
		}
		return _results;
	}
	
	/*
	function loadGoogleMap() {
		gMaps.loadAPI({ APIKey: google_api_key, continent: 'US', origin: getOrigin(), callback: gMaps.initMap, callbackProps: { mapId: 'map-canvas', callback: function () { }, callbackProps: {}} });
	}
	*/
	
	function validateLocationRequest() {
		var valid = true;
		jQuery(".error-msg").hide();
		jQuery(".error-msg ul").remove();
		var ul = document.createElement("ul");

		if (jQuery("#fs-audience input:checked").val() == "D")
		{
			if (jQuery("#fld-first-name").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("First Name is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-last-name").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Last Name is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-email").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Email is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-company-name").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Company Name is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-address").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Business Address is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-city").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("City is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-state").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("State is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-form-zip").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("A zipcode is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-phone").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Phone is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#fld-profession").val() == "-1") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Profession is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}

		}
		else
		{
			if (jQuery("#fld-zip").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("A zipcode is required to search for locations");
				jQuery(ul).append(li1);
				valid = false;
			}
		}
					
		if (jQuery(".selectproducts input:checked").length == 1) {
			var li2 = document.createElement("li");
			jQuery(li2).text("At least one product must be selected to search for locations");
			jQuery(ul).append(li2);
			valid = false;
		}

		if (jQuery(ul).find("li").length > 0) {
			jQuery(".error-msg").append(ul);
			jQuery(".error-msg").fadeIn(200, function () { jQuery(this).css('filter', ''); jQuery(this).css('opacity', ''); });

			//jQuery(".error-msg").css('display', 'inline');
		}
		return valid;
	}

	function validateConnectRequest() {

		var valid = true;
		jQuery(".connect-error-msg").hide();
		jQuery(".connect-error-msg ul").remove();
		var ul = document.createElement("ul");

		if (jQuery("#fs-audience input:checked").val() == "R") {
			
			if (jQuery("#connect-firstname").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("First Name is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-lastname").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Last Name is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-email").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Email is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-address").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Address is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-city").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("City is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-state").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("State is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-form-zip").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("A zipcode is required");
				jQuery(ul).append(li1);
				valid = false;
			}

			if (jQuery("#connect-phone").val() == "") {
				var li1 = document.createElement("li");
				jQuery(li1).text("Phone is required");
				jQuery(ul).append(li1);
				valid = false;
			}

		}


		if (jQuery(".selectproducts input:checked").length == 0) {
			var li2 = document.createElement("li");
			jQuery(li2).text("At least one product must be selected");
			jQuery(ul).append(li2);
			valid = false;
		}

		if (jQuery(ul).find("li").length > 0) {
			jQuery(".connect-error-msg").append(ul);
			jQuery(".connect-error-msg").fadeIn(200, function () { jQuery(this).css('filter', ''); jQuery(this).css('opacity', ''); });
		}
		return valid;
	}

	function setFormDefaults() {
		jQuery("tr.listing").remove(); jQuery("#cnt-tabbed-content").append(getEmptyRow());
		jQuery(".listing td").html("Enter your search criteria to view locations in your area");

		if (jQuery("#fs-audience input:checked").val() == "R") {
			jQuery(".homeOwner").css("display", "inline");
			jQuery(".professional").css("display", "none");
			jQuery("#fs-action").removeClass('shiftLeft');
			//jQuery("#instructions").html('Please fill out all required information. Dealer Results will be displayed below. <span style="color:#000; font-style:italic; font-size: 13px;">All fields required</span>');

			jQuery("#map_container").attr("style", "background-color:#fff;");
			jQuery("#sec-listings").attr("style", "background-color:#fff;");
			jQuery("#results_message").html("");
			jQuery("#fld-wtb-go").attr("value", "get results"); ;

		} else {
			jQuery(".homeOwner").css("display", "none");
			jQuery('#fld-prod-siding-details').attr('style', 'display:none;');
			jQuery(".professional").css("display", "inline");
			/*jQuery("#fs-action").addClass('shiftLeft');*/
			//jQuery("#instructions").html('Please complete the information below to be connected with an Account Manager who will help you find a Distributor. <br /><span style="color:#000; font-style:italic; font-size: 13px;">All fields required</span>');
			jQuery("#map_container").attr("style", "display:none;");
			jQuery("#sec-listings").attr("style", "display:none;");
			jQuery("#results_message").html("");
			jQuery("#fld-wtb-go").attr("value", "submit request");

		}
	}

	 function showLeadCapture(btn) {
		
		//get cust no from rel            
		var cust_no = btn.getAttribute('rel');
		var cust_name = btn.getAttribute('lang');

		//assign cust id
		jQuery("#connect-custno").attr('value', cust_no);
		//assign cust name to pop-up
		jQuery("#connectme_custname").html(cust_name);

		//show professional div
		jQuery(".dialog").attr('style', 'display: inline;');
		jQuery(".dialog").dialog({ height: 500, width: 600 });

		 //add x to button, auto scroll
		setTimeout("jQuery('.ui-dialog-titlebar-close').text('x');", 1000);
		setTimeout("jQuery('html, body').animate({scrollTop:jQuery('.ui-dialog').position().top}, 'slow');", 1000);

	}

	function showLeadCapture_google(cust_no, cust_name) {

		//show professional div
		jQuery(".dialog").attr('style', 'display: inline;');
		jQuery(".dialog").dialog({ height: 500, width: 600 });

		//assign cust id
		jQuery("#connect-custno").attr('value', cust_no);
		//assign cust name to pop-up
		jQuery("#connectme_custname").html(cust_name);

	}

	function autoSearch() {

		//check to see if zip override is set
		var _zipcode = jQuery("#fld-zip").val();

		if (_zipcode != null)
		{
			_zipcode = parseInt(_zipcode);
			if (_zipcode > 0) {

				//set vars
				//jQuery("#recognition-type-embarq").prop('checked', true);
				//jQuery("#fld-prod-entry").prop('checked', true); 

				//search
				getLocations(jQuery('.tabbed .active a').attr('id'));

			}
		}
	}