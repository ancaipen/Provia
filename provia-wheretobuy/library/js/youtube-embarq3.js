var ytplayer = null;

jQuery( document ).ready(function() {
	load_video();
});
	
jQuery("#sammy-vid").click(function() {
	
	var isMobile = detectmob();
	if(!isMobile)
	{
		jQuery("#sammy-vid-video").attr('style', 'display:block;');
		jQuery("#sammy-vid").attr('style', 'display:none;');
	}
	else
	{
		jQuery("#sammy-vid-video").attr('style', 'display:block;');
		jQuery("#sammy-vid").attr('style', 'display:none;');
		
		var video_html = '<iframe class="youtube-player" type="text/html" width="100%" height="100%" src="http://www.youtube.com/embed/L4xFPdV1DPw?autoplay=1" allowfullscreen frameborder="0"></iframe>';
		jQuery("#sammy-vid-video").html(video_html);
		
	}
	
});

function load_video()
{
	var params = { allowScriptAccess: "always", wmode: "transparent", allowFullScreen: "true" };
	var atts = { id: "myytplayer" };
	swfobject.embedSWF("http://www.youtube.com/v/L4xFPdV1DPw?autoplay=1&cc_load_policy=1&rel=0&enablejsapi=1&playerapiid=myytplayer&version=3",
					   "youtube-vid", "100%", "100%", "8", null, null, params, atts);
					   
}

function onYouTubePlayerReady(playerId) {
  ytplayer = document.getElementById("myytplayer");
  ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
}

function onytplayerStateChange(newState) {
   if (newState == 0) {
		//alert("video is over");
		jQuery("#sammy-vid-video").attr('style', 'display:none;');
		jQuery("#sammy-vid").attr('style', 'display:block;');
	}
}

function detectmob() { 
 if( navigator.userAgent.match(/Android/i)
 || navigator.userAgent.match(/webOS/i)
 || navigator.userAgent.match(/iPhone/i)
 || navigator.userAgent.match(/iPad/i)
 || navigator.userAgent.match(/iPod/i)
 || navigator.userAgent.match(/BlackBerry/i)
 || navigator.userAgent.match(/Windows Phone/i)
 ){
    return true;
  }
 else {
    return false;
  }
}


