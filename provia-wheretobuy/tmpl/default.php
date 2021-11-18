<?php 


$randnum = rand();
$audience = get_query_var('audience');

//roofing defaults based on querystring value
$roofing_checked = '';
$zipcode = '';

if(isset($_GET['roofing']))
{
	$roofing_checked = ' CHECKED';
}

if(isset($_GET['zipcode']))
{
	$zipcode = $_GET['zipcode'];
	$zipcode = filter_var($zipcode, FILTER_SANITIZE_STRING);
}

$query_str = '/where-to-buy-2/';

if(isset($_GET['audience']))
{
	$query_str = $query_str. "?audience=".$_GET['audience'];
}

?>
<link href="/wp-content/plugins/provia-wheretobuy/css/where-to-buy.css" rel="stylesheet" type="text/css" />
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBBNzHIIdHxWk68i_x0iPmcu3mz-iAu28I" type="text/javascript"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/jquery-validate/jquery.validate.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/jquery-validate/jquery.validate.unobtrusive.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/jquery-validate/jquery.form.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/tooltipster-master/dist/js/tooltipster.bundle.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/wheretobuy.js?version=<?php echo  $randnum; ?>"></script>
<script type="text/javascript" src="/wp-content/plugins/provia-wheretobuy/scripts/preferreddealer.js?version=<?php echo  $randnum; ?>"></script>
<style type="text/css">
    .ui-dialog {
        z-index: 1002 !important;
        margin-left: 20%;
        margin-top: 100px;
    }
    #nav {
        padding: 10px;
    }
	#cnt-tabbed-content td
	{
		padding: 16px;
	}
	.tooltip-certified
	{
		width: 200px;
	}
	.tooltip_templates 
	{ 
		display: none; 
	}
	.tooltip-highlight-text
	{
		color:#799915;
	}
</style>


<div class="container-fluid">
<div class="container">

