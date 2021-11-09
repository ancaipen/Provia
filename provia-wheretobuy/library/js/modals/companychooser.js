// see editassets.js for examples of making this extensible
// core functionality defined in library/js/incontext.js
$.extend(inContextMgr.modals["companychooser"].modalOptions, { delayLoad: false });
$.extend(inContextMgr.modals["companychooser"].events, {
    onOpen: function (props) {  //define the open function
        if (typeof (console) != "undefined")
            console.log(props.result);

        var companys = props.result.result;

        $.each(companys, function (index, company) {
            var categoryStyle = "company";
            if (index % 2 == 1)
                categoryStyle = "company_alternative";

            $("#companys").append("<div id=\"" + company.id + "\" class=\"row " + categoryStyle + "\"><div class=\"companyName\"><a href=\"javascript:void(0);\">" + company.name + "</a><span>" + company.startDate + " - " + company.endDate + "</span></div><div class=\"clear\"><!-- clear --></div></div>");
            $("#" + company.id + " a").data("info", { modal: "companyeditor", title: "Edit Company", typeName: "Company", guid: company.id }).bind("click", inContextMgr.sideTrack.openModal);

            $("#" + company.id).data('object', company);
        });

        $("#addCompany").data("info", { modal: "companyeditor", title: "Add Company", typeName: "Company", guid: "00000000-0000-0000-0000-000000000000", newItem: true }).bind("click", inContextMgr.sideTrack.openModal);

    },
    onValidate: function () {  //define the validaiton function
        var errors = [];
        return errors;
    },
    onSave: function () {  //define the save function

    }
});
