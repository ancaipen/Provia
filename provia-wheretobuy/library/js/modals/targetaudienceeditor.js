$.extend(inContextMgr.modals["targetaudienceeditor"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(props);
        $('#targetAudienceEditorContent').data('object', props.result.result);
        $('#txtShortName').val(props.result.result.shortName);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty)
            $('#cbEnabled').attr('checked', true);
        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Title":
                        $('#txtTitle').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value);
                        break;
                }
            });
        }
        $('textarea:last').cleditor();
        $('#adminDelete').click(function () {
            conBox.ask({
                question: "Are you sure you want to delete this Targeted Audience Page?",
                onYes: function () {
                    inContextMgr.modals["targetaudienceeditor"].custom.deleteItem();
                    modalMgr.close('targetaudienceeditor');
                }
            })
        });
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('#targetAudienceEditorContent').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "Title", value: $('#txtTitle').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });

        return { itemModel: { id: itemModel.id, name: $('#txtTitle').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties }, parentId: parentId, relationshipTypeName: relName };
    }
});
inContextMgr.modals["targetaudienceeditor"].custom = {

    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem?itemid=' + inContextMgr.currentModalInfo.guid,
            type: "POST"
        });
    }
};