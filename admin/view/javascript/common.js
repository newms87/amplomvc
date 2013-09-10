//-----------------------------------------
// Submit form on enter key
// Confirm Actions (delete, uninstall)
// Drop down menu
//-----------------------------------------
$(document).ready(function () {
    //Submit form on enter key
    $('form input').keydown(function (e) {
        if (e.keyCode == 13) {
            $(this).closest('form').submit();
        }
    });

    // Confirm Delete
    $('#form').submit(function () {
        if ($(this).attr('action').indexOf('delete', 1) != -1) {
            if (!confirm('Deleting this entry will completely remove all data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });

    $('.action-delete').click(function () {
        return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
    });

    // Confirm Uninstall
    $('a').click(function () {
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
            if (!confirm('Uninstalling will completely remove the data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });


    //toggle active state for drop down menus
    $('.link_list li').mouseover(function () {
        if (!$(this).hasClass('hover')) {
            $(this).closest('.top_menu').find('.hover').removeClass('hover');
            $(this).addClass('hover').parents('.link_list li').addClass('hover');
        }
    });

});

function show_msg(type, html) {
    if ($('.content:first .' + type).length) {
        $('.content:first .' + type).append('<br />' + html);
    } else {
        $('.content:first').prepend('<div class="message_box ' + type + '" style="display: none;">' + html + '<span class="close"></span></div>');
        $('.message_box.' + type).fadeIn('slow');
        $('.message_box .close').click(function () {
            $(this).parent().remove();
        });
    }
}

function show_msgs(data) {
    for (var m in data) {
        if (typeof data[m] == 'object') {
            msg = '';

            for (var m2 in data[m]) {
                msg += (msg ? '<br />' : '') + data[m][m2];
            }

            show_msg(m + ' ' + m2, msg, true);
        }
        else {
            show_msg(m, data[m], true);
        }
    }
}

if (!console) {
    console = {};
    console.log = function (msg) {
    };
    console.dir = function (obj) {
    };
}
