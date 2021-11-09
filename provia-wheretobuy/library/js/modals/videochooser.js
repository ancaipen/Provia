// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["videochooser"].events, {
    onOpen: function (props) {  //define the open function
        if (typeof (console) != "undefined")
            console.log(props.result);

        var audioItems = props.result.result;

        $.each(audioItems, function (index, item) {
            var categoryStyle = "article";
            if (index % 2 == 1)
                categoryStyle = "article_alternative";

            $("#articles").append("<div id=\"" + item.id + "\" class=\"row " + categoryStyle + "\"><div class=\"articleName\"><a href=\"javascript:void(0)\">" + item.name + "</a>" + (!item.enabled ? " <span>DISABLED</span>" : "") + "</div><div class=\"clear\"><!-- clear --></div></div>");
            $("#" + item.id).data('object', item).find("a").data("info", { modal: "editvideo", title: "Edit Video Item", typeName: "Audio", guid: item.id }).bind("click", inContextMgr.sideTrack.openModal);
        });

        $("#addArticle").data("info", { modal: "editvideo", title: "Add Video Item", typeName: "Video", guid: "00000000-0000-0000-0000-000000000000", newItem: true }).bind("click", inContextMgr.sideTrack.openModal);


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
