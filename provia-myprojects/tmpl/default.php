<?php
	
	//get current user and asspociated images
	$user = wp_get_current_user();
	$userid = 0;
	$image_html = '';
	$project_html = '';
	$showcanvas = false;
	
	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	if($userid > 0)
	{
		//create project dropdown
		$sql = "SELECT ID, author, title FROM wp_tinvwl_lists ";
		$sql .= "where title <> '' and author = ".$userid;
		
		$result = $GLOBALS['wpdb']->get_results($sql);
		
		$project_html = '<select name="project-lists" id="project-lists">';
		$project_html .= '<option value="-1">All Project Items</option>';
		foreach ( $result as $list )
		{
			$project_html .= '<option value="'.$list->ID.'">'.$list->title.'</option>';
		}
		$project_html .= '</select>';
		$showcanvas = true;
	}
	

?>

<link href="/wp-content/plugins/provia-myprojects/css/myprojects.css" rel="stylesheet" type="text/css" />
<script src="/wp-content/plugins/provia-myprojects/js/interact.min.js"></script>
<script src="/wp-content/plugins/provia-myprojects/js/html2canvas.min.js"></script>

<?php if($showcanvas == true) { ?>

<div id="my-projects-save">
	<?php echo $project_html; ?>
	<input type="text" name="myproject-name" id="myproject-name" value="" placeholder="Enter Project Name Here" />
	<input type="hidden" name="list_id" id="list_id" value="" />
	<a href="javascript:void(0);" id="save-project"><img src="/wp-content/plugins/provia-myprojects/images/save.png" width="35"/></a>
	<a href="javascript:void(0);" id="refresh-project"><img src="/wp-content/plugins/provia-myprojects/images/refresh5.png" width="35"/></a>
	<a href="javascript:void(0);" id="save-facebook"><img src="/wp-content/plugins/provia-myprojects/images/facebook.png" width="35"/></a>
	<a href="javascript:void(0);" id="save-twitter"><img src="/wp-content/plugins/provia-myprojects/images/twitter.png" width="35"/></a>
</div>

<div id="my-projects-overlay" style="display:none;"></div>
<div id="my-projects-images"></div>
<div id="my-projects-container"></div>

<script>
	
	jQuery(document).ready(function() {
		
		loadProjectImages();	
		
		jQuery("#project-lists").change(function () {
			
			var allowUpdate =  confirm("Are you sure you want to change the project?  Any unsaved changes may be lost!");
			
			if(allowUpdate)
			{
				loadProjectImages();
			}
			
		});
		
		jQuery('body').on('mouseover', '#my-projects-container div.drag-drop', function() {
			
			//debugger;
			
			var links = jQuery(this).children();

			if(links != null)
			{
				jQuery(links[0]).attr('style', '');
			}
			
		});
		
		jQuery('body').on('mouseout', '#my-projects-container div.drag-drop', function() {
			
			//debugger;
			
			var links = jQuery(this).children();

			if(links != null)
			{
				jQuery(links[0]).attr('style', 'display:none;');
			}
			
		});
		
		jQuery("#save-project").click(function () {
			saveProject();
		});
		
		jQuery("#save-facebook").click(function () {
			saveProject('facebook');
		});
		
		jQuery("#save-twitter").click(function () {
			saveProject('twitter');
		});
		
		jQuery('body').on('click', 'a.myprojects-close-image', function() {
			
			//get parent div (drag-drop)
			var parentDiv = jQuery(this).parent();
			
			if(parentDiv != null)
			{
				//remove element from canvas
				jQuery(parentDiv).remove();
				
				//sync hide show of toolset
				hideShowImagesInToolset();
				
			}
			
		});
		
		jQuery("#refresh-project").click(function () {
			
			var allowUpdate =  confirm("Are you sure you want to refresh the project?  Any unsaved changes may be lost!");
			
			if(allowUpdate)
			{
				loadProjectImages();
			}
		});
		
	});
	
	function showHideLoading(showDiv, displayText)
	{
		
		if(displayText == null)
		{
			displayText = '<img src="/wp-content/plugins/provia-myprojects/images/loading-buffering.gif" width="25" /> <span class="my-projects-overlay-text">Loading....</span>';
		}
		else
		{
			displayText = '<img src="/wp-content/plugins/provia-myprojects/images/loading-buffering.gif" width="25" /> <span class="my-projects-overlay-text">' + displayText + '</span>';
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
			
			//disable buttons from being clicked twice
			jQuery("#save-project").prop('disabled', true);
			jQuery("#refresh-project").prop('disabled', true);
			jQuery("#save-facebook").prop('disabled', true);
			jQuery("#save-twitter").prop('disabled', true);
			
		}
		else
		{
			jQuery('#my-projects-overlay').html(displayText);
			jQuery('#my-projects-overlay').attr('style', 'display:none;');
			
			//unlock project fields
			jQuery("#project-lists").prop('disabled', false);
			jQuery("#myproject-name").prop('disabled', false);
			
			//enable buttons again
			jQuery("#save-project").prop('disabled', false);
			jQuery("#refresh-project").prop('disabled', false);
			jQuery("#save-facebook").prop('disabled', false);
			jQuery("#save-twitter").prop('disabled', false);
			
		}
	}
	
	function loadProjectImages()
	{
		
		//debugger;
		
		showHideLoading(true);
		
		//set list text defaults
		var list_id = jQuery('#project-lists').val();
		var projectName = jQuery("#project-lists option:selected" ).text();
		
		//set selected projectid
		jQuery('#list_id').val(list_id);
		
		//set project name to textbox
		if(projectName != null && projectName != "" && list_id > 0)
		{
			jQuery('#myproject-name').val(projectName);
		}
		
		if(list_id == -1)
		{
			jQuery('#myproject-name').val('');
		}
		
		//get images and write html to div
		var url = "/wp-json/provia/v1/provia_getproject/getimages/?uid=<?php echo $userid; ?>&toolset=true";
		
		//load available images
		jQuery.get( url, function(result) {
			
			//clear existing html with replacement
			jQuery('#my-projects-images').html('');
			jQuery('#my-projects-container').html('');
			
			var image_html = result;
			if(image_html != null && image_html != "")
			{
				jQuery('#my-projects-images').html(image_html);
			}
			
			//load canvas images
			if(list_id != null && parseInt(list_id)  > 0)
			{
				url = "/wp-json/provia/v1/provia_getproject/getimages/?uid=<?php echo $userid; ?>" + '&list_id=' + list_id;
				
				jQuery.get( url, function(result) {
				
					//clear existing html with replacement
					jQuery('#my-projects-container').html('');
					
					var image_html = result;
					if(image_html != null && image_html != "")
					{
						jQuery('#my-projects-container').html(image_html);
						
						//hide any images already used in the toolset
						setTimeout("hideShowImagesInToolset();", 500);
						
					}
					
				});
			}
		
			showHideLoading(false);
			
			setTimeout("hideShowImagesInToolset();", 500);
			
		});
		
	}
	
	function saveProject(socialMediaType)
	{
		
		//debugger;
		
		//show loading overlay
		showHideLoading(true, 'Saving...');
		
		//set defaults		
		if(socialMediaType == null)
		{
			socialMediaType = '';
		}
		
		var project = jQuery('#myproject-name').val();
		var list_id = jQuery('#list_id').val();
		
		//validate input
		if(project == "" || project == "")
		{
			alert('Project name is missing, please enter to continue save');
			showHideLoading(false);
			return;
		}
		
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
				
				if(result != null && result != "")
				{
					
					debugger;
					
					var project_result = JSON.parse(result);
					var id = parseInt(project_result[0]);
					var imagePath = project_result[1];
										
					if(id > 0)
					{
						
						//set as current list
						jQuery('#list_id').val(id);
						
						//save individual images in the background
						saveProjectImages();

						if(socialMediaType != '')
						{
							if(socialMediaType == 'facebook')
							{
								shareToFacebook(imagePath);
							}
							else if(socialMediaType == 'twitter')
							{
								shareToTwitter(imagePath);
							}
						}
						
						setTimeout("hideShowImagesInToolset();", 500);
						
						showHideLoading(false);
												
					}
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
			
			//debugger;
			
			var imgStyle = jQuery(this).attr('style');
			var imgDataX = jQuery(this).attr('data-x');
			var imgDataY = jQuery(this).attr('data-y');
			var imgProductId = jQuery(this).attr('product_id');
			var imgChildren = jQuery(this).children();
			var imgSrc = null;
			
			if(imgChildren[1] != null)
			{
				imgSrc = jQuery(imgChildren[1]).attr('src');
			}
						
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
				}).fail(function(xhr, status, error) {
					console.log(status);
				});
			}
			catch(e)
			{
				alert('saveProjectImages() ERROR: ' + e);
			}
			
		});
		
	}
	
	function hideShowImagesInToolset()
	{
		
		//show all toolset images by default
		jQuery('#my-projects-images .drag-drop').each(function() {		
			jQuery(this).attr('style', '');
		}).promise()
		.done( function() {
			
			//debugger;
			
			//remove image duplicates from toolset, loop through toolset & canvas images and remove any images already in the canvas
			jQuery('#my-projects-container .drag-drop').each(function() {
				
				var childImageCont = jQuery(this).find('img');
				if(childImageCont != null && childImageCont[1] != null)
				{
					var childImageContSrc = jQuery(childImageCont[1]).attr('src');
					
					//cross check image in 
					jQuery('#my-projects-images .drag-drop').each(function() {
						
						var childImageToolset = jQuery(this).find('img');
						
						if(childImageToolset != null && childImageToolset[0] != null)
						{
							var childImageToolsetSrc = jQuery(childImageToolset[0]).attr('src');
							
							if(childImageToolsetSrc == childImageContSrc)
							{
								//hide image div
								jQuery(childImageToolset[0]).parent().attr('style', 'display:none;');
							}
						}
						
					});
				}
			});	

		});
		
	}
	
	function shareToTwitter(imagePath)
	{
		
		if(imagePath == null || imagePath == "")
		{
			return;
		}
		
		var text = encodeURIComponent("Provia: My Projects Image!");
		var url = "https://provia.com/";
		var user_id = "jagathish1123";
		var hash_tags = "ProVia,MyProjects";

		var params = "menubar=no,toolbar=no,status=no,width=570,height=570"; // for window
		var ShareUrl = 'https://twitter.com/intent/tweet?url=' + imagePath + '&text=' + text + '&hashtags=' + hash_tags;
		window.open(ShareUrl,"NewWindow" , params);  
	}
	
	function shareToFacebook(imagePath)
	{
		
		if(imagePath == null || imagePath == "")
		{
			return;
		}
		
		var params = "menubar=no,toolbar=no,status=no,width=570,height=570";
		var ShareUrl = 'https://www.facebook.com/sharer.php?u=https://provia.com?imageurl=' + imagePath;
		window.open(ShareUrl,"NewWindow" , params);  
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
	}).on('move', function (event) {
		var interaction = event.interaction
	
		// if the pointer was moved while being held down
		// and an interaction hasn't started yet
		if (interaction.pointerIsDown && !interaction.interacting()) {

			var original = event.currentTarget,
			// create a clone of the currentTarget element
			clone = event.currentTarget.cloneNode(true)

			//check to make sure we don't clone any elements in the main container
			var originalParentId = original.parentElement.id;	
			if(originalParentId == "my-projects-container")
			{
				return;
			}
			else
			{
				
				var originalParentCss = original.className;
				
				//hide the original image dragged to the my projects container
				jQuery(original).attr('style', 'display:none;');
			}
			
			//remove class: image-toolset
			clone.className = "drag-drop";
			
			//insert close icon div
			var closeLink = document.createElement('a');
			closeLink.href = 'javascript:void(0);';
            closeLink.innerHTML = '<img src="/wp-content/plugins/provia-myprojects/images/close.png" width="25"/>';
			closeLink.className = 'myprojects-close-image';
			closeLink.style = 'display:none;';
			clone.prepend(closeLink);
			
			// insert the clone to the page
			// TODO: position the clone appropriately
			document.getElementById("my-projects-container").appendChild(clone);

			// start a drag interaction targeting the clone
			interaction.start({ name: 'drag' }, event.interactable, clone);
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

<?php } else { ?>
<div id="my-projects-overlay">To begin using the Project Builder <a href="/login">Sign-in or Register for an account here</a></div>
<div id="my-projects-container"></div>
<?php } ?>
