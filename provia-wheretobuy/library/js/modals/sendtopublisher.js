$.extend(inContextMgr.modals["sendtopublisher"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["sendtopublisher"].events, {
    onOpen: function (props) {
        //check to see if any items were edited by someone other than current user
        var sameuser = [];
        var diffuser = [];
        $(props.result.result).each(function () {
            if (this.lastModifiedBy !== readCookie("__RCMS_UNAME")) {
                diffuser.push({ name: this.name, owner: this.lastModifiedBy, type: this.itemType.typeName });
            }
            else {
                sameuser.push({ name: this.name, owner: this.lastModifiedBy, type: this.itemType.typeName });
            }
        });
        var notesString = "";
        //if so, throw warning/error
        if (diffuser.length > 0) {
            //alert("This will not get published:" + rerrors[0].name);
            notesString += "<div class='warning'>There are edited items on this page that will not get submitted to the publisher because they were not last edited by you:</div><table>";
            notesString += '<tr style="border-bottom:1px solid black"><th class="label" style="padding-right:10px">Type</th><th class="label" style="padding-right:10px">Name</th><th class="value">Owner</th></tr>';
            $(diffuser).each(function () {
                notesString += '<tr><td class="label" style="padding-right:10px">' + this.type + '</td><td class="label" style="padding-right:10px">' + this.name + '</td><td class="value">' + this.owner + '</td></tr>';
            });
            notesString += "</table>";
        }
        //else run through normal publishing
        if (sameuser.length > 0) {
            notesString += "<div>These items will be sent along with the current page if you choose to submit all items:</div>";
            notesString += "<table>";
            notesString += '<tr style="border-bottom:1px solid black"><th class="label" style="padding-right:10px">Type</th><th class="label" style="padding-right:10px">Name</th><th class="value">Owner</th></tr>';
            $(sameuser).each(function () {
                notesString += '<tr><td class="label" style="padding-right:10px">' + this.type + '</td><td class="label" style="padding-right:10px">' + this.name + '</td><td class="value">' + this.owner + '</td></tr>';
            });
            notesString += "</table>";
        }
        cyoaBox.assert({ choice: 'Submit Page for Publishing?', actions: [{ title: "cancel", action: function () { modalMgr.close("sendtopublisher"); } }, { title: "Submit Page Only", action: function () { inContextMgr.modals["sendtopublisher"].custom.publishAllItems = false; inContextMgr.save() } }, { title: "Submit All", action: function () { inContextMgr.modals["sendtopublisher"].custom.publishAllItems = true; inContextMgr.save() } }], notes: notesString });

    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var itemModel = $('#hdnItem').data('object');

        var newItem = {
            guid: inContextMgr.currentItemInfo.guid
            , publishAssociated: inContextMgr.modals["sendtopublisher"].custom.publishAllItems
        };
        return newItem;
    }
});

inContextMgr.modals["sendtopublisher"].custom = {
    publishAllItems: false
};