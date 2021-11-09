
$(function () {
    $('#dialog').dialog({
        autoOpen: false,
        width: '60%',
        resizable: false,
        title: 'Edit Item',
        url: "",
        modal: true,
        position: [null, 100],
        buttons: {
            "Save": function () {

                if ($('#parentrelator').html() != null) {
                    saveParentRelationship();
                } else if ($('#relator').html() != null) {
                    saveRelationship();
                } else {

                    if (typeof tinyMCE != 'undefined')
                        tinyMCE.triggerSave();

                    $("#dialog form").ajaxSubmit({
                        success: function () {
                            alert('Success!');
                            self.location.reload();
                        },
                        failure: function () {
                            alert('An error has occurred.');
                        }
                    });

                    //$("#dialog form").submit();            

                }
            },
            //            "Save & Publish": function () {
            //                $("#publish").val(true);
            //                $("#dialog form").submit();
            //            },
            "Close": function () {
                $('.html').each(function (i) {
                    tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id"));
                });
                $(this).html("");
                $(this).dialog("close");
            },
            "Delete": function () {

                var delConfirm = confirm("Are you sure you want to DELETE this item?")
                if (delConfirm == true) {
                    $("#toDelete").attr("value", "true");
                    $("#toDelete").attr("data-val", "true");
                    $("#dialog form").submit();
                }

            }
        },
        close: function (event, ui) {
            $('.html').each(function (i) {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id"));
            });
            $(this).html("");
        }

    });


});

$(function () {
    if ($('.editor-image img').attr('src') == '')
        $('.editor-image img').css('display', 'none');

    $('#imgselector').dialog({
        autoOpen: false,
        width: 800,
        resizable: false,
        title: 'Asset Manager',
        url: "",
        modal: false,
        position: [null, 120],
        stack: true,
        zIndex: 9999,
        buttons: {
            "Save & Close": function () {
                $(this).dialog("close");
            },
           
            "Close": function () {
                $(this).dialog("close");
            }
        }
    });
});

$(function () {
    $('#uploader').dialog({
        autoOpen: false,
        width: 800,
        resizable: false,
        title: 'Asset Uploader',
        url: "",
        modal: true,
        position: [null, 120],
        stack: true,
        zIndex: 9999
        
    });
});
$(".incontext-upload").on("click", function () {
    $('#uploader').load($(this).attr("rel"));
    $('#uploader').dialog('open');
});
var callbackInput;
$(".selectorlink").on("click", function () {
    callbackInput = $(this).attr("name");
    $('#imgselector').load($(this).attr("rel"));
    if ($('#imgselector').html() != "") {
        $('#imgselector').html("");
    }
    $('#imgselector').dialog('open');



})
$(".img-selector").on("click", function () {

    $(".img-selector").removeClass('ui-selected');
    $(this).addClass('ui-selected');

    $("#" + callbackInput).val($(this).attr("rel"));
    $("[name=QuickView_" + callbackInput + "]").attr("src", $(this).attr("rel")).css('display', '');
});

function ReLoadTinyMCE()
{
    $('.html').each(function (i) {

        var editor_id = $(this).attr("id");

        if (window.tinyMCE.get(editor_id)) {
            tinyMCE.execCommand('mceRemoveEditor', true, editor_id);
        }

        //alert('test editor load: ' + editor_id);
        tinymce.execCommand('mceAddEditor', true, editor_id);
        tinyMCE.triggerSave();

    });
}

function LoadTinyMCE() {
    
    window.tinymce.dom.Event.domLoaded = true;

    tinyMCE.init({
        script_url: '/library/js/tinymce/tinymce.min.js',
        mode: "none",
        plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
        ],
        theme: "modern",
        convert_urls: false,
        theme_advanced_toolbar_location: "top",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_toolbar_align: "left",
        add_form_submit_trigger: false,
		force_br_newlines : false,
		force_p_newlines : false,
		forced_root_block : '',
        external_link_list_url: "/library/js/tiny_mce/pages.js",
        theme_advanced_buttons1: "bold,italic,underline,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
        theme_advanced_buttons2: "table,bullist,numlist,outdent,indent,undo,redo,link,unlink,anchor,image,cleanup,code,hr,removeformat,visualaid,sub,sup,fullscreen",
        theme_advanced_buttons3: "",
        valid_children: "+body[style]",
        extended_valid_elements: "li[class|id|onclick|style], a[href|rel|rev|charset|hreflang|tabindex|accesskey|type|piccaption|name|target|title|class|id|alt|style|onclick|option],iframe[wmode|src|frameborder|width|height|style|class|id|width|height|onload],header[id|class|width|height|style],article[id|class|width|height|style],link[href|id|class|rel],script[id|class|language|type|src],style[type|rel|class]",

        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
        image_advtab: true,

        external_filemanager_path: "/library/js/filemanager/",
        filemanager_title: "Responsive Filemanager"

    });

    setTimeout(ReLoadTinyMCE(), 1500);

}
