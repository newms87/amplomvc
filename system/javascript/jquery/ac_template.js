$.ac_template = $.fn.ac_template = function (name, action, data) {
	templates = $.fn.ac_template.templates;

	//Load Template
	if (!action || typeof action === 'object') {
		template_row = this.find('[data-row="__ac_template__"]');

		if (!template_row.length) {
			$.error('Unable to find template row for ' + name + '! The element containing the template row must have the following attribute: data-row="__ac_template__"');
			return this;
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

			var row_list = [row.count];

			template.parents('[data-row]').each(function(i,e){
				row_list.unshift(parseInt($(e).attr('data-row')));
			});

			template.find('[name]').each(function (i, e) {
				var e_name = $(e).attr('name');

				var match = e_name.match(/__ac_template__/g);
				var row_count = match ? match.length : 0;

				//rowset filters which parent row ID are filled (For nested AC_Template Calls)
				//(eg: rowset=[0,3,4], this will use the topmost parent, skips the next 2 parents, then 4th & 5th parent)
				var rowset = $(e).attr('data-rowset');
				rowset = typeof rowset == 'string' ? rowset.split(',') : false;

				var find = [];
				var replace = [];
				for (var i = 0; i < row_count; i++) {
					if (rowset !== false && ($.inArray(''+i,rowset) == -1)) continue; //only insert rows requested
					find.push('__ac_template__');
					replace.push(row_list[i]);
				}

				t_name = e_name.str_replace(find, replace);

				$(e).attr('name', t_name);

				key = e_name.replace(/^.*\[([^\]]+)\]$/, '$1');

				var value = typeof data[key] !== 'undefined' ? data[key] : '';
				if ($.inArray($(e).attr('type'), ['checkbox','radio']) >= 0) {
					$(e).prop('checked', value);
				} else {
					$(e).val(value);
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