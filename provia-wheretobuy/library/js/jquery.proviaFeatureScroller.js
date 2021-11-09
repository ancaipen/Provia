/*
 * jQuery ProVia Feature Scroller
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function($)
{

	var nFeaturesDisplayed = 3;
	var sFeatureClass, sFeatureCnt, sBtnLeft, sBtnRight;
	
	$.fn.proviaFeatureScroller = function(sFetClass, sFetCnt, sBLeft, sBRight, nDisp) {
		sFeatureClass = sFetClass;
		sFeatureCnt = sFetCnt;
		sBtnLeft = sBLeft;
		sBtnRight = sBRight;
		nFeaturesDisplayed = nDisp;
		setupFeatures();
		return this.each(function() {});  
	}  
	
	function setupFeatures() {
		var oFeatures = $(sFeatureClass);
		var iWidth = 0;
		$.each(oFeatures, function(indx, itm) {
			iWidth += $(itm).width();
		});
		$(sFeatureCnt).css("width", iWidth);
		$(sFeatureCnt).css("margin-left", 0);
		checkPrevNextFeature();
		$(sFeatureClass).hover(function(){
			$.each($(sFeatureClass), function(indx, itm) {
				$(itm).stop().animate({
					opacity:.5
				});
				$(itm).find("img").stop().animate({
					opacity:.5
				});
			});
			$(this).stop().animate({
				opacity:1
            }, function () { $(this).css('filter', ""); });
			$(this).find("img").stop().animate({
				opacity:1
            }, 'fast', function () { $(this).css('filter', ""); });
		},
		function() {
			$.each($(sFeatureClass), function(indx, itm) {
				$(itm).stop().animate({
					opacity:1
				}, 'fast');
				$(itm).find("img").stop().animate({
					opacity:1
	            }, 'fast', function () { $(this).css('filter', ""); });
			});
		});
	}
	
	function nextFeature() {
		if ( parseInt($(sFeatureCnt).css("width"),10) + parseInt($(sFeatureCnt).css("margin-left"), 10) > ($(".feature").width() * nFeaturesDisplayed) ) {
			disableFeatureNav();
			$(sFeatureCnt).animate({
				marginLeft: (parseInt($(sFeatureCnt).css("margin-left"), 10) - $(".feature").width()).toString() + "px"
			}, 500, function() {
				checkPrevNextFeature();	
			});
		}
	}
	
	function prevFeature() {
		if ( parseInt($(sFeatureCnt).css("margin-left"), 10) < 0 ) {
			disableFeatureNav();
			$(sFeatureCnt).animate({
				marginLeft: (parseInt($(sFeatureCnt).css("margin-left"), 10) + $(".feature").width()).toString() + "px"
			}, 500, function() {
				checkPrevNextFeature();	
			});
		}
	}
	
	function disableFeatureNav() {
		$(sBtnLeft).unbind();
		$(sBtnRight).unbind();	
	}
	
	function checkPrevNextFeature() {
		checkPrevFeature();
		checkNextFeature();
		laodPageFeatures();
	}
	
	function laodPageFeatures() {
		var aFeatures = $(sFeatureClass)
		$.each(aFeatures, function(indx, itm) {
			var pos = $(itm).position();
			if (pos.left >= 0 && pos.left < $(sFeatureClass).width() * nFeaturesDisplayed) {
				if ($(itm).find("img").attr("src")) {
					// image is already loaded. ignore.
				} else {
					var o = $(itm).find("a");
					$(o).addClass("loading");
					loadFeatureImg($(o).attr("rel"), o);
				}
			}
		});	
	}
	
	function checkPrevFeature() {
		if (parseInt($(sFeatureCnt).css("margin-left"), 10) >= 0) {
			// hide button
			$(sBtnLeft).unbind();
			$(sBtnLeft).stop().animate({
				opacity:.1
			});
			$(sBtnLeft).css("cursor", "default");
		} else {
			// show button
			$(sBtnLeft).bind("click", function() {
				prevFeature();
			});
			$(sBtnLeft).hover(
				function() {
					$(this).stop().animate({
						opacity:1
					});
				}, 
				function() {
					$(this).stop().animate({
						opacity:.7
					});	
				}
			);
			$(sBtnLeft).stop().animate({
				opacity:.7
			});
			$(sBtnLeft).css("cursor", "pointer");
		}
	}
	
	function checkNextFeature() {
		if ( parseInt($(sFeatureCnt).css("width"),10) + parseInt($(sFeatureCnt).css("margin-left"), 10) <= ($(".feature").width() * nFeaturesDisplayed) ) {
			// hide button
			$(sBtnRight).unbind();
			$(sBtnRight).stop().animate({
				opacity:.1	
			});
			$(sBtnRight).css("cursor", "default");
		} else {
			// show button
			$(sBtnRight).bind("click", function() {
				nextFeature();
			});
			$(sBtnRight).hover(
				function() {
					$(this).stop().animate({
						opacity:1
					});
				}, 
				function() {
					$(this).stop().animate({
						opacity:.7
					});	
				}
			);
			$(sBtnRight).stop().animate({
				opacity:.7
			});
			$(sBtnRight).css("cursor", "pointer");
		}
	}
	
	function loadFeatureImg(sPath, sContainer) {
		var img = new Image();
		$(img)
			.load(function () {
				$(sContainer)
				.html("")
				.removeClass('loading')
				.append(this);
				$(this).hide().fadeIn(1000);
				})
				.error(function () {
				// notify the user that the image could not be loaded
			})
			.attr('src', sPath);
	}
	
})(jQuery);
