
jQuery( document ).ready(function() {
    
	updateEntryLinkIframes();
	
	//add delete click
	jQuery('body').on('click', 'a.entrylink-configurator-close-image', function() {
		var image_id = jQuery(this).attr('image_id');
		deleteConfiguration(image_id);
	});
	
});

function deleteConfiguration(image_id)
{
	
	//debugger;
	
	if(image_id == null || image_id == "")
	{
		return;
	}
	
	var userId = jQuery('#provia-wp-id').val();
	
	if(userId == null || userId == "")
	{
		return;
	}
	
	//update type
	image_id = parseInt(image_id);
	userId = parseInt(userId);
	
	var confirmDelete = confirm("Are you sure you want to DELETE this item?");
	
	if(confirmDelete)
	{
		var url = '/wp-json/provia/v1/saveimage/delete/';
		
		var data = 
		{
			image_id : image_id,
			userid : userId
		};
		
		try
		{
			jQuery.post( url, data, function(result) {
				if(result == "success")
				{
					//hide image for now, delete with not allow it to load again
					jQuery('#entrylink-configurator-gallery-image-item-' + image_id).attr('style', 'display:none;');
				}
			}).fail(function(xhr, status, error) {
				console.log(status);
			});
		}
		catch(e)
		{
			alert('deleteConfiguration() ERROR: ' + e);
		}
	}
	
}

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



