$.extend(inContextMgr.modals["editasset"].modalOptions, { delayLoad: false });
$.extend(inContextMgr.modals["editasset"].events, {
    onOpen: function (props) {

        // Fix for silverlight button click issue
        changeuserAgent();

        var asset = props.result.result;
        var currentVersion = asset.versions[0];

        $("#editAsset").data("object", asset);
        $("#currentVersion").data("object", currentVersion);

        inContextMgr.modals["editasset"].custom.setAsset(props);


        Silverlight.createObjectEx({
            source: "/incontext/assets/xap/Ratchet.Framework.AssetManagement.FileUploader.xap",
            parentElement: document.getElementById("silverlightControlHost"),
            id: "sl",
            properties: {
                width: "400",
                height: "34",
                background: "white",
                alt: "<!--not installed-->",
                version: "3.0.40624.0",
                windowless: "true"
            },
            events: {
                onError: onSilverlightError,
                onLoad: null
            },
            initParams: "wsUploadAddress=/incontext/webservices/AssetManagementUpload.svc/AssetManager/UploadVersion/" + asset.asset_id + ",buttonText=Upload New Version,size=small,multiselect=false,callback=reloadAsset",
            context: null
        });


    },
    onValidate: function () {
        var errors = [];

        return errors;
    },
    onSave: function () {
        var versions = [];

        var currentVersion = $("#currentVersion").data("object");

        delete currentVersion.file_type;
        delete currentVersion.user_name;

        currentVersion.enabled = $("#editAsset #enabled").is(':checked');

        versions.push(currentVersion);

        var categories = [];

        $("#editAsset #categorySelect option:selected").each(function () {
            var category = {
                mapping_from_key: $(this).val(),
                is_priority: $(this).data("isPriority"),
                enabled: true
            };

            categories.push(category);
        });

        var asset = $("#editAsset").data("object");
        asset.file_name = $("#editAsset #file_name").val();
        asset.description = $("#editAsset #description").val();
        asset.alt_text = $("#editAsset #txtTitle").val();
        asset.categories = categories;
        asset.versions = versions;
        asset.permissions = [];
        asset.isPrivate = $("#editAsset #private").is(':checked');

        $("ul#quickSearchResults").remove();

        if (typeof (console) != "undefined")
            console.log(asset);

        return { asset: asset };
    }
});


