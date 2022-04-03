
jQuery( document ).ready(function() {
    updateEntryLinkIframes();
});

function updateEntryLinkIframes()
{
	
	//debugger;
	
	var userId = jQuery('#provia-wp-id').val();
	var entryLinkUrl = "entrylink.provia.com";
	
	if(userId != null && userId != "")
	{
		userId = parseInt(userId);
		
		jQuery( "iframe" ).each(function( index ) {

			var linkAttr = jQuery(this).attr('src');
			var iframeResult = linkAttr.indexOf(entryLinkUrl);
			
			if(iframeResult >= 0)
			{
				//update iframe src
				jQuery(this).attr('src', linkAttr + "&userid=" + userId);
			}
			
		});
		
	}
	
	
	
}



