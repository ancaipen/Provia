/*
 * jQuery ProVia Shared Functions
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function ($) {

    $.fn.proviaShared = function (sHexOver, sHexOverText, sHexOff, sHexOffText) {
        setup();
        return this.each(function () { });
    }

    $.fn.proviaFixIEImgs = function () {
        fixIEImgs();
        return this.each(function () { });
    }

    $.fn.proviaStripe = function () {
        stripe();
        return this.each(function () { });
    }

    function fixIEImgs() {
        var i;
        for (i in document.images) {
            if (document.images[i].src) {
                var imgSrc = document.images[i].src;
                if (imgSrc.substr(imgSrc.length - 4) === '.png' || imgSrc.substr(imgSrc.length - 4) === '.PNG') {
                    document.images[i].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true',sizingMethod='crop',src='" + imgSrc + "')";
                }
            }
        }
    }

    function setup() {
        stripe();
        $(".rollover").hover(function () {
            $(this).find("img").attr("src", $(this).find("img").attr("src").replace("_s1", "_s2"));
        }, function () {
            $(this).find("img").attr("src", $(this).find("img").attr("src").replace("_s2", "_s1"));
        });
        $("#cnt-footer-band").append("<div id='footer-shadow'></div>");
        $("a[href='#top']").click(function (event) {
            event.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 500);
        });
        $(".youtube").click(function (event) {
            event.preventDefault();
            openYouTubeVideo(getParameterByName($(this).attr("href").toString(), "v"));
        });
        
//        $(".tooltip").tooltip({ track: true,
//            delay: 0,
//            showURL: false,
//            showBody: " - ",
//            extraClass: "pretty",
//            fixPNG: true,
//            opacity: 0.95,
//            left: -120
//        });
        // fixes for IE
        $(".primary .subnav li:last-child").css("border-bottom", "1px solid transparent");
        $(".rich-dd li ul li:last-child").css("border-bottom", "none");
    }

    function stripe() {
        $(".nav-list li").removeClass("even").removeClass("odd");
        $(".nav-list li:even").addClass("even");
        $(".nav-list li:odd").addClass("odd");
    }

    function openYouTubeVideo(sCode) {
        Shadowbox.open({
            content: '<iframe width="560" height="349" src="http://www.youtube.com/embed/' + sCode + '" frameborder="0" allowfullscreen></iframe>',
            player: "html",
            height: 360,
            width: 580
        });
    }

    function getParameterByName(s, name) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(s);
        if (results == null)
            return "";
        else
            return decodeURIComponent(results[1].replace(/\+/g, " "));
    }

})(jQuery);
