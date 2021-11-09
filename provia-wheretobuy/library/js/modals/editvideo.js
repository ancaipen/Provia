$.extend(inContextMgr.modals["editvideo"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["editvideo"].events, {
    onOpen: function (props) {
        $('.fmaEditor').data('object', props.result.result);
        $('#txtName').val(props.result.result.name);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty)
            $('#cbEnabled').attr('checked', true);

        var videoItem = props.result.result;

        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "title":
                        $('#txtTitle').val(this.value);
                        break;
                    case "description":
                        $('#txtDesc').val(this.value);
                        break;
                    case "thumbUrl":
                        $(".imgThumb").imageAssetSelector(this.value, 95, 70);
                        break;
                    case "postSurvey":
                        $('#txtSurvey').val(this.value);
                        break;
                    case "videoURL":
                        $('#txtUrl').val(this.value);
                        break;
                    case "videoAltURL":
                        $('#txtAltUrl').val(this.value);
                        break;
                    case "width":
                        $('#txtWidth').val(this.value);
                        break;
                    case "height":
                        $('#txtHeight').val(this.value);
                        break;
                    case "categories":
                        $('#txtCategories').val(this.value);
                        break;
                }
            });
            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this audio item?",
                    onYes: function () {
                        inContextMgr.modals["editvideo"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });
        } else {
            $(".imgThumb").imageAssetSelector("", 95, 70);
        }
        $("#lnkItem").click(function () { inContextMgr.itemSelector.open($("#txtUrl"), "Audio"); });

        ajax.go({
            url: '/incontext/webservices/incontext.svc/GetItemByType',
            data: { typeName: "Company" },
            type: "GET"
        }, { func: function (props) {
            inContextMgr.modals["editvideo"].custom.originalParentId = GUID.empty;
            $(props.result.result).each(function () {
                var selected = false;
                var parentId = this.id;
                $(this.children).each(function () {
                    if (videoItem.id == this.relatedItem.id) {
                        selected = true;
                        inContextMgr.modals["editvideo"].custom.originalParentId = parentId;
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
                $('<a id="adminBtnSaveAndPublish" class="inContextBtn" href="javascript:void(0)">Save And Send to Publisher</a>').bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["editvideo"].custom.sendToPublish; inContextMgr.save(); })
            );

            $('#adminBtnCancel').before(
                $('<a href="javascript:void(0)" class="inContextBtn leftBtn" style="float:left;">Edit Related Resources</a>').data("info", { modal: "multirelationshipeditor", title: "Edit Related Resources", typeName: "Resource", guid: props.result.result.id }).bind("click", inContextMgr.sideTrack.openModal)
            );
        }
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('.fmaEditor').data('object');
        var relName = $("#drpCompanies").val() == "" ? "" : "Resource";
        var parentId = $("#drpCompanies").val() == "" ? GUID.empty : $("#drpCompanies").val();
        var properties = [];

        properties.push({ propertyName: "title", value: $('#txtTitle').val() });
        properties.push({ propertyName: "description", value: $('#txtDescription').val() });
        properties.push({ propertyName: "thumbUrl", value: $(".imgThumb").data("image") });
        properties.push({ propertyName: "postSurvey", value: $('#txtSurvey').val() });
        properties.push({ propertyName: "videoURL", value: $('#txtUrl').val() });
        properties.push({ propertyName: "videoAltURL", value: $('#txtAltUrl').val() });
        properties.push({ propertyName: "width", value: $('#txtWidth').val() });
        properties.push({ propertyName: "height", value: $('#txtHeight').val() });
        properties.push({ propertyName: "isPrivate", value: (parentId != GUID.empty).toString() });
        properties.push({ propertyName: "categories", value: $('#txtCategories').val() });

        return { itemModel: { id: itemModel.id, name: $('#txtName').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties, itemType: { typeName: $('.fmaEditor').data('object').itemType.typeName} }, parentId: parentId, originalParentId: inContextMgr.modals["editvideo"].custom.originalParentId, relationshipTypeName: relName };
    }
});

inContextMgr.modals["editvideo"].custom = {
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
