// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["edittab"].modalOptions, { delayLoad: false });
$.extend(inContextMgr.modals["edittab"].events, {
    onOpen: function (props) {
        var item = props.result.result;
        $('#hdnItem').data('object', item);
        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Title":
                        $('#txtTitle').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value).cleditor();
                        break;
                }
            });
        }
        else {
            $('#txtBodyCopy').val(this.value).cleditor();
        }

    },
    onValidate: function () {  //define the validaiton function
        var errors = [];

        return errors;
    },
    onSave: function () {  //define the save function

        var itemModel = $('#tabEditor #hdnItem').data('object');

        itemModel.name = $("#txtTitle").val();
        itemModel.enabled = true;

        var properties = [];

        properties.push({ propertyName: "Title", value: $('#txtTitle').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });

        itemModel.propertyList = properties;
        var newItem = { itemModel: itemModel };
        return newItem;
    }
});
