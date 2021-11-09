// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["articlechooser"].modalOptions, { delayLoad: false });
$.extend(inContextMgr.modals["articlechooser"].events, {
    onOpen: function (props) {  //define the open function
        if (typeof (console) != "undefined")
            console.log(props.result);

        var articles = props.result.result;

        var path = window.location.pathname.substring(1);
        var parts = path.split(/\//);
        var prefix = parts[0] == "" ? "/public" : "/" + parts[0];

        $.each(articles, function (index, article) {
            var categoryStyle = "article";
            if (index % 2 == 1)
                categoryStyle = "article_alternative";

            $("#articles").append("<div id=\"" + article.id + "\" class=\"row " + categoryStyle + "\"><div class=\"articleName\"><a href=\"javascript:void(0)\">" + article.name + "</a>" + "</div><div class=\"clear\"><!-- clear --></div></div>");
            $("#" + article.id).data('object', article).find("a").data("info", { modal: "articleeditor", title: "Edit Article", typeName: "Article", guid: article.id }).bind("click", inContextMgr.sideTrack.openModal);
        });

        $("#addArticle").data("info", { modal: "articleeditor", title: "Add Article", typeName: "Article", guid: "00000000-0000-0000-0000-000000000000", newItem: true, shortNamePrefix: "/public/resources/" }).bind("click", inContextMgr.sideTrack.openModal);


    },
    onValidate: function () {  //define the validaiton function
        var errors = [];
        return errors;
    },
    onSave: function () {  //define the save function

    }
});


function getProperty(propertyList, name) {
    var value = "";
    $(propertyList).each(function () {
        if (this.propertyName == name) {
            value = this.value;
            return;
        }
    });
    return value;
}
