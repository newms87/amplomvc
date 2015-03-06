$.fn.list_widget = function (view_id) {
	var $list_widget = this.use_once();

	if (view_id) {
		$list_widget.attr('data-view-id', view_id);
	}

	$list_widget.find('.export-view').click(function () {
		if (confirm("{{Do you want to export the full data set?}}")) {
			window.location = $(this).attr('href') + '&limit=0';
			return false;
		}
	});

	$list_widget.find('.pagination a, .sortable, .filter-button, .reset-button, .limits a, .refresh-listing').click(update_list_widget);

	var $view_config = $list_widget.find('.view-config');

	$view_config.find('.view-tabs a').tabs();
	$view_config.find('.save-view-cols').click(update_list_widget);
	$view_config.find('.save-settings').click(save_list_widget_settings);

	$list_widget[0].view_config = $view_config.clone(true);
	$view_config.remove();

	$list_widget.find('.modify-view').click(function () {
		var $view_config = $(this).closest('.widget-listing')[0].view_config;
		$view_config.find('.chart-data-cols .multiselect-list').sortable();
		$view_config.find('.select-cols .multiselect-list').sortable();

		$view_config.addClass('show');

		$.colorbox({
			href:   $view_config,
			inline: true
		});
	});
}

function update_list_widget() {
	var $this = $(this);

	var href = $this.attr('href') || $this.attr('data-href');

	if (!href) {
		return false;
	}

	$.colorbox.close();

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
