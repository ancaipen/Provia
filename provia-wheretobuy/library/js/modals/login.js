$.extend(inContextMgr.modals["login"].modalOptions, { showClose: false });
$.extend(inContextMgr.modals["login"].events, {
    onOpen: function () {
        $("#txtUsername").emptyText({ defaultText: "Username" }).onEnter($("#adminBtnSave"));
        $("#txtPassword").emptyText({ defaultText: "Password", useLayer: true }).onEnter($("#adminBtnSave"));
    },
    onValidate: function () {
        var errors = [];
        var username = $.trim($("#txtUsername").val());
        var password = $.trim($("#txtPassword").val());

        if (regEx.isMatch(username, "empty") || username.toLowerCase() === $("#txtUsername").data("_eto").defaultText.toLowerCase())
            errors.push("Username is not specified");
        if (regEx.isMatch(password, "empty"))
            errors.push("Password is not specified");

        return errors;
    },
    onSave: function () {
        var loginModel = { username: "", password: "" };

        loginModel.username = $.trim($("#txtUsername").val());
        loginModel.password = $.trim($("#txtPassword").val());

        return { loginModel: loginModel };
    }
});