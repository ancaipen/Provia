/*
 * jQuery ProVia Rich Content Nav
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function ($) {
    var sContentTarget, sNavTarget;
    var bOnClick = false;
    var nTimerRichNav;
    var oCurrentRichItem;
    var xhr;

    $.fn.proviaRichContentNav = function (sNav, sContent, bClick) {
        sContentTarget = sContent;
        sNavTarget = sNav;
        if (typeof (bClick) == "boolean") {
            bOnClick = bClick;
        }
        setupRichContentNav();
        loadContent($(sNavTarget).find("a:first"));
        return this.each(function () { });
    }

    function setupRichContentNav() {
        if (bOnClick) {
            $(sNavTarget).find("a").bind("click", function (e) {
                e.preventDefault();
                loadContent($(this));
            });
        } else {
            // on hover
            //$(sNavTarget).find("a").hover(function () {
            //    var oTarget = $(this);
            //    nTimerRichNav = setInterval(function () { loadContent(oTarget); }, 700);
            //}, function () {
            //    clearInterval(nTimerRichNav);
            //});
            $(sNavTarget).find("a").hover(function () {
                var oTarget = $(this);
                loadContent(oTarget);
            });
        }
        $(sContentTarget).append("<div class='rich-inner'></div>");
    }

    function loadContent(o) {
        if (o != oCurrentRichItem) {
            oCurrentRichItem = o;
            $(sContentTarget).find("div").animate({
                opacity: 0
            }, 1, function () {
                $(sNavTarget).find("a").removeClass("active");
                $(o).addClass("active");
                var sPath = $(o).attr("rel");
                $(sContentTarget).addClass("loading");
                if (sPath != undefined && sPath.match("/assets/") == "/assets/") {
                    $(sContentTarget).find(".rich-inner").html("<img src=\"" + sPath + "\"/>");
                    $(sContentTarget).find(".rich-inner").animate({
                        opacity: 1
                    }, 250, function () { $(this).css('filter', ""); });
                    $(sContentTarget).removeClass("loading");
                }
                else {
                    if (xhr && xhr.readystate != 4) {
                        xhr.abort();
                    }
                    xhr = $.ajax({
                        url: sPath,
                        cache: false,
                        success: function (html) {
                            $(sContentTarget).find(".rich-inner").html(html);
                            $(sContentTarget).find(".rich-inner").animate({
                                opacity: 1
                            }, 1, function () { $(this).css('filter', ""); });
                            $(sContentTarget).removeClass("loading");
                        }
                    });
                }

            });
        }
    }

})(jQuery);
