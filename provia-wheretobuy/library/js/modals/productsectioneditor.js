$.extend(inContextMgr.modals["productsectioneditor"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(props);

        inContextMgr.modals["productsectioneditor"].custom.item = props.result.result;

        $('#txtShortName').val(props.result.result.shortName);
        if (inContextMgr.currentModalInfo.guid == GUID.empty)
            $('.shortname').text(window.location.pathname + '/');
        else
            $('#txtShortName').parents('li').hide();

        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case 'Title':
                        $('#txtTitle').attr('value', this.value);
                        break;
                    case 'DisplayName':
                        $('#txtDisplayName').attr('value', this.value);
                        break;
                    case 'BodyCopy':
                        $('#txtBodyCopy').attr('value', this.value);
                        break;
                    case 'IconOnURL':
                        $('#iconOn').imageAssetSelector(this.value, 32, 40);
                        break;
                    case 'IconURL':
                        $('#iconOff').imageAssetSelector(this.value, 32, 40);
                        break;

                }
            });
            $('#adminBtnDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this Product Section?",
                    onYes: function () {
                        inContextMgr.modals["productsectioneditor"].custom.deleteItem();
                        modalMgr.close('productsectioneditor');
                    }
                })
            });

        } else {
            $('#iconOn').imageAssetSelector("", 32, 40);
            $('#iconOff').imageAssetSelector("", 32, 40);
        }

        $('textarea').cleditor();

    },
    onValidate: function () {
        var errors = [];
        if (regEx.isMatch($("#txtTitle").attr('value'), "empty"))
            errors.push("Title cannot be empty.");

        if (regEx.isMatch($("#txtDisplayName").attr('value'), "empty"))
            errors.push("Display name cannot be empty.");

        if (regEx.isMatch($("#txtBodyCopy").attr('value'), "empty"))
            errors.push("Content body cannot be empty.");


        return errors;
    },
    onSave: function () {
        var itemModel = inContextMgr.modals["productsectioneditor"].custom.item;
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "Title", value: $('#txtTitle').val() });
        properties.push({ propertyName: "DisplayName", value: $('#txtDisplayName').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });
        properties.push({ propertyName: "IconOnURL", value: $('#iconOn').data('image') });
        properties.push({ propertyName: "IconURL", value: $('#iconOff').data('image') });

        return { itemModel: $.extend(itemModel, {
            id: itemModel.id,
            shortName: inContextMgr.currentModalInfo.guid == GUID.empty ? window.location.pathname + '/' + $('#txtShortName').val() : $('#txtShortName').val(),
            name: $('#txtTitle').val(),
            enabled: true,
            propertyList: properties
            }), parentId: parentId, relationshipTypeName: relName
        };
    }
});
inContextMgr.modals["productsectioneditor"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    },
    item: {}
};
