function hideShowWhereToBuy()
{
	
	try
	{
		var isPartner = jQuery.cookie("IsPartner");
	
		if(isPartner == null || isPartner == "")
		{
			//manually check partner webservice
			var referer = document.referrer;
			if(referer != null && referer != "")
			{
				var partnerUrl = "/modules/mod_provia_wtb/ajax/partner.php?urlreferrer=" + referer;
				jQuery.get(partnerUrl, function(data, status){
					if(data != null && data != "")
					{
						if(data == "true")
						{
							jQuery('#where-to-buy').attr('style', 'display: none;');
							jQuery.cookie("IsPartner", 1);
						}
					}
				});
			}
			
			return;
		}
		
		if(isPartner == "1" || isPartner == "true")
		{
			//hide where to buy link
			jQuery('#where-to-buy').attr('style', 'display: none;');
		}
	}
	catch(e)
	{
		console.log("ERROR: " + e);
		return;
	}
	
}

function updateDomainLinks()
{
	
	var url = window.location.href;
	
	if(url.indexOf('qa.provia.com') >= 0)
	{
		window.onload = function() {
			var domain = 'https://www.provia.com/';

			// For images        
			var imgs = document.getElementsByTagName("img");
			for (var i = 0; i < imgs.length; i++) {
				imgs[i].setAttribute("src",domain + imgs[i].getAttribute("src"));
			}

			// For CSS files
			/*
			var links = document.getElementsByTagName("link");
			for (var i = 0; i < imgs.length; i++) {
				links[i].setAttribute("href",domain + links[i].getAttribute("href"));
			}
			*/
		};
	}
}


function hideShowProductMenu(menu)
{
	
	var linkText = jQuery(menu).text();
	if(linkText.trim() == "PRODUCTS")
	{
		//open close as needed
		var ulLinkText = jQuery('#navbar li ul.nav').attr('class');
		if(ulLinkText == "nav navbar-nav navbar-closed")
		{
			
			if(!IsMobile())
			{
				jQuery(menu).attr('style', 'width: 123px;');
				jQuery(menu).attr('class', 'dropdown open');
			}
			
			jQuery('li#nav-products').attr('class', 'dropdown');
			jQuery('li#nav-design').attr('class', 'dropdown nav-design');
			jQuery('li#nav-resources').attr('class', 'dropdown nav-resources');
			jQuery('#navbar li ul.nav').attr('class', 'nav navbar-nav navbar-open');
		}
		else
		{
			
			if(!IsMobile())
			{
				jQuery(menu).attr('style', '');
				jQuery(menu).attr('class', 'dropdown');
			}
								
			jQuery('li#nav-products').attr('class', 'dropdown open');
			jQuery('li#nav-design').attr('class', 'dropdown');
			jQuery('li#nav-resources').attr('class', 'dropdown');
			jQuery('#navbar li ul.nav').attr('class', 'nav navbar-nav navbar-closed');
		}
	}
}

function adjustMainMenu()
{
	//adjust top menu for mobile or non-mobile styles
	if(!IsMobile())
	{
		jQuery('li#nav-design').attr('style', 'position: absolute;left: 136px;');
		jQuery('li#nav-resources').attr('style', 'position: absolute;left: 240px;');
	}
	else
	{
		jQuery('li#nav-design').attr('style', '');
		jQuery('li#nav-resources').attr('style', '');
	}
}

function IsMobile() {
   if(window.innerWidth <= 600) {
	 return true;
   } else {
	 return false;
   }
}

function updatePhotoIframe()
{
	var iframe = jQuery('iframe#homeowner_photo_iframe');
	var warid = getUrlParameter('warid');
	var sc = getUrlParameter('sc');
	
	if(iframe != null && warid != null && warid != "" && sc != null && sc != "")
	{
		//add/update the iframe source if found
		var iframe_url = jQuery('iframe#homeowner_photo_iframe').attr('src');
		if(iframe_url != null && iframe_url != "")
		{
			var urls = iframe_url.split("?");
			var url = urls[0];
			if(url != null)
			{
				url = url + "?m=1&warid=" + warid + "&sc=" + sc;
				iframe.attr('src', url);
			}
		}
	}
}

function getUrlParameter(name) {
	name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
	var results = regex.exec(location.search);
	return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}