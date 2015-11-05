$.ampListing = $.fn.ampListing = function (o) {
	return $.amp.call(this, $.ampListing, arguments)
}

$.extend($.ampListing, {
	init: function (o) {
		o = $.extend({}, {
			view_id: null,
			query:   {}
		}, o);

		this.data('o', o).addClass('amp-listing');

		var $listing = this.use_once();

		if (o.view_id) {
			$listing.attr('data-view-id', o.view_id);
		}

		$listing.find('.export-view').click(function () {
			if (confirm("Do you want to export the full data set?")) {
				window.location = $(this).attr('href') + '&limit=0';
				return false;
			}
		});

		$listing.find('.pagination a, .sortable, .filter-button, .reset-button, .limit-items a, .refresh-listing').click(update_list_widget);

		var $view_config = $listing.find('.view-config');

		$view_config.find('.view-tabs a').tabs({pushState: false});
		$view_config.find('.save-view-cols').click(update_list_widget);
		$view_config.find('.save-settings').click(save_list_widget_settings);

		$listing[0].view_config = $view_config;

		$view_config.ampModal();
		$view_config.find('[name="columns[]"]').ampSelect('sortable');

		$listing.find('.modify-view').click(function () {
			var $view_config = $(this).closest('.widget-listing')[0].view_config;
			$view_config.find('.chart-data-cols .multiselect-list').sortable();

			$view_config.ampModal('open');
		});
	},

	getQuery: function (key) {
		var query = this.data('o').query;

		return key ? query[key] : query;
	},

	queryString: function (key) {
		var query = this.data('o').query;

		if (key) {
			if (query[key]) {
				var obj = {}
				obj[key] = query[key]
				return $.param(obj)
			}

			return '';
		}

		return $.param(query);
	},

	rowCount: function() {
		return this.find('.table-list-view tbody tr').length;
	}
})

//List View Scripts
$.fn.listview = function () {
	$.ac_datepicker();

	return this.use_once().each(function (i, e) {
		var $listview = $(e).addClass('list-view');

		var $zoom = $listview.find('.zoom-hover')
		$zoom.find('.clear').click(zoom_hover_clear);
		$zoom.find('input, textarea').focus(zoom_hover_in).blur(zoom_hover_out).change(zoom_hover_change).keyup(zoom_hover_keyup);

		$listview.find('.select-all').click(listview_select_all);
		$listview.find('.filter-list-item').click(listview_toggle_checked);
		$listview.find('.filter-list-item [name="batch[]"]').change(listview_batch_checked).siblings('label').click(listview_batch_checked_label);

		$listview.find('.filter-button').click(listview_apply_filter);
		$listview.find('.filter-type').click(listview_toggle_filter_type);

		if ($listview.attr('data-filter-style') === 'persistent') {
			$listview.find('.filter-type').removeClass('not').addClass('equals').hide();
			$listview.find('.column-filter').find('input, select').on('keyup change', delay_update);
		}

		$listview.find('.reset-button').click(listview_reset);
		$listview.find('.filter-list').keyup(listview_filter_on_enter);
		$listview.find('.hide-filter').click(listview_toggle_filter);

		$listview.find('.filter-list > td').click(function () {
			if ($(this).closest('.filter-list').hasClass('hide')) {
				listview_toggle_filter.call($(this).closest('.listing'), false);
			}
		});

		if ($listview.attr('data-save-url')) {
			$listview.find('tr.filter-list-item td.editable').click(listview_edit_field);
			$listview.find('.editable-options').click(listview_noaction);
			$listview.find('.editable-options .save-edit').click(listview_save_edit);
			$listview.find('.editable-options .cancel-form').click(listview_cancel_edit);
			$listview.find('.editable-option .input input[type=text]').keyup(listview_save_on_enter);
		}

		$listview.find('.action-buttons').overflown('y', 5);
	})
}

$.ampFilter = $.fn.ampFilter = function (o) {
	return $.amp.call(this, $.ampFilter, arguments);
}

