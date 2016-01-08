//Amplo Manager jQuery plugin
$.ampExtend($.ampManager = function() {}, {
	instanceId: 0,
	init:       function(o) {
		var $managers = this.use_once().addClass('amp-manager');

		if (!$managers.length) {
			return this;
		}

		o = $.extend({}, {
			id:             null,
			input:          null,
			selected:       null,
			type:           'amplo',
			type_id:        'amplo_id',
			showAddress:    true,
			template:       null,
			selectMultiple: false,
			syncFields:     null,
			defaults:       {},
			onChange:       null,
			onEdit:         null,
			url:            $ac.site_url + 'manager/',
			listingUrl:     $ac.site_url + 'manager/get-records',
			loadListings:   true,
			listing:        {}
		}, o);

		o.template_id = 'am-' + o.type + '-' + $.ampManager.instanceId++;

		if (o.type) {
			o.listing.filter || (o.listing.filter = {});
			o.listing.filter.type = o.type;
		}

		if (o.input && o.selected === null) {
			o.selected = o.input.val();
		}

		$managers.each(function() {
			var $am = $(this);

			$am.setOptions(o);

			if ($am.children().length) {
				$am.ampManager('initTemplate');
			} else {
				$am.load(o.url, {template: o.template}, function() {
					$am.ampManager('initTemplate');
				});
			}
		})

		return this;
	},

	sync: function($fields, record) {
		var $am = this;
		var o = $am.getOptions();

		if (!record) {
			record = o.defaults;
		}

		$fields.each(function() {
			var $field = $(this);
			var value = record[$field.attr('data-name')];

			if ($field.is('[data-type=select]')) {
				value = o.recordForm.find('[name=' + f + '] option[value=' + value + ']').html();
			}

			$field.html(value);
		})

		for (var f in record) {
			var $field = $fields.filter('[data-name=' + f + ']');
			var value = value = record[f];

			if ($field.is('[data-type=select]')) {
				value = o.recordForm.find('[name=' + f + '] option[value=' + value + ']').html();
			}

			$field.html(value);
		}

		return this;
	},

	select: function($record) {
		var $am = this;
		var o = $am.getOptions(), is_changed = false;

		if (typeof $record !== 'object') {
			$record = $am.find('[data-am-record-id=' + $record + ']');
		}

		if (o.selectMultiple) {
			$record.toggleClass('is-selected');

			selected = []

			$am.find('.am-record.is-selected').each(function() {
				selected.push($(this).attr('data-am-record-id'))
			})

			is_changed = o.selected.toString() === selected.toString();
		} else {
			$am.find('.am-record').removeClass('is-selected');
			selected = $record.addClass('is-selected').attr('data-am-record-id');
			is_changed = o.selected !== selected;
		}

		o.selected = selected;

		if (is_changed) {
			if (o.input.length) {
				o.input.val(o.selected).change();
			}

			if (o.syncFields) {
				$am.ampManager('sync', o.syncFields, $record.data('record'));
			}

			if (o.onChange) {
				o.onChange.call($am, o.selected, $record, $record.data('record'));
			}
		}

		return this;
	},

	getSelected: function() {
		return this.getOptions().selected;
	},

	getSelectedData: function() {
		var $am = this;
		var o = this.getOptions(), $selected = $am.find('.am-record.is-selected');

		if (o.selectMultiple) {
			var data = {};

			$selected.each(function() {
				var $c = $(this);
				data[$c.attr('data-am-record-id')] = $c.data('record')
			})

			return data;
		} else {
			return $selected.data('record');
		}
	},

	editRecord: function($record, record) {
		var $am = this;
		var o = $am.getOptions();

		$am.ampManager('sync', $record.find('[data-name]'), record);

		if (o.syncFields) {
			$am.ampManager('sync', o.syncFields, record);
		}

		$record.data('record', record);

		if (o.onEdit) {
			o.onEdit.call($am, $record, record);
		}

		return this;
	},

	get: function(listing) {
		var $am = this;
		var o = $am.getOptions();

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function(response) {
			$am.ampManager('results', response.records, response.total);
		})

		return this;
	},

	results: function(records, total) {
		var $am = this;
		var o = $am.getOptions();
		var $recordList = $am.find('.am-record-list'),
			isEmpty = typeof records !== 'object' || $.isEmptyObject(records);

		$recordList.toggleClass('empty', isEmpty).html('');

		if (!isEmpty) {
			for (var c in records) {
				var record = records[c];
				record.id = record[o.type_id];

				var $record = $recordList.ac_template(o.template_id, 'add', record);

				$record.data('record', record);

				$am.ampManager('sync', $record.find('[data-name]'), record);

				$record.attr('data-am-record-id', record.id);

				$recordList.append($record);
			}

			if (o.selectMultiple) {
				for (var s in o.selected) {
					$recordList.find('[data-am-record-id=' + o.selected[s] + ']').addClass('is-selected');
				}
			} else {
				$recordList.find('[data-am-record-id=' + o.selected + ']').addClass('is-selected');
			}
		}

		return this;
	},

	removeUnselected: function() {
		this.find('.am-record').not('.is-selected').remove();
	},

	initTemplate: function() {
		var $am = this;
		var o = $am.getOptions();

		o.recordForm = $am.find('.am-record-form').remove().removeClass('hidden');

		if (o.type) {
			o.recordForm.find('[name=type]').val(o.type);
		}

		$am.find('.amp-nested-form').ampNestedForm();

		$am.find('.am-deselect').click(function() {
			$(this).closest('.amp-manager').ampManager('select', '');
		})

		$am.find('.am-add-record').click(function() {
			var $am = $(this).closest('.amp-manager');
			var o = $am.getOptions();

			var $results = $(this).closest('.am-results').toggleClass('adding');

			var $form = $results.find('.am-new-record-form');

			if (!$form.children().length) {
				$form.append(o.recordForm.clone())
			}
		})

		$am.find('.edit-record').click(function() {
			var $am = $(this).closest('.amp-manager');
			var o = $am.getOptions();

			$am.find('.am-record').removeClass('editing');

			var $record = $(this).closest('.am-record').addClass('editing');
			var $form = $record.find('.am-edit-record-form');

			if (!$form.children().length) {
				$form.append(o.recordForm.clone())

				var record = $record.data('record');

				for (var f in record) {
					if (f === 'address') {
						for (var a in record[f]) {
							$form.find('[name="address[' + a + ']"]').val(record[f][a]);
						}
					} else {
						$form.find('[name=' + f + ']').val(record[f]);
					}
				}
			}
		})

		$am.find('.cancel-record').click(function() {
			$(this).closest('.am-record').removeClass('editing');
		})

		$am.find('.am-record').click(function() {
			$(this).closest('.amp-manager').ampManager('select', $(this));
		})

		$am.find('.am-record .am-edit-record-form').ampNestedForm('onDone', function(response) {
			var $am = $(this).closest('.amp-manager');
			var $record = $(this).closest('.am-record');

			if (response.success) {
				$am.ampManager('editRecord', $record, response.data)

				$record.removeClass('editing');
			}

			$record.show_msg(response);
		})

		$am.find('.am-new-record-form').ampNestedForm('onDone', function(response) {
			var $form = $(this);

			if (response.success) {
				var $am = $form.closest('.amp-manager').ampManager('results', {0: response.data}, 1);
				$am.find('.am-results').removeClass('adding');
				$form.find('input').val('');

				$am.ampManager('select', response.data[o.type_id])
			}

			$form.show_msg(response);
		})

		var $searchForm = $am.find('.am-search-form');

		$searchForm.ampNestedForm('onSubmit', function() {
			this.closest('.amp-manager').ampManager('get', $(this).find('[name]').serializeObject());

			return false;
		})

		$searchForm.find('input')
			.on('keyup', function(e) {
				if (e.keyCode === 13) {
					e.stopPropagation();
					return false;
				}
			})
			.ampDelay({
				callback: function() {
					$(this).closest('.amp-nested-form').submit();
				},
				delay:    200,
				on:       'keyup'
			});

		$am.find('.am-record[data-row=__ac_template__]').ac_template(o.template_id);

		if (o.loadListings) {
			$am.ampManager('get');
		}

		return this;
	}
})
