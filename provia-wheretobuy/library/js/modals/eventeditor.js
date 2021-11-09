// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["eventeditor"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["eventeditor"].events, {
    onOpen: function (props) {
        var item = props.result.result;
        $('#hdnItem').data('object', item);
        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Title":
                        $('#txtTitle').val(this.value);
                        break;
                    case "ImageURL":
                        $(".imageSelector").imageAssetSelector(this.value, 200, 200);
                        break;
                    case "BodyCopy":
                        $('#txtBodyCopy').val(this.value).cleditor();
                        break;
                    case "URL":
                        $('#txtUrl').val(this.value);
                        break;
                    case "Cost":
                        $('#txtEventCost').val(this.value);
                        break;
                    case "Date":
                        $('#txtStartDate').val(this.value);
                        break;
                    case "StartTime":
                        $('#txtStartDateTime').val(this.value);
                        break;
                    case "EndTime":
                        $('#txtEndDateTime').val(this.value);
                        break;
                }
            });

            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this audio item?",
                    onYes: function () {
                        inContextMgr.modals["eventeditor"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });

            $(".redirectUrls").removeClass("hide");
            $("#txtPayPage").val("http://www.workingfamilyresourcecenter.org/events/paynow?eventid=" + item.siteId);
            $("#txtThankYouPage").val("http://www.workingfamilyresourcecenter.org/events/thankyou?eventid=" + item.siteId);
        }
        else {
            $('#txtBodyCopy').val(this.value).cleditor();
            $(".imageSelector").imageAssetSelector("", 200, 200);
            $("#txtStartDate").val((new Date()).toShortDateString() + " 12:00 am");
        }

        inContextMgr.modalLoaded();

        $("#txtStartDate,#txtEndDate").attr("readonly", true).datepicker({
            showOn: "both",
            buttonImage: "/incontext/assets/image/calendar.gif",
            buttonImageOnly: true,
            changeYear: true
        });

        if (props.result.result.id != GUID.empty && inContextMgr.currentItemInfo.userRole.indexOf('publisher') == -1) {
            $('#adminBtnSave').after(
                $('<a id="adminBtnSaveAndPublish" class="inContextBtn" href="javascript:void(0)">Save And Send to Publisher</a>').bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["eventeditor"].custom.sendToPublish; inContextMgr.save(); })
            );
        }

        ajax.go({
            url: '/incontext/webservices/incontext.svc/GetItemByType',
            data: { typeName: "Company" },
            type: "GET"
        }, { func: function (props) {
            inContextMgr.modals["eventeditor"].custom.originalParentId = GUID.empty;
            $(props.result.result).each(function () {
                var selected = false;
                var parentId = this.id;
                $(this.children).each(function () {
                    if (item.id == this.relatedItem.id) {
                        selected = true;
                        inContextMgr.modals["eventeditor"].custom.originalParentId = parentId;
                        return;
                    }
                });

                $("#drpCompanies").append('<option value="' + parentId + '"' + (selected ? 'selected="selected"' : '') + '>' + this.name + '</option>');
            });

            //inContextMgr.modalLoaded();
        }
        });
    },
    onValidate: function () {  //define the validaiton function
        var errors = [];

        if (regEx.isMatch($('#txtTitle').val(), "empty"))
            errors.push("You must specify an event title");

        /*
        if (inContextMgr.currentModalInfo.guid != GUID.empty) {
        if (!regEx.isMatch($('#txtUrl').val(), "url"))
        errors.push("Registration URL is not valid");
        }

        */
        return errors;
    },
    onSave: function () {  //define the save function

        var itemModel = $('#eventEditor #hdnItem').data('object');
        var parentId = $("#drpCompanies").val() == "" ? GUID.empty : $("#drpCompanies").val();
        var relName = $("#drpCompanies").val() == "" ? "" : "Resource";
        var originalParentIdGUID = inContextMgr.modals["eventeditor"].custom.originalParentId;

        itemModel.name = $("#txtTitle").val();
        itemModel.enabled = true;
        itemModel.parentId = parentId;
        itemModel.originalParentId = originalParentIdGUID;
        //itemModel.startDate = $("#txtStartDate").val();
        //itemModel.endDate = $("#txtEndDate").val();

        var properties = [];

        properties.push({ propertyName: "Title", value: $('#txtTitle').val() });
        properties.push({ propertyName: "BodyCopy", value: $('#txtBodyCopy').val() });
        properties.push({ propertyName: "Url", value: $('#txtUrl').val() });
        properties.push({ propertyName: "ImageURL", value: $(".imageSelector").data("image") });
        properties.push({ propertyName: "Date", value: $("#txtStartDate").val() });
        properties.push({ propertyName: "Cost", value: $("#txtEventCost").val() });
        properties.push({ propertyName: "StartTime", value: $("#txtStartDateTime").val() });
        properties.push({ propertyName: "EndTime", value: $("#txtEndDateTime").val() });
        properties.push({ propertyName: "isPrivate", value: (parentId != GUID.empty).toString() });

        itemModel.propertyList = properties;
        var newItem = { itemModel: itemModel, parentId: parentId, originalParentId: originalParentIdGUID, relationshipTypeName: relName };

        return newItem;
    }
});

inContextMgr.modals["eventeditor"].custom = {
    originalParentId: GUID.empty,
    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        }, { func: function () {
            inContextMgr.modals["eventeditor"].events.onSuccess();
        }
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
