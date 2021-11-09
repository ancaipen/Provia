$.extend(inContextMgr.modals["pageproperties"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["pageproperties"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(props);
        $('#productContentEditor').data('object', props.result.result);
        $('#txtTitle').val(props.result.result.name);
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "PageHeading":
                        $('#txtHeading').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtBody').val(this.value);
                        break;
                }
            });
        }

        $("#txtBody").cleditor();
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
        if (regEx.isMatch($("input#txtHeading").attr('value'), "empty"))
            errors.push("Heading cannot be empty.");

        if (regEx.isMatch($("input#txtBody").attr('value'), "empty"))
            errors.push("Content body cannot be empty.");


        return errors;
    },
    onSave: function () {
        var itemModel = $('#productContentEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "PageHeading", value: $('#txtHeading').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBody').val() });

        itemModel.propertyList = properties;

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