<div class="error-msg"><h3>Please correct the following issues:</h3></div>
<div id="instructions">Please fill out all required information. Dealer Results will be displayed below. <span style="color:#000; font-style:italic; font-size: 13px;">All fields required</span></div>
<section style="z-index:1;" id="sec-form">
	
	<fieldset id="fs-audience" style="display:none;">
		<h2>I AM A:</h2>
		<input type="radio" value="R" name="fld-type" id="fld-type-homeowner" checked="checked">
		<label for="fld-type-homeowner">HOMEOWNER</label><br>
		<input type="radio" value="D" name="fld-type" id="fld-type-professional">
		<label for="fld-type-professional">PROFESSIONAL</label><br><br>

	</fieldset>
	
	<fieldset id="fs-zip">
		<div class="homeOwner" style="display:inline;margin-top: 10px;">
			<h2>ENTER ZIP CODE:</h2>
			<input type="text" value="<?php echo $zipcode; ?>" class="txtfld" name="fld-zip" id="fld-zip">
		</div>
		<div class="professional" style="display: none;">
			<h2>My Profession<span class="required">&nbsp;</span></h2>
			<select class="txtfldprofessional" id="fld-profession">
				<option value="-1">- select one -</option>
				<option value="4">Architect</option>
				<option value="5">Contractor</option>
				<option value="2">Dealer</option>
				<option value="3">Distributor</option>
				<option value="6">Installer</option>
				<option value="7">Multi-Family Property Manager</option>
			</select><br>
			<h2 class="fieldspace">First Name<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-first-name" id="fld-first-name"><br>
			<h2 class="fieldspace">Last Name<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-last-name" id="fld-last-name"><br>
			<h2 class="fieldspace">Email<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-email" id="fld-email"><br>
		</div>
	</fieldset>
	<fieldset id="fs-product">

		<div class="professional" style="display: none;">
			<h2>Company Name<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-company-name" id="fld-company-name"><br>
			<h2 class="fieldspace">Business Address<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-address" id="fld-address"><br>
			<div class="professionaladdress">
				<div>
					<h2 class="fieldspace">City<span class="required">&nbsp;</span></h2>
					<input type="text" class="txtfld" name="fld-city" id="fld-city">
				</div>
				<div>
					<h2 class="fieldspace">State<span class="required">&nbsp;</span></h2>
					<input type="text" class="txtfldprofessional" name="fld-state" id="fld-state">
				</div>
				<div>
					<h2 class="fieldspace">Zip<span class="required">&nbsp;</span></h2>
					<input type="text" class="txtfldprofessional" name="fld-form-zip" id="fld-form-zip">
				</div>
			</div>
			<h2 style="clear:both;" class="fieldspace">Phone<span class="required">&nbsp;</span></h2>
			<input type="text" class="txtfldprofessional" name="fld-phone" id="fld-phone"><br>
		</div>

		<div style="display: none;" class="dialog">

			<div style="text-align:left; padding-left: 10px;" class="connectme-container">
				<h1>Connect Me</h1>
				<h3>Please fill out the information below to connect with <span id="connectme_custname"></span></h3>
				<div style="width: 500px;" class="connect-error-msg"></div>
				<table cellspacing="0" cellpadding="2">
					<tbody><tr>
						<td>First Name<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-firstname" id="connect-firstname"></td>
					</tr>
					<tr>
						<td>Last Name<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-lastname" id="connect-lastname"></td>
					</tr>
					<tr>
						<td>Address<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-address" id="connect-address"></td>
					</tr>
					<tr>
						<td>City<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfld" name="connect-city" id="connect-city"></td>
					</tr>
					<tr>
						<td>State<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-state" id="connect-state"></td>
					</tr>
					<tr>
						<td>Zip<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-form-zip" id="connect-form-zip"></td>
					</tr>
					<tr>
						<td>Email<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-email" id="connect-email"></td>
					</tr>
					<tr>
						<td>Phone<span class="required">&nbsp;</span></td>
						<td><input type="text" class="txtfldprofessional" name="connect-phone" id="connect-phone"></td>
					</tr>
					<tr>
						<td>Additional Comments<span class="required">&nbsp;</span></td>
						<td><textarea class="txtfldprofessional" name="connect-comments" id="connect-comments"></textarea></td>
					</tr>
					<tr>
						<td><input type="hidden" name="connect-custno" id="connect-custno"></td>
						<td><div style="padding: 5px; width: 75px;" class="btn" id="connect-submit">Submit</div></td>
					</tr>
				</tbody></table>
			</div>

		</div>

		<div class="selectproducts" style="clear:both;">
			<h2>SELECT PRODUCT(S): <i style="font-weight:normal;">Select at least one</i><span class="required">&nbsp;</span></h2>
			<div id="fld-prod-col1">
				<input type="checkbox" value="Entry Doors" name="fld-prod" id="fld-prod-entry">
				<label for="fld-prod-entry">ENTRY DOORS</label><br>
				
				<input type="checkbox" value="Storm Doors" name="fld-prod" id="fld-prod-storm">
				<label for="fld-prod-storm">STORM DOORS</label><br>
				
				<input type="checkbox" value="Patio Doors" name="fld-prod" id="fld-prod-patio">
				<label for="fld-prod-patio">PATIO DOORS</label><br>
				
				<input type="checkbox" id="fld-prod-windows-vinyl" name="fld-prod" value="windows-vinyl" />
				<label for="fld-prod-windows-vinyl">VINYL WINDOWS</label><br />
				
			</div>

			<div id="fld-prod-col2">

				<input type="checkbox" id="fld-prod-windows-storm" name="fld-prod" value="windows-storm" />
				<label for="fld-prod-windows-storm">STORM WINDOWS</label><br />

				<input type="checkbox" value="Stone Veneer" name="fld-prod" id="fld-prod-stone">
				<label for="fld-prod-stone">MANUFACTURED STONE</label><br>
				
				<span id="metal-roofing-container">
				<input type="checkbox" value="Metal Roofing" name="fld-prod" id="fld-prod-roofing" <?php echo $roofing_checked ?>>
				<label for="fld-prod-roofing">METAL ROOFING</label><br>
				</span>
				
				<input type="checkbox" value="Vinyl Siding" name="fld-prod" id="fld-prod-siding">
				<label for="fld-prod-siding">VINYL SIDING</label><br>
				
				<div style="display:none;" id="fld-prod-siding-details">
					<input type="radio" checked="" value="R" name="fld-prod-siding" id="fld-prod-siding-contractors">
					<label style="width:300px;">Show me Contractors/Installers who sell and install siding</label><br>
					<input type="radio" value="D" name="fld-prod-siding" id="fld-prod-siding-distributors">
					<label style="width:300px;">Show me Distributors who sell siding (I already have a Contractor/Installer)</label><br>
				</div>

			</div>

			</div>
	</fieldset>
	<fieldset class="homeOwner" id="fs-filter" style="display:inline;margin-top: 10px;">
		<h2>SHOW ONLY (OPTIONAL):</h2>
		<ul>
			<li class="col-md-3"id="recognition-type-platiumn-container" style="display:none;">
				<input type="checkbox" value="Platium Club" name="recognition-type" id="recognition-type-platiumn">
				<label for="recognition-type-platiumn">PLATINUM DEALER:</label>
				<p>ProVia's performance-based recognition program for those who have demonstrated the highest level of commitment to selling and installing our products.</p>
			</li>
			<!--
			<li>
				<input type="checkbox" id="recognition-type-embarq" name="recognition-type" value="Embarq Dealer" />
				<label for="recognition-type-embarq">EMBARQ DEALER:</label>
				<p>Embarq Fiberglass Doors with EnVision Innovation are currently available through select dealers.</p>
			</li>
			-->
			<li class="col-md-3" id="recognition-type-certified-container" style="display:none;">
				<input type="checkbox" value="Certified Installer" name="recognition-type" id="recognition-type-certified">
				<label for="recognition-type-certified">CERTIFIED INSTALLER:</label>
				<p>Companies bearing this symbol have completed specialized training for installing ProVia products in a professional-class manner.</p>
			</li>
			<li class="col-md-3" id="recognition-type-studio-container" style="display:none;">
				<input type="checkbox" value="Provia Studio" name="recognition-type" id="recognition-type-studio">
				<label for="recognition-type-studio">VISUALIZATION:</label>
				<p>Dealers who utilize ProVia's visualization tools (ProVia iPad App or entryLINK Spec Sheet) to virtually show you your products before work begins.</p>
			</li>
			<!--
			<li>
				<input type="checkbox" id="recognition-type-aeris" name="recognition-type" value="Exclusive Aeris Dealer" />
				<label for="recognition-type-aeris">AERIS DEALER:</label>
				<p>Aeris Dealers have earned the distinction of having exclusive rights to selling this exceptional collection of windows and patio doors.</p>
			</li>
			-->
		</ul>
	</fieldset>
	<fieldset id="fs-action">
		<input type="button" value="get results" class="btn" name="fld-wtb-go" id="fld-wtb-go">
	</fieldset>
	<div class="loading"></div>
	<div class="clear"></div>
