
jQuery( document ).ready(function() {
    updateIframeLinks();
});


function updateIframeLinks()
{
	//update li onclicks
	jQuery("li").each(function () {
		if (jQuery(this).attr('onclick') != null) {

			//update attribute to open in within the same window
			var _js = jQuery(this).attr('onclick');
			_js = _js.substring(0, _js.length - 1);
			_js = _js + "?iframe=true";

			jQuery(this).attr('onclick', _js);

		}
	});


	//update all links to inlcude querystring vals
	jQuery("a").each(function () {

		try {

			var urls = jQuery(this).attr('href').toString().split('?');
			var link = jQuery(this).attr('href');

			if (urls[0] != null) {
				link = urls[0];
			}

			//alert(link);

			var anchors = link.split('#');
			var anchor = "";

			if (anchors[1] != null) {
				link = anchors[0];
				anchor = anchors[1];
			}

			//update link by default to open in the same window
			jQuery(this).attr('target', '_self');

			//update all links that do not contain productdetail to open in new window
			/*
			if (link.toLowerCase().indexOf("productdetail") == -1) {
				if (link.toLowerCase().indexOf("javascript") == -1) {
					jQuery(this).attr('target', '_blank');
				}
			}
			*/
						
			
			
			//add iframe link to all links
			if (link != null) {

				//make sure to only update standard links
				if (link.toLowerCase().indexOf("javascript") == -1) {

					link = link + "?iframe=true";
					
					//append remaining querystring vals if found
					if(urls != null)
					{
						if(urls.length > 0)
						{
							var i=1
							for (i=1;i<=urls.length;i++)
							{
								if(urls[i] != null)
								{
									link = link + '&' + urls[i];
								}
							}
						}
					}
					
					//append anchor if found
					if (anchor != "") {
						link = link + '#' + anchor;
					}

					jQuery(this).attr('href', link);
				}

			}
			
			//remove links from any links that contain the class: linkframe
			if (jQuery(this).attr('class') == "linkframe") {
				jQuery(this).removeAttr("href");
			}


		}
		catch (e) { }

	});
	
}
