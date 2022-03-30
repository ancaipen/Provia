<?php       

$dealertype = '';
$username = '';
$aeris = true;
$embarq = true;

//get url and parse out username and dealer type and aeris, embarq settings
if(isset($_GET['d']))
{
	$dealertype = base64_decode($_GET['d']);
}

if(isset($_GET['u']))
{
	$username = base64_decode($_GET['u']);
	$username = htmlentities($username);
}

if(isset($_GET['aeris']))
{
	$aeris_temp = strtolower(trim($_GET['aeris']));
	if($aeris_temp == 'n')
	{
		$aeris = false;
	}
}

if(isset($_GET['embarq']))
{
	$embarq_temp = strtolower(trim($_GET['embarq']));
	if($embarq_temp == 'n')
	{
		$embarq = false;
	}
}

?>
<div class="container-fluid">        
	

    <div id="iframe-container" style="padding:0 115px;">
	
	<div class="row">
	   	   
		<div class="col-lg-8">
		 
     <!-- form data here --> 
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div id="product_group_select" style="width:60%; float:left;">
                Product Group:<br />
                <select id="drpProductGroups">
                    <option value="-1" selected="selected">- select one -</option>
                    <option value="Doors - Entry Doors">Doors - Entry Doors</option>
                    <option value="Doors - Storm Doors">Doors - Storm Doors</option>
                    <option value="Doors - Patio Doors">Doors - Patio Doors</option>
                    <option value="Windows">Windows</option>
                    <option value="Vinyl Siding">Vinyl Siding</option>
                    <option value="Vinyl Soffit">Vinyl Soffit</option>
                    <option value="Manufactured Stone">Manufactured Stone</option>
					<option value="Metal Roofing">Metal Roofing</option>
                    <option value="ProVia Visualizer">ProVia Visualizer</option>
                </select>
                </div>

            <div id="products_select" style="float:left; width:30%; display:none;">
              
                Product/Page:<br />
                <div id="iframe_additionaltext"></div>
                <select id="drpProductPages"></select>
            </div>
       
           
</td>
          </tr>
        <tr style="display:none;">
            <td>
			
			<div id="advanced-features-container">
			
			<a href="javascript:void(0);" id="show_advanced_options" style="font-size:14px; width:60%;">Advanced Options</a>
			<p>(Customize your iFrame to complement your website width, colors, font size and style.)</p>
			<div id="advanced_options" class="clear" style="padding-top:15px; display:none;">

            <div style="float:left; width:31%;padding: 4px;">
              IFrame Width:<br />
              <select name="drpIframeWidth" id="drpIframeWidth">
                <option value="100%">Responsive (100%)</option>
                <option value="75%">75%</option>
                <option value="50%">50%</option>
                <option value="980">980 Pixels</option>
                <option value="800">800 Pixels</option>
                <option value="600">600 Pixels</option>
                <option value="400">400 Pixels</option>
              </select>              
              </div>
              
            <div style="float:left; width:100%;padding: 4px;">
            Font:<br />
              <select name="drpFontScheme" id="drpFontScheme">
                <option value="default">Default</option>
                <option value="times">Times New Roman</option>
                <option value="serif">Serif</option>
                <option value="arial">Arial</option>
                <option value="verdana">Verdana</option>
                <option value="mono">Mono Spaced</option>
              </select>
            </div>
			<div style="float:left; width:100%;padding: 4px;">
            Font Size:<br />
              <select name="drpFontSize" id="drpFontSize">
                <option value="default">Responsive</option>
                <option value="9px">9px</option>
                <option value="14px">10px</option>
                <option value="16px">12px</option>
                <option value="18px">14px</option>
                <option value="20px">16px</option>
              </select>
            </div>
			
			</div>
			
			</div>
			
			</td>
        </tr>
        <tr>
            <td>
				<div id="embed_container" style="display: none;">
				iFrame Embed Code:<br />
                <textarea id="final_html" style="width: 400px; height: 100px;" onclick="this.focus();this.select();"></textarea>
				</div>
            </td>
          </tr>
        <tr>
            <td>
                <input type="button" name="btnCreate" id="btnCreate" value="Generate iframe" />
            </td>
          </tr>
        </table>
        <div id="errors"></div>
        
        <input type="hidden" name="dealertype" id="dealertype" value="<?php echo $dealertype; ?>" />

		</div>
		
		<div class="col-lg-4">
			<ul class="aside-nav">
				<li><a href="/iframe-help" target="_blank">HELP GUIDE</a></li>
				<li><a href="/iframe-help-contact" target="_blank">CONTACT INFORMATION</a></li>
			</ul>
		</div>
	
	</div>
	

