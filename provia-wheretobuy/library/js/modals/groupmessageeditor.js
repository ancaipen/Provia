$.extend(inContextMgr.modals["groupmessageeditor"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["groupmessageeditor"].events, {
    onOpen: function (props) {
        var item = props.result.result;

        $("#groupMessageEditor").data("item", item);

        if (item.propertyList.length > 0) {
            $(item.propertyList).each(function () {
                switch (this.propertyName) {
                    case "Title":
                        $('#txtTitle').val(this.value).attr("readonly", true);
                        break;
                    case "Message":
                        $('#txtMessage').val(this.value).attr("readonly", true);
                        break;
                    case "SendEmail":
                        $("#chkEmailGroups").attr("checked", this.value == 1).attr("disabled", true);
                        break;
                }
            });
            $('#adminDelete').removeClass("hide").click(function () {
                conBox.ask({
                    question: "Are you sure you want to delete this message?",
                    onYes: function () {
                        inContextMgr.modals["groupmessageeditor"].custom.deleteItem();
                        inContextMgr.cancel();
                    }
                });
            });

            $("#txtStartDate").val(item.startDate == "1/1/0001" ? (new Date()).toShortDateString() : item.startDate);
            $("#txtEndDate").val(item.endDate == "1/1/0001" ? "" : item.endDate);

            $("#typeOption").hide();
            $("#typeSelected").html(item.permissions.length > 0 ? "Group Specific Message" : "Global Message");
            if (item.permissions.length > 0) {
                $("#multigroup").removeClass("hide");
                ajax.go({ url: '/eClient/SelectAccountAPI/GetGroupList' },
                { func: function (props) { inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.init(props, item.permissions, false); } });
                $("#selectMoreGroups").hide();
            } else {
                inContextMgr.modalLoaded();

                $("#txtStartDate,#txtEndDate").attr("readonly", true).datepicker({
                    showOn: "both",
                    buttonImage: "/incontext/assets/image/calendar.gif",
                    buttonImageOnly: true
                });
            }

        } else {

            $("#txtStartDate").val((new Date()).toShortDateString());

            ajax.go({ url: '/eClient/SelectAccountAPI/GetGroupList' },
            { func: function (props) { inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.init(props, item.permissions, true); } });
        }



        $("#fileUpload").fileUploader({ title: "Import CSV", method: "csv", callback: { func: inContextMgr.modals["groupmessageeditor"].custom.parseCSV} })
        $("input[name=radMessageType]").bind("change", function (e) {
            switch ($("input[name=radMessageType]:checked").val()) {
                case "Global":
                    $("#multigroup").addClass("hide");
                    break;
                case "Group":
                    $("#multigroup").removeClass("hide");
                    break;
            }

            modalMgr.adjust(e);
        });

        if (inContextMgr.currentItemInfo.userRole.indexOf('publisher') == -1) {
            $('#adminBtnSave').unbind("click").die("click").bind("click", function () { inContextMgr.currentModalInfo.callback = inContextMgr.modals["groupmessageeditor"].custom.sendToPublish; inContextMgr.save(); })
        }
    },
    onValidate: function () {
        var errors = [];

        if ($("input[name=radMessageType]:checked").val() == "Group" && inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys.length == 0)
            errors.push("You have selected that you would like this message to be a group specific message, yet you have no groups selected");

        if (regEx.isMatch($.trim($("#txtTitle").val()), "empty"))
            errors.push("You must specify a title");

        if (regEx.isMatch($.trim($("#txtMessage").val()), "empty"))
            errors.push("You must specify a message");

        if (regEx.isMatch($.trim($("#txtStartDate").val()), "empty"))
            errors.push("You must specify a start date");
        else if (!regEx.isMatch($("#txtStartDate").val(), "date"))
            errors.push("You must specify a valid start date");

        if (!regEx.isMatch($.trim($("#txtEndDate").val()), "empty") && !regEx.isMatch($("#txtEndDate").val(), "date"))
            errors.push("You must specify a valid end date");

        return errors;
    },
    onSave: function () {
        $("ul#quickSearchResults").remove();
        var item = $("#groupMessageEditor").data("item");

        item.name = $("#txtTitle").val();
        item.enabled = true;
        item.startDate = $("#txtStartDate").val();
        item.endDate = $("#txtEndDate").val();
        item.propertyList = [];
        item.propertyList.push({ propertyName: "Title", value: $("#txtTitle").val() });
        item.propertyList.push({ propertyName: "Message", value: $("#txtMessage").val() });
        item.propertyList.push({ propertyName: "SendEmail", value: $("#chkEmailGroups").is(':checked') ? 1 : 0 });

        item.permissions = [];
        $(inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys).each(function () {
            var key = this.toString();

            item.permissions.push({
                mapping_from_key: key,
                enabled: true
            });

        });

        return { itemModel: item };
    }
});

inContextMgr.modals["groupmessageeditor"].custom = {
    parseCSV: function (props) {

        var groupNumberArray = props.result.result;
        var error = [];

        $(groupNumberArray).each(function () {
            var groupNumber = this.toString();
            var groupKey = null;
            $(inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.groupList).each(function () {
                if (this.groupNumberField.toString() == groupNumber) {
                    groupKey = this.groupKeyField;
                    return;
                }
            });

            if (groupKey == null && $.inArray(groupNumber, error) == -1) {
                error.push(groupNumber);
            }
            else if (groupKey != null) {
                inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.addGroup(groupKey);
            }
        });

        if (error.length > 0) {
            msgBox.open({ heading: "Invalid Group Number", message: ["The following group number(s) in the CSV do not correspond with an actual group: " + error.join(", ")], type: "error" });
        }
    },
    groupQuickSearch: {
        groupList: [],
        filteredList: [],
        selectedKeys: [],
        addGroup: function (groupKey, allowRemoval) {
            if ($.inArray(groupKey, inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys) == -1) {
                inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys.push(groupKey);
                $("ul#quickSearchResults").addClass("hide").empty();
                $("#txtGroupSearch").val("").blur();
                var groupName = "";
                $(inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.groupList).each(function () {
                    if (this.groupKeyField.toString() == groupKey) {
                        groupName = this.groupNameField;
                        return;
                    }
                });

                $("#permissionsList").append($('<li class="bsmListItem-custom" style="display: block;"><span class="bsmListItemLabel-custom">' + groupName + '</span>' + (allowRemoval ? '<a href="javascript:void(0)" class="bsmListItemRemove-custom"><!-- --></a>' : '') + '</li>').data("key", groupKey));
                if (allowRemoval)
                    $("#permissionsList .bsmListItemRemove-custom:last").bind("click", inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.removeGroup);
            }
        },
        removeGroup: function () {
            inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys.splice($.inArray($(this).data("key"), inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.selectedKeys), 1);
            $(this).parent().remove();
        },
        displayGroups: function () {
            inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.filteredList = inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.filteredList.slice(0, 5);
            var $groupList = $("ul#quickSearchResults");
            $groupList.css({ top: $("#txtGroupSearch").offset().top - $("body").offset().top + 18, left: $("#txtGroupSearch").offset().left });
            $groupList.removeClass("hide").empty();
            $(inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.filteredList).each(function () {
                $groupList.append("<li><a href='javascript:void(0)' rel='" + this.groupKeyField + "'>" + this.groupNameField + "</a></li>");
            });
            $groupList.find("a").bind("click", function () { inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.addGroup($(this).attr("rel"), true); });
            $groupList.append('<li class="clear"><!-- ie --></li>');
        },
        search: function (e) {
            var query = $("#txtGroupSearch").val().toLowerCase();
            inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.filteredList = [];
            if (query.length > 2) {
                $(inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.groupList).each(function () {
                    var group = this;
                    if (group.groupNameField.toLowerCase().indexOf(query) == 0 || group.groupNumberField.indexOf(query) != -1)
                        inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.filteredList.push(this);
                });
                inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.displayGroups();
            } else {
                $("ul#quickSearchResults").addClass("hide").empty();
            }
        },
        init: function (props, selectedGroups, allowRemove) {
            if (!props.hasError) {
                $("#permissionsList").empty();
                inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.groupList = props.result.result.groupsListField.groupField;
                $(selectedGroups).each(function () { inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.addGroup(this.mapping_from_key, allowRemove); });
                $("#txtGroupSearch").emptyText({ defaultText: "Enter Group Name or Number", focusColor: "#000" }).bind("keyup", inContextMgr.modals["groupmessageeditor"].custom.groupQuickSearch.search);
                $("body").append($("ul#quickSearchResults").addClass("hide"));
            }
            inContextMgr.modalLoaded();
            $("#txtStartDate,#txtEndDate").attr("readonly", true).datepicker({
                showOn: "both",
                buttonImage: "/incontext/assets/image/calendar.gif",
                buttonImageOnly: true
            });
        }
    },
    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    },
    sendToPublish: function (currentModalInfo, props) {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/SubmitForPublishing',
            data: JSON.stringify({ guid: props.result.result }),
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