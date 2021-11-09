// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["productchooser"].modalOptions, { delayLoad: false });
$.extend(inContextMgr.modals["productchooser"].events, {
    onOpen: function (props) {  //define the open function
        if (typeof (console) != "undefined")
            console.log(props.result);

        if (props.result.result != null) {

            var menuItems = props.result.result;

            $.each(menuItems, function (index, menuItem) {
                var disabled = menuItem.relatedItem.enabled ? '' : " <span>DISABLED</span>";
                $('#featuredItems').append('<li id="' + menuItem.relatedItem.id + '"><div class="handle">&nbsp;</div><div class="text">' + menuItem.relatedItem.name + disabled + '</div><a href="' + menuItem.relatedItem.shortName + '" title="Edit" class="edit">&nbsp;</a><div class="clear"></div></li>');
                $('#featuredItems #' + menuItem.relatedItem.id).data("object", menuItem);
            });


            $('#featuredItems').sortable({
                connectWith: '',
                items: '>li:not(li.adminDivider)',
                handle: '.handle',
                change: inContextMgr.modals["productchooser"].custom.setDivider,
                receive: function (event, ui) {
                    var maxItems = inContextMgr.currentModalInfo.maxItems;
                    if ($(this).sortable('toArray').length > maxItems) {
                        $(ui.sender).sortable('cancel');
                        msgBox.open({ heading: 'Limit Reached', message: ['The maximum number of ' + inContextMgr.currentModalInfo.typeName + 's has been reached. Please remove an item from the list if you would like to add an additional item.'], type: "error" });
                    }
                },
                stop: inContextMgr.modals["productchooser"].custom.setDivider
            });
        }

        $("#addProduct").data("info", { modal: "addproduct", title: "Add Product", typeName: "Product", guid: "00000000-0000-0000-0000-000000000000", shortNamePrefix: "/public/products/", menuGroupName: "Product" }).bind("click", inContextMgr.sideTrack.openModal);

    },
    onValidate: function () {  //define the validaiton function
        var errors = [];
        return errors;
    },
    onSave: function () {  //define the save function
        var menuItems = [];

        $('#featuredItems li:not(li.adminDivider)').each(function (rnk) {
            var menuModel = $('#' + this.id).data("object");
            menuModel.rank = rnk;
            menuItems.push(menuModel);
        });
        return { menuSaveModel: { groupName: 'Product', MenuItems: menuItems} };
    }
});

inContextMgr.modals["productchooser"].custom = {
    setDivider: function () {
        var length = $("#featuredItems li:not(li.adminDivider)").length;
        var injectAfter = length > inContextMgr.currentModalInfo.maxItems ? inContextMgr.currentModalInfo.maxItems - 1 : length > 0 ? length - 1 : null;
        if (injectAfter != null) {
            $($("#featuredItems li:not(li.adminDivider)")[injectAfter]).after($("#featuredItems li.adminDivider"));
        }
    }
};
