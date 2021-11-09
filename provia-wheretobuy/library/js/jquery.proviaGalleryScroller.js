/*
 * jQuery ProVia Feature Scroller
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function ($) {
    var nSetWidth = 0;
    var sDisplayCnt, sNavigationClass, sNavigationCnt, sBtnLeft, sBtnRight, sDetailsCnt;

    $.fn.proviaGalleryScroller = function (sDispCnt, sNavClass, sNavCnt, sBLeft, sBRight, sDetCnt, nWidth) {
        sDisplayCnt = sDispCnt;
        sNavigationClass = sNavClass;
        sNavigationCnt = sNavCnt;
        sBtnLeft = sBLeft;
        sBtnRight = sBRight;
        nSetWidth = nWidth;
        sDetailsCnt = sDetCnt;
        setupGallery();
        return this.each(function () { });
    }

    function __handleHoverOver(o) {
        $.each($(sNavigationClass), function (indx, itm) {
            if (!$(itm).hasClass("active")) {
                $(itm).find(".thumbimg").stop().animate({
                    opacity: .3
                });
            }
        });
        $(o).find(".thumbimg").stop().animate({
            opacity: 1
        }, function () { $(this).css('filter', ""); });
    }

    function __handleHoverOut(o) {
        $.each($(sNavigationClass), function (indx, itm) {
            if (!$(itm).hasClass("active")) {
                $(itm).find(".thumbimg").stop().animate({
                    opacity: .5
                });
            } else {
                $(itm).find(".thumbimg").stop().animate({
                    opacity: 1
                }, function () { $(this).css('filter', ""); });
            }
        });
    }

    function setupGallery() {
        var oGalleryNav = $(sNavigationClass);
        $(sNavigationCnt).css("width", (oGalleryNav.length * 181) + "px");
        $(sNavigationClass).click(function (e) {
            e.preventDefault();
            $(sNavigationClass).removeClass("active");
            $(this).addClass("active");
          //  $(sDisplayCnt).addClass('loading');
            loadGalleryImg($(this).children("a").attr("href"));
            var sDetailHTML = $(this).find(".details").html();
            if ($(this).attr("init") == "true") {
                setDetail(sDetailHTML, false);
                $(this).attr("init", "");
            }
            else
                setDetail(sDetailHTML);
            __handleHoverOut($(this));
        });

        $(sNavigationClass).hover(function () {
            __handleHoverOver($(this));
        },
		function () {
		    __handleHoverOut($(this));
		});
        $(sDetailsCnt).parent().click(function () {
            if ($("#cnt-details-tab p").text() == "DETAILS")
                showDetail();
            else
                hideDetail();
        });

        checkPrevNextGalleryItem();
        $(sNavigationClass + ":first").attr("init", "true");
        $(sNavigationClass + ":first").click();
    }

    function setDetail(sHTML, display) {
        var sDisplay = "none";
        if (sHTML && sHTML.length > 0) {
            sDisplay = "block";
        } else {
            sHTML = "<p>&nbsp;</p>";
        }
        $(sDetailsCnt).html(sHTML + "<div class='clear' style='height:1px'></div>");
        $(sDetailsCnt).find("img, p").animate({ opacity: 0 });
        $(sDetailsCnt).find("a").click(function (e) { e.stopImmediatePropagation(); });
        $(sDetailsCnt).parent().css("display", sDisplay);
        if (typeof display == "undefined" || display)
            showDetail();
        else
            hideDetail();
    }

    function showDetail() {
        $("#cnt-details-tab p").text("CLOSE");
        $(sDetailsCnt).parent().stop(true, true).delay(200).animate({
            top: (-($(sDetailsCnt).parent().height() - 15)).toString() + "px"
        });
        $(sDetailsCnt).find("img, p").stop(true, true).delay(200).animate({
            opacity: 1
        });
    }

    function hideDetail() {
        $("#cnt-details-tab p").text("DETAILS");
        $(sDetailsCnt).parent().stop(true, true).delay(200).animate({
            top: '0px'
        });
    }

    function nextGalleryItem() {
        if (parseInt($(sNavigationCnt).css("width"), 10) + parseInt($(sNavigationCnt).css("margin-left"), 10) > nSetWidth) {
            disableFeatureNav();
            $(sNavigationCnt).animate({
                marginLeft: (parseInt($(sNavigationCnt).css("margin-left"), 10) - $(sNavigationClass).outerWidth()).toString() + "px"
            }, 500, function () {
                checkPrevNextGalleryItem();
            });
        }
    }

    function prevGalleryItem() {
        if (parseInt($(sNavigationCnt).css("margin-left"), 10) < 0) {
            disableFeatureNav();
            $(sNavigationCnt).animate({
                marginLeft: (parseInt($(sNavigationCnt).css("margin-left"), 10) + $(sNavigationClass).outerWidth()).toString() + "px"
            }, 500, function () {
                checkPrevNextGalleryItem();
            });
        }
    }

    function disableFeatureNav() {
        $(sBtnLeft).unbind();
        $(sBtnRight).unbind();
    }

    function checkPrevNextGalleryItem() {
        checkPrevGalleryItem();
        checkNextGalleryItem();
    }

    function checkPrevGalleryItem() {
        if (parseInt($(sNavigationCnt).css("margin-left"), 10) >= 0) {
            // hide button
            $(sBtnLeft).unbind();
            $(sBtnLeft).stop().animate({
                opacity: .1
            });
            $(sBtnLeft).css("cursor", "default");
        } else {
            // show button
            $(sBtnLeft).bind("click", function () {
                prevGalleryItem();
            });
            $(sBtnLeft).hover(
				function () {
				    $(this).stop().animate({
				        opacity: 1
				    }, function () { $(this).css('filter', ""); });
				},
				function () {
				    $(this).stop().animate({
				        opacity: .7
				    });
				}
			);
            $(sBtnLeft).stop().animate({
                opacity: .7
            });
            $(sBtnLeft).css("cursor", "pointer");
        }
    }

    function checkNextGalleryItem() {
        if (parseInt($(sNavigationCnt).css("width"), 10) + parseInt($(sNavigationCnt).css("margin-left"), 10) <= nSetWidth) {
            // hide button
            $(sBtnRight).unbind();
            $(sBtnRight).stop().animate({
                opacity: .1
            });
            $(sBtnRight).css("cursor", "default");
        } else {
            // show button
            $(sBtnRight).bind("click", function () {
                nextGalleryItem();
            });
            $(sBtnRight).hover(
				function () {
				    $(this).stop().animate({
				        opacity: 1
				    }, function () { $(this).css('filter', ""); });
				},
				function () {
				    $(this).stop().animate({
				        opacity: .7
				    });
				}
			);
            $(sBtnRight).stop().animate({
                opacity: .7
            });
            $(sBtnRight).css("cursor", "pointer");
        }
    }

    function loadGalleryImg(sPath) {
        if ($(sDisplayCnt).find("img").length == 0) {
            $(sDisplayCnt).addClass('loading');
            var img = new Image();
            $(img)
			.load(function () {
			    $(sDisplayCnt)
				.html("")
				.removeClass('loading')
				.append(this);
			    //    $(this).hide().fadeIn(1000);
			})
				.error(function () {
				    // notify the user that the image could not be loaded
				})
			.attr('src', sPath);
        }
    }

})(jQuery);
