jQuery(document).ready(function($) {
    $("#text_search").keyup(function() {
        var sval = $(this).val().toUpperCase();
        var isNotFound = true;
        $(".ncheckbox").each(function() {
            var title = $(this).attr("ctitle").toUpperCase();
            var n = title.indexOf(sval);
            if (n >= 0) {
                $(this).parent().show();
                isNotFound = false;
            } else {
                $(this).parent().hide();
                // $(this).prop("checked", false);
            }
        });
        if (isNotFound) {
            $(".cmessage").show();
        } else {
            $(".cmessage").hide();
        }
    });
    $("#clear_btn").live("click", function() {
        $("#text_search").val("");
        var sval = $("#text_search").val().toUpperCase();
        var isNotFound = true;
        $(".ncheckbox").each(function() {
            var title = $(this).attr("ctitle").toUpperCase();
            var n = title.indexOf(sval);
            if (n >= 0) {
                $(this).parent().show();
                isNotFound = false;
            } else {
                $(this).parent().hide();
                // $(this).prop("checked", false);
            }
        });
        if (isNotFound) {
            $(".cmessage").show();
        } else {
            $(".cmessage").hide();
        }
    });

    // input search page

    $("#text_search_page").keyup(function() {
        var sval = $(this).val().toUpperCase();
        var isNotFound = true;
        $(".ncheckboxpage").each(function() {
            var title = $(this).attr("ctitle").toUpperCase();
            var n = title.indexOf(sval);
            if (n >= 0) {
                $(this).parent().show();
                isNotFound = false;
            } else {
                $(this).parent().hide();
                // $(this).prop("checked", false);
            }
        });
        if (isNotFound) {
            $(".cmessage-page").show();
        } else {
            $(".cmessage-page").hide();
        }
    });
    $("#clear_btn_page").live("click", function() {
        $("#text_search_page").val("");
        var sval = $("#text_search_page").val().toUpperCase();
        var isNotFound = true;
        $(".ncheckboxpage").each(function() {
            var title = $(this).attr("ctitle").toUpperCase();
            var n = title.indexOf(sval);
            if (n >= 0) {
                $(this).parent().show();
                isNotFound = false;
            } else {
                $(this).parent().hide();
                // $(this).prop("checked", false);
            }
        });
        if (isNotFound) {
            $(".cmessage-page").show();
        } else {
            $(".cmessage-page").hide();
        }
    });

    // check all for post

    $('#checkallpost').change(function() {
        var checked = $(this).is(':checked');
        $(".ncheckbox").each(function() {
            if (checked) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });
    });
    var checked_all = true;
    $(".ncheckbox").each(function() {
        if ($(this).is(':checked') == false) {
            checked_all = false;
        }
    });
    $('#checkallpost').prop('checked', checked_all);
    $(".ncheckbox").change(function() {
        var checked_all = true;
        $(".ncheckbox").each(function() {
            if ($(this).is(':checked') == false) {
                checked_all = false;
            }
            $('#checkallpost').prop('checked', checked_all);
        });
    });

    // check all for page

    $('#checkallpage').change(function() {
        var checked = $(this).is(':checked');
        $(".ncheckboxpage").each(function() {
            if (checked) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });
    });
    var checked_all = true;
    $(".ncheckboxpage").each(function() {
        if ($(this).is(':checked') == false) {
            checked_all = false;
        }
    });
    $('#checkallpage').prop('checked', checked_all);
    $(".ncheckboxpage").change(function() {
        var checked_all = true;
        $(".ncheckboxpage").each(function() {
            if ($(this).is(':checked') == false) {
                checked_all = false;
            }
            $('#checkallpage').prop('checked', checked_all);
        });
    });
});
