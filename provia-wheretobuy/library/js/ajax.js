
function loadproductfamily(eventvalue, time) {
    
    var animatetime = time;
    var args = eventvalue.split("/");
    var href = $('[rel="address:/' + args[1] + '"]').attr('href');

    $("#rich-product-target ").animate({
        opacity: 0
    }, animatetime, function () {

        //show loading image
        $("#rich-product-target-loading").html('<div class="image_loading"></div>');

        $("#rich-product-target").load(href, function () {

            $("#rich-product-target").animate({
                opacity: 1
            }, animatetime, function () { $(this).css('filter', ""); });

            if (args.length <= 2) // load default
                $("#cnt-productfamily-content").load($(".address:first").attr('href'), function () {
                    $("#cnt-productfamily-content").animate({
                        opacity: 1
                    }, 100, function () { $(this).css('filter', ""); });
                });
            else
                loadfamilytab(eventvalue, animatetime);

            $('#options-conextitems').load('/ajax/optionsmenu/' + args[1]);
            $('#features-conextitems').load('/ajax/featuresmenu/' + args[1]);
            $('#gallery-conextitems').load('/ajax/gallerymenu/' + args[1]);
            $('#family-contextitems').load('/ajax/familymenu/' + args[1]);

            $('.productlink').removeClass("active");
            $('[rel="address:/' + args[1] + '"]').addClass("active");

        });
    });

    

}

function loadfamilytab(eventvalue, time) {
    
    var animatetime = time;
    var args = eventvalue.split("/");
    var href = $('[rel="address:/' + args[1] + '/' + args[2] + '"]').attr('hreflang');

    $("#rich-product-target-loading").html('<div class="image_loading"></div>');

    if (args.length <= 3) {

        $("#option-cnt").load($("#" + args[2]  + "-container .optionlink:first").attr('href'), function () {
            $("#option-cnt").animate({
                opacity: 1
            }, 100, function () { $(this).css('filter', ""); });

            $('#product-options-list li').removeClass("active");
            $('#' + args[2]  + '-container .optionlink:first').parent().addClass("active");
        });
    }
    else
    {
        loadoption(eventvalue, animatetime);
    }

}

function loadoption(eventvalue, time) {
    var animatetime = time;
    var args = eventvalue.split("/");
    var href = $('[rel="address:' + eventvalue + '"]').attr('href');
    
    $("#option-cnt").animate({
        opacity: 0
    }, animatetime, function () {
//        $(document).delegate(".optionlink", "click", function (e) {
//            e.preventDefault();
//            $.address.value($(this).attr("rel").replace("address:", ""));
//        });

        $("#option-cnt").load(href, function () {
            $("#option-cnt").animate({
                opacity: 1
            }, animatetime, function () { $(this).css('filter', ""); });
        });

        $('#product-options-list li').removeClass("active");
        $('[rel="address:' + eventvalue + '"]').parent().addClass("active");
    });
}