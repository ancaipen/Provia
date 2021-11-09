$.extend(inContextMgr.modals["editfma"].events, {
    onOpen: function (props) {
        $('.fmaEditor').data('object', props.result.result);
        $('#txtName').val(props.result.result.name);
        $('#cbEnabled').attr('checked', props.result.result.enabled);
        if (props.result.result.id == GUID.empty)
            $('#cbEnabled').attr('checked', true);

        if (props.result.result.propertyList.length > 0) {
            $(props.result.result.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Header":
                        $('#txtHeading').val(this.value);
                        break;
                    case "HeaderBold":
                        $('#txtHeadingBold').val(this.value);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value);
                        break;
                    case "LinkTitle":
                        $('#txtLinkTitle').val(this.value);
                        break;
                    case "LinkUrl":
                        $('#txtLinkUrl').val(this.value);
                        break;
                    case "HeaderImage":
                        $('li#HeaderImage').removeClass('hide');
                        $(".imageSelector").imageAssetSelector(this.value, 44, 44);
                        break;
                }
            });
            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this fma?",
                    onYes: function () {
                        inContextMgr.modals["editfma"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });
        } else {
            if ($('.fmaEditor').data('object').itemType.typeName == "GroupPortalFMA" || $('.fmaEditor').data('object').itemType.typeName == "ResourceFMA") {
                $('li#HeaderImage').removeClass('hide');
                $(".imageSelector").imageAssetSelector("", 44, 44);
            }
        }
        $('textarea:last').cleditor();

        $("#lnkItem").click(function () { inContextMgr.itemSelector.open($("#txtLinkUrl")); });


        if (props.result.result.id != GUID.empty && inContextMgr.currentItemInfo.userRole.indexOf('publisher') == -1) {
            $('#adminBtnSave').after(
                $('<a id="adminBtnSaveAndPublish" class="inContextBtn" href="javascript:void(0)">Save And Send to Publisher</a>').bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["editfma"].custom.sendToPublish; inContextMgr.save(); })
            );
        }
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('.fmaEditor').data('object');
        var relName = inContextMgr.currentModalInfo.hasOwnProperty('typeName') ? inContextMgr.currentModalInfo.typeName : "";
        var parentId = relName == "" ? GUID.empty : inContextMgr.currentModalInfo.parentId;
        var properties = [];

        properties.push({ propertyName: "Header", value: $('#txtHeading').val() });
        properties.push({ propertyName: "HeaderBold", value: $('#txtHeadingBold').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });
        properties.push({ propertyName: "LinkTitle", value: $('#txtLinkTitle').val() });
        properties.push({ propertyName: "LinkUrl", value: $('#txtLinkUrl').val() });

        if (!$('li#HeaderImage').hasClass('hide'))
            properties.push({ propertyName: "HeaderImage", value: $(".imageSelector").data("image") });

        return { itemModel: { id: itemModel.id, name: $('#txtName').val(), enabled: $('#cbEnabled').attr('checked'), propertyList: properties, itemType: { typeName: $('.fmaEditor').data('object').itemType.typeName} }, parentId: parentId, relationshipTypeName: relName };
    }
});

inContextMgr.modals["editfma"].custom = {

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