$.extend($.ampFilter, {
	init: function (o) {
		var $me = this;

		o = $.extend({}, {
			replace: false,
			url:     null,
			start:   'hide'
		}, o);

		$me.each(function (i, e) {
			var $filter = $(e);
			var $button = $filter.find('.amp-filter-apply');
			$filter.data('opts', o);
			$filter.find('.amp-filter-toggle').click($.ampFilter.toggle);
			$filter.find('.amp-filter-reset').click($.ampFilter.reset);
			$button.click($.ampFilter.filter);
			$filter.find('.field.disabled [name]').prop('disabled', true);
			$filter.find('[name]').keyup(function (e) {
				if (e.keyCode === 13) {
					$(this).ampFilter('filter');
				}
			})
		});

		$me.data('o', o);

		return this;
	},

	reset: function () {
		$.ampFilter.toggle.call($(this).closest('.amp-filter').find('.field [name]').val('').change(), false);
	},

	toggle: function (enabled) {
		var $field = $(this).closest('.field');
		enabled = typeof enabled !== 'object' ? enabled : $field.hasClass('disabled');
		$field.removeClass('enabled disabled').addClass(enabled ? 'enabled' : 'disabled');
		$field.find('[name]').prop('disabled', !enabled);
	},

	filter: function () {
		var $filter = $(this).closest('.amp-filter');
		var $button = $filter.find('.amp-filter-apply'),
			opts = $filter.data('opts');

		var url = opts.url || $button.attr('href');

		$filter.find('[data-loading]').loading();

		if (opts.replace) {
			$.post(url, $filter.find('[name]').serialize(), function (response) {
				if (response.error) {
					$filter.show_msg(response);
				} else {
					$(opts.replace).replaceWith(response);

					if (typeof opts.success === 'function') {
						opts.success.call(this, response);
					}
				}
			}).always(function (jqxhr, status, e) {
				$filter.find('[data-loading]').loading('stop');

				if (!status === 'success') {
					$filter.show_msg('error', "There was an error while applying the filter.");
				}

				if (typeof opts.always === 'function') {
					opts.always.call(this, jqxhr, status, e);
				}
			});

			e.preventDefault();
			return false;
		} else {
			url = url + (url.indexOf('?') >= 0 ? '&' : '?') + $filter.find('[name]').serialize();
			$button.attr('href', url);
			window.location = url;
		}
	}
});

function update_list_widget() {
	var $this = $(this);

	var href = $this.attr('href') || $this.attr('data-href');

	if (!href) {
		return false;
	}

	$this.closest('.amp-modal').ampModal('close');

	var $list_widget = $this.is('.widget-listing') ? $this : $this.closest('.widget-listing');

	if (!$list_widget.length) {
		$list_widget = $this.attr('data-listing') ? $($this.attr('data-listing')) : $($this.closest('[data-listing]').attr('data-listing'));
	}

	$list_widget.addClass("loading");
	$list_widget.find('.refresh-listing').addClass('refreshing');

	var data = {
		columns: {}
	};

	$this.closest('.select-cols').find(':checked').each(function (i, e) {
		data.columns[$(e).val()] = i;
	});

	data.view_id = $list_widget.attr('data-view-id');

	$.get(href, data, function (response) {
		var $parent = $list_widget.closest('.listing');
		$list_widget.siblings('.messages').remove();
		$list_widget.replaceWith(response);
		$parent.trigger('loaded');
	});

	return false;
}

function save_list_widget_settings() {
	var $this = $(this);
	var $form = $this.closest('.form');
	var $widget = $this.closest('.widget-view');
	var view_type = $form.find('[name="view_type"]').val();

	$this.loading();

	$.post($ac.site_url + "block/widget/listing/save-settings", $form.find('[name]').serialize(), function (response) {
		$form.show_msg(response);
		$this.closest('.view-config').removeClass('show');
		$widget.find('.refresh-listing').click();

		//Hack to show chart / listing view
		$widget.find('[data-view-type="' + view_type + '"]').click();

	}, 'json').always(function () {
		$this.loading('stop');
	});
}

function listview_batch_checked() {
	var $this = $(this);
	$this.closest('.filter-list-item').toggleClass('active', $this.prop('checked'));
}

function listview_batch_checked_label(event) {
	var $input = $('#' + $(this).attr('for'));
	$input.prop('checked', !$input.prop('checked')).change();
	event.stopPropagation();
	return false;
}

function listview_select_all() {
	$(this).closest('.list-view').find('[name="batch[]"]').prop('checked', this.checked).change();
}

function listview_toggle_checked() {
	var cb = $(this).find('[name="batch[]"]');
	if (cb.data('clicked')) {
		cb.data('clicked', false);
	} else {
		cb.prop('checked', !cb.prop('checked')).change();
	}
}

function listview_noaction(e) {
	e.stopPropagation();
	return false;
}

function listview_reset() {
	var $this = $(this);
	$filter = $this.closest('.filter-list');
	$filter.find('[name]').val('');
	$filter.find('.filter-type').removeClass('not equals');
	$this.attr('href', $filter.apply_filter($this.closest('.list-view').attr('data-filter-url')));
}

function listview_filter_on_enter(e) {
	if (e.keyCode === 13) {
		var $filter = $(this).is('.filter-list') ? $(this) : $(this).closest('.filter-list');
		$filter.find('.filter-button')[0].click();
	}
}

function listview_save_on_enter(e) {
	if (e.keyCode === 13) {
		$(this).closest('.editable-options').find('.save-edit').click();
		e.stopPropagation();
		return false;
	}
}

function listview_toggle_filter(hide) {
	var $listing = $(this).closest('.listing');
	var $list = $listing.find('.filter-list');
	var $refresh = $listing.find('.refresh-listing');

	$list.toggleClass('hide', hide);
	$refresh.attr('href', $refresh.attr('href').replace(/&hidefilter=1/, '') + ($list.hasClass('hide') ? '&hidefilter=1' : ''));

	return false;
}

