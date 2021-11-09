$.extend(inContextMgr.modals["multirelationshipeditor"].events, {
    onOpen: function (props) {
        var data = props.result;
        var rels = new Array();

        if (inContextMgr.currentModalInfo.hasOwnProperty("maxItems") && inContextMgr.currentModalInfo.hasOwnProperty("featuredOnly")) {
            $('#featuredItems').append('<li class="adminDivider">-- Above items will display on page, unless disabled --</li>');
        }

        $(data.result.relationships).each(function (relCnt) {
            rels[relCnt] = this.relatedItemId;
            var rItemName = this.relatedItemNameWithType.replace('[Tab] ', '').replace('[ProductGalleryItem] ', '').replace('[HomeFMA] ', '');
            var disabled = this.relatedItemEnabled ? '' : " <span>DISABLED</span>";
            $('#featuredItems').append('<li id="' + this.relatedItemId + '"><div class="handle">&nbsp;</div><div class="text">' + rItemName + disabled + '</div><a href="javascript:void(0)" title="Edit" class="edit">&nbsp;</a><div class="clear"></div></li>');
            $('#' + this.relatedItemId).data('obj', this.relationshipTypeId);

            if (typeof props.modal != "undefined") {
                $('#' + this.relatedItemId).find("a").data("info", { modal: props.modal, guid: this.relatedItemId, parentId: data.result.itemId }).bind("click", inContextMgr.sideTrack.openModal);
            } else {
                $('#' + this.relatedItemId).find("a").remove();
            }            
        });
        $(data.result.items).each(function () {
            if ($.inArray(this.id, rels) <= -1) {
                var itemName = this.displayNameWithType;
                var disabled = (this.hasOwnProperty("enabled") && this.enabled) || !this.hasOwnProperty("enabled") ? '' : " <span>DISABLED</span>";
                $('#allItems').append('<li id="' + this.id + '"><div class="handle">&nbsp;</div><div class="text">' + itemName + disabled + '</div><a href="javascript:void(0)" title="Edit" class="edit">&nbsp;</a><div class="clear"></div></li>');
                $('#' + this.id).data('obj', data.result.relationshipTypeId);
                if (typeof props.modal != "undefined") {
                    $('#' + this.id).find("a").data("info", { modal: props.modal, guid: this.id }).bind("click", inContextMgr.sideTrack.openModal);
                } else {
                    $('#' + this.id).find("a").remove();
                }
            }
        });
        $('#multiRelationshipEditor').data('obj', data.result.relationshipTypeId);
        //var modalInfo = inContextMgr.currentModalInfo.hasOwnProperty("featuredOnly") && inContextMgr.currentModalInfo.featuredOnly ? { guid: GUID.empty, parentId: data.result.itemId, typeName: inContextMgr.currentModalInfo.typeName, modal: props.modal} : { modal: props.modal };
        var modalInfo = { guid: GUID.empty, parentId: data.result.itemId, typeName: inContextMgr.currentModalInfo.typeName, modal: props.modal };

        if (typeof props.modal != "undefined") {
            $('#adminBtnAdd').data("hasBeenClicked", false).data("info", modalInfo).text('Add new ' + inContextMgr.currentModalInfo.typeName).bind("click", inContextMgr.sideTrack.openModal);
        } else {
            $('#adminBtnAdd').addClass("hide");
        }

        inContextMgr.modals["multirelationshipeditor"].custom.setDivider();
        if (inContextMgr.currentModalInfo.hasOwnProperty("featuredOnly") && inContextMgr.currentModalInfo.featuredOnly) { $('#multiRelationshipEditor .left').hide().parent('#multiRelationshipEditor').width(304); }
        //Configuring sortable containers

        $('#featuredItems').sortable({
            connectWith: inContextMgr.currentModalInfo.hasOwnProperty("featuredOnly") && inContextMgr.currentModalInfo.featuredOnly ? '' : '#allItems',
            items: '>li:not(li.adminDivider)',
            handle: '.handle',
            change: inContextMgr.modals["multirelationshipeditor"].custom.setDivider,
            receive: function (event, ui) {
                var maxItems = inContextMgr.currentModalInfo.maxItems;

                if ($(this).sortable('toArray').length > maxItems) {
                    $(ui.sender).sortable('cancel');
                    msgBox.open({ heading: 'Limit Reached', message: ['The maximum number of ' + inContextMgr.currentModalInfo.typeName + 's has been reached. Please remove an item from the list if you would like to add an additional item.'], type: "error" });
                }
            },
            stop: inContextMgr.modals["multirelationshipeditor"].custom.setDivider
        });
        
        

        $('#allItems').sortable({
            handle: '.handle',
            scroll: false,
            connectWith: '#featuredItems',
            placeholder: 'ui-state-highlight'
        }).disableSelection();
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var relationships = [];
        $('#featuredItems li:not(li.adminDivider)').each(function (rnk) {
            relationships.push({
                relatedItemId: $(this).attr('id'),
                relationshipRank: rnk,
                relationshipTypeId: $(this).data('obj'),
                relationshipTypeName: inContextMgr.currentModalInfo.typeName
            });
        });
        var rm = {
            itemId: inContextMgr.currentModalInfo.hasOwnProperty("guid") && inContextMgr.currentModalInfo.guid != GUID.empty ? inContextMgr.currentModalInfo.guid : inContextMgr.currentItemInfo.guid,
            items: null,
            relationshipTypeId: $('#multiRelationshipEditor').data('obj'),
            relationships: relationships
        };
        return { relationshipModel: rm };
    }
});


inContextMgr.modals["multirelationshipeditor"].custom = {
    setDivider: function () {
        if (inContextMgr.currentModalInfo.hasOwnProperty("featuredOnly")) {
            var length = $("#featuredItems li:not(li.adminDivider)").length;
            var injectAfter = length > inContextMgr.currentModalInfo.maxItems ? inContextMgr.currentModalInfo.maxItems - 1 : length > 0 ? length - 1 : null;
            if (injectAfter != null) {
                $($("#featuredItems li:not(li.adminDivider)")[injectAfter]).after($("#featuredItems li.adminDivider"));
            }
        }
    }
};
