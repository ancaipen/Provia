$.extend(inContextMgr.modals["addproduct"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["addproduct"].events, {
    onOpen: function (props) {

        $('.pageEditor').data('object', props.result.result);

        if (inContextMgr.currentModalInfo.hasOwnProperty("shortNamePrefix"))
            $(".pageEditor #shortNamePrefix").html(inContextMgr.currentModalInfo.shortNamePrefix);

        if (inContextMgr.currentModalInfo.hasOwnProperty("title"))
            $(".modalPane .modalHeader .innerContent h3").html(inContextMgr.currentModalInfo.title);


        $('.pageProps li textarea').each(function () {
            if (regEx.isMatch($(this).attr("id"), "empty")) {
                $(this).attr("id", "00000000-0000-0000-0000-000000000000");
            }
        });


    },
    onValidate: function () {
        var errors = [];

        var name = $.trim($('#txtName').val());
        var shortName = $.trim($('#txtShortName').val());
        var pageTitle = $.trim($("#txtPageTitle").val());

        if (regEx.isMatch(name, "empty"))
            errors.push("Name is not specified");
        if (regEx.isMatch(shortName, "empty"))
            errors.push("Shortname is not specified");
        if (regEx.isMatch(pageTitle, "empty"))
            errors.push("Page Title is not specified");

        return errors;
    },
    onSave: function () {
        var shortName = $(".pageEditor #shortNamePrefix").text() + $('#txtShortName').val();

        inContextMgr.currentModalInfo.callback = function () {
            document.location.href = shortName;
        };

        var itemModel = $('.pageEditor').data('object');
        var properties = [];
        $('.pageProps li').each(function () {
            properties.push({
                propertyName: $(this).find("span").text()
                , id: $(this).find("textarea").attr("id")
                , value: $(this).find("textarea").val()
            });
        });

        var menuItems = [];

        menuItems.push({
            name: $('#txtName').val(),
            templateUrl: shortName,
            enabled: true
        });


        var menu = {
            groupName: inContextMgr.currentModalInfo.menuGroupName,
            MenuItems: menuItems
        }

        var newItem = { itemModel: { id: itemModel.id, name: $('#txtName').val(), shortName: shortName, enabled: $('#cbEnabled').attr('checked'), itemType: itemModel.itemType, propertyList: properties }, menu: menu };

        return newItem;
    }
});
