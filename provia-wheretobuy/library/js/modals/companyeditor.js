// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["companyeditor"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["companyeditor"].events, {
    onOpen: function (props) {
        var item = props.result.result;
        $('#hdnItem').data('object', item);

        $('#txtTitle').val(item.name);
        $('#txtStartDate').val(item.startDate);
        $('#txtEndDate').val(item.endDate);
        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                switch (this.propertyName) {
                    case "loginCode":
                        $('#txtLoginCode').val(this.value);
                        break;
                    case "path":
                        $('#txtCompanyPath').val(this.value);
                        break;
                    case "intro":
                        $('#txtIntro').val(this.value);
                        break;
                    case "logoUrl":
                        $(".imageSelector").imageAssetSelector(this.value, 200, 200);
                        break;
                    case "reqEmployeeNumber":
                        if (this.value == "true")
                            $("#chkReqEmployeeNumber").attr("checked", "checked");
                        break;
                    case "reqDepartment":
                        if (this.value == "true")
                            $("#chkReqDepartment").attr("checked", "checked");
                        break;
                    case "reqLocation":
                        if (this.value == "true")
                            $("#chkReqLocation").attr("checked", "checked");
                        break;
                }
            });
            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this company?",
                    onYes: function () {
                        inContextMgr.modals["companyeditor"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });
        }
        else {
            $(".imageSelector").imageAssetSelector("", 200, 200);
        }

        $('#txtIntro').cleditor();
        inContextMgr.modalLoaded();

        $("#txtEndDate").datepicker('destroy').datepicker({
            showOn: "both",
            buttonImage: "/incontext/assets/image/calendar.gif",
            buttonImageOnly: true,
            changeYear: true
        });

        $("#txtStartDate").datepicker('destroy').datepicker({
            showOn: "both",
            buttonImage: "/incontext/assets/image/calendar.gif",
            buttonImageOnly: true,
            changeYear: true
        });

        if (props.result.result.id != GUID.empty && inContextMgr.currentItemInfo.userRole.indexOf('publisher') == -1) {
            $('#adminBtnSave').after(
                $('<a id="adminBtnSaveAndPublish" class="inContextBtn" href="javascript:void(0)">Save And Send to Publisher</a>').bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["companyeditor"].custom.sendToPublish; inContextMgr.save(); })
            );
        }
    },
    onValidate: function () {  //define the validaiton function
        var errors = [];

        if (regEx.isMatch($('#txtTitle').val(), "empty"))
            errors.push("You must specify a Company Name");

        if (regEx.isMatch($('#txtCompanyPath').val(), "empty"))
            errors.push("You must specify a Company Path");

        if (regEx.isMatch($('#txtLoginCode').val(), "empty"))
            errors.push("You must specify a Company Code");

        return errors;
    },
    onSave: function () {  //define the save function

        var itemModel = $('#companyEditor #hdnItem').data('object');

        itemModel.name = $("#txtTitle").val();
        itemModel.enabled = true;
        itemModel.startDate = $("#txtStartDate").val();
        itemModel.endDate = $("#txtEndDate").val();

        var properties = [];

        properties.push({ propertyName: "loginCode", value: $('#txtLoginCode').val() });
        properties.push({ propertyName: "path", value: $('#txtCompanyPath').val() });
        properties.push({ propertyName: "intro", value: $('#txtIntro').val() });
        properties.push({ propertyName: "logoUrl", value: $(".imageSelector").data("image") });

        properties.push({ propertyName: "reqEmployeeNumber", value: $("#chkReqEmployeeNumber").is(':checked') });
        properties.push({ propertyName: "reqDepartment", value: $("#chkReqDepartment").is(':checked') });
        properties.push({ propertyName: "reqLocation", value: $("#chkReqLocation").is(':checked') });

        itemModel.propertyList = properties;
        var newItem = { itemModel: itemModel };
        return newItem;
    }
});


inContextMgr.modals["companyeditor"].custom = {

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