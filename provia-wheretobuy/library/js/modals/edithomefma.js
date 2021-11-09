$.extend(inContextMgr.modals["edithomefma"].events, {
    onOpen: function (props) {
        $('.fmaEditor').data('object', props.result.result);
        $('#txtName').val(props.result.result.name);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty)
            $('#cbEnabled').attr('checked', true);
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "HomePageImageURL":
                        $('#imagePicker').imageAssetSelector(this.value, 228, 100);
                        break;
                    case "ImageLinkURL":
                        $('#txtImageLinkURL').val(this.value);
                        break;
                    case "HomePageContent":
                        $('#txtHomePageContent').val(this.value);
                        break;
                }
            });
            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this fma?",
                    onYes: function () {
                        inContextMgr.modals["edithomefma"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });

            });
        } else {
            $('#imagePicker').imageAssetSelector("", 228, 100);
        }

        $('textarea:last').cleditor();

        $("#lnkItem").click(function () { inContextMgr.itemSelector.open($("#txtImageLinkURL")); });

    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('.fmaEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "HomePageImageURL", value: $('#imagePicker').data('image') });
        properties.push({ propertyName: "ImageLinkURL", value: $('#txtImageLinkURL').val() });
        properties.push({ propertyName: "HomePageContent", value: $('#txtHomePageContent').val() });

        return { itemModel: { id: itemModel.id, name: $('#txtName').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties, itemType: { typeName: "HomeFMA"} }, parentId: parentId, relationshipTypeName: relName };
    }
});

inContextMgr.modals["edithomefma"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    }
};
