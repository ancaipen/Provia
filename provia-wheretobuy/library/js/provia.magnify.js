		
		var magnifyCount = 0;
        var removeMagnifyCount = 0;
        var magnifyCountLimit = 45;

        jQuery(document).on("click", "#fancybox-buttons a.btnNext", function () {
            setTimeout("setMagnify();", 1000);
        });

        jQuery(document).on("click", "#fancybox-buttons a.btnPrev", function () {
            setTimeout("setMagnify();", 1000);
        });

        jQuery(document).on("click", "#fancybox-buttons a.btnPlay", function () {
            setTimeout("setMagnify();", 1000);
        });

        jQuery(document).on("click", "#fancybox-buttons a.btnToggle", function () {
            removeMagnify();
        });
                
        jQuery(document).ready(function () {
            
            jQuery('.fancybox-buttons[rev="zoom"]').click(function () {
                setTimeout("setMagnify();", 1000);
            });         
			
			try
			{
				jQuery('.fancybox-buttons').fancybox({

					openEffect: 'fade',
					closeEffect: 'fade',

					prevEffect: 'none',
					nextEffect: 'none',

					closeBtn: false,
					mouseWheel: true,

					helpers: {
						title: {
							type: 'inside'
						},
						buttons: {}
					},

					afterLoad: function () {

						try {

							//display custimze button based on renoworks id
							var _html = '';
							var _comments_id = this.element[0].accessKey;
							var _renoworksid = this.element[0].rel;

							//get comments
							if (_comments_id != '') {
								_html += jQuery('#' + _comments_id).html();
							}

							//get renoworks id
							//TODO: Uncomment code below after renoworks adds https
							if (_renoworksid != '') {
								_html += '<div id="lightbox-image-customize"><a href="/ideas/visualize/' + _renoworksid + '"><img src="/assets/img/shared/btn-customize.png"></a></div>';
							}

							//set pop-up caption text
							if (_html != null) {
								if (_html != '') {
									this.title = _html;
								}
							}

							//setTimeout("setMagnify();", 800);

						}
						catch (e)
						{ }

					}
				});
			}
			catch(e)
			{
				//eat error it may already be loaded
			}
			
            jQuery(".video-popup").click(function () {

                jQuery.fancybox({
                    'padding': 0,
                    'autoScale': false,
                    'transitionIn': 'none',
                    'transitionOut': 'none',
                    'title': this.title,
                    'width': 680,
                    'height': 495,
                    'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
                    'type': 'swf',
                    'swf': {
                        'wmode': 'transparent',
                        'allowfullscreen': 'true'
                    }
                });

                return false;
            });

        });

        function removeMagnify()
        {
            if (jQuery("div.magnify").length) {
                jQuery('img.fancybox-image').removeAttr('data-magnify-src');
                jQuery("div.magnify-lens").remove();
                removeMagnifyCount = 0;
                return;
            }
            
            if (removeMagnifyCount < magnifyCountLimit) {
                removeMagnifyCount++;
                setTimeout("removeMagnify();", 1000);
            }

        }

        function setMagnify()
        {
            try
            {

                //make sure div is not already present
                if (!jQuery("div.magnify").length) {

                    //check to make sure source image has rev=zoom attribute
                    var _src = jQuery('img.fancybox-image').attr('src');
                    var _found = false;

                    //loop through all zoom links and find matching image
                    jQuery('a[rev="zoom"]').each(function () {
                        var _href = jQuery(this).attr('href');
                        if(_href == _src)
                        {
                            _found = true;
                        }
                    });

                    //add magnify
                    if (_found)
                    {
                        jQuery('img.fancybox-image').attr('data-magnify-src', _src);
                        jQuery('img.fancybox-image').magnify();

                        //add custom class to div
                        jQuery('div.fancybox-inner').addClass('fancybox-custom');

                        magnifyCount = 0;
                        return;
                    }

                }

                if (magnifyCount < magnifyCountLimit) {
                    magnifyCount++;
                    setTimeout("setMagnify();", 1000);
                }

                
            }
            catch (e) { }
            
        }