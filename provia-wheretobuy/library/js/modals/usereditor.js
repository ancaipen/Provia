$.extend(inContextMgr.modals["usereditor"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["usereditor"].events, {
    onOpen: function (props) {
        if (typeof (console) != "undefined")
            console.log(props.result);
        $("#hdnUser").val(props.result.result.userId);
        $("#txtUserName").val(props.result.result.username);
        if (props.result.result.username !== null && !regEx.isMatch(props.result.result.username, "empty")) {
            $("#txtUserName").attr("readonly", true);
        }
        else {
            $("#divOldPw").hide();
            $("#divOptional").hide();
        }
        $("#txtFName").val(props.result.result.firstName);
        $("#txtLName").val(props.result.result.lastName);
        $("#txtEmail").val(props.result.result.email);

        $('.formLine:visible').filter(":even").css('background-color', '#eaeaea');


    },
    onValidate: function () {
        var errors = [];
        if (!regEx.isMatch($("#txtNPW").val(), "empty")) {
            if (regEx.isMatch($("#txtOPW").val(), "empty") && $("#txtUserName").val() === readCookie("__RCMS_UNAME")) {
                errors.push("Current password was not provided when attempting to change it.");
            }
            else if ($("#txtNPW").val() !== $("#txtCPW").val()) {
                errors.push("Passwords do not match.");
            }
        }
        if (regEx.isMatch($("#txtLName").val(), "empty"))
            errors.push("Last Name cannot be empty.");
        if (regEx.isMatch($("#txtFName").val(), "empty"))
            errors.push("First Name cannot be empty.");

        if (!regEx.isMatch($("#txtEmail").val(), "email"))
            errors.push("Email is not valid.");
        if (regEx.isMatch($("#txtUserName").val(), "empty"))
            errors.push("Username cannot be empty.");
        return errors;
    },
    onSave: function () {

        var u = {
            userId: $("#hdnUser").val(),
            username: $("#txtUserName").val(),
            password: $("#txtNPW").val(),
            oldPassword: $("#txtOPW").val(),
            firstName: $("#txtFName").val(),
            lastName: $("#txtLName").val(),
            email: $("#txtEmail").val(),
            roles: null
        };


        if (typeof (console) != "undefined")
            console.log(u);
        return { user: u };
    }
});
