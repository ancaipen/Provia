$.extend(inContextMgr.modals["resourcelisteditor"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["resourcelisteditor"].events, {
    onOpen: function (props) {
        var item = props.result.result;

        ajax.go({ url: '/incontext/webservices/MappingManagement.svc/GetCategories' },
            { func: function (props) {
                var categories = props.result.result;

                $.each(categories, function (index, category) {
                    $("#catList").append("<option value=\"" + category.category_id + "\">" + category.category_name + "</option>");
                });

                $(item.children).each(function () {
                    var relationshipItem = this;
                    var resourceListItem = relationshipItem.relatedItem;
                    var title = "";
                    var catId = "";

                    $(resourceListItem.propertyList).each(function () {
                        switch (this.propertyName) {
                            case "Title":
                                title = this.value;
                                break;
                            case "CategoryID":
                                catId = this.value;
                                break;
                        }
                    });

                    $('#' + relationshipItem.relationshipType.typeName).append(
                        $('<li><div class="handle">&nbsp;</div><div class="text"><p>' + title + '</p><p class="resourceCat">' + $("#catList option[value=" + catId + "]").text() + '</p></div><a href="javascript:void(0)" title="Edit" class="edit">&nbsp;</a><a href="javascript:void(0)" title="Delete" class="delete">&nbsp;</a><div class="clear"></div></li>').data("info", { title: title, catId: catId, relTypeId: relationshipItem.relationshipType.id, relType: relationshipItem.relationshipType.typeName, relId: relationshipItem.id, relItemId: relationshipItem.relatedItem.id, deleteItem: false, update: false })
                    );
                });

                // remove from list
                $('a.delete').click(inContextMgr.modals["resourcelisteditor"].custom.removeResource);
                $('a.edit').click(inContextMgr.modals["resourcelisteditor"].custom.editResource);

                inContextMgr.modalLoaded();
            }
            });

        $('#ProductList, #AudienceList, #PurposeList').sortable({
            items: '>li',
            handle: '.handle',
            scroll: false,
            placeholder: 'ui-state-highlight'
        });

        // set height of handle
        $('.handle').css('height', $('.text').parent().innerHeight() - 8);

        // add to list
        $('#adminBtnAdd').bind("click", inContextMgr.modals["resourcelisteditor"].custom.addResource);

    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var relationshipModels = [
            { relationshipTypeName: "ProductList", itemId: inContextMgr.currentItemInfo.guid, relationships: [], items: null, relationshipTypeId: GUID.empty },
            { relationshipTypeName: "AudienceList", itemId: inContextMgr.currentItemInfo.guid, relationships: [], items: null, relationshipTypeId: GUID.empty },
            { relationshipTypeName: "PurposeList", itemId: inContextMgr.currentItemInfo.guid, relationships: [], items: null, relationshipTypeId: GUID.empty }
        ];

        $('#ProductList li').each(function(idx) { inContextMgr.modals["resourcelisteditor"].custom.setRelationshipListItem(relationshipModels[0], $(this).data("info"), idx); });
        $('#AudienceList li').each(function(idx) { inContextMgr.modals["resourcelisteditor"].custom.setRelationshipListItem(relationshipModels[1], $(this).data("info"), 100 + idx); });
        $('#PurposeList li').each(function(idx) { inContextMgr.modals["resourcelisteditor"].custom.setRelationshipListItem(relationshipModels[2], $(this).data("info"), 200 + idx); });
        
        return { relationshipModels: relationshipModels };
    }
});

inContextMgr.modals["resourcelisteditor"].custom = {
    setRelationshipListItem: function (relationshipModel, itemInfo, idx) {
        relationshipModel.relationships.push({
            relationshipTypeName: itemInfo.relType,
            relationshipId: itemInfo.relId,
            relationshipTypeId: itemInfo.relTypeId,
            relationshipRank: idx,
            relatedItemId: itemInfo.relItemId,
            deleteRelationship: itemInfo.deleteItem,
            relatedItem: {
                id: itemInfo.relItemId,
                itemType: {
                    id: GUID.empty,
                    typeName: "ResourceListItem"
                },
                templatePath: "",
                description: "",
                name: itemInfo.title,
                shortName: "",
                enabled: true,
                deleteItem: itemInfo.deleteItem,
                update: itemInfo.update,
                propertyList: [
                    { propertyName: "Title", value: itemInfo.title },
                    { propertyName: "CategoryID", value: itemInfo.catId }
                ],
                children: [],
                categories: [],
                permissions: []
            }
        });

        if (relationshipModel.relationshipTypeId == GUID.empty && relationshipModel.relationships[relationshipModel.relationships.length - 1].relationshipTypeId != GUID.empty)
            relationshipModel.relationshipTypeId = relationshipModel.relationships[relationshipModel.relationships.length - 1].relationshipTypeId;
    },
    addResource: function (e) {
        e.stopPropagation();
        var resourceName = $('input#catName').val();
        var categoryName = $('select#catList option:selected').text();
        var catId = $('select#catList').val();
        var targetList = $('select#placement').val();
        var errors = [];

        if (resourceName == '') {
            errors.push("You must enter a Title");
        }

        if (catId == '') {
            errors.push("You must select a Category");
        }

        if (targetList == '') {
            errors.push("You must select a List");
        }

        if (errors.length == 0) {
            $('#' + targetList + ' li:visible').each(function () {
                if (resourceName.toLowerCase() == $(this).data("info").title.toLowerCase()) {
                    errors.push("An item with the name '" + $(this).data("info").title + "' already exists in the '" + $('select#placement option:selected').text() + "' list");
                    return;
                }
            });
        }

        if (errors.length > 0) {
            msgBox.open({ heading: 'Unable to add', message: errors, type: "error" });
        } else {
            $('ul#' + targetList).append(
                $('<li><div class="handle">&nbsp;</div><div class="text"><p>' + resourceName + '</p><p class="resourceCat">' + categoryName + '</p></div><a href="javascript:void(0)" title="Edit" class="edit">&nbsp;</a><a href="javascript:void(0)" title="Delete" class="delete">&nbsp;</a><div class="clear"></div></li>').data("info", { title: resourceName, catId: catId, relTypeId: GUID.empty, relType: targetList, relId: GUID.empty, relItemId: GUID.empty, deleteItem: false, update: false })
            );
        }

        // remove from list
        $('a.delete').click(inContextMgr.modals["resourcelisteditor"].custom.removeResource);
        $('a.edit').click(inContextMgr.modals["resourcelisteditor"].custom.editResource);
        $('input#catName').val("");
        $('select#catList').val("");
        $('select#placement').val("");

    },
    editResource: function (e) {
        e.stopPropagation();
        var li = $(this).parent();
        li.addClass("editmode");
        li.find("a").addClass("hide");
        $('input#catName').val(li.data("info").title);
        $('select#catList').val(li.data("info").catId);
        $('select#placement').val(li.data("info").relType);

        $('#adminBtnAdd').unbind("click").text("save").bind("click", inContextMgr.modals["resourcelisteditor"].custom.saveResource);
    },
    saveResource: function (e) {
        e.stopPropagation();
        var li = $("li.editmode");
        var resourceName = $('input#catName').val();
        var categoryName = $('select#catList option:selected').text();
        var catId = $('select#catList').val();
        var targetList = $('select#placement').val();
        var errors = [];

        if (resourceName == '') {
            errors.push("You must enter a Title");
        }

        if (catId == '') {
            errors.push("You must select a Category");
        }

        if (targetList == '') {
            errors.push("You must select a List");
        }

        if (errors.length == 0) {
            $('#' + targetList + ' li:visible').each(function () {
                if (!$(this).hasClass("editmode")) {
                    if (resourceName.toLowerCase() == $(this).data("info").title.toLowerCase()) {
                        errors.push("An item with the name '" + $(this).data("info").title + "' already exists in the '" + $('select#placement option:selected').text() + "' list");
                        return;
                    }
                }
            });
        }

        if (errors.length > 0) {
            msgBox.open({ heading: 'Unable to save', message: errors, type: "error" });
        } else {
            li.find("p:first").text(resourceName);
            li.find("p.resourceCat").text(categoryName);

            if (li.parent() != $('ul#' + targetList)) {
                var newItem = li.clone(true).data("info", { title: resourceName, catId: catId, relTypeId: GUID.empty, relType: targetList, relId: GUID.empty, relItemId: GUID.empty, deleteItem: false, update: false }).removeClass("editmode");
                newItem.find("a").removeClass("hide");
                $('ul#' + targetList).append(newItem);
                $.extend(li.hide().data("info"), { deleteItem: true });
            } else {
                $.extend(li.hide().data("info"), { deleteItem: false, update: true });
            }

            li.find("a").removeClass("hide");
            $('#adminBtnAdd').unbind("click").text("add").bind("click", inContextMgr.modals["resourcelisteditor"].custom.addResource);
            $('input#catName').val("");
            $('select#catList').val("");
            $('select#placement').val("");
        }
    },
    removeResource: function () {
        var li = $(this).parent();
        cyoaBox.assert({
            choice: "Are you sure you want to delete '" + li.data("info").title + "'?",
            actions: [
                { title: 'No' },
                { title: 'Yes', action: function () {
                    // Do more here, somehow this needs to get deleted upon save
                    $.extend(li.hide().data("info"), { deleteItem: true });
                }
                }
            ]
        });


    }
};