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
	
	//load images from all projects
	$sql = "SELECT ti_i.ID wishlistitem_id, ti_i.wishlist_id, u.ID as user_id, p.ID as product_id  ";
	$sql .= "FROM wp_tinvwl_items ti_i ";
	$sql .= "inner join wp_users u on u.ID = ti_i.author ";
	$sql .= "inner join wp_posts p on p.ID=ti_i.product_id ";
	$sql .= "where p.post_status = 'publish' and p.post_type='product' and ti_i.author = ".$userid;
	
	$result = $GLOBALS['wpdb']->get_results($sql);

	foreach ( $result as $product )
	{
		$product_id = $product->product_id;
		$wishlist_id = $product->wishlist_id;
		
		$query_thumb = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_thumbnail_id' AND post_id = ".$product_id;
		$result_query = $GLOBALS['wpdb']->get_results($query_thumb);
		$thumb_post_id = $result_query[0]->meta_value;
		
		if($thumb_post_id != "")
		{
		
			$query_file = "SELECT meta_value FROM wp_postmeta WHERE meta_key ='_wp_attached_file' AND post_id = ".$thumb_post_id;
			$result_query = $GLOBALS['wpdb']->get_results($query_file);
			$attached_file_path = $result_query[0]->meta_value;
			
			if($attached_file_path != "")
			{
				$image_html .= '<div class="drag-drop" wishlist-id="'.$wishlist_id.'">';
				$image_html .= '<img src="/wp-content/uploads/'.$attached_file_path.'" class="myprojects-image" />';
				$image_html .= '</div>';
			}
		
		}
		
	}

?>
<link href="/wp-content/plugins/provia-myprojects/css/myprojects.css" rel="stylesheet" type="text/css" />
<script src="/wp-content/plugins/provia-myprojects/js/interact.min.js"></script>
<script src="/wp-content/plugins/provia-myprojects/js/html2canvas.min.js"></script>

<div id="my-projects-save">
	<?php echo $project_html; ?>
	<input type="text" name="myproject-name" id="myproject-name" value="My New Project" />
	<input type="hidden" name="list_id" id="list_id" value="" />
	<a href="javascript::void(0);" id="save-project"><img src="/wp-content/plugins/provia-myprojects/images/pencil.svg" width="25"/></a>
</div>

<div id="my-projects-container">
	<?php echo $image_html; ?>
</div>

<script>
	
	jQuery(document).ready(function() {
		
		jQuery("#project-lists").change(function () {
			filterImages(this);
		});
		
		jQuery("#save-project").click(function () {
			saveProject();
		});
		
	});
	
	function saveProject()
	{
		
		debugger;
		
		//create snapshot of canvas
		html2canvas(document.querySelector("#my-projects-container")).then(canvas => {

			var img = canvas.toDataURL();

			// Send the screenshot to PHP to save it on the server
			var url = '/provia/v1/provia_saveproject/image/';
			jQuery.ajax({ 
				type: "POST", 
				url: url,
				dataType: 'text',
				data: {
					project_image : img,
					user_id : <?php echo $userid; ?>
				}
			});
			
		});
		
		//attempt to save or update project first
	}
	
	function filterImages(dropdown)
	{
		
		//debugger;
		
		//get selected project
		var projectId = jQuery(dropdown).val(); 
		
		//set selected projectid
		jQuery('#list_id').val(projectId);
		
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