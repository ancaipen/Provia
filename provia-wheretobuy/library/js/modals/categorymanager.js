$.extend(inContextMgr.modals["categorymanager"].modalOptions, { delayLoad: true });
$.extend(inContextMgr.modals["categorymanager"].events, {
    onOpen: function (props) {
        inContextMgr.modals["categorymanager"].custom.refreshCategories();

        $("#txtAddCategory").emptyText({ defaultText: "New Category" }).onEnter($("#btnAddCategory"));

        $("#btnAddCategory").click(inContextMgr.modals["categorymanager"].custom.addCategory);
    },
    onValidate: function () {
        var errors = [];

        return errors;
    },
    onSave: function () {

    }
});

inContextMgr.modals["categorymanager"].custom = {
    addCategory: function () {

        var category_name = $("#txtAddCategory").val();
        if (regEx.isMatch(category_name, "empty") || category_name.toLowerCase() === $("#txtAddCategory").data("_eto").defaultText.toLowerCase()) {
            $("#txtAddCategory").css("border", "1px solid red");
            return;
        }
        $("#txtAddCategory").css("border", "");

        var category = { category_name: "", enabled: "" }
        category.category_name = $("#txtAddCategory").val();
        category.enabled = true;

        ajax.go({
            url: '/incontext/webservices/MappingManagement.svc/AddCategory',
            data: JSON.stringify({ category: category }),
            type: "POST"
        }, {
            func: function (data) {
                inContextMgr.modals["categorymanager"].custom.refreshCategories(data);
            }
        });
    },
    editCategory: function (event) {
        var categoryId = event.data.categoryId;

        $(".view_category").show();
        $(".edit_category").hide();
        $(".edit").show();
        $(".save").hide();

        $("#" + categoryId + " .view_category").hide();
        $("#" + categoryId + " .edit_category").show();

        $("#" + categoryId + " .edit").hide();
        $("#" + categoryId + " .save").show();
    },
    refreshCategories: function (results) {
        $("#categories").empty();
        $("#adminBtnCancel").hide();
        
        ajax.go({ url: '/incontext/webservices/MappingManagement.svc/GetCategoriesWithCount/False' },
            { func: function (props) {
                var data = props.result;
                for (var i = 0; i < data.result.length; i++) {
                    var category = data.result[i];

                    var categoryStyle = "category";
                    if (i % 2 == 1)
                        categoryStyle = "category_alternative";

                    if (results) {
                        if (results.result.result === category.category_id)
                            categoryStyle = "category_added";
                    }

                    var view = "<div class=\"view_category\"><div class=\"category_name\">" + category.category_name + "</div><div class=\"category_mapping_count\">" + category.mapping_count + "</div><div class=\"category_enabled\"><input type=\"checkbox\" " + (category.enabled ? "checked=\"checked\"" : "") + " disabled =\"disabled\" /></div></div>";
                    var edit = "<div class=\"edit_category\"><div class=\"category_name\"><input type=\"text\" id=\"updateCategory_" + category.category_id + "\" value=\"" + category.category_name + "\" /></div><div class=\"category_mapping_count\">" + category.mapping_count + "</div><div class=\"category_enabled\"><input type=\"checkbox\" id=\"enabledCategory_" + category.category_id + "\" " + (category.enabled ? "checked=\"checked\"" : "") + " /></div></div>";
                    var actions = "<div class=\"actions\"><div class=\"remove\" title=\"Remove\">&nbsp;</div><div class=\"edit\" title=\"Edit\">&nbsp;</div><div class=\"save\" title=\"Save\">&nbsp;</div></div>";
                    $("#categories").append("<div class=\"" + categoryStyle + "\" id=\"" + category.category_id + "\">" + view + edit + actions + "<div class=\"clear\"><!-- clear --></div></div>");

                    $("#" + category.category_id + " .remove").bind("click", { categoryId: category.category_id }, function (e) {
                        conBox.ask({ question: "Are you sure you would like to delete this category?", onYes: function () {
                            inContextMgr.modals["categorymanager"].custom.removeCategory(e);
                        }
                        });
                    });
                    $("#" + category.category_id + " .edit").bind("click", { categoryId: category.category_id }, inContextMgr.modals["categorymanager"].custom.editCategory);
                    $("#" + category.category_id + " .save").bind("click", { categoryId: category.category_id }, inContextMgr.modals["categorymanager"].custom.saveCategory);


                }
                $("#adminBtnCancel").show();

                inContextMgr.modalLoaded();
            }
            });
    },
    removeCategory: function (event) {
        var category = { category_id: event.data.categoryId }

        ajax.go({
            url: '/incontext/webservices/MappingManagement.svc/DeleteCategory',
            data: JSON.stringify({ category: category }),
            type: "POST"
        }, {
            func: function (data) {
                inContextMgr.modals["categorymanager"].custom.refreshCategories(data);
            }
        });
    },
    saveCategory: function (event) {
        var categoryId = event.data.categoryId;
        var categoryName = $("#" + categoryId + " #updateCategory_" + categoryId).val();
        var categoryEnabled = $("#" + categoryId + " #enabledCategory_" + categoryId).is(':checked');

        var category = { category_id: categoryId, category_name: categoryName, enabled: categoryEnabled }

        ajax.go({
            url: '/incontext/webservices/MappingManagement.svc/UpdateCategory',
            data: JSON.stringify({ category: category }),
            type: "POST"
        }, {
            func: function (data) {
                inContextMgr.modals["categorymanager"].custom.refreshCategories(data);
            }
        });
    }
};
