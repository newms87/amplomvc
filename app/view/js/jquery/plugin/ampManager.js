//Amplo Manager jQuery plugin
$.ampExtend($.ampManager = function() {}, {
	instanceId: 0,
	init:       function(o) {
		var $managers = this.use_once().addClass('amp-manager is-ready');

		if (!$managers.length) {
			return this;
		}

		o = $.extend({}, {
			id:              null,
			input:           null,
			label:           'record',
			selected:        null,
			type:            'amplo',
			type_id:         'amplo_id',
			showAddress:     true,
			template:        null,
			selectMultiple:  false,
			deselectOnClick: true,
			syncFields:      null,
			defaults:        {},
			allowRemove:     true,
			onResults:       null,
			onAppend:        null,
			onChange:        null,
			onEdit:          null,
			onReady:         null,
			onInitTemplate:  null,
			url:             $ac.site_url + 'manager/',
			removeUrl:       null,
			listingUrl:      null,
			loadListings:    true,
			listing:         {},
			showPagination:  true,
			paginateOptions: {
				limit: 10,
				page:  1,
			}
		}, o);

		o.url = o.url.replace(/\/$/, '') + '/';

		o.template_id = 'am-' + o.type + '-' + $.ampManager.instanceId++;

		o.removeUrl = o.removeUrl || o.url + 'remove';
		o.listingUrl = o.listingUrl || o.url + 'get-records';

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
			$fields.html('');
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

	select: function($record, data, triggerChange) {
		var $am = this;
		var o = $am.getOptions(), is_changed = false, selected = null;

		if (typeof $record !== 'object') {
			selected = $record;
			$record = $am.find('[data-am-record-id=' + selected + ']');

			if (!$record.length) {
				if (data) {
					$am.ampManager('append', data);
					$record = $am.find('[data-am-record-id=' + selected + ']')
				}
			}
		}

		if (!$record.length) {
			is_changed = $am.find('.am-record.is-selected').length;
			$am.find('.am-record').removeClass('is-selected');
		} else if (o.selectMultiple) {
			record_id = $record.toggleClass('is-selected').attr('data-am-record-id');

			selected = o.selected || [];

			if ($record.hasClass('is-selected')) {
				selected.push(record_id)
				is_changed = true;
			} else if ((index = $am.ampManager('selectedIndex', record_id)) !== false) {
				selected.splice(index, 1)
				is_changed = true;
			}
		} else {
			var isSelected = o.deselectOnClick ? !$record.hasClass('is-selected') : true;

			$am.find('.am-record').removeClass('is-selected');
			$record.toggleClass('is-selected', isSelected);
			isSelected || ($record = $('body'));
			selected = $record.attr('data-am-record-id');

			is_changed = o.selected !== selected;
		}

		o.selected = selected;

		if (is_changed && triggerChange !== false) {
			if (o.input && o.input.length) {
				o.input.val(o.selected).change();
			}

			if (o.syncFields) {
				$am.ampManager('sync', o.syncFields, $record.data('record'));
			}

			if (o.onChange) {
				o.onChange.call($am, o.selected, $record, $record.data('record'), $record.hasClass('is-selected'));
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

	setSelected: function(selected) {
		var o = this.getOptions();
		o.selected = selected;
		return this;
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

	get: function(listing, callback) {
		var $am = this;
		var o = $am.getOptions();

		$am.addClass('is-loading').removeClass('is-ready');

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function(response) {
			$am.ampManager('results', response.records, response.total);

			if (!listing) {
				$am.toggleClass('has-records', !!+response.total).toggleClass('no-records', !+response.total);
			}

			$am.removeClass('is-loading').addClass('is-ready');

			if (callback) {
				callback.call($am);
			}
		})

		return this;
	},

	results: function(records, total, updatePager) {
		var $am = this;
		var o = $am.getOptions();
		var $recordList = $am.find('.am-record-list').html(''),
			isEmpty = typeof records !== 'object' || $.isEmptyObject(records);

		$am.toggleClass('is-empty', isEmpty).toggleClass('is-filled', !isEmpty);

		if (!isEmpty) {
			for (var c in records) {
				var record = records[c];
				$am.ampManager('append', record);
			}
		}

		if (o.showPagination && updatePager !== false) {
			if (($pager = $am.find('.amp-pager')).length) {
				$pager.ampPager($.extend({}, {
					listingUrl:    o.listingUrl,
					listing:       o.listing,
					total:         total,
					beforeLoading: function() {
						$am.addClass('is-loading').removeClass('is-ready')
					},
					afterLoading:  function() {
						$am.removeClass('is-loading').addClass('is-ready')
					},
					target:        function(response) {
						$am.ampManager('results', response.records, response.total, false)
					}
				}, o.paginateOptions));
			}
		}

		if (o.onResults) {
			o.onResults.call($am, $recordList.children(), records, total);
		}

		return this;
	},

	append: function(record) {
		var o = this.getOptions();
		var $recordList = this.find('.am-record-list');

		record.id = record[o.type_id];

		//record already exists
		if ($recordList.find('[data-am-record-id=' + record.id + ']').length) {
			return this;
		}

		var $record = $recordList.ac_template(o.template_id, 'add', record);

		$record.data('record', record);

		if (!record.can_access || !o.allowRemove) {
			$record.find('.am-remove-record').addClass('hidden');
		}

		this.ampManager('sync', $record.find('[data-name]'), record);

		$record.attr('data-am-record-id', record.id);

		if (o.selectMultiple) {
			if (o.selected && this.ampManager('selectedIndex', record.id) !== false) {
				$record.addClass('is-selected');
			}
		} else {
			$record.toggleClass('is-selected', record.id == o.selected);
		}

		$recordList.append($record);

		if (o.onAppend) {
			o.onAppend.call(this, $record, record);
		}

		return this;
	},

	remove: function($record) {
		var $am = this;
		var o = $am.getOptions(), data = {};

		if (o.allowRemove) {
			$.ampConfirm({
				title:     "Remove " + o.label,
				text:      "Are you sure you want to remove this " + o.label + "?",
				onConfirm: function() {
					data[o.type_id] = $record.attr('data-am-record-id');

					$.get(o.removeUrl, data, function(response) {
						if (response.success) {
							$record.remove();
						}

						$am.show_msg(response);
					})
				}
			})
		} else {
			$.ampAlert("Removing records is not allowed.");
		}

		return $am;
	},

	removeUnselected: function() {
		this.find('.am-record').not('.is-selected').remove();
	},

	selectedIndex: function(value) {
		var selected = this.getOptions().selected;

		for (var s in selected) {
			if (selected[s] == value) {
				return s;
			}
		}

		return false;
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

		$am.find('.am-record').click(function(e) {
			var $t = $(e.target);

			var $acm = $t.closest('.amp-manager, .amp-select-cancel');
			if ($acm.is('.amp-manager')) {
				$acm.ampManager('select', $(this));
			}
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

		if (o.allowRemove) {
			$am.find('.am-remove-record').click(function() {
				$(this).closest('.amp-manager').ampManager('remove', $(this).closest('.am-record'));
				return false;
			})
		}

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
			var $am = this.closest('.amp-manager');

			$am.ampDelay({
				callback: function() {
					this.ampManager('get', this.find('[name]').serializeObject());
				}
			})

			return false;
		})

		$searchForm.ampDelay({
			callback: function() {
				$(this).closest('.amp-nested-form').submit();
			},
			delay:    200,
			on:       'keyup change'
		});

		$searchForm.find('input').on('keyup', function(e) {
			if (e.keyCode === 13) {
				e.stopPropagation();
				return false;
			}
		})

		$am.find('.am-record[data-row=__ac_template__]').ac_template(o.template_id);

		$am.addClass('is-empty no-records');

		if (o.onInitTemplate) {
			o.onInitTemplate.call(this);
		}

		if (o.loadListings) {
			$am.ampManager('get', null, o.onReady);
		} else if (o.onReady) {
			o.onReady.call(this);
		}

		return this;
	}
})