</section>

<nav style="width: 902px">
	<div id="results_message"></div>
	<div id="map_container" style="background-color:#fff;">
		<ul class="tabbed">
		<li class="active"><a id="tab-map" href="<?php echo $query_str; ?>#map">LIST AND MAP</a></li>
		<li class="inactive"><a id="tab-list" href="<?php echo $query_str; ?>#list">LIST ONLY</a></li>
		</ul>
	</div>
</nav>
<br />
<section id="sec-listings" style="background-color:#fff;">
	<div class="clear"><!--ie--></div>

	<a name="map_anchor"></a>

	<div id="map-canvas-conatiner">
		<div id="map-canvas"></div>
	</div>
	
	<!--
	<table cellspacing="0" cellpadding="0" style="width: 100%" id="cnt-tabbed-content">
		<tbody><tr>
			<th id="col-business">BUSINESS NAME</th>
			<th id="col-contact">CONTACT INFORMATION</th>
			<th id="col-products">PRODUCTS OFFERED</th>
			<th id="col-certifications">RECOGNITIONS</th>
		</tr>
		
	<tr class="listing list-def"><td colspan="4">Enter your search criteria to view locations in your area</td></tr></tbody></table>
	-->
	
	<div id="cnt-search-results"></div>
	
</section>

</div>
</div>

