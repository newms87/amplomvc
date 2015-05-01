$.ac_template = $.fn.ac_template = function (name, action, data, relate) {
	var templates = $.fn.ac_template.templates;

	function get_count(list) {
		var count = 0;

		list.find('[data-rel="' + list.attr('data-list-rel') + '"]').each(function (i, e) {
			count = Math.max(count, (parseInt($(e).attr('data-row')) || 0) + 1);
		});

		return count;
	}

	//Load Template
	if (!action || typeof action === 'object') {
		var template_row = this;
		var list = this.parent();

		if (template_row.attr('data-row') !== '__ac_template__') {
			template_row = this.find('[data-row="__ac_template__"]');
			list = this;
		}

		if (!template_row.length) {
			$.error('Element attribute data-row="__ac_template__" is required for ' + name);
			return this;
		}

		list.attr('data-list-rel', relate || name);
		list.find('[data-row]').not('[data-rel]').attr('data-rel', relate || name);

		template = template_row.clone(true);
		template_row.remove();

		templates[name] = $.extend({
			list:     list,
			template: template,
			defaults: {}, //action being used as data here
			unique:   false
		}, action);

		//update counts
		$('[data-list-rel]').each(function(i,e){
			$(e).attr('data-count', $(e).children().length);
		});
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

			count = data.id || get_count(list);

			template.attr('data-row', count);

			if (row.unique) {
				template.attr('data-id', data[row.unique]);
			}

			template.find('[id]').each(function (i, e) {
				var id = $(e).attr('id');
				if ($('#' + id).length) {
					$(e).attr('id', id + count);
					template.find('[for=' + id + ']').attr('for', id + count);
				}
			});

			list.append(template);

			list.attr('data-count', list.children().length);

			var row_list = [count];

			template.parents('[data-row]').each(function (i, e) {
				row_list.unshift(parseInt($(e).attr('data-row')));
			});

			template.find('[name]').each(function (i, e) {
				var $e = $(e);
				var e_name = $e.attr('name');

				var match = e_name.match(/__ac_template__/g);
				var row_count = match ? match.length : 0;

				//rowset filters which parent row ID are filled (For nested AC_Template Calls)
				//(eg: rowset=[0,3,4], this will use the topmost parent, skips the next 2 parents, then 4th & 5th parent)
				var rowset = $e.attr('data-rowset');
				rowset = typeof rowset == 'string' ? rowset.split(',') : false;

				var find = [];
				var replace = [];
				for (var i = 0; i < row_count; i++) {
					if (rowset !== false && ($.inArray('' + i, rowset) == -1)) continue; //only insert rows requested
					find.push('__ac_template__');
					replace.push(row_list[i]);
				}

				t_name = e_name.str_replace(find, replace);

				$e.attr('name', t_name);

				key = e_name.replace(/^.*\[([^\]]+)\]$/, '$1');

				//Depth-First search for key
				function find_value(key, data) {
					var v = '';

					for (d in data) {
						if (typeof d === 'object') {
							v = find_value(key, data);
							if (v) return v;
						}
						else {
							if (d == key) return data[d];
						}
					}
				}

				var value = find_value(key, data);

				if ($e.is('select')) {
					if (typeof value !== 'undefined') {
						$e.val(value);
					}

					if (!$e.find(':selected').length) {
						$e.val($e.find(':first').val());
					}
				} else if ($.inArray($e.attr('type'), ['checkbox', 'radio']) >= 0) {
					$e.prop('checked', $e.val() == value);
				} else {
					$e.val(value).attr('value', value);
				}
			});

			//Replace all attribute occurrences
			template.find('*').addBack().not('option').each(function (i, e) {
				var $e = $(e);
				$.each(this.attributes, function (a, attr) {
					$e.attr(attr.name, attr.value.replace('__ac_template__', count));
					if (attr.name === 'value') {
						$e.val($e.attr('value'));
					}
				});
			});

			//Replace all text occurrences
			template.find('*').contents().filter(function () {
				return this.nodeType === 3;
			}).each(function (i, e) {
				e.nodeValue = e.nodeValue.replace('__ac_template__', count);
			});

			return template;
		}
		else if (action === 'get_row_count') {
			return get_count(list);
		}
	}

	return this;
};

$.fn.ac_template.templates = {};
