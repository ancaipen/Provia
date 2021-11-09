/*
 * jQuery ProVia Main Nav
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function ($) {

    var iNav;
    var iCurrMenu;
    var sMenuHexOver = "#000000";
    var sMenuHexOverText = "#FFFFFF";
    var sMenuHexOff = "#FFFFFF";
    var sMenuHexOffText = "#333333";

    $.fn.proviaMainNav = function (sHexOver, sHexOverText, sHexOff, sHexOffText) {
        if (sHexOver) { sMenuHexOver = sHexOver };
        if (sHexOverText) { sMenuHexOverText = sHexOverText };
        if (sHexOff) { sMenuHexOff = sHexOff };
        if (sHexOffText) { sMenuHexOffText = sHexOffText };
        setupMainNav();
        return this.each(function () { });
    }

    function setupMainNav() {
        $("#nav-main li:last").addClass("last");
        $("#cnt-search #q").blur(function () {
            if ($(this).val().length > 0 && $(this).val() != "search") {
                $(this).removeClass("empty");
            } else {
                $(this).val("search");
                $(this).addClass("empty");
            }
        });
        $("#cnt-search #q").focus(function () {
            if ($(this).val() == "search") {
                $(this).removeClass("empty");
                $(this).val("");
            }
        });
        $(".primary").hover(function () {
            showDD(this.id);
        }, function () {
            hideDD(this.id);
        });
        $(".nav-grp a").hover(function () {
            showProdPhotoNav($(this).attr("name"));
        }, function () {
            showProdBgNav();
        });
    }

    function showDD(sLinkage) {
        /*if (!$.browser.msie) {
        $("#cnt-overlay").stop(true, true).delay(500).css("display", "block").animate({ opacity: .5}, 2200);
        }*/
        $("#prod-nav-bg").delay(500).css("background", "url(/assets/img/prodNav/nav-bg-1.jpg)");
        $(".subnav").css("display", "none");
        // reset any currently active menus
        $(".dd-hovering").stop(true, true).animate({
            paddingBottom: '0'
        });
        $(".dd-hovering").css("border-right-color", "#C1BEB7");
        $(".dd-hovering a:first").stop(true, true).animate({
            color: sMenuHexOffText,
            paddingBottom: '8px',
            backgroundColor: sMenuHexOff
        });
        $("#" + sLinkage).css("border-right-color", "transparent");
        //
        var dd = $("#" + sLinkage).find(".subnav").attr("id");
        if (dd) {
            // animate current menu
            $("#" + sLinkage).addClass("dd-hovering");
            $("#" + sLinkage).stop(true, true).animate({
                paddingBottom: '8px'
            });
            $("#" + sLinkage + " a:first").stop(true, true).animate({
                color: sMenuHexOverText,
                backgroundColor: sMenuHexOver
            }, 0, function () {
                $("#" + sLinkage).css("padding-bottom", "8px");
                //$("#" + sLinkage).css("background-image", "url(/assets/img/shared/pic-nav-dd-arrow.png)");
            });


            //$("#" + dd).stop(true, true).fadeIn();
            $("#" + dd).stop(true, true).show();
            $("#fade").show();

            var topheight = 0;
            $("#" + dd + " .menuColumn").each(function (index, col) {
                if ($(col).height() > topheight)
                    topheight = $(col).height();
            });
            $("#" + dd + " .menuColumn").each(function (index, col) {
                $(col).height(topheight);
            });

            //$("#" + dd).css("left", Math.floor(($("#" + dd).parent().width() - $("#" + dd).width()) / 2) + "px");
            $("#" + dd).css("left", $("#" + sLinkage + " a:first").offset().left() + 3 + "px");
            //$("#" + sLinkage + " a:first").offset().top()

            var iOffset = $("#" + dd).offset().left;
            if (iOffset < 10) {
                var iLeft = $("#" + dd).css("left");
                $("#" + dd).css("left", (parseInt(iLeft) - parseInt(iOffset) + 10) + "px");
            }


        } else {
            // animate current menu
            $("#" + sLinkage).addClass("dd-hovering");
            $("#" + sLinkage + " a:first").stop(true, true).animate({
                color: sMenuHexOverText,
                backgroundColor: sMenuHexOver
            }, 0);
        }
    }

    function hideDD(sLinkage) {

        $("#" + sLinkage).stop(true, true).animate({
            paddingBottom: '0'
        });
        $(".dd-hovering").css("border-right-color", "#C1BEB7");
        $("#" + sLinkage + " a:first").stop(true, true).animate({
            color: sMenuHexOffText,
            paddingBottom: '8px',
            backgroundColor: sMenuHexOff
        }, 0, function () {
            $("#" + sLinkage).css("padding-bottom", "0");
            $("#" + sLinkage).css("background-image", "none");
        });
        var dd = $("#" + sLinkage).find(".subnav").attr("id");
        if (dd) {
            //$("#" + dd).stop(true, true).delay(350).fadeOut(200);
            $("#" + dd).stop(true, true).hide();
        }
        $("#fade").hide();
        /*if (!$.browser.msie) {
        $("#cnt-overlay").stop(true, true).delay(350).animate({ opacity: 0}, 200, function() { $(this).css("display", "none") });
        }*/
    }
    //
    function showProdPhotoNav(sLinkage) {
        $("#prod-nav-photo").addClass("loading");
        var img = $("#prod-nav-photo").find("img");
        if (img.length > 0) {
            $(img[0]).stop(true, false).fadeOut(100, function () {
                loadMainNavImg("/assets/img/prodNav/" + sLinkage + ".jpg");
            });
        } else {
            loadMainNavImg("/assets/img/prodNav/" + sLinkage + ".jpg");
        }
    }
    //
    function showProdBgNav() {
        $("#prod-nav-photo").removeClass('loading');
        $("#prod-nav-photo img").fadeOut();
    }
    //
    function loadMainNavImg(sPath) {
        var img = new Image();
        $(img)
			.delay(50).load(function () {
			    $("#prod-nav-photo")
				.html("")
				.removeClass('loading')
				.css("overflow", "hidden")
				.append(this);

			    $(this).hide().fadeIn(500, function () { $("#prod-nav-bg").css("background", "url(" + sPath + ")"); });
			})
				.error(function () {
				    // notify the user that the image could not be loaded
				})
			.attr('src', sPath);
    }

})(jQuery);
