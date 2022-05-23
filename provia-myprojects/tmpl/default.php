<?php
	
	//get current user and asspociated images
	$user = wp_get_current_user();
	$userid = 0;
	$image_html = '';
	$project_html = '';
	$showcanvas = false;
	$project_id_default = -1;
	
	if(isset($user))
	{
		$userid = $user->ID;
	}
	
	//check for defaulted project id from another page
	if(isset($_GET['myprojects_projectid']))
	{
		$project_id_default = filter_var($_GET['myprojects_projectid'], FILTER_SANITIZE_NUMBER_INT);
	}
	
	if($userid > 0)
	{
		//create project dropdown
		$sql = "SELECT project_id, project_name, canvas_width, canvas_height FROM wp_provia_projects ";
		$sql .= "where (deleted IS NULL OR deleted = 0) AND userid = ".$userid;
		
		$result = $GLOBALS['wpdb']->get_results($sql);
		
		$project_html = '<select name="project-lists" id="project-lists">';
		$project_html .= '<option value="-1">- My Vision Boards -</option>';
		foreach ( $result as $project )
		{
			
			$selected = '';
			if($project_id_default == $project->project_id)
			{
				$selected = ' SELECTED';
			}
			
			$project_html .= '<option canvas_height="'.$project->canvas_height.'" canvas_width="'.$project->canvas_width.'" value="'.$project->project_id.'"'.$selected.'>'.$project->project_name.'</option>';
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

	<div class="myprojects-input-container">
		<?php echo $project_html; ?>
		<input type="text" name="myproject-name" id="myproject-name" value="" placeholder="Enter Vision Board Name" />
	</div>
	
	<div class="myprojects-button-container">
		<a href="javascript:void(0);" id="save-project"><img src="/wp-content/plugins/provia-myprojects/images/save.png" width="35"/></a>
		<a href="javascript:void(0);" id="refresh-project"><img src="/wp-content/plugins/provia-myprojects/images/refresh5.png" width="35"/></a>
		<a href="javascript:void(0);" id="save-file"><img src="/wp-content/plugins/provia-myprojects/images/download.png" width="35"/></a>
	</div>
	
	<div class="myprojects-social-container">
		<a href="javascript:void(0);" id="save-facebook"><img src="/wp-content/plugins/provia-myprojects/images/facebook.png" width="35"/></a>
		<a href="javascript:void(0);" id="save-twitter"><img src="/wp-content/plugins/provia-myprojects/images/twitter.png" width="35"/></a>
	</div>
	
	<input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id_default; ?>" />
	<input type="hidden" name="project_userid" id="project_userid" value="<?php echo $userid; ?>" />
	
	</div>
</div>

<div id="my-projects-overlay" style="display:none;"></div>

<div class="my-projects-rightcol">
<div id="my-projects-textsearch"><input type="text" id="my-projects-textsearch-input" placeholder="Filter Images" onkeyup="filterProjectImages();" /></div>
<div id="my-projects-images"></div>
</div>

<div id="my-projects-container"></div>

<script>
	
	var maxSaveCount = 0;
	var maxSaveLimit = 8;
	var canvasAdjustWidth = 0;
	var canvasAdjustHeight = 0;
	var defaultProjectName = 'Untitled Vision Board';
	
	jQuery(document).ready(function() {
		
		//check for untitled vision board if found select it by default
		var project_id = jQuery("#project-lists option:contains('" + defaultProjectName + "')").val();
		
		if(project_id != null && project_id != "-1")
		{
			jQuery('#project-lists').val(project_id);
		}
		
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
		
		jQuery("#save-file").click(function () {
			saveProject('file');
		});
		
		//enable autosave every 2.5 min up to 8 times
		setInterval('saveProject("autosave");', 160000);
		
		jQuery('body').on('click', 'a.myprojects-close-image', function() {
			
			//debugger;
			
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
	
	function filterProjectImages()
	{
		
		var searchText = jQuery('#my-projects-textsearch-input').val().toLowerCase();
		var searchTextLength = searchText.length;
		
		if(searchTextLength >= 4)
		{
			
			//debugger;
			
			jQuery("#my-projects-images div.toolset-image").each(function() {
				
				//make sure item is not already hidden
				var productItemDisplay = jQuery(this).attr('style');
				
				if(productItemDisplay == "")
				{
					var productTitle = jQuery(this).find(".product-title");
					var productTerms = jQuery(this).find(".product-searchterms");
					
					var productTitleName = jQuery(productTitle[0]).html().toLowerCase();
					var productTermsName = jQuery(productTerms[0]).html().toLowerCase();
					
					if(productTermsName != null && productTermsName != "")
					{
						productTitleName = productTitleName + " " + productTermsName;
					}
					
					if(productTitleName != "")
					{
						var productTitleResult = productTitleName.indexOf(searchText);
							
						//hide if not found
						if(productTitleResult == -1)
						{
							jQuery(this).attr('style', 'display:none;');
							jQuery(this).attr('textsearch', 'true');
						}
					}
				}
				
			});
		}
		else if(searchTextLength == 0)
		{
			//reset search items
			jQuery("#my-projects-images div.toolset-image").each(function() {
				
				var productSearchFilter = jQuery(this).attr('textsearch');
				if(productSearchFilter == "true")
				{
					jQuery(this).attr('style', '');
					jQuery(this).attr('textsearch', '');
				}
				
			});
		}
		
	}
	
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
		var project_id = jQuery('#project-lists').val();
		var projectName = jQuery("#project-lists option:selected" ).text();
			
		//look for current/previously saved width and height
		var canvasWidth = Math.round(jQuery('#my-projects-container').width());
		var canvasHeight = Math.round(jQuery('#my-projects-container').height());
		var canvasHeightPrev = jQuery('#project-lists option:selected').attr('canvas_height');
		var canvasWidthPrev = jQuery('#project-lists option:selected').attr('canvas_width');
		
		if(canvasHeightPrev != null && canvasHeightPrev != "")
		{
			canvasHeightPrev = parseInt(canvasHeightPrev);
		}
		else
		{
			canvasHeightPrev = 0;
		}
		
		if(canvasWidthPrev != null && canvasWidthPrev != "")
		{
			canvasWidthPrev = parseInt(canvasWidthPrev);
		}
		else
		{
			canvasWidthPrev = 0;
		}
		
		//figure out image adjustment
		if(canvasWidthPrev > 0 && canvasWidthPrev != canvasWidth)
		{
			canvasAdjustWidth = canvasWidthPrev - canvasWidth;
		}
		
		if(canvasHeightPrev > 0 && canvasHeightPrev != canvasHeight)
		{
			canvasAdjustHeight = canvasHeightPrev - canvasHeight;
		}
		
		//set selected projectid
		jQuery('#project_id').val(project_id);
		
		//set project name to textbox
		if(projectName != null && projectName != "" && project_id > 0)
		{
			jQuery('#myproject-name').val(projectName);
		}
		
		if(project_id == -1)
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
			
			//debugger;
			
			//load canvas images
			if(project_id != null && parseInt(project_id)  > 0)
			{
				url = "/wp-json/provia/v1/provia_getproject/getimages/?uid=<?php echo $userid; ?>" + '&project_id=' + project_id + '&adjust_width=' + canvasAdjustWidth + '&adjust_height=' + canvasAdjustHeight;
				
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
	
	function saveProject(saveOperation)
	{
		
		//debugger;
		
		//check for valid user, if not found do not allow save
		var userid_check = parseInt(jQuery('#project_userid').val());
		
		if(userid_check == null)
		{
			return;
		}
		
		if(userid_check <= 0)
		{
			return;
		}
		
		//show loading overlay
		showHideLoading(true, 'Saving...');
		
		//set defaults		
		if(saveOperation == null)
		{
			saveOperation = '';
		}
		
		var project = jQuery('#myproject-name').val();
		var project_id = jQuery('#project_id').val();
			
		//validate input only for non-autosave
		if(saveOperation != "autosave")
		{
			if(project == "" || project == "")
			{
				alert('Project name is missing, please enter to continue save');
				showHideLoading(false);
				return;
			}
		}
		
		if(saveOperation == "autosave")
		{
			
			//debugger;
			
			//check if items have been moved to canvas before enabling auto-save
			var numImageItems = jQuery('#my-projects-container .drag-drop').length;
			if(numImageItems == 0)
			{
				showHideLoading(false);
				return;
			}
			
			//debugger;
			showHideLoading(false);
			maxSaveCount += 1;
			
			//only allow 25 auto save
			if(maxSaveCount >= maxSaveLimit)
			{
				return;
			}
			
			//default project name if not found for autosave
			if(project == "")
			{
				
				jQuery('#myproject-name').val(defaultProjectName);
				project = defaultProjectName;
				
				//check if untitled vision board is already saved and select in dropdown
				jQuery("#project-lists option:contains('" + defaultProjectName + "')").prop('selected',true);
				
			}
			
		}
		
		//create snapshot of canvas
		html2canvas(document.querySelector("#my-projects-container")).then(canvas => {
			
			//debugger;
			
			var img = canvas.toDataURL('image/jpeg', 0.5);

			// Send the screenshot to PHP to save it on the server
			var url = '/wp-json/provia/v1/provia_saveproject/default/';
			var screenWidth = Math.round(window.screen.width);
			var screenHeight = Math.round(window.screen.height);
			var canvasWidth = Math.round(jQuery('#my-projects-container').width());
			var canvasHeight = Math.round(jQuery('#my-projects-container').height());
			
			var data = 
			{
				project_image : img,
				project_name : project,
				project_id: project_id,
				user_id : "<?php echo base64_encode($userid); ?>",
				screen_width: screenWidth,
				screen_height: screenHeight,
				canvas_width: canvasWidth,
				canvas_height: canvasHeight
			};
			
			jQuery.post( url, data, function(result) {
				
				if(result != null && result != "")
				{
					
					//debugger;
					
					var project_result = JSON.parse(result);
					var id = parseInt(project_result[0]);
					var imagePath = project_result[1];
										
					if(id > 0)
					{
						
						//set as current list
						jQuery('#project_id').val(id);
						
						//save individual images in the background
						saveProjectImages();

						if(saveOperation != '')
						{
							if(saveOperation == 'facebook')
							{
								shareToFacebook(imagePath);
							}
							else if(saveOperation == 'twitter')
							{
								shareToTwitter(imagePath);
							}
							else if(saveOperation == 'file')
							{
								downloadFile(imagePath);
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
		var project_id = jQuery('#project_id').val();
		
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
					project_id : project_id,
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
		var hash_tags = "ProVia,ProViaVisionBoard";

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
		var ShareUrl = 'https://www.facebook.com/sharer.php?quote=My ProVia Vision Board!&u=' + imagePath;
		window.open(ShareUrl,"NewWindow" , params);  
	}
	
	function downloadFile(imagePath) 
	{
		window.location.href = '/wp-content/plugins/provia-myprojects/tmpl/downloadfile.php?f=' + imagePath;
    }
	
	interact('.drag-drop').draggable({
		// enable inertial throwing
		inertia: false,
		// enable autoScroll
		autoScroll: true,
		listeners: {
		  // call this function on every dragmove event
		  move: dragMoveListener
		},
		modifiers: [
			interact.modifiers.restrictRect({
			  restriction: 'parent'
			})
		  ]
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
			
			var positionContLeft = document.querySelector('#my-projects-container').getBoundingClientRect().left;
			var positionContTop = document.querySelector('#my-projects-container').getBoundingClientRect().top;
			var positionEventX = event.clientX;
			var positionEventY = event.clientY;
			var positionLeft = parseInt(positionEventX) - parseInt(positionContLeft) - 90;
			var positionTop = parseInt(positionEventY) - parseInt(positionContTop) - 5;
			
			//get image height and width
			var cloneWidth = '25%';
			var cloneWidthOrig = 0;
			var cloneHeightOrig = 0; 
			
			//get image from clone
			var cloneImages = jQuery(clone).find('img');
			
			try
			{
				cloneWidthOrig = cloneImages[1].width; 
				cloneHeightOrig = cloneImages[1].height;  
			}
			catch(e){}
			
			//adjust width down for taller images
			if(cloneHeightOrig > cloneWidthOrig)
			{
				cloneWidth = '15%';
			}
			
			if(positionLeft < 0)
			{
				positionLeft = 0;
			}
			
			if(positionTop < 0)
			{
				positionTop = 0;
			}
			
			//debugger;
			
			//update clone position
			clone.style.position = 'absolute';
			clone.style.left = positionLeft + 'px';
			clone.style.top = positionTop + 'px';
			clone.style.width = cloneWidth;
			
			// insert the clone to the page
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

	function getOffset( el ) {
		var _x = 0;
		var _y = 0;
		while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
			_x += el.offsetLeft - el.scrollLeft;
			_y += el.offsetTop - el.scrollTop;
			el = el.offsetParent;
		}
		return { top: _y, left: _x };
	}

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
<div id="my-projects-overlay">To begin using the Vision Board <a href="/login">Sign-in or Register for an account here</a></div>
<div id="my-projects-container"></div>
<?php } ?>
