jQuery( document ).ready(function() {

	//add delete click
	jQuery('body').on('click', 'a.visionboards-item-close-image', function() {
		var project_id = jQuery(this).attr('project_id');
		deleteBoard(project_id);
	});
	
});

function deleteBoard(project_id)
{
	
	debugger;
	
	if(project_id == null || project_id == "")
	{
		return;
	}
	
	var userId = jQuery('#provia-wp-id').val();
	
	if(userId == null || userId == "")
	{
		return;
	}
	
	//update type
	project_id = parseInt(project_id);
	userId = parseInt(userId);
	
	var confirmDelete = confirm("Are you sure you want to DELETE this item?");
	
	if(confirmDelete)
	{
		var url = '/wp-json/provia/v1/provia_saveproject/delete/';
		
		var data = 
		{
			project_id : project_id,
			user_id : userId
		};
		
		try
		{
			jQuery.post( url, data, function(result) {
				if(result == "success")
				{
					//hide image for now, delete with not allow it to load again
					jQuery('#visionboards-item-container-' + project_id).attr('style', 'display:none;');
				}
			}).fail(function(xhr, status, error) {
				console.log(status);
			});
		}
		catch(e)
		{
			alert('deleteBoard() ERROR: ' + e);
		}
	}
	
}