<script type="text/javascript">

jQuery(document).ready(function () {

	jQuery(".mapkey img").tooltip({ track: true,
		delay: 0,
		showURL: false,
		showBody: " - ",
		extraClass: "pretty",
		fixPNG: true,
		opacity: 0.95,
		left: -120
	});
				
    jQuery(".mapkey").hide(); 

    jQuery("#fld-wtb-go").click(function () { 
	
		//get results
		getLocations(jQuery('.tabbed .active a').attr('id'), "1");
		
		//get authorized dealers, wait until locations display
		setTimeout(function(){ 
			jQuery('a#authorized-contractors-button').trigger('click');
		 }, 8000);
		
	});
	
    jQuery(".listing .btn").on("click", function(event) { getDirections(event) });
    jQuery("#tab-map").click(function() {  jQuery("#tab-list").parent().removeClass("active").addClass("inactive"); jQuery("#tab-map").parent().addClass("active"); jQuery("#fld-wtb-go").click(); });
    jQuery("#tab-list").click(function() {  jQuery("#tab-map").parent().removeClass("active").addClass("inactive"); jQuery("#tab-list").parent().addClass("active"); jQuery("#fld-wtb-go").click(); });
    
    jQuery("#fs-audience input").change(function changeAudience() { 
        setFormDefaults();        
    });

    jQuery("input").keypress(function(e) { if(e.keyCode == 13) getLocations(jQuery('.tabbed .active a').attr('id'), "1"); });

    //hide or show 
    jQuery("#fld-prod-siding").click(function() { 
    
        //check to make sure that options only available to homeowners
        if (jQuery('#fld-type-homeowner').is(':checked')) {
            if (jQuery('#fld-prod-siding').is(':checked')) {
                //show details
                jQuery('#fld-prod-siding-details').attr('style', 'display:inline;');
            } else {
                //hide details
                jQuery('#fld-prod-siding-details').attr('style', 'display:none;');
            }  
        } 
        
    });

    jQuery("#connect-submit").click(function () { postConnectMeLeadCapture() });
    
    jQuery(document).delegate('span.btn_connectme', "click", function (event) {
        showLeadCapture(this); 
    });

    jQuery(document).delegate('a.text_connectme', "click", function (event) {
        showLeadCapture(this); 
    });
	
	jQuery(document).delegate('a#authorized-contractors-button', "click", function (event) {
		getLocations(jQuery('.tabbed .active a').attr('id'), "all", false);
	});
	
    //hide/show form elements based on selection
    setFormDefaults();

    //if zipcode is found in querystring perform embarq autosearch
    autoSearch();
	
	//hide/show additional parameters based on product selections
	jQuery("input[name=fld-prod]").click(function () { 
		hideShowParams();
	});

});

function hideShowParams()
{
	
	jQuery('#recognition-type-platiumn-container').attr('style', 'display:none;');
	jQuery('#recognition-type-studio-container').attr('style', 'display:none;');
	jQuery('#recognition-type-certified-container').attr('style', 'display:none;');
	
	//loop through all checkboxes and check all values
	jQuery("input[name=fld-prod]").each( function () {
	   
		var paramId = jQuery(this).attr('id');
		if(paramId == "fld-prod-entry" || paramId == "fld-prod-storm" || paramId == "fld-prod-patio" || paramId == "fld-prod-windows-vinyl" || paramId == "fld-prod-windows-storm")
		{
			if (jQuery(this).is(':checked')) {
				jQuery('#recognition-type-platiumn-container').attr('style', '');
				jQuery('#recognition-type-studio-container').attr('style', '');
			} 
		}
		
		if(paramId == "fld-prod-entry" || paramId == "fld-prod-storm" || paramId == "fld-prod-patio" || paramId == "fld-prod-windows-vinyl" || paramId == "fld-prod-roofing")
		{
			if (jQuery(this).is(':checked')) {
				jQuery('#recognition-type-certified-container').attr('style', '');
			} 
		}
	   
	});

} 

</script>

