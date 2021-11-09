$.extend(inContextMgr.modals["leadershipteammembereditor"].events, {
    onOpen: function (props) {
        $('.teamMemberEditor').data('object', props.result.result);
        $('#txtName').val(props.result.result.name);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty) {
            $('#cbEnabled').attr('checked', true);
            $('#ImageURL .ImageSelector').attr("id", GUID.empty).imageAssetSelector('', 142, 167);
        }
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Name":
                        $('#txtPropName').val(this.value);
                        break;
                    case "JobTitle":
                        $('#txtJobTitle').val(this.value);
                        break;
                    case "ImageURL":
                        $('#ImageURL .ImageSelector').attr("id", this.id).imageAssetSelector(this.value, 142, 167);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value);
                        break;
                }
            });

            $('#adminDelete').removeClass('hide').click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this team member?",
                    onYes: function () {
                        inContextMgr.modals["leadershipteammembereditor"].custom.deleteItem();
                        modalMgr.close('leadershipteammembereditor');
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
        var itemModel = $('.teamMemberEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];


        var imageURLProperty = { propertyName: "ImageURL", id: $('#ImageURL .ImageSelector').attr("id"), value: $('#ImageURL .ImageSelector').data("image") };

        properties.push({ propertyName: "Name", value: $('#txtPropName').val() });
        properties.push({ propertyName: "JobTitle", value: $('#txtJobTitle').val() });
        properties.push(imageURLProperty);
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });

        return { itemModel: $.extend(itemModel, { id: itemModel.id, name: $('#txtName').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties }), parentId: parentId, relationshipTypeName: relName };
    }
});

inContextMgr.modals["leadershipteammembereditor"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    }
};
