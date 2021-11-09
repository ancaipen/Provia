$.extend(inContextMgr.modals["editpage"].modalOptions, { showClose: true, delayLoad: true });
$.extend(inContextMgr.modals["editpage"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(inContextMgr.currentModalInfo);

        $('.pageEditor').data('object', props.result.result);

        var selectedPermissions = {};
        var selectedCategories = {};

        var isArticle = false;

        inContextMgr.modals["editpage"].custom.isNewItem = inContextMgr.currentModalInfo.newItem;
        if (inContextMgr.modals["editpage"].custom.isNewItem) {

            var typeName = "";

            if (inContextMgr.currentModalInfo.hasOwnProperty("typeName"))
                typeName = inContextMgr.currentModalInfo.typeName;

            if (typeName === "Article")
                isArticle = true;

            $("#adminDelete").hide();

            if (inContextMgr.currentModalInfo.hasOwnProperty("shortNamePrefix"))
                $(".pageEditor #shortNamePrefix").html(inContextMgr.currentModalInfo.shortNamePrefix);

            if (inContextMgr.currentModalInfo.hasOwnProperty("title"))
                $(".modalPane .modalHeader .innerContent h3").html(inContextMgr.currentModalInfo.title);
        }
        else {
            var page = props.result.result;
            $('#txtName').val(page.name);
            $('#txtShortName').val(page.shortName).attr('disabled', true);
            $('#cbEnabled').attr('checked', page.enabled);
            $(page.propertyList).each(function () {
                if (this.propertyName == 'meta_keywords' || this.propertyName == 'meta_description') {
                    $('#' + this.propertyName + ' textarea').val(this.value).attr('id', this.id);
                }
                if (this.propertyName == "page_title") {
                    $("#txtPageTitle").val(this.value);
                }
                if (this.propertyName == "Private") {
                    $('#chkPrivate').attr('checked', (this.value === 'true'));
                }
            });

            if (page.itemType.typeName === "Article") {
                $('#adminDelete').click(function () {
                    conBox.ask({
                        question: "Are you sure you want to delete this page?",
                        onYes: function () {
                            inContextMgr.modals["editpage"].custom.deleteItem();
                            window.location = '/';
                        }
                    });
                });

                isArticle = true;
                selectedPermissions = page.permissions;
                selectedCategories = page.categories;
            }
        }

        $('.pageProps li textarea').each(function () {
            if (regEx.isMatch($(this).attr("id"), "empty")) {
                $(this).attr("id", "00000000-0000-0000-0000-000000000000");
            }
        });

        if (isArticle) {

            $(".pageEditor #categories").removeClass("hide");
            $(".pageEditor #permissions").show();
            $(".pageEditor #private").show();


            ajax.go({ url: '/incontext/webservices/MappingManagement.svc/GetCategories' },
            { func: function (props) { inContextMgr.modals["editpage"].custom.populateCategories(props, selectedCategories); } });

            ajax.go({ url: '/eClient/SelectAccountAPI/GetGroupList' },
            { func: function (props) { inContextMgr.modals["editpage"].custom.groupQuickSearch.init(props, selectedPermissions); } });

        } else {
            inContextMgr.modalLoaded();
        }
    },
    onValidate: function () {
        var errors = [];
        if ($.trim($("#txtPageTitle").val()) === "") {
            errors.push("You must enter a page title");
        }


        return errors;
    },
    onSave: function () {
        $("ul#quickSearchResults").remove();

        var shortName = $(".pageEditor #shortNamePrefix").text() + $('#txtShortName').val();

        var isPrivate = $("#chkPrivate").is(':checked')
        var url = isPrivate ? "/eClient/resources" + shortName.substring(shortName.lastIndexOf("/")) : shortName;

        if (inContextMgr.modals["editpage"].custom.isNewItem) {
            inContextMgr.currentModalInfo.callback = function () {
                document.location.href = url;
            };
        } else if (isPrivate && document.location.href.indexOf(url) == -1) {
            inContextMgr.currentModalInfo.callback = function () {
                document.location.href = url;
            };
        } else if (!isPrivate && document.location.href.indexOf(url) == -1) {
            inContextMgr.currentModalInfo.callback = function () {
                document.location.href = url;
            };
        }

        var itemModel = $('.pageEditor').data('object');
        var properties = [];
        properties.push({ propertyName: "meta_description", value: $("#txtDescription").val() });
        properties.push({ propertyName: "meta_keywords", value: $("#txtKeywords").val() });
        properties.push({ propertyName: "page_title", value: $("#txtPageTitle").val() });

        itemModel.name = $('#txtName').val();
        itemModel.shortName = shortName;
        itemModel.enabled = $('#cbEnabled').attr('checked');

        var typeName = "";
        if (inContextMgr.currentModalInfo.hasOwnProperty("typeName"))
            typeName = inContextMgr.currentModalInfo.typeName;

        if (itemModel.itemType.typeName === "Article" || typeName === "Article") {
            properties.push({ propertyName: "Private", value: $("#chkPrivate").attr('checked') });

            var categories = [];

            $(".pageEditor #categorySelect option:selected").each(function () {
                var category = {
                    mapping_from_key: $(this).val(),
                    is_priority: $(this).data("isPriority"),
                    enabled: true
                };

                categories.push(category);
            });

            var permissions = [];

            $(".pageEditor #permissionsList li").each(function () {

                var permission = {
                    mapping_from_key: $(this).data("key"),
                    enabled: true
                };

                permissions.push(permission);
            });

            itemModel.categories = categories;
            itemModel.permissions = permissions;
        }

        itemModel.propertyList = properties;

        var newItem = { itemModel: itemModel };
        return newItem;
    }
});

