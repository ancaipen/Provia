/*
 * jQuery ProVia Main Nav
 * Copyright 2011 Liggett Stashower
 * Released under the MIT and GPL licenses.
 */

(function($)
{

	var iNav;
	var iCurrMenu;
	var sMenuHexOver = "#6B8C21";
	var sMenuHexOverText = "#FFFFFF";
	var sMenuHexOff = "#FFFFFF";
	var sMenuHexOffText = "#333333";
	
	$.fn.proviaTabbedNav = function(sHexOver, sHexOverText, sHexOff, sHexOffText) {
		if (sHexOver) { sMenuHexOver = sHexOver };
		if (sHexOverText) { sMenuHexOverText = sHexOverText };
		if (sHexOff) { sMenuHexOff = sHexOff };
		if (sHexOffText) { sMenuHexOffText = sHexOffText };
		setupTabbedNav();
		return this.each(function() {});
	}
	
	function setupTabbedNav() {
		$(".tabbed .inactive a").hover(function(){
			$(this).stop(true, true).animate({
				backgroundColor: sMenuHexOver,
				color: sMenuHexOverText	
			});
			
		}, function() {
			$(this).stop(true, true).animate({
				backgroundColor: sMenuHexOff,
				color: sMenuHexOffText	
			});
		});
	}
	
})(jQuery);
