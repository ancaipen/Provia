$.extend(inContextMgr.modals["usermanager"].modalOptions, { showClose: true });
$.extend(inContextMgr.modals["usermanager"].events, {
    onOpen: function (props) {
        $(props.result.result.users).each(function () {
            var user = this;
            $('#tblUsers').append('<tr><td>' + user.username + '</td><td><select class="usrMgrSelect" /></td><td class="enabledHolder"><input class="usrMgrInput" type="checkbox" /></td><td><a href="javascript:void(0)" class="editUser" title="Edit">&nbsp;</a><a href="javascript:void(0)" class="deleteUser" title="Delete">&nbsp;</a></td></tr>');
            $('#tblUsers tr:last').data("user", user);
            $('#tblUsers tr:last a.editUser').data("info", { modal: "usereditor", guid: user.userId }).bind("click", inContextMgr.sideTrack.openModal);
            $('#tblUsers tr:last input').attr("checked", user.enabled);
            if (user.username == readCookie("__RCMS_UNAME")) {
                $('#tblUsers tr:last input, #tblUsers tr:last select').attr("disabled", true);
                $('#tblUsers tr:last a.deleteUser').hide();
            } else {
                $('#tblUsers tr:last a.deleteUser').click(function () {
                    var userRow = $(this).parent().parent();
                    conBox.ask({
                        question: "Are you sure you want to delete '" + user.username + "'?",
                        onYes: function () {
                            inContextMgr.modals["usermanager"].custom.deleteUser(userRow);
                        }
                    });
                });
            }
        });
        $("a#lnkNew").data("info", { modal: "usereditor", guid: GUID.empty }).bind("click", inContextMgr.sideTrack.openModal);

        $(props.result.result.roles).each(function () {
            $("#tblUsers select").append($("<option />", { value: this.roleId }).text(this.roleName));
        });
        $("#tblUsers select").prepend($("<option />", { value: "" }).text("-- Role --")).val("");
        $("#tblUsers tr:not(tr:first)").each(function () {
            if ($(this).data("user").roles.length > 0) {
                $(this).find("select:first").val($(this).data("user").roles[0].roleId);
            }
        });
        $('#tblUsers tr:even').addClass('lineItemEven');

    },
    onValidate: function () {
        var errors = [];
        $("#tblUsers select").each(function () {
            if (errors.length == 0 && regEx.isMatch($(this).val(), "empty") || $(this).val() == null) {
                errors.push("All users must be assigned a role.");
                return;
            }
        });
        return errors;
    },
    onSave: function () {

        var userModelList = [];
        $("#tblUsers tr:not(tr:first)").each(function () {
            var user = $(this).data("user");
            if (user.username !== readCookie("__RCMS_UNAME")) {
                user.enabled = $(this).find(":checkbox").attr("checked") != "";
                user.roles = [{ roleId: $(this).find("option:selected").val(), roleName: $(this).find("option:selected").text()}];
                userModelList.push(user);
            }
        });

        return { userlist: userModelList };
    }
});


inContextMgr.modals["usermanager"].custom = {
    deleteUser: function (userRow) {
        var user = userRow.data("user");
        //delete user.password;
        ajax.go({
            url: '/incontext/webservices/incontext.svc/DeleteUser',
            data: JSON.stringify({ user: user }),
            type: "POST"
        }, { func: function (props) { props.userRow.remove(); $(window).trigger("resize"); }, props: { userRow: userRow, json: true} });
    }
};
