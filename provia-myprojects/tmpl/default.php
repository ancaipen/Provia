<?php
	
	//get current user and asspociated images
	$user = wp_get_current_user();
	$userid = 0;
	$image_html = '';
	$project_html = '';
	
	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	if($userid == 0)
	{
		return;
	}
	
	//create project dropdown
	$sql = "SELECT ID, author, title FROM wp_tinvwl_lists ";
	$sql .= "where title <> '' and author = ".$userid;
	
	$result = $GLOBALS['wpdb']->get_results($sql);
	
	$project_html = '<select name="project-lists" id="project-lists">';
	$project_html .= '<option value="-1">All Items</option>';
	foreach ( $result as $list )
	{
		$project_html .= '<option value="'.$list->ID.'">'.$list->title.'</option>';
	}
	$project_html .= '</select>';
	

?>
<link href="/wp-content/plugins/provia-myprojects/css/myprojects.css" rel="stylesheet" type="text/css" />
<script src="/wp-content/plugins/provia-myprojects/js/interact.min.js"></script>
<script src="/wp-content/plugins/provia-myprojects/js/html2canvas.min.js"></script>

<div id="my-projects-save">
	<?php echo $project_html; ?>
	<input type="text" name="myproject-name" id="myproject-name" value="" />
	<input type="hidden" name="list_id" id="list_id" value="" />
	<a href="javascript::void(0);" id="save-project"><img src="/wp-content/plugins/provia-myprojects/images/pencil.svg" width="25"/></a>
</div>

<div id="my-projects-overlay" style="display:none;">
	
</div>

<div id="my-projects-container">
	
</div>

