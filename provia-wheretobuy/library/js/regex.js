/*
    This is a regular expression library for use in validation
*/

var regEx = {
    lib: {
        zip: new RegExp("^[0-9]{5}$|^[0-9]{5}-[0-9]{4}$"),
        date: new RegExp("^([0-9]{1,2})[./-]([0-9]{1,2})[./-]([0-9]{2}|[0-9]{4})$"),
        email: new RegExp("^([0-9a-zA-Z_]{0,1}[\.]?)+([0-9a-zA-Z_]{1})+@[0-9a-zA-Z]+[\.]{1}[0-9a-zA-Z]+[\.]?[0-9a-zA-Z]+$"),
        phone: new RegExp("^([0-9][0-9][0-9])([0-9][0-9]{2})([0-9]{4})$"),
        url: new RegExp("^((http:\/\/www\.)|(www\.)|(http:\/\/))[a-zA-Z0-9._-]+\.[a-zA-Z.]{2,5}$"),
        empty: new RegExp("^$|^\s+$")
    },
    isMatch: function (val, type) { return regEx.lib[type].test(val); }
};