function listview_toggle_filter_type() {
	var $this = $(this);
	if ($this.hasClass('not')) {
		$this.removeClass('not');
	} else if ($this.hasClass('equals')) {
		$this.removeClass('equals').addClass('not');
	} else {
		$this.addClass('equals');
	}
}

function listview_apply_filter() {
	var $this = $(this);
	$filter = $this.closest('.filter-list');
	$this.attr('href', $filter.apply_filter($this.closest('.list-view').attr('data-filter-url')));
}

function listview_edit_field() {
	var $this = $(this);
	var field = $this.attr('data-field');
	var value = $this.attr('data-value').replace(/&quot;/g, '"');

	if (field) {
		var $options = $this.closest('.list-view').find('.editable-options');
		$options.children('.show').removeClass('show');
		$options.find('[data-field="' + field + '"]').addClass('show').find('.input-value').val(value);
		$this.append($options);
		$options.attr('data-id', $this.closest('[data-row-id]').attr('data-row-id'));
	}
}

function listview_save_edit() {
	var $this = $(this);
	var $listview = $this.closest('.list-view');
	var $options = $this.closest('.editable-options');
	var $option = $options.find('.show');
	var $input = $option.find('.input-value');
	var field = $option.attr('data-field');
	var value = $input.val();
	var id = $options.attr('data-id');

	var data = {};
	data[$listview.attr('data-index')] = id;
	data[field] = value;

	$this.loading();
	$listview.append($options);

	var display = value;

	if ($input.is('select')) {
		display = $input.find('option[value="' + value + '"]').html();
	}

	display = display.replace(/\n/g, '<BR>');

	var $field = $listview.find('[data-row-id="' + id + '"] td[data-field="' + field + '"]');
	$field.attr('data-value', value.replace('"', '&quot;'));

	var orig_display = $field.html();
	$field.html('Saving...');

	$.post($listview.attr('data-save-url'), data, function (response) {
		$this.loading('stop');
		$listview.show_msg(response);

		if (response.success) {
			$field.html(display);
		} else {
			$field.html(orig_display);
		}
	}, 'json');
}

function listview_cancel_edit(e) {
	var $box = $(this).closest('.list-view');
	$box.append($(this).closest('.editable-options'));
	e.stopPropagation();
	return false;
}

function refresh_listing() {
	var $this = $(this);
	var $list = $this.hasClass('listing') ? $this : $this.closest('.listing');
	$list.find('.refresh-listing').click();
}

var delay = false;

function delay_update(my_delay) {
	var $filter = $(this).is('.filter-list') ? $(this) : $(this).closest('.filter-list');

	if (typeof my_delay === 'number') {
		if (my_delay === delay) {
			var $widget = $filter.closest('.widget-listing').addClass('loading');

			$('#ui-datepicker-div').remove();

			$.get($filter.apply_filter($filter.closest('.list-view').attr('data-filter-url')), {}, function (response) {
				$widget.replaceWith(response);
			});
		}
	} else {
		var e = $filter;
		my_delay = Date.now();
		delay = my_delay;

		if (e.keyCode === 13) {
			delay_update.call($filter, my_delay);
		} else {
			setTimeout(function () {
				delay_update.call($filter, my_delay)
			}, 1500);
		}
	}
}

function zoom_hover_in() {
	$(this).closest('.zoom-hover').addClass('active');
}

function zoom_hover_out() {
	$(this).closest('.zoom-hover').removeClass('active');
}

function zoom_hover_change() {
	var $zoom = $(this).closest('.zoom-hover');
	var $value = $zoom.find('.value');

	if ($zoom.is('.daterange')) {
		var start = $zoom.find('.date_start').val();
		var end = $zoom.find('.date_end').val();

		if (end || start) {
			$value.html(start + ' - ' + end);
		} else {
			$value.html($value.attr('data-default') || 'Modify');
		}
	} else if ($zoom.is('.multiselect')) {
		var $selected = $zoom.find(':checked');

		if ($selected.length == 0) {
			$value.html($value.attr('data-default') || 'Modify');
		} else {
			var str = '';
			$selected.each(function (i, e) {
				var label = $('[for="' + $(e).attr('id') + '"]').html();

				str += (str ? ', ' : '') + (label || $(e).val());
			});
			$value.html(str.length > 20 ? str.substr(0, 20) + '...' : str);
		}
	}
}

function zoom_hover_keyup() {
	var $zoom = $(this).closest('.zoom-hover');
	var $value = $zoom.find('.value');

	if ($zoom.is('.int')) {
		low = $zoom.find('.int_low').val();
		high = $zoom.find('.int_high').val();

		if (high || low) {
			$value.html(low + ' - ' + high);
		} else {
			$value.html($value.attr('data-default') || 'Modify');
		}
	}
}

function zoom_hover_clear() {
	$(this).closest('.zoom-hover').find('input, textarea').val('').trigger('keyup').trigger('change');
}