inContextMgr.modals["editpage"].custom = {
    isNewItem: false,
    deleteItem: function () {
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteItem',
            data: JSON.stringify({ itemId: inContextMgr.currentModalInfo.guid }),
            type: "POST"
        });
    },
    populateCategories: function (props, selectedCategories) {
        var categories = props.result.result;

        $(".pageEditor #categories select[multiple]").empty();

        for (var i = 0; i < categories.length; i++) {
            var selected = false;
            var isPriority = false;
            for (var j = 0; j < selectedCategories.length; j++) {
                if (categories[i].category_id == selectedCategories[j].mapping_from_key) {
                    selected = true;
                    isPriority = selectedCategories[j].is_priority;

                    break;
                }
            }
            $(".pageEditor #categories select[multiple]").append("<option value=\"" + categories[i].category_id + "\"" + (selected ? " selected=\"selected\"" : "") + ">" + categories[i].category_name + "</option>");
            $(".pageEditor #categories select option:last").data("isPriority", isPriority);
        }

        $(".pageEditor #categories select[multiple]").bsmSelect({
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
    groupQuickSearch: {
        groupList: [],
        filteredList: [],
        addGroup: function (groupKey) {
            $("ul#quickSearchResults").addClass("hide").empty();
            $("#txtGroupSearch").val("").blur();
            var groupName = "";
            $(inContextMgr.modals["editpage"].custom.groupQuickSearch.groupList).each(function () {
                if (this.groupKeyField.toString() == groupKey) {
                    groupName = this.groupNameField;
                    return;
                }
            });

            $("#permissionsList").append($('<li class="bsmListItem-custom" style="display: block;"><span class="bsmListItemLabel-custom">' + groupName + '</span><a href="javascript:void(0)" class="bsmListItemRemove-custom"><!-- --></a></li>').data("key", groupKey));
            $("#permissionsList .bsmListItemRemove-custom:last").bind("click", inContextMgr.modals["editpage"].custom.groupQuickSearch.removeGroup);
        },
        removeGroup: function () {
            $(this).parent().remove();
        },
        displayGroups: function () {
            inContextMgr.modals["editpage"].custom.groupQuickSearch.filteredList = inContextMgr.modals["editpage"].custom.groupQuickSearch.filteredList.slice(0, 5);
            var $groupList = $("ul#quickSearchResults");
            $groupList.css({ top: $("#txtGroupSearch").offset().top - $("body").offset().top + 18, left: $("#txtGroupSearch").offset().left });
            $groupList.removeClass("hide").empty();
            $(inContextMgr.modals["editpage"].custom.groupQuickSearch.filteredList).each(function () {
                $groupList.append("<li><a href='javascript:void(0)' rel='" + this.groupKeyField + "'>" + this.groupNameField + "</a></li>");
            });
            $groupList.find("a").bind("click", function () { inContextMgr.modals["editpage"].custom.groupQuickSearch.addGroup($(this).attr("rel")); });
            $groupList.append('<li class="clear"><!-- ie --></li>');
        },
        search: function (e) {
            var query = $("#txtGroupSearch").val().toLowerCase();
            inContextMgr.modals["editpage"].custom.groupQuickSearch.filteredList = [];
            if (query.length > 2) {
                $(inContextMgr.modals["editpage"].custom.groupQuickSearch.groupList).each(function () {
                    var group = this;
                    if (group.groupNameField.toLowerCase().indexOf(query) == 0 || group.groupNumberField.indexOf(query) != -1)
                        inContextMgr.modals["editpage"].custom.groupQuickSearch.filteredList.push(this);
                });
                inContextMgr.modals["editpage"].custom.groupQuickSearch.displayGroups();
            } else {
                $("ul#quickSearchResults").addClass("hide").empty();
            }
        },
        init: function (props, selectedGroups) {
            if (!props.hasError) {
                $("#permissionsList").empty();
                inContextMgr.modals["editpage"].custom.groupQuickSearch.groupList = props.result.result.groupsListField.groupField;
                $(selectedGroups).each(function () { inContextMgr.modals["editpage"].custom.groupQuickSearch.addGroup(this.mapping_from_key); });
                $("#txtGroupSearch").emptyText({ defaultText: "Enter Group Name or Number", focusColor: "#000" }).bind("keyup", inContextMgr.modals["editpage"].custom.groupQuickSearch.search);
                $("body").append($("ul#quickSearchResults"));
            }
            inContextMgr.modalLoaded();

        }
    }
};
