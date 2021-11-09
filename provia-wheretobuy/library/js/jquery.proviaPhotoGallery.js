/*
 * jQuery ProVia Photo Gallery
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function($)
{
	var aProviaPhotos;
	var sGalleryNavContainer;
	var sGalleryPhotoContainer;
	var nCurrentItemIndx = 0;
	var nMaxItemIndx = 0;
	//
	$.fn.createProviaPhotoGallery = function(sGallNavCnt, sPhotoTarget) {
		sGalleryNavContainer = sGallNavCnt;
		sGalleryPhotoContainer = sPhotoTarget;
		aProviaPhotos = $(".provia-gallery-item");
		buildProviaPhotoNav();
		return this.each(function() {});
	}
	//
	function buildProviaPhotoNav() {
		var nItemsWidth = 0;
		$(sGalleryNavContainer).html("");
		$.each(aProviaPhotos, function(indx, itm) {
			$(sGalleryNavContainer).append("<li><a class='provia-gallery-btn provia-gallery-nav-item' href='" + $(itm).attr("href") + "' id='nav-item-" + indx + "'></a></li>");
			nItemsWidth += $(".provia-gallery-nav-item").width();
		});
		nMaxItemIndx = $(".provia-gallery-nav-item").length;
		var nItemOffset = ($(sGalleryNavContainer).width() - nItemsWidth);
		if (nItemOffset > 0) {
			nItemOffset /= 2;	
		} else {
			nItemOffset = 0;
		}
		$(".provia-gallery-nav-item").click(function(e) {
			e.preventDefault();
			showItem("#"+$(this).attr("id"));
		});
		if ($(sGalleryNavContainer).parent().parent().find("#btn-prev").length <= 0) {
			$(sGalleryNavContainer).parent().parent().prepend("<div id='btn-prev'><a class='provia-gallery-btn provia-gallery-prev'></a></div>");
		}
		if ($(sGalleryNavContainer).parent().parent().find("#btn-next").length <= 0) {
			$(sGalleryNavContainer).parent().parent().append("<div id='btn-next'><a class='provia-gallery-btn provia-gallery-next'></a></div>");
		}
		if ($(sGalleryNavContainer).parent().parent().find("#cnt-nav-summary").length <= 0) {
			$(sGalleryNavContainer).parent().parent().append("<div id='cnt-nav-summary'></div>");
		}
		$(".provia-gallery-nav-item:first").click();
	}
	//
	function showItem(s) {
		$(".provia-gallery-nav-item").stop().removeClass("active").animate({
			opacity:.3
		});
		$(s).stop().addClass("active").animate({
			opacity:1
		});
		checkPrevNextItems();
		$("#cnt-nav-summary").html("Showing photo " + (parseInt(s.replace("#nav-item-", "")) + 1).toString() + " of " + nMaxItemIndx);
		//
		var nItemLeft = $(s).parent().position().left;
		var nItemWidth = $(s).parent().width();
		var nCntLeft = $(s).parent().parent().position().left;
		var nCntWidth = $(s).parent().parent().parent().width();
		//var nCntWidth = $(s).parent().parent().parent().width() + parseInt($(s).parent().parent().css("margin-left"), 10);
		var nDiff = 0;
		if (nItemLeft < nCntLeft) {
			nDiff = nItemLeft - nCntLeft;
		} else if ( (nItemLeft + nItemWidth) > (nCntWidth + nCntLeft) ) {
			var nDiff = (nItemLeft + nItemWidth) - (nCntWidth + nCntLeft);
		}
		if (nDiff != 0) {
			$(".provia-gallery-nav-item").parent().parent().stop().animate({
				marginLeft: ('-='+nDiff)
			}, 1000, function() {
				handlePhotoLoad($(s).attr("href"));
			});
		} else {
			handlePhotoLoad($(s).attr("href"));
		}
	}
	//
	function handlePhotoLoad(href) {
		if ($(sGalleryPhotoContainer + " img").length) {
			$(sGalleryPhotoContainer).find("img").animate({
				opacity:0
			}, 500, function() {
				loadPhotoGalleryImg(href);
			});
		} else {
			loadPhotoGalleryImg(href);
		}
	}
	//
	function showNextImage() {
		var aItems = $(".provia-gallery-nav-item");
		var iIndx = -1;
		$.each(aItems, function(indx, itm) {
			if ($(itm).hasClass("active")) {
				iIndx = indx + 1;
			}
		});
		try { $(aItems[iIndx]).click(); } catch(e) {}
	}
	//
	function showPrevImage() {
		var aItems = $(".provia-gallery-nav-item");
		var iIndx = -1;
		$.each(aItems, function(indx, itm) {
			if ($(itm).hasClass("active")) {
				iIndx = indx - 1;
			}
		});
		try { $(aItems[iIndx]).click(); } catch(e) {}
	}
	//
	function checkPrevNextItems() {
		var nPrevOpacity, nNextOpacity;
		$(".provia-gallery-next").unbind();
		if ($(".provia-gallery-nav-item:last").hasClass("active")) {
			$(".provia-gallery-next").css("cursor", "none");
			nNextOpacity = .2;
		} else {
			$(".provia-gallery-next").css("cursor", "pointer");
			$(".provia-gallery-next").bind("click", function(e) {
				showNextImage();	
			});
			nNextOpacity = 1;
		}
		$(".provia-gallery-next").animate({
			opacity:nNextOpacity
		});
		$(".provia-gallery-prev").unbind();
		if ($(".provia-gallery-nav-item:first").hasClass("active")) {
			$(".provia-gallery-prev").css("cursor", "none");
			nPrevOpacity = .2;
		} else {
			$(".provia-gallery-prev").css("cursor", "pointer");
			$(".provia-gallery-prev").bind("click", function(e) {
				showPrevImage();	
			});
			nPrevOpacity = 1;
		}
		$(".provia-gallery-prev").animate({
			opacity:nPrevOpacity
		});
	}
	//
	function loadPhotoGalleryImg(sPath) {
		$(sGalleryPhotoContainer).addClass("loading");
		var img = new Image();
		$(img)
			.delay(500).stop().load(function () {
				$(sGalleryPhotoContainer)
				.html("")
				.removeClass('loading')
				.append(this);
				$(this).stop().hide().fadeIn(1000);
				})
				.error(function () {
				// notify the user that the image could not be loaded
			})
			.attr('src', sPath);
	}
	
})(jQuery);
