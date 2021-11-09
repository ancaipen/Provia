$.extend(inContextMgr.modals["assetmanager"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["assetmanager"].events, {
    onOpen: function (props) {
        $("#lnkMgr").click(function () { $("#file_manager").removeClass("hide"); $("#file_upload").addClass("hide"); });
        $("#lnkUpload").click(function () { $("#file_manager").addClass("hide"); $("#file_upload").removeClass("hide"); });

        $("#silverlightControlHost").css("display", "block");

        var url = '/incontext/webservices/AssetManagement.svc/GetAssets/True';
        inContextMgr.modals["assetmanager"].custom.refreshAssets(url);
    },
    onValidate: function () {
        var errors = [];

        return errors;
    },
    onSave: function () {

    }
});


inContextMgr.modals["assetmanager"].custom = {
    refreshAssets: function (url) {

        $("#assetTable").clearGridData();

        $("#assetTable").jqGrid(
        {
            datatype: "clientSide",
            colNames: ['assetid', 'File Name', 'Uploaded By', 'Upload Date', 'File Type', "", "", "", ""],
            colModel: [
                { name: 'assetid', index: 'assetid', width: 0, hidden: true, key: true, resizable: false },
                { name: 'file_name', index: 'file_name', width: 300, resizable: false },
		        { name: 'user_name', index: 'user_name', width: 120, sorttype: "text", resizable: false },
                { name: 'upload_date', index: 'upload_date', width: 120, sorttype: "date", formatter: "date", formatoptions: { srcformat: 'Y-m-d', newformat: 'm/d/Y' }, resizable: false },
                { name: 'file_type', index: 'file_type', width: 90, resizable: false },
                { name: 'remove_btn', index: 'remove_btn', width: 18, sortable: false, resizable: false },
                { name: 'edit_btn', index: 'edit_btn', width: 18, sortable: false, resizable: false },
                { name: 'download_btn', index: 'download_btn', width: 18, sortable: false, resizable: false },
                { name: 'detail_content', index: 'detail_content', width: 1, sortable: false, resizable: false, hidden: true }

            ],
            pager: '#assetTablePager',
            sortname: 'upload_date',
            height: 220,
            viewrecords: true,
            hidegrid: false,
            rowNum: 10,
            rowList: [10, 20, 30],
            sortorder: "desc",
            caption: " ",
            loadComplete: function () {
                var ids = $("#assetTable").getDataIDs();
                for (var i = 0; i < ids.length; i++) {
                    var assetId = ids[i];

                    $("#assetTable #" + assetId + " .grid_remove").bind("click", { assetId: assetId }, function (e) {
                        conBox.ask({ question: "Are you sure you would like to delete this asset?", onYes: function () {
                            inContextMgr.modals["assetmanager"].custom.removeAsset(e);
                        }
                        });
                    });

                    $("#assetTable #" + assetId + " .grid_edit").data("info", { modal: "editasset", assetId: assetId, includeRelationships: "true" }).one("click", inContextMgr.sideTrack.openModal);
                    $("#assetTable #" + assetId + " .grid_download").click(function () { window.open("/public/AssetManager/GetFileByName/" + $(this).attr("rel")); });
                }
                $("#assetTablePager_center").css("width", "300px");
            },
            subGrid: true,
            subGridRowExpanded: function (subgrid_id, row_id) {
                var subgrid_table_id;
                subgrid_table_id = subgrid_id + "_t";
                $("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table>");
                $("#" + subgrid_table_id).jqGrid({
                    datatype: "clientSide",
                    colModel: [{ name: "details", index: "details", width: 600}],
                    height: '100%',
                    loadCompleted: function () {
                        $("#assetTable .ui-jqgrid-hdiv").hide();
                    }
                });

                var detailContent = $("#" + row_id + " td[aria-describedby='assetTable_detail_content']").html();
                $("#" + subgrid_table_id).jqGrid('addRowData', "i_" + row_id, { details: detailContent });
                $("#assetTable .ui-jqgrid-hdiv").hide();
            },
            subGridRowColapsed: function (subgrid_id, row_id) { }
        });

        ajax.go({ url: url },
            { func: function (props) {
                var data = props.result;

                if (data.result === null || props.hasError)
                    return;

                for (var i = 0; i < data.result.length; i++) {
                    var assetStyle = "asset";
                    if (i % 2 == 1)
                        assetStyle = "asset_alternative";

                    var asset = data.result[i];
                    var currentVersion = asset.versions[0];

                    var detailRow1 = "<div class=\"detail_row\"><div class=\"col1\">file size</div><div class=\"col2\">" + Math.ceil(parseFloat(currentVersion.file_size) / 1000) + " KB</div><div class=\"clear\"><!-- clear --></div></div>";
                    var detailRow2 = "<div class=\"detail_row\"><div class=\"col1\">categories</div><div class=\"col2\">";
                    for (var x = 0; x < asset.categories.length; x++) {
                        if (x > 0)
                            detailRow2 += ", ";
                        detailRow2 += asset.categories[x].category_name;
                    }
                    detailRow2 += "</div><div class=\"clear\"><!-- clear --></div></div>";
                    var detailRow3 = "<div class=\"detail_row\"><div class=\"col1\">description</div><div class=\"col2\">" + asset.description + "</div><div class=\"clear\"><!-- clear --></div></div>";

                    var assetDetails = "<div class=\"asset_details\">" + detailRow1 + detailRow2 + detailRow3 + "</div>";

                    var gridModel =
                    {
                        assetid: asset.asset_id,
                        file_name: asset.file_name,
                        user_name: currentVersion.user_name,
                        upload_date: new Date(parseInt(currentVersion.created_dt.substr(6))),
                        file_type: currentVersion.file_type,
                        remove_btn: "<div class=\"grid_remove\">&nbsp;</div>",
                        edit_btn: "<div class=\"grid_edit\">&nbsp;</div>",
                        download_btn: "<div class=\"grid_download\" rel=\"" + asset.file_name + "\">&nbsp;</div>",
                        detail_content: assetDetails
                    };

                    $("#assetTable").jqGrid('addRowData', asset.asset_id, gridModel);


                    $("#assetTable #" + asset.asset_id + " .grid_edit").data("info", { modal: "editasset", assetId: asset.asset_id, includeRelationships: "true" }).one("click", inContextMgr.sideTrack.openModal);
                }



                $("#assets .asset_info").toggle(
                function () {
                    $(".asset_details").hide();
                    $(this).siblings(".asset_details").first().show();
                    modalMgr.adjust(null, true);
                },
                function () {
                    $(this).siblings(".asset_details").first().hide();
                });

                $("#file_manager .ui-jqgrid-hdiv .s-ico").append("&nbsp;");

                inContextMgr.modalLoaded();
                $("#assetTable").trigger("reloadGrid");
            }
            });
    },
    removeAsset: function (event) {
        var asset =
        {
            asset_id: event.data.assetId,
            file_name: "",
            description: "",
            enabled: true,
            deleted: true
        };

        ajax.go({
            url: '/incontext/webservices/AssetManagement.svc/DeleteAsset',
            data: JSON.stringify({ asset: asset }),
            type: "POST"
        }, {
            func: function (data) {
                var url = '/incontext/webservices/AssetManagement.svc/GetAssets/True';
                inContextMgr.modals["assetmanager"].custom.refreshAssets(url);
            }
        });
    }
};

function refreshAssets() {
    var url = '/incontext/webservices/AssetManagement.svc/GetAssets/True';
    inContextMgr.modals["assetmanager"].custom.refreshAssets(url);
    $("#lnkMgr").click();
}