$.extend(inContextMgr.modals["tabeditor"].events, {
    onOpen: function (props) {
        $('.tabEditor').data('object', props.result.result);
        $('#txtName').val(props.result.result.name);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty)
            $('#cbEnabled').attr('checked', true);
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Heading":
                        $('#txtHeading').val(this.value);
                        break;
                    case "HeadingBold":
                        $('#txtHeadingBold').val(this.value);
                        break;
                    case "DisplayName":
                        $('#txtDisplayName').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value);
                        break;
                }
            });

            $('#adminDelete').removeClass('hide').click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this tab?",
                    onYes: function () {
                        inContextMgr.modals["tabeditor"].custom.deleteItem();
                        modalMgr.close('tabeditor');
                    }
                });

            });
        }
        $('textarea#txtBodyCopy').cleditor();
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('.tabEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "Heading", value: $('#txtHeading').val() });
        properties.push({ propertyName: "HeadingBold", value: $('#txtHeadingBold').val() });
        properties.push({ propertyName: "DisplayName", value: $('#txtDisplayName').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });

        return { itemModel: $.extend(itemModel, { id: itemModel.id, name: $('#txtName').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties }), parentId: parentId, relationshipTypeName: relName };
    }
});

inContextMgr.modals["tabeditor"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    }
};
