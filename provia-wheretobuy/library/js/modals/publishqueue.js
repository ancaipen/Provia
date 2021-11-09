$.extend(inContextMgr.modals["publishqueue"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["publishqueue"].events, {
    onOpen: function (props) {

        var emptyList = 0;
        var publishLists = props.result.result;
        if (publishLists.publishItemList.length == 0) {
            $("#tblPublishItems").addClass("hide").prev("h4").addClass("hide");
            emptyList++;
        } else {
            inContextMgr.modals["publishqueue"].custom.createRows("#tblPublishItems", publishLists.publishItemList);
        }

        if (publishLists.publishMenuList.length == 0) {
            $("#tblPublishMenu").addClass("hide").prev("h4").addClass("hide");
            emptyList++;
        } else {
            inContextMgr.modals["publishqueue"].custom.createRows("#tblPublishMenu", publishLists.publishMenuList);
        }

        if (publishLists.deleteItemList.length == 0) {
            $("#tblDeleteItems").addClass("hide").prev("h4").addClass("hide");
            emptyList++;
        } else {
            inContextMgr.modals["publishqueue"].custom.createRows("#tblDeleteItems", publishLists.deleteItemList);
        }

        if (publishLists.deleteMenuList.length == 0) {
            $("#tblDeleteMenu").addClass("hide").prev("h4").addClass("hide");
            emptyList++;
        } else {
            inContextMgr.modals["publishqueue"].custom.createRows("#tblDeleteMenu", publishLists.deleteMenuList);
        }

        ajax.go({ url: "/incontext/webservices/assetmanagement.svc/GetAssetsWithCurrentVersion?enabled=false" }, { func: function (props) {
            var assetList = props.result.result;

            if (assetList === null || assetList.length == 0) {
                $("#tblDisabledAssets").addClass("hide").prev("h4").addClass("hide");
                emptyList++;
            } else {
                for (var i = 0; i < assetList.length; i++) {
                    $("#tblDisabledAssets").append('<tr><th><input id="' + assetList[i].asset_id + '" type="checkbox" /></th><th>' + assetList[i].file_name + '</th><th>' + assetList[i].versions[0].file_type + '</th><th>' + assetList[i].versions[0].user_name + '</th><th><a href="/public/AssetManager/GetFileByVersion/' + assetList[i].versions[0].asset_version_id + '" target="_blank" class="download">&nbsp;</a></th></tr>');
                    $("#tblDisabledAssets tr:last input").data("asset", assetList[i]);
                }
            }

            if (emptyList == 5) {
                $("#noLists").removeClass("hide");
                $(".btnContainer").addClass("hide");
            } else {
                $("#tblPublishItems .revertlink, #tblDeleteItems .revertlink").click(function () {
                    var item = this;
                    conBox.ask({
                        question: "Are you sure you want to " + $(item).attr("title").toLowerCase() + " this item?",
                        onYes: function () {
                            inContextMgr.modals["publishqueue"].custom.revertItem(item);
                        }
                    });
                });

                $("#tblPublishMenu .revertlink, #tblDeleteMenu .revertlink").click(function () {
                    var item = this;
                    conBox.ask({
                        question: "Are you sure you want to " + $(item).attr("title").toLowerCase() + " this menu?",
                        onYes: function () {
                            inContextMgr.modals["publishqueue"].custom.revertMenu(item);
                        }
                    });
                });

                $("#tblPublishItems tr:odd, #tblPublishMenu tr:odd, #tblDeleteItems tr:odd, #tblDeleteMenu tr:odd").addClass("odd");
                $("#btnSelectAll").click(function () { $("tr input[type=checkbox]").each(function () { $(this).attr('checked', true); }); });
                $("#btnClearSelected").click(function () { $("tr input[type=checkbox]").each(function () { $(this).attr('checked', false); }); });

                $("#adminBtnPublish").bind("click", function () {
                    conBox.ask({
                        question: "Are you sure you want to publish these items?",
                        onYes: inContextMgr.save
                    });
                });
            }

            inContextMgr.modalLoaded();
        }
        });
    },
    onValidate: function () {
        var errors = [];
        return errors;
    },
    onSave: function () {
        var publishItemList = inContextMgr.modals["publishqueue"].custom.populateItemList("#tblPublishItems");
        var publishMenuList = inContextMgr.modals["publishqueue"].custom.populateItemList("#tblPublishMenu");
        var deleteItemList = inContextMgr.modals["publishqueue"].custom.populateItemList("#tblDeleteItems");
        var deleteMenuList = inContextMgr.modals["publishqueue"].custom.populateItemList("#tblDeleteMenu");

        var publishModel = { publishItemList: publishItemList, publishMenuList: publishMenuList, deleteItemList: deleteItemList, deleteMenuList: deleteMenuList };
        return { publishModel: publishModel };
    },
    onSuccess: function () {
        if ($("#tblDisabledAssets input[type=checkbox]:checked").length > 0) {
            inContextMgr.modals["publishqueue"].custom.enableAssets();
        } else {
            window.location.reload();
        }
    }
});

/* Helper Methods */
inContextMgr.modals["publishqueue"].custom = {
    createRows: function (tbl, itemList) {
        for (var i = 0; i < itemList.length; i++) {
            $(tbl).append('<tr><th><input id="'
                + itemList[i].id + '" type="checkbox" /></th><th>'
                + (tbl == "#tblPublishMenu" || tbl == "#tblDeleteMenu" ? itemList[i].groupName : itemList[i].itemType.typeName) + '</th><th>'
                + (itemList[i].hasOwnProperty("isWebpage") && itemList[i].isWebpage ? '<a href="' + itemList[i].shortName + '" target="_blank">' : '')
                + (tbl == "#tblPublishMenu" || tbl == "#tblDeleteMenu" ? itemList[i].menuName : itemList[i].name) + '</th><th>'
                + (itemList[i].hasOwnProperty("isWebpage") && itemList[i].isWebpage ? '</a>' : '')
                + itemList[i].modifiedDate + '</th><th>'
                + (itemList[i].lastPublishDate == "1/1/0001" ? 'NEVER' : itemList[i].lastPublishDate) + '</th><th>'
                + (itemList[i].lastPublishDate == "1/1/0001" ? tbl == '#tblDeleteItems' || tbl == '#tblDeleteMenu' ? '<a href="javascript:void(0);" class="revertlink" title="Revert">&nbsp;</a>' : '' : '<a href="javascript:void(0);" class="revertlink" title="Revert">&nbsp;</a>') + '</th></tr>');

            $("#" + itemList[i].id).data('object', itemList[i]);
        }
    },
    populateItemList: function (tbl) {
        var array = [];
        $(tbl + " input[type=checkbox]:checked").each(function () {
            array.push({
                id: $("#" + this.id).data('object').id,
                name: $("#" + this.id).data('object').name,
                itemType: $("#" + this.id).data('object').itemType,
                lastPublishDate: $("#" + this.id).data('object').lastPublishDate,
                modifiedDate: $("#" + this.id).data('object').modifiedDate,
                publish: $("#" + this.id).data('object').publish,
                shortName: $("#" + this.id).data('object').shortName,
                siteId: $("#" + this.id).data('object').siteId
            });
        });
        return array;
    },
    revertItem: function (alink) {
        var trItem = $(alink).closest("tr");
        var data = JSON.stringify({ itemId: trItem.find(":checkbox").data('object').id });
        var callback = function () { window.location.reload(); };
        var url = "/incontext/webservices/incontext.svc/RevertItem";
        ajax.go({ url: url, data: data, type: "POST" }, { func: callback });
    },
    revertMenu: function (alink) {
        var trItem = $(alink).closest("tr");
        var data = JSON.stringify({ menuId: trItem.find(":checkbox").data('object').id });
        var callback = function () { window.location.reload(); };
        var url = "/incontext/webservices/incontext.svc/RevertMenu";
        ajax.go({ url: url, data: data, type: "POST" }, { func: callback });
    },
    enableAssets: function () {

        var assetList = [];
        $("#tblDisabledAssets input[type=checkbox]:checked").each(function () {
            var asset = $(this).data("asset");
            delete asset.versions[0].file_type;
            delete asset.versions[0].user_name;
            assetList.push(asset);
        });

        var data = JSON.stringify({ assets: assetList });
        var url = "/incontext/webservices/assetmanagement.svc/EnableAssetsCurrentVersion";
        ajax.go({ url: url, data: data, type: "POST" }, { func: function () { window.location.reload(); } });
    }
};
