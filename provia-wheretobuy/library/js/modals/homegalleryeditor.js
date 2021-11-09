// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["homegalleryeditor"].events, {
    onOpen: function (props) {  //define the open function
        if (typeof (console) != "undefined")
            console.log(props.result);

        $('#homeGalleryEditorContent').data('object', props.result.result);

        $('#txtName').val(props.result.result.name);
        $("#cbEnabled").attr("checked", props.result.result.enabled);

        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "ImageURL":
                        $(".imageSelector").imageAssetSelector(this.value, 310, 165);
                        break;
                    case "TagLine":
                        $("#txtCopy").val(this.value).cleditor();
                        break;
                    case "LinkTitle":
                        $("#linkTitle").val(this.value);
                        break;
                    case "LinkUrl":
                        $("#linkUrl").val(this.value).emptyText({ defaultText: "http://", focusColor: "#000" });
                        break;
                }
            });

            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this?",
                    onYes: function () {
                        inContextMgr.modals["homegalleryeditor"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });
        } else {
            $(".imageSelector").imageAssetSelector("", 310, 165);
            $("#txtCopy").cleditor();
            $("#linkUrl").emptyText({ defaultText: "http://", focusColor: "#000" });
        }

        $("#lnkItem").click(function () { inContextMgr.itemSelector.open($("#linkUrl")); });
    },
    onValidate: function () {  //define the validaiton function
        var errors = [];
        //Need to know required fields for this modal
        if ($(".imageSelector").data("image") === "") {
            errors.push("You must select an image before saving a home gallery item");
        }

        return errors;
    },
    onSave: function () {  //define the save function
        var itemModel = $('#homeGalleryEditorContent').data('object');

        itemModel.propertyList = [];
        itemModel.propertyList.push({ propertyName: "ImageURL", value: $(".imageSelector").data("image") });
        itemModel.propertyList.push({ propertyName: "TagLine", value: $("#txtCopy").val() });
        itemModel.propertyList.push({ propertyName: "LinkTitle", value: $("#linkTitle").val() });
        itemModel.propertyList.push({ propertyName: "LinkUrl", value: $("#linkUrl").val() });
        itemModel.enabled = $('#cbEnabled').attr('checked');
        itemModel.name = $.trim($('#txtName').val()); ;


        return { itemModel: itemModel, parentId: inContextMgr.currentModalInfo.parentId, relationshipTypeName: inContextMgr.currentModalInfo.typeName };
    }
});

inContextMgr.modals["homegalleryeditor"].custom = {
    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    }
}
