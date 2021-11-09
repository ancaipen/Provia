$.extend(inContextMgr.modals["productcontenteditor"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["productcontenteditor"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(props);
        $('#productContentEditor').data('object', props.result.result);
        $('#txtTitle').val(props.result.result.name);
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case 'DisplayName':
                        $('#txtDisplayName').attr('value', this.value);
                        break;
                    case "Header":
                        $('#txtHeading').val(this.value);
                        break;
                    case "HeaderBold":
                        $('#txtHeadingBold').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtContentSectionBody').val(this.value);
                        break;
                    case "MainBodyCopy":
                        $('#txtMainContentSectionBody').val(this.value);
                        break;
                }
            });
        }
        $("#txtContentSectionBody,#txtMainContentSectionBody").cleditor();
        $('#adminDelete').click(function () {
            conBox.ask({
                question: "Are you sure you want to delete this Product Section?",
                onYes: function () {
                    inContextMgr.modals["productcontenteditor"].custom.deleteItem();
                    modalMgr.close('productcontenteditor');
                }
            })
        });

    },
    onValidate: function () {
        var errors = [];
        if (regEx.isMatch($("input#txtTitle").attr('value'), "empty"))
            errors.push("Title cannot be empty.");

        if (regEx.isMatch($("input#txtDisplayName").attr('value'), "empty"))
            errors.push("Display name cannot be empty.");

        if (regEx.isMatch($("input#txtContentSectionBody").attr('value'), "empty"))
            errors.push("Content body cannot be empty.");


        return errors;
    },
    onSave: function () {
        var itemModel = $('#productContentEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "DisplayName", value: $('#txtDisplayName').val() });
        properties.push({ propertyName: "Header", value: $('#txtHeading').val() });
        properties.push({ propertyName: "HeaderBold", value: $('#txtHeadingBold').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtContentSectionBody').val() });
        properties.push({ propertyName: "MainBodyCopy", value: $('#txtMainContentSectionBody').val() });


        itemModel.propertyList = properties;
        itemModel.name = $('#txtTitle').val();

        var newItem = {
            itemModel: itemModel,
            parentId: parentId,
            relationshipTypeName: relName
        }
        return newItem;
    }
});
inContextMgr.modals["productcontenteditor"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem?itemid=' + inContextMgr.currentModalInfo.guid,
            type: "POST"
        });
    }
};