inContextMgr.modals["editasset"].custom = {
    populateCategories: function (props, selectedCategories) {
        var categories = props.result.result;

        $("#editAsset #categories select[multiple]").empty();

        for (var i = 0; i < categories.length; i++) {
            var selected = false;
            var isPriority = false;
            for (var j = 0; j < selectedCategories.length; j++) {
                if (categories[i].category_id === selectedCategories[j].mapping_from_key) {
                    selected = true;
                    isPriority = selectedCategories[j].is_priority;
                    break;
                }
            }
            $("#editAsset #categories select[multiple]").append("<option value=\"" + categories[i].category_id + "\"" + (selected ? " selected=\"selected\"" : "") + ">" + categories[i].category_name + "</option>");
            $("#editAsset #categories select option:last").data("isPriority", isPriority);
        }
        inContextMgr.modalLoaded();

        $("#editAsset #categories select[multiple]").bsmSelect({
            showEffect: function ($el) { $el.fadeIn(); modalMgr.adjust(null, true); },
            hideEffect: function ($el) { $el.fadeOut(function () { $(this).remove(); }); },
            highlight: 'highlight',
            removeLabel: '<!-- -->',
            containerClass: 'bsmContainer',                // Class for container that wraps this widget
            listClass: 'bsmList-custom',                   // Class for the list ($ol)
            listItemClass: 'bsmListItem-custom',           // Class for the <li> list items
            listItemLabelClass: 'bsmListItemLabel-custom', // Class for the label text that appears in list items
            removeClass: 'bsmListItemRemove-custom',
            hasPriority: true
        });

    },
    reloadAsset: function () {
        ajax.go({ url: '/incontext/webservices/AssetManagement.svc/GetAssetById?assetId=' + $("#asset_id").val() + '&includeRelationships=true' },
            { func: function (props) { inContextMgr.modals["editasset"].custom.setAsset(props); } });
    },
    revertAsset: function (params) {
        var assetVersionId = params.data.assetVersionId;
        ajax.go({ url: '/incontext/webservices/AssetManagement.svc/RevertToVersion/' + assetVersionId, type: "POST" },
            { func: function () { inContextMgr.modals["editasset"].custom.reloadAsset(); } });
    },
    setAsset: function (props) {
        var asset = props.result.result;
        var currentVersion = asset.versions[0];

        $("#editAsset #asset_id").val(asset.asset_id);
        $("#editAsset #asset_version_id").val(currentVersion.asset_version_id);
        $("#editAsset #version_file_name").val(currentVersion.file_name);
        $("#editAsset #file_name").val(asset.file_name);
        $("#editAsset #txtTitle").val(asset.alt_text);
        $("#editAsset #description").val(asset.description);
        $("#editAsset #enabled").attr("checked", (currentVersion.enabled ? "checked" : ""));
        $("#editAsset #private").attr("checked", (asset.isPrivate ? "checked" : ""));
        $("#editAsset #file_size").html(Math.ceil(parseFloat(currentVersion.file_size) / 1000) + " KB");
        $("#editAsset #file_type").html(currentVersion.file_type);
        $("#editAsset #uploaded_by").html(currentVersion.user_name);
        $("#editAsset #upload_date").html((new Date(parseInt(currentVersion.created_dt.substr(6)))).toShortDateString());
        $("#editAsset #uploaded_as").html(currentVersion.file_name);

        $("#editAsset #currentDownload").data("id", currentVersion.asset_version_id).unbind("click").bind("click", function () { window.open("/public/AssetManager/GetFileByVersion/" + $(this).data("id")); });

        $("#editAsset .oldVersions").empty();
        for (var i = 1; i < asset.versions.length; i++) {
            var version = asset.versions[i];
            $("#editAsset .oldVersions").append("<div id=\"" + version.asset_version_id + "\" class=\"row\"><div class=\"fileName\">" + version.file_name + "</div><div class=\"fileSize\">" + Math.ceil(parseFloat(version.file_size) / 1000) + " KB" + "</div><div class=\"uploadDate\">" + (new Date(parseInt(version.created_dt.substr(6)))).toShortDateString() + "</div><div class=\"uploadUser\">" + version.user_name + "</div><div class=\"enabled\">" + version.enabled + "</div><div class=\"actions\"><div class=\"download\" title=\"Download\">&nbsp;</div><div class=\"revert\" title=\"Revert\">&nbsp;</div></div><div class=\"clear\"><!--clear--></div></div>");
            $("#editAsset .oldVersions #" + version.asset_version_id + " .download").data("id", version.asset_version_id).click(function () { window.open("/public/AssetManager/GetFileByVersion/" + $(this).data("id")); });
            $("#editAsset .oldVersions #" + version.asset_version_id + " .revert").bind("click", { assetVersionId: version.asset_version_id }, function (e) {
                conBox.ask({ question: "Are you sure you would like to revert this asset to a previous version?", onYes: function () {
                    inContextMgr.modals["editasset"].custom.revertAsset(e);
                }
                });
            });
        }
    }
};

function reloadAsset() {
    inContextMgr.modals["editasset"].custom.reloadAsset();
}

function changeuserAgent() {

    var altuserAgentGetter = function () {
        return "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 InternetExplorer/3.6";
    };
    if (Object.defineProperty) {
        Object.defineProperty(navigator, "userAgent", {
            get: altuserAgentGetter
        });
    } else if (Object.prototype.__defineGetter__) {
        navigator.__defineGetter__("userAgent", altuserAgentGetter);
    }
}
