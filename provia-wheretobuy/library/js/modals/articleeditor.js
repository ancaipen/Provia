// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["articleeditor"].events, {
    onOpen: function (props) {

        var item = props.result.result;
        $('#hdnItem').data('object', item);
        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                if (this.propertyName === 'PageTitle') {
                    $('#txtTitle').val(this.value);
                }
                else if (this.propertyName === 'BodyCopy') {
                    $('#txtBodyCopy').val(this.value).cleditor();
                } else if (this.propertyName === "categories") {
                    $('#txtCategories').val(this.value);
                }
            });


            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this article?",
                    onYes: function () {
                        inContextMgr.modals["articleeditor"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });
        } else {
            $('#txtBodyCopy').cleditor();
        }

        ajax.go({
            url: '/incontext/webservices/incontext.svc/GetItemByType',
            data: { typeName: "Company" },
            type: "GET"
        }, { func: function (props) {
            inContextMgr.modals["articleeditor"].custom.originalParentId = GUID.empty;
            $(props.result.result).each(function () {
                var selected = false;
                var parentId = this.id;
                $(this.children).each(function () {
                    if (item.id == this.relatedItem.id) {
                        selected = true;
                        inContextMgr.modals["articleeditor"].custom.originalParentId = parentId;
                        return;
                    }
                });

                $("#drpCompanies").append('<option value="' + parentId + '"' + (selected ? 'selected="selected"' : '') + '>' + this.name + '</option>');
            });

            inContextMgr.modalLoaded();
        }
        });

        if (props.result.result.id != GUID.empty && inContextMgr.currentItemInfo.userRole.indexOf('publisher') == -1) {
            $('#adminBtnSave').after(
                $('<a id="adminBtnSaveAndPublish" class="inContextBtn" href="javascript:void(0)">Save And Send to Publisher</a>').bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["articleeditor"].custom.sendToPublish; inContextMgr.save(); })
            );
        }
    },
    onValidate: function () {  //define the validaiton function
        var errors = [];

        if (regEx.isMatch($('#txtTitle').val(), "empty"))
            errors.push("You must specify a Title");

        if (regEx.isMatch($('#txtBodyCopy').val(), "empty"))
            errors.push("You must specify a Body");

        return errors;
    },
    onSave: function () {  //define the save function

        var itemModel = $('#articleEditor #hdnItem').data('object');
        var relName = $("#drpCompanies").val() == "" ? "" : "Resource";
        var parentId = $("#drpCompanies").val() == "" ? GUID.empty : $("#drpCompanies").val();

        itemModel.name = $("#txtTitle").val();
        itemModel.enabled = true;
        itemModel.parentId = parentId;
        itemModel.originalParentId = 
        itemModel.relationshipTypeName = relName;

        var properties = [];

        var titleProperty = { propertyName: "PageTitle", value: $('#txtTitle').val() };
        var bodyCopyProperty = { propertyName: "BodyCopy", value: $('#txtBodyCopy').val() };
        var cats = { propertyName: "categories", value: $('#txtCategories').val() };
        var isPrivate = { propertyName: "isPrivate", value: (parentId != GUID.empty).toString() };

        properties.push(titleProperty);
        properties.push(bodyCopyProperty);
        properties.push(cats);
        properties.push(isPrivate);

        itemModel.propertyList = properties;


        var newItem = { itemModel: itemModel, parentId: parentId, originalParentId: inContextMgr.modals["articleeditor"].custom.originalParentId, relationshipTypeName: relName };
        return newItem;
    }
});

inContextMgr.modals["articleeditor"].custom = {
    originalParentId: GUID.empty,
    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    },
    sendToPublish: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/SubmitForPublishing',
            data: JSON.stringify({ guid: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        }, { func: function () {
            if (inContextMgr.sideTrack.sideTrackedModalInfo.hasOwnProperty("modal")) {
                modalMgr.close(inContextMgr.currentModalInfo.modal, true);
            } else {
                inContextMgr.modals[inContextMgr.currentModalInfo.modal].events.onSuccess();
            }
        }
        });

    }
};
