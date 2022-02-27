if (typeof jQuery == 'undefined') {
	var script = document.createElement('script');
	script.type = "text/javascript";
	script.src = "https://provia.proviaserver-v2.com/wp-content/plugins/provia-iframe/scripts/iframe/jquery-3.3.1.min.js";
	script.onreadystatechange= function () {//This is for IE
	   if (this.readyState == 'complete'){ includeResize(); };
	}
	script.onload = includeResize;
	document.getElementsByTagName('head')[0].appendChild(script); 
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
	
	/*
	setInterval(function() {
        loadProviaIframe();
    }, 2000);
	*/
	
}

function loadProviaIframe()
{
	//console.log('iframe code run');
	jQuery("#provia_iframe").iFrameResize({
        heightCalculationMethod : 'lowestElement',
        inPageLinks             : true
     });
}