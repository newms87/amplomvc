$.ac_template = $.fn.ac_template = function (name, action, data) {
    templates = $.fn.ac_template.templates;

    //Load Template
    if (!action || typeof action === 'object') {
        template_row = this.find('[data-row="__ac_template__"]');

        if (!template_row.length) {
            $.error('Unable to find template row! The element containing the template row must have the following attribute: data-row="__ac_template__"');
            return;
        }

        template = template_row.clone(true);
        template_row.remove();

        count = 0;

        this.find('[data-row]').each(function (i, e) {
            count = Math.max(count, parseInt($(e).attr('data-row')) + 1);
        });

        templates[name] = $.extend({
            list: this,
            template: template,
            defaults: {}, //action being used as data here
            count: count,
            unique: false
        }, action);
    }
    else {
        row = templates[name];
        template = row.template.clone(true);
        data = $.extend({}, row.defaults, data || {});

        if (typeof this === 'function') {
            list = row.list;
        } else {
            list = this;
        }

        if (action === 'add') {
            if (row.unique && (duplicate = list.children('[data-id="' + data[row.unique] + '"]')).length) {
                setTimeout(function () {
                    duplicate.flash_highlight();
                }, 100);
                return false;
            }

            template.attr('data-row', row.count);

            if (row.unique) {
                template.attr('data-id', data[row.unique]);
            }

            list.append(template);

            datarow_parents = template.parents('[data-row]');

            template.find('[name]').each(function (i, e) {
                e_name = $(e).attr('name');

                row_count = e_name.match(/__ac_template__/g).length;

                if (row_count > 1) {
                    find = ['__ac_template__'];
                    replace = [row.count];

                    for (var i = 0; i < row_count - 1; i++) {
                        find.push('__ac_template__');
                        replace.push(parseInt($(datarow_parents[i]).attr('data-row')));
                    }

                    t_name = e_name.str_replace(find, replace.reverse());
                }
                else {
                    t_name = e_name.replace(/__ac_template__/g, row.count);
                }

                $(e).attr('name', t_name);

                key = e_name.replace(/^.*\[([^\]]+)\]$/, '$1');

                if (typeof data[key] !== 'undefined') {
                    $(e).val(data[key]);
                } else {
                    $(e).val('');
                }

                if ($(e).is('select') && !$(e).find(':selected').length) {
                    $(e).val($(e).find(':first').val());
                }
            });

            template.find('.ckedit').each(function (i, e) {
                init_ckeditor_for($(e));
            });

            template.find('.date').each(function (i, e) {
                init_ckeditor_for($(e));
            });

            row.count++;

            return template.flash_highlight();
        }
    }
    return this;
};

$.fn.ac_template.templates = {};