<p id="callback"></p>

<div style="clear:both">&nbsp;</div>
<div id="preview_message"></div>
<div id="preview_html"></div>

</div> 
        
 </div>

<script type="text/javascript" src="/wp-content/plugins/provia-iframe/scripts/iframe/proviaResize.js"></script>
<script language="javascript" type="text/javascript">
	
	var base_protocol = location.protocol.toString();
	var base_url = base_protocol + "//provia.proviaserver-v2.com"
	//var base_url = base_protocol + "//www.provia.com"
	
	jQuery("#show_advanced_options").click(function () {
		jQuery("#advanced_options").toggle();
    });

    jQuery("#btnCreate").click(function () {

        var err_msg = validateForm();
        var iframe_html = "";

        //blank out iframe code
        jQuery('#final_html').val('');
        jQuery('div#preview_message').html('');

        if (err_msg == "") {
            createIframeLink();
        }

    });

    jQuery('#drpProductGroups').change(function () {
		//show product dropdown
		jQuery('#products_select').attr('style', 'float:left; width:38%;');
		//get sub category
        getSubCategory();
    });

    function getSubCategory() {

        var _sel = jQuery('#drpProductGroups').val();
        
        jQuery('#iframe_additionaltext').html('');

        //clear selections
        jQuery('#drpProductPages').empty();

        if (_sel == 'Doors - Entry Doors') {
            <?php if($embarq) { ?>
                jQuery('#drpProductPages').append('<option value="/doors/entry-doors/embarq">Embarq</option>');
            <?php } ?>                        
            jQuery('#drpProductPages').append('<option value="/doors/entry-doors/signet">Signet</option>');
            jQuery('#drpProductPages').append('<option value="/doors/entry-doors/heritage">Heritage</option>');
            jQuery('#drpProductPages').append('<option value="/doors/entry-doors/legacy">Legacy</option>');
        }
        else if (_sel == 'Doors - Storm Doors') {
            jQuery('#drpProductPages').append('<option value="/doors/storm-doors/spectrum">Spectrum</option>');
            jQuery('#drpProductPages').append('<option value="/doors/storm-doors/decorator">Decorator</option>');
            jQuery('#drpProductPages').append('<option value="/doors/storm-doors/deluxe">Deluxe</option>');
            jQuery('#drpProductPages').append('<option value="/doors/storm-doors/duraguard">Duraguard</option>');
            jQuery('#drpProductPages').append('<option value="/doors/storm-doors/superview">Superview</option>');
        }
        else if (_sel == 'Doors - Patio Doors') {            
            <?php if($aeris) { ?>
                jQuery('#drpProductPages').append('<option value="/doors/patio-doors/aeris">Aeris&#8482; Patio Doors</option>');
            <?php } ?>
            jQuery('#drpProductPages').append('<option value="/doors/patio-doors/endure">Endure&#8482; Patio Doors</option>');
            jQuery('#drpProductPages').append('<option value="/doors/patio-doors/aspect">Aspect&#8482; Patio Doors</option>');
			jQuery('#drpProductPages').append('<option value="/doors/storm-doors/duraguard">Duraguard&#8482; Patio Doors</option>');
            jQuery('#drpProductPages').append('<option value="/doors/hinged-patio-doors">Designer&#8482; Patio Doors</option>');
			jQuery('#drpProductPages').append('<option value="/doors/patio-doors/ecolite">Ecolite&#8482; Patio Doors</option>');
        }
		/*
        else if (_sel == 'Doors - Door Glass') {
            //jQuery('#drpProductPages').append('<option value="/glass/inspirations-art">Inspirations&#8482; Art Glass</option>');
            jQuery('#drpProductPages').append('<option value="/windows/decorative-glass">Decorative Glass</option>');
            //jQuery('#drpProductPages').append('<option value="/glass/internal-blinds">Internal Blinds</option>');
            //jQuery('#drpProductPages').append('<option value="/glass/privacy">Privacy Collection</option>');
            //jQuery('#drpProductPages').append('<option value="/glass/clear-and-grids">Clear Glass and Grids</option>');
        }
		*/
        else if (_sel == 'Windows') {
            <?php if($aeris) { ?>
				jQuery('#drpProductPages').append('<option value="/windows/brands/aeris">Aeris&#8482; Windows</option>');
            <?php } ?>
            jQuery('#drpProductPages').append('<option value="/windows/brands/endure">Endure&#8482; Windows</option>');
            jQuery('#drpProductPages').append('<option value="/windows/brands/aspect">Aspect&#8482; Windows</option>');
            jQuery('#drpProductPages').append('<option value="/windows/brands/ecolite">ecoLite&#8482; Windows</option>');
            //jQuery('#drpProductPages').append('<option value="/aluminum-storm-windows">Storm Windows</option>');
        }
        else if (_sel == 'Vinyl Siding') {
            jQuery('#drpProductPages').append('<option value="/siding/cedarmax">CedarMAX</option>');
            jQuery('#drpProductPages').append('<option value="/siding/cedar-peaks">Cedar Peaks</option>');
            jQuery('#drpProductPages').append('<option value="/siding/hearttech">HeartTech</option>');
            //jQuery('#drpProductPages').append('<option value="/vinyl-siding/autumnwood">Autumnwood</option>');
            //jQuery('#drpProductPages').append('<option value="/vinyl-siding/arbor-glen">Arbor Glen</option>');
			jQuery('#drpProductPages').append('<option value="/siding/vinyl-soffit">Vinyl Soffit</option>');
			
            jQuery('#drpProductPages').append('<option value="/siding/ultra">Ultra</option>');
            //jQuery('#drpProductPages').append('<option value="/vinyl-siding/traditional">Traditional</option>');
            jQuery('#drpProductPages').append('<option value="/siding/timberbay">Timberbay</option>');
            jQuery('#drpProductPages').append('<option value="/siding/willowbrook">Willowbrook</option>');
        }
        else if (_sel == 'Vinyl Soffit') {
            jQuery('#drpProductPages').append('<option value="/siding/vinyl-soffit/">Vinyl Soffit</option>');
            //jQuery('#drpProductPages').append('<option value="/soffit/hearttech">HeartTech</option>');
            //jQuery('#drpProductPages').append('<option value="/soffit/beaded">Beaded / Wainscot</option>');
            //jQuery('#drpProductPages').append('<option value="/soffit/universal">Universal</option>');
        }
        else if (_sel == 'Manufactured Stone') {
            jQuery('#drpProductPages').append('<option value="/stone/ridge-cut">Ridge Cut</option>');
            jQuery('#drpProductPages').append('<option value="/stone/edge-cut">Edge Cut</option>');
            jQuery('#drpProductPages').append('<option value="/stone/chisel-cut">Chisel Cut</option>');
			jQuery('#drpProductPages').append('<option value="/stone/dry-stack">Dry Stack</option>');
			jQuery('#drpProductPages').append('<option value="/stone/terra-cut">Terra Cut</option>');
			jQuery('#drpProductPages').append('<option value="/stone/ledgestone">Ledgestone</option>');
			jQuery('#drpProductPages').append('<option value="/stone/limestone">Limestone</option>');
			jQuery('#drpProductPages').append('<option value="/stone/fieldstone">Field Stone</option>');
			jQuery('#drpProductPages').append('<option value="/stone/river-rock">River Rock</option>');
			jQuery('#drpProductPages').append('<option value="/stone/natural-cut-stone">Natural Cut</option>');
			jQuery('#drpProductPages').append('<option value="/stone/precision-fit">Precision Fit</option>');
			jQuery('#drpProductPages').append('<option value="/stone/grout-visualizer">Grout Visualizer</option>');
			//jQuery('#drpProductPages').append('<option value="/stone-calculator">Stone Calculator</option>');
        }
		else if (_sel == 'Metal Roofing') {
            jQuery('#drpProductPages').append('<option value="/metal-roofing">Metal Roofing</option>');
        }
        else if (_sel == 'ProVia Visualizer') {
		    jQuery('#drpProductPages').append('<option value="https://provia.renoworks.com/">ProVia Visualizer</option>');
            //append additional text
		    var _html_additional = '<p style="font-size:14px; color:red;"><b>**NOTE:</b> The Visualizer is designed for wider format website widths, at a minimum of 1065 pixels.  If the Visualizer does not render properly on your website within the iFrame code, we recommend linking directly to the Visualizer using this link:  <a href="http://provia.renoworks.com/" target="_blank">http://provia.renoworks.com/</a></p>';
		    jQuery('#iframe_additionaltext').html(_html_additional);
            //jQuery('#drpProductPages').append('<option value="http://provia.renoworks.com/en/">ProVia Visualizer - Renoworks</option>');
        }

    }

    function validateForm() {

        var err_msg = "";
        var err_html = "";

        //reset error message div
        jQuery('#errors').html("");
        
        var url = jQuery('#drpProductPages').val();

        //make sure that URL is selected
        if (url == "-1" || url == null) {
            err_msg = err_msg + '<li>Please select a Product Page to continue submission.</li>';
            jQuery('#drpProductGroups').focus();
        }

        //assign error message(s)
        if (err_msg != "") {
            
            err_html = '<div class="error-msg" style="display: block;"><h3>Please correct the following issues:</h3><ul>';
            err_html = err_html + err_msg;
            err_html = err_html + '</ul></div>';

            jQuery('#errors').html(err_html);

        }

        return err_html;

    }

    function createIframeLink() {

        var iframe_html = "";
        var iframe_html_js = "";
		
        var urls = jQuery('#drpProductPages').val().split("#");
        var width = jQuery('#drpIframeWidth').val();
        var font = jQuery('#drpFontScheme').val();
		var fontsize = jQuery('#drpFontSize').val();
        var username = "<?php echo $username; ?>";

        var url = urls[0];
        var anchor = null;
		var script_url = base_url; 
		
        if(urls[1] != null)
        {
            anchor = urls[1];
        }

        //hide by default
        jQuery('#embed_container').attr('style', 'display: none;');

        //create iframe html
        if (url != null) {
            
            var querystring_val = '?iframe=true&font=' + font + '&fontsize=' + fontsize + '&username=' + username;

            if(anchor != null)
            {
                querystring_val = querystring_val + '#' + anchor;
            }

            //override with renoworks url
            if(url.indexOf("provia.renoworks.com") > -1)
            {
                script_url = "https://provia.renoworks.com/en/";
                url = "";
                querystring_val = "";
            }

            //create iframe html, onLoad="alertsize(document.body.scrollHeight);"
			var src = (script_url + url + querystring_val).replace("'", "&#39;");
            iframe_html = '<iframe id="provia_iframe" src="' + src + '" width="' + width + '" style="overflow:hidden;width:' + width + '" frameborder="0" scrolling="no"></iframe>';

            //set preview
            jQuery('#preview_html').html('<h3>iFrame Preview:</h3>' + iframe_html);
            
            //wait for iframe to load to get height and assign to textbox
			jQuery('#embed_container').attr('style', '');
            jQuery('textarea#final_html').attr('value', 'Generating Embed Code...');
            setTimeout("assignIframeJS('" + iframe_html + "');", 4000);
            
        }

        return iframe_html;

    }

    function assignIframeJS(iframe_html) {
		
		//debugger;
		
        var the_height = 1200;
        var iframe_html_js = "";
        var base_protocol = location.protocol.toString();

        //attempt to assign height
        try
        {
            the_height = document.getElementById("provia_iframe").contentWindow.document.body.scrollHeight;
        }
        catch(e){}

        var _sel = jQuery('#drpProductGroups').val();
        if(_sel == "ProVia Visualizer")
        {
            base_protocol = "https:"
        }

        //add javascript code for height
        iframe_html_js = "\n" + '<script type="text/javascript" src="' + base_url + '/wp-content/plugins/provia-iframe/scripts/iframe/proviaResize.js">';
        iframe_html_js = iframe_html_js + '\<\/script\>';

        //assign iframe html to textbox and autoselect
        jQuery('#embed_container').attr('style', '');
        jQuery('textarea#final_html').val(iframe_html + iframe_html_js);
        jQuery('textarea#final_html').select();
        jQuery('textarea#final_html').focus();
		
		//set default first
		jQuery('#provia_iframe').attr('height', the_height);
		
		//attempt to resize to exact size
		jQuery("#provia_iframe").iFrameResize({ log : false });
		
    }
	
	function htmlEncode(value){
	  //create a in-memory div, set it's inner text(which jQuery automatically encodes)
	  //then grab the encoded contents back out.  The div never exists on the page.
	  return jQuery('<div/>').text(value).html();
	}

	function htmlDecode(value){
	  return jQuery('<div/>').html(value).text();
	}

</script>

<style type="text/css">
.cnt {
    width: 80%;
	min-width:500px;
	max-width:1200px;
}

.cnt-article-photo img {width:100%;}

#cnt-main {min-height:0;}

td {padding:15px 0px 0px 0px;}

</style>





    



