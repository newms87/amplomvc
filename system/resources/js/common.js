//Similar to LESS screen sizing
var screen_width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
var screen_lg = screen_width >= 1200,
    screen_md = screen_width >= 768 && screen_width < 1200,
    screen_sm = screen_width >= 480 && screen_width < 768,
    screen_xs = screen_width < 480;


String.prototype.toSlug = function () {
    return this.toLowerCase().replace(/\s/, '-').replace(/[^a-z0-9-_]/, '');
}

String.prototype.repeat = function (times) {
    return (new Array(times + 1)).join(this);
};

String.prototype.str_replace = function (find, replace) {
    var str = this;
    for (var i = 0; i < find.length; i++) {
        str = str.replace(find[i], replace[i]);
    }
    return str;
};

//Load synchronously
function syncload(s) {
    if (s.indexOf('//') != 0 && !s.match(/^https?:\/\//)) {
        s = $ac.site_url + s;
    }

    $.ajax({
        async: false,
        cache: true,
        url: s,
        error: function (e) {
            $.error('Failed to load script from ' + s)
        },
        dataType: 'script'
    });
}

//Load jQuery Plugins On Call
$.fn.codemirror = function (params) {
    if (!$.fn.codemirror.once) {
        $.fn.codemirror.once = [];

        $.get($ac.site_url + 'admin/common/codemirror', {}, function (response) {
            $('body').append(response);
            var once = $.fn.codemirror.once;
            for (var a in once) {
                once[a].s.init_codemirror(once[a].p);
            }
        });
    }

    if (typeof this.init_codemirror == 'function') {
        this.init_codemirror(params);
    } else {
        $.fn.codemirror.once.push({s: this, p: params});
    }
}

$.ac_template = $.fn.ac_template = function (name, action, data) {
    $.ac_template = $.fn.ac_template = null;
    syncload('system/resources/js/ac_template.js');
    if (this.ac_template) this.ac_template(name, action, data);
}

$.fn.jqzoom = function (params) {
    $.fn.jqzoom = null;
    syncload('system/resources/js/jquery/jqzoom/jqzoom.js');
    if (this.jqzoom) this.jqzoom(params);
}

$.fn.use_once = function (label) {
    label = label || 'activated';
    return this.not('.' + label).addClass(label);
}

$.fn.scrollTo = function (target, options) {
    var $this = this;
    options = $.extend({}, {
        offset: 0,
        callback: null
    }, options);

    var top = target.offset().top + options.offset;
    this.stop();

    $this.animate({scrollTop: top}, {
        duration: 1000, complete: function (e) {
            if (typeof options.callback == 'function') {
                options.callback(e);
            }
        }
    });
}

//Add the date/time picker to the elements with the special classes
$.ac_datepicker = function (params) {
    $('.datepicker, .timepicker, .datetimepicker').ac_datepicker(params);
}

$.fn.ac_datepicker = function ac_datepicker(params) {
    if (!$.ui.timepicker) {
        var selector = this;
        $.ajaxSetup({cache: true});
        if (!$.ac_datepicker.loading) {
            $.ac_datepicker.loading = true;
            $.getScript($ac.site_url + 'system/resources/js/jquery/ui/datetimepicker.js', function () {
                selector.ac_datepicker(params);
            });
        }
        return;
    }

    params = $.extend({}, {
        type: null,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm'
    }, params);

    return this.each(function (i, e) {
        type = params.type ||
        $(e).hasClass('datepicker') ? 'datepicker' :
            $(e).hasClass('timepicker') ? 'timepicker' : 'datetimepicker';

        $(e)[type](params);
    });
}

$.fn.ac_radio = function (params) {
    params = $.extend({}, {
        elements: $(this).children().not('.noradio')
    }, params);

    this.find('input[type=radio]').hide();

    params.elements.each(function (i, e) {
        if ($(e).find('input[type=radio]:checked').length) {
            $(e).addClass('checked');
        }
    })

        .click(function () {
            params.elements.removeClass('checked').find('input[type=radio]').prop('checked', false);
            $(this).addClass("checked").find('input[type=radio]').prop('checked', true);
        });

    return this;
}

$.fn.ac_checklist = function (params) {
    params = $.extend({}, {
        elements: $(this).children().not('.nocheck'),
        change: null
    }, params);

    this.find('input[type=checkbox]').hide();

    params.elements.each(function (i, e) {
        if ($(e).find('input[type=checkbox]:checked').length) {
            $(e).addClass('checked');
        }
    })

        .click(function () {
            if ($(this).hasClass('checked')) {
                $(this).removeClass('checked');
                $(this).find('input[type=checkbox]').prop('checked', false).change();
            } else {
                $(this).addClass('checked');
                $(this).find('input[type=checkbox]').prop('checked', true).change();
            }

            if (typeof params.change === 'function') {
                params.change($(this), $(this).hasClass('checked'));
            }
        });

    return this;
}

//Apply a filter form to the URL
$.fn.apply_filter = function (url) {
    var filter_list = this.find('[name]');

    if (filter_list.length) {
        filter_list.each(function (i, e) {
            var $e = $(e);
            var $filter = $e.closest('.column_filter');
            var $type = $filter.find('.filter-type');

            if ($type.hasClass('not')) {
                $e.attr('name', $e.attr('name').replace(/^filter\[!?/, 'filter[!'));
            }

            if (!$type.hasClass('not') && !$type.hasClass('equals')) {
                delete filter_list[i];
            }
        });

        url += (url.search(/\?/) ? '&' : '?') + filter_list.serialize();
    }

    return url;
}

//A jQuery Plugin to update the sort orders columns (or any column needing to be indexed)
$.fn.update_index = function (column) {
    column = column || '.sort_order';

    return this.each(function (i, ele) {
        count = 0;
        $(ele).find(column).each(function (i, e) {
            $(e).val(count++);
        });
    });
}

$.fn.flash_highlight = function () {
    pos = this.offset();

    var ele = $('<div />');

    ele.css({
        background: 'rgba(255,255,255,0)',
        position: 'absolute',
        top: pos.top,
        left: pos.left,
        opacity: .8,
        'z-index': 10000
    })
        .width($(this).width())
        .height($(this).height());

    $('body').css({position: 'relative'});
    $('body').append(ele);

    ele.animate({'background-color': 'rgba(255,255,85,1)'}, {
        duration: 300, always: function () {
            ele.animate({'background-color': 'rgba(255,255,255,0)'}, {
                duration: 700, always: function () {
                    ele.remove()
                }
            });
        }
    });

    return this;
}

$.fn.overflown = function (dir, tolerance) {
    return this.each(function (i, e) {
        var over;

        if (dir) {
            over = dir === 'y' ? e.scrollHeight > (e.clientHeight + tolerance) : e.scrollWidth > (e.clientWidth + tolerance);
        }

        over = e.scrollHeight > (e.clientHeight + tolerance) || e.scrollWidth > (e.clientWidth + tolerance);

        if (over) {
            $(e).addClass('overflown');
        }
    });
}

$.fn.tabs = function (callback) {
    var $this = this;

    this.each(function (i, obj) {
        var obj = $(obj);

        $(obj.attr('href')).hide();

        obj.click(function () {
            $this.removeClass('selected');

            $this.each(function (i, e) {
                $($(e).attr('href')).hide();
            });

            obj.addClass('selected');

            content = $(obj.attr('href')).show();

            if (typeof callback === 'function') {
                callback(content.attr('id'), obj, content);
            }
            return false;
        });

        var tab_name = obj.find('.tab_name');

        if (tab_name.length) {
            $(obj.attr('href')).find('.tab_name').keyup(function () {
                tab_name.html($(this).val());
            });
        }
    });

    this.show().first().click();

    return this;
};

$.fn.ac_msg = function (type, msg, append, close) {
    if (type == 'clear') {
        return $(this).find('.messages').remove();
    }

    if (typeof msg == 'undefined') {
        msg = type;
        type = null;
    }

    if (!append) {
        append = 1;
        this.find('.messages').remove();
    }

    if (typeof msg == 'object') {
        for (var m in msg) {
            this.ac_msg(type || m, msg[m], append, close);
        }
        return this;
    }

    return this.each(function (i, e) {
        var box = $(e).find('.messages.' + type);

        if (!box.length) {
            box = $('<div />').addClass('messages ' + type);

            if (typeof close == 'undefined' || close) {
                box.append($('<div />').addClass('close').click(function () {
                    $(this).closest('.messages').remove();
                }));
            }

            if (append > 0) {
                $(e).append(box);
            } else {
                $(e).prepend(box);
            }
        }

        box.prepend($('<div />').addClass('message').html(msg));
    });
}

$.fn.ac_errors = function (errors, noclear) {
    if (!noclear) {
        this.find('.err-msg').remove();
    }

    for (var err in errors) {
        if (typeof errors[err] == 'object') {
            this.ac_errors(errors[err], true);
            continue;
        }

        var ele = this.find('[name="' + err + '"]');

        if (!ele.length) {
            ele = $('#' + err);
        }

        if (!ele.length) {
            ele = $(err);
        }

        if (!ele.length) {
            return this.ac_msg('error', errors);
        }

        ele.after($("<div />").addClass('error err-msg').html(errors[err]));
    }

    return this;
}

$.ac_errors = function (errors) {
    $('body').ac_errors(errors);
}

$.fn.fade_post = function (url, data, callback, dataType) {
    var $this = this;
    $this.stop().fadeOut(300).after($.loading);

    $.post(url, data, function (data) {
        $this.fadeIn(0);
        $.loading('stop');

        if (typeof callback === 'function') {
            callback(data);
        }
    }, dataType || null);

    return this;
}

$('.ajax-form').submit(function () {
    ac_form();
});

function ac_form(params) {
    var $form = $(this);
    var callback = params.success;
    var complete = params.complete;
    var $button = $form.find('button, input[type=submit]');
    if (!$button.length) {
        $button = $form;
    }

    params = $.extend({}, {
        data: $form.serialize(),
        dataType: 'json'
    }, params);

    params.success = function (data, textStatus, jqXHR) {
        if (typeof data == 'object') {
            if (data.error) {
                $form.ac_errors(data.error);
            }

            $form.ac_msg(data);
        } else {
            $form.replaceWith(data);
        }

        if (typeof callback == 'function') {
            callback(data, textStatus, jqXHR);
        }
    }

    params.complete = function (jqXHR, textStatus) {
        $button.loading('stop');

        if (typeof complete == 'function') {
            complete(jqXHR, textStatus);
        }
    }

    $button.loading();

    $.ajax($form.attr('action'), params);

    return false;
}

$.loading = function (params) {
    if (params == 'stop') {
        $('.loader').remove();
        return;
    }

    params = $.extend({}, {
        dots: 8,
        width: null,
        height: null,
        animations: 'bounce, fadecolor'
    }, params);

    loader = $('<div class="loader">' + '<div class="loader-item"></div>'.repeat(params.dots) + '</div>');

    loader.children('.loader_item').each(function (i, e) {
        $(e).attr('style', '-webkit-animation-name: ' + params.animations + '; animation-name: ' + params.animations);
    });

    if (params.width) {
        loader.width(params.width);
    }

    if (params.height) {
        loader.height(params.height);
    }

    return loader[0].outerHTML;
}

$.fn.loading = function (params) {
    if (typeof params !== 'string') {
        params = $.extend({}, {
            text: this.attr('data-loading'),
            disable: true
        }, params);
    }

    if (params.text || this.data('original')) {
        if (params === 'stop') {
            this.prop('disabled', false);
            this.html(this.data('original'));
        } else {
            if (params.disable) {
                this.prop('disabled', true);
            }
            if (!this.data('original')) {
                this.data('original', this.html());
            }
            this.html(params.text);
        }

        return this;
    }

    this.find('.loader').remove();

    return this.append($.loading(params));
}

$.fn.ac_zoneselect = function (params, callback) {
    var $this = this;

    params = $.extend({}, {
        listen: null,
        allow_all: false,
        select: null,
        url: $ac.site_url + 'data/locale/load_zones?ajax=1'
    }, params);

    if (!params.listen) {
        throw "You must specify 'listen' in the parameters. This is the Country selector element";
    } else {
        params.listen = $(params.listen);
    }

    if (params.allow_all) {
        params.url += '&allow_all';
    }

    if (params.select) {
        params.url += '&zone_id=' + params.select;
    }

    if (callback) {
        $this.success = callback;
    }

    params.listen.change(function () {
        var $cs = $(this);

        if (!$cs.val()) return;

        if ($this.children().length && $this.attr('data-country-id') == $this.val()) return;

        $this.attr('data-country-id', $cs.val());
        $this.attr('data-zone-id', $this.val() || $this.attr('data-zone-id') || 0);

        $this.load(params.url + '&country_id=' + $cs.val(), $this.success);
    });

    if ($this.children().length < 1 || !$this.val()) {
        params.listen.change();
    }

    return $this;
}

jQuery.fn.serializeObject = function () {
    var arrayData, objectData;
    arrayData = this.serializeArray();
    objectData = {};

    $.each(arrayData, function () {
        var value;

        if (this.value != null) {
            value = this.value;
        } else {
            value = '';
        }

        if (objectData[this.name] != null) {
            if (!objectData[this.name].push) {
                objectData[this.name] = [objectData[this.name]];
            }

            objectData[this.name].push(value);
        } else {
            objectData[this.name] = value;
        }
    });

    return objectData;
};

function getQueryString(key, defaultValue) {
    if (defaultValue == null) defaultValue = "";
    key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
    var qs = regex.exec(window.location.href);
    if (qs == null)
        return defaultValue;
    else
        return qs[1];
}

function currency_format(number, params) {
    params = $.extend({}, {
        symbol_left: $ac.currency.symbol_left,
        symbol_right: $ac.currency.symbol_right,
        decimals: $ac.currency.decimals,
        dec_point: $ac.currency.decimal_point,
        thousands_sep: $ac.currency.thousands_sep,
        neg: '-',
        pos: '+'
    }, params);

    str = number_format(Math.abs(number), params.decimals, params.dec_point, params.thousands_sep);

    return (number < 0 ? params.neg : params.pos) + params.symbol_left + str + params.symbol_right;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

$.cookie = function (key, value, options) {
    if (arguments.length > 1 && (value === null || typeof value !== "object")) {
        options = options || {};

        if (value === null) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? String(value) : encodeURIComponent(String(value)),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) {
        return s;
    } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

function ac_radio_bubble() {
    $('.ac-radio').not('ac-radio-bubble').click(function () {
        $(this).addClass('ac-radio-bubble');
        var $radio = $(this).parents('label').children('input[type=radio]');

        if (!$radio.prop('checked')) {
            $radio.prop('checked', true).change();
        }
    });

    $('.ac-radio input').focus(function () {
        var $radio = $(this).closest('.ac-radio').children('input[type=radio]');
        if (!$radio.prop('checked')) {
            $radio.prop('checked', true).change();
        }
    });
}

function register_confirms() {
    var $confirms = $('[data-confirm], [data-confirm-text]').use_once();

    $confirms.click(function () {
        var $this = $(this);

        if ($this.prop('disabled')) {
            return false;
        }

        if ($this.is('[data-confirm]') && !$this.hasClass('confirm')) {
            setTimeout(function () {
                $this.removeClass('confirm').loading('stop');
            }, 2000);
            $this.loading({text: $this.attr('data-confirm') || "Confirm?", disable: false}).addClass('confirm');

            return false;
        }

        if ($this.is('[data-confirm-text]')) {
            if (!confirm($this.attr('data-confirm-text') || "Are you sure you want to continue?")) {
                return false;
            }
        }

        if ($this.hasClass('ajax-call')) {
            amplo_ajax_cb.call($this);
            return false;
        }
    });

    $('.action-delete').use_once().click(function () {
        return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
    });

    $('.ajax-form').use_once('ajax-init').submit(function () {
        ac_form();
    });
}

function register_ajax_calls(is_ajax) {
    $((is_ajax ? '[data-if-ajax],' : '') + '[data-ajax]').use_once('ajax-call').not('[data-confirm], [data-confirm-text]').amplo_ajax();

    // Multistate Checkboxes
    $('[data-multistate]').use_once().click(function () {
        var $this = $(this);
        var val = $this.val();
        var states = $this.attr('data-multistate').split(';');

        if (!$this.prop('checked')) {
            for (var s = 0; s < states.length; s++) {
                if (states[s] === val) {
                    if (s < states.length - 1) {
                        $this.val(states[s + 1]);
                    } else {
                        $this.val(states[0]);
                        return true;
                    }
                }
            }
            return false;
        }
    });

    if (is_ajax) {
        $('form.ctrl-save').use_once().submit(function() {
            var $form = $(this);

            var params = {
                callback: function(response) {
                    $form.ac_msg(response);

                    if (!response.error && $form.closest('#colorbox').length) {
                        $.colorbox.close();
                    }
                }
            }

            $(this).submit_ajax_form(params);

            return false;
        }).find('a.cancel, a.back').click(function() {
            if ($(this).closest('#colorbox').length) {
                $.colorbox.close();
                return false;
            }
        });
    }
}

function register_colorbox() {
    var $colorbox = $('.colorbox').use_once('colorbox-init');

    if ($colorbox.length) {
        var width = Math.max($('body').width() * .6, Math.min($('body').width() - 16, 400));

        var defaults = {
            overlayClose: true,
            opacity: 0.5,
            width: width,
            height: '80%'
        }

        $colorbox.colorbox(defaults);
    }
}

function colorbox(params) {
    $.extend(params, {
        maxWidth: '90%',
        maxHeight: '90%'
    });

    $.colorbox(params);
}

var amplo_ajax_cb = function () {
    var $this = $(this), callback;

    if ($this.prop('disabled')) {
        return false;
    }

    var ajax_cb = $this.attr('data-if-ajax') || $this.attr('data-ajax');

    if (typeof window[ajax_cb] !== 'function') {
        var $replace = $(ajax_cb);

        if (!$replace.length) {
            colorbox({href: $this.attr('href')});
            return false;
        }

        callback = function (response) {
            $replace.replaceWith(response);
        }
    } else {
        callback = function (response) {
            window[ajax_cb].call($this, response);
        }
    }

    if ($this.is('form')) {
        $this.submit_ajax_form({callback: callback});
    } else {
        $this.loading({text: $this.is('[data-loading]') || 'Loading...'})
        $.get($this.attr('href'), {}, callback)
            .always(function () {
                $this.loading('stop');
            });

    }

    return false;
};

$.fn.amplo_ajax = function () {
    return this.each(function (i, e) {
        var $e = $(e);

        if ($e.is('form')) {
            $e.submit(amplo_ajax_cb);
        } else {
            $e.click(amplo_ajax_cb);
        }
    });
}

$.fn.submit_ajax_form = function(params) {
    params = $.extend({}, {
        callback: null
    }, params);

    return this.each(function (i, e) {
        var $form = $(e);

        var $btns = $form.find('button, input[type=submit], [data-loading]').loading({text: 'Saving...'});

        $.post($form.attr('action'), $form.serialize(), typeof params.callback === 'function' ? params.callback : function (response) {
            $form.ac_msg(response);
        }, 'json').always(function() {
            $btns.loading('stop');
        });
    });
}

function amplo_auto_ajax() {
    register_ajax_calls(false);
    register_confirms();
    register_colorbox();

    $(document).ajaxComplete(function () {
        register_ajax_calls(true);
        register_confirms();
        register_colorbox();
    });
}

$(document).ready(function () {
    amplo_auto_ajax();

    $('.ui-autocomplete-input').on("autocompleteselect", function (e, ui) {
        if (!ui.item.value && ui.item.href) {
            window.open(ui.item.href);
        }
    });

    $('form input').keydown(function (e) {
        if (e.keyCode == 13) {
            $(this).closest('form').submit();
        }
    });

    $(document).keydown(function (e) {
        if (e.ctrlKey && (e.which == 83)) {
            $('form.ctrl-save').submit_ajax_form();

            e.preventDefault();
            return false;
        }
    });

    $(document).on("DOMNodeInserted", function () {
        ac_radio_bubble();
    });

    //AC Checkbox (No IE8)
    if ($('body.IE8').length === 0) {
        $('.ac_checkbox').each(function (i, e) {
            var div = $('<div class="ac_checkbox"></div>');
            var cb = $(e);

            div.toggleClass("checked", cb.prop('checked'));

            cb.after(div).removeClass('ac_checkbox');
            $(e).appendTo(div);

            div.click(function () {
                cb.prop('checked', !cb.prop('checked'));
                div.toggleClass("checked", cb.prop('checked'));
            });
        });
    }
});


//Chrome Autofill disable hack
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
    $(window).load(function () {
        $('input:-webkit-autofill[autocomplete="off"]').each(function () {
            var $this = $(this);
            if (!$this.attr('value')) {
                $this.val('');
                setTimeout(function () {
                    $this.val('');
                }, 200);
            }
        });
    });
}