<script>
	
	jQuery(document).ready(function() {
		
		loadProjectImages();
		
		jQuery("#project-lists").change(function () {
			filterImages(this);
			loadProjectImages();
		});
		
		jQuery("#save-project").click(function () {
			saveProject();
		});
		
	});
	
	function showHideLoading(showDiv, displayText)
	{
		
		if(displayText == null)
		{
			displayText = '<img src="/wp-content/plugins/provia-myprojects/images/load-icon-png-7952.png" width="25" /> <span class="my-projects-overlay-text">Loading....</span>';
		}
		
		if(showDiv == null)
		{
			showDiv = false;
		}
		
		if(showDiv == true)
		{
			//show loading text
			jQuery('#my-projects-overlay').html(displayText);
			jQuery('#my-projects-overlay').attr('style', 'display:block;');
			
			//lock project fields
			jQuery("#project-lists").prop('disabled', true);
			jQuery("#myproject-name").prop('disabled', true);
			jQuery("#save-project").prop('disabled', true);
			
		}
		else
		{
			jQuery('#my-projects-overlay').html(displayText);
			jQuery('#my-projects-overlay').attr('style', 'display:none;');
			
			//unlock project fields
			jQuery("#project-lists").prop('disabled', false);
			jQuery("#myproject-name").prop('disabled', false);
			jQuery("#save-project").prop('disabled', false);
			
		}
	}
	
	function loadProjectImages()
	{
		
		debugger;
		
		showHideLoading(true);
		var list_id = jQuery('#project-lists').val();
		
		//get images and write html to div
		var url = "/wp-json/provia/v1/provia_getproject/getimages/?uid=<?php echo $userid; ?>";
		
		if(list_id != null && parseInt(list_id)  > 0)
		{
			url += '&list_id=' + list_id;
		}
		
		jQuery.get( url, function(result) {
			var image_html = result;
			if(image_html != null && image_html != "")
			{
				jQuery('#my-projects-container').html(image_html);
			}
			showHideLoading(false);
		});
		
	}
	
	function saveProject()
	{
		
		//debugger;
		
		var project = jQuery('#myproject-name').val();
		var list_id = jQuery('#list_id').val();
		
		//validate input
		if(project == "" || project == "")
		{
			alert('Project name is missing, please enter to continue save');
			return;
		}
		
		//show loading overlay
		showHideLoading(true);
		
		//create snapshot of canvas
		html2canvas(document.querySelector("#my-projects-container")).then(canvas => {

			var img = canvas.toDataURL();

			// Send the screenshot to PHP to save it on the server
			var url = '/wp-json/provia/v1/provia_saveproject/default/';
			
			var data = 
			{
					project_image : img,
					project_name : project,
					list_id: list_id,
					user_id : "<?php echo base64_encode($userid); ?>"
			};
			
			jQuery.post( url, data, function(result) {
				var id = parseInt(result);
				if(id > 0)
				{
					jQuery('#list_id').val(id);
					saveProjectImages();
					showHideLoading(false);
				}
			}).fail(function(xhr, status, error) {
				showHideLoading(false);
			});
			
		});

	}
	
	function saveProjectImages()
	{
		
		//debugger;
		
		var project = jQuery('#myproject-name').val();
		var list_id = jQuery('#list_id').val();
		
		//validate input
		if(project == "" || project == "")
		{
			alert('Project name is missing, please enter to continue save');
		}
		
		//loop through all images 
		jQuery('#my-projects-container .drag-drop:not(.drag-drop-hidden)').each(function() {
			
			var imgStyle = jQuery(this).attr('style');
			var imgDataX = jQuery(this).attr('data-x');
			var imgDataY = jQuery(this).attr('data-y');
			var imgProductId = jQuery(this).attr('product_id');
			var imgSrc = jQuery(this).children().attr('src');
			
			var url = '/wp-json/provia/v1/provia_saveproject/image/';
			
			var data = 
			{
					project_name : project,
					image_style : imgStyle,
					image_x : imgDataX,
					image_y: imgDataY,
					image_src: imgSrc,
					image_product: imgProductId,
					user_id : "<?php echo base64_encode($userid); ?>"
			};
			
			try
			{
				jQuery.post( url, data, function(result) {
					var id = parseInt(result);
					if(id > 0)
					{
						//assign to image
						jQuery(this).children().attr('image-id', id);
					}
				});
			}
			catch(e)
			{
				alert('saveProjectImages() ERROR: ' + e);
			}
			
		});
		
	}
	
	function filterImages(dropdown)
	{
		
		//debugger;
		
		//get selected project
		var projectId = parseInt(jQuery(dropdown).val()); 
		var projectName = jQuery("#project-lists option:selected" ).text();
		
		//set selected projectid
		jQuery('#list_id').val(projectId);
		
		//set project name to textbox
		if(projectName != null && projectName != "" && projectId > 0)
		{
			jQuery('#myproject-name').val(projectName);
		}
		
		if(projectId == -1)
		{
			jQuery('#myproject-name').val('');
		}
		
		//loop through all images
		jQuery('div', '#my-projects-container').each(function () {
			
			var wishListId = jQuery(this).attr('wishlist-id');
						
			if(projectId == "-1")
			{
				jQuery(this).removeClass( "drag-drop-hidden" );
			}
			else if(projectId == wishListId)
			{
				jQuery(this).removeClass( "drag-drop-hidden" );
			}
			else
			{
				jQuery(this).addClass( "drag-drop-hidden" );
			}
			
		});
		
	}
	
	interact('.drag-drop').draggable({
		// enable inertial throwing
		inertia: true,
		// enable autoScroll
		autoScroll: true,
		listeners: {
		  // call this function on every dragmove event
		  move: dragMoveListener
		}
	});
	  
	interact('.drag-drop').resizable({
		edges: { top: true, left: true, bottom: true, right: true },
		listeners: {
		  move: function (event) {
			let { x, y } = event.target.dataset

			x = (parseFloat(x) || 0) + event.deltaRect.left
			y = (parseFloat(y) || 0) + event.deltaRect.top

			Object.assign(event.target.style, {
			  width: `${event.rect.width}px`,
			  height: `${event.rect.height}px`,
			  transform: `translate(${x}px, ${y}px)`
			})

			Object.assign(event.target.dataset, { x, y })
		  }
		}
	});

	function dragMoveListener (event) {
	  var target = event.target
	  // keep the dragged position in the data-x/data-y attributes
	  var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
	  var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

	  // translate the element
	  target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'

	  // update the posiion attributes
	  target.setAttribute('data-x', x)
	  target.setAttribute('data-y', y)
	}

	// this function is used later in the resizing and gesture demos
	window.dragMoveListener = dragMoveListener

	

</script>