$.extend(inContextMgr.modals["imageeditor"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["imageeditor"].events, {
    onOpen: function (props) {
        var item = props.result.result;

        $('#imageEditor').data('object', item);

        var w = inContextMgr.currentModalInfo.hasOwnProperty("image") ? inContextMgr.currentModalInfo.image.w : 300;
        var h = inContextMgr.currentModalInfo.hasOwnProperty("image") ? inContextMgr.currentModalInfo.image.h : 300;

        if (inContextMgr.currentModalInfo.hasOwnProperty("image")) {
            $("#ImageURL>span").append(" (dimensions for the selected image should be " + w + "x" + h + ")");
        }
        var imageUrl = "";
        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                switch (this.propertyName) {
                    case "ImageURL":
                        imageUrl = this.value;
                        break;
                    default:
                        break;
                }
            });
        }

        $('#imagePicker').imageAssetSelector(imageUrl, w, h);

    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('#imageEditor').data('object');
        var prop = itemModel.propertyList['ImageUrl'];
        if (null == prop) {
            var newProp = { propertyName: 'ImageURL', value: $("#imagePicker").data("image") };
            itemModel.propertyList.push(newProp);
        }
        else {
            itemModel.propertyList['ImageURL'].value = $("#imagePicker").data("image");
        }

        return { itemModel: itemModel };
    }
});
