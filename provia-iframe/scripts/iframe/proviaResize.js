if (typeof jQuery == 'undefined') {
	var script = document.createElement('script');
	script.type = "text/javascript";
	script.src = "https://provia.proviaserver-v2.com/wp-content/plugins/provia-iframe/scripts/iframe/jquery-3.3.1.min.js";
	script.onreadystatechange= function () {//This is for IE
	   if (this.readyState == 'complete'){ includeResize(); };
	}
	script.onload = includeResize;
	document.getElementsByTagName('head')[0].appendChild(script); 
	
	includeResize();
	
}
else
{
	includeResize();
}

function includeResize()
{

	//we assume that jquery is included
	jQuery.noConflict();

	//add resize script
	var scriptResize = document.createElement('script');
	scriptResize.type = "text/javascript";
	scriptResize.src = "https://provia.proviaserver-v2.com/wp-content/plugins/provia-iframe/scripts/iframe/iframeResizer.min.js";
	scriptResize.onreadystatechange= function () {
	   if (this.readyState == 'complete'){ loadProviaIframe(); };
	}
	scriptResize.onload= loadProviaIframe;
	document.getElementsByTagName('head')[0].appendChild(scriptResize);	

}

function loadProviaIframe()
{
	//console.log('iframe code run');
	jQuery("#provia_iframe").iFrameResize({
        inPageLinks: true,
		checkOrigin:false,
		minHeight:1500,
		heightCalculationMethod:'lowestElement'
     });
	 
}

jQuery(document).ready(function () {
    
	//each iframe load
	jQuery("#provia_iframe").each(function () {
        
		//scroll to the top when new pages load
        var iframe = jQuery(this);
        iframe.on("load", function () { 
			setTimeout("iframeScrollTop();", 1000);
        });
		
    });
	
});

function iframeScrollTop()
{	
	//resize iframe
	loadProviaIframe();
	
	//scroll to the top
	jQuery('html, body').animate({
        scrollTop: jQuery('#provia_iframe').offset().top - 20
	}, 'fast');
}

