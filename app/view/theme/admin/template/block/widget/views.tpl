<div class="block-widget-view block">
	<div class="widget-view-list row">
		<? foreach ($views as $row => $view) {
			$_REQUEST = $view['query'];
			$_GET     = $view['query']; ?>

			<? $col_sizes = array(
				25  => 'xs-12 sm-6 lg-3',
				33  => 'xs-12 sm-6 lg-4',
				50  => 'xs-12 sm-6',
				100 => 'xs-12',
			);

			$col_class = isset($col_sizes[$view['settings']['size']]) ? $col_sizes[$view['settings']['size']] : 'xs-12';
			?>

			<div class="widget-view <?= $view['show'] ? 'show' : ''; ?> col top left <?= $col_class; ?>" data-row="<?= $row; ?>" data-group="<?= $view['group']; ?>" data-query="<?= http_build_query($view['query']); ?>" data-view-id="<?= $view['view_id']; ?>">
				<div class="widget-view-box">
					<div class="view-header clearfix">
						<h3 class="view-title" <?= $can_modify ? 'contenteditable' : ''; ?>><?= $view['title']; ?></h3>

						<div class="view-choices buttons">
							<div class="show-hide buttons">
								<a class="hide-view button small" data-loading="Showing...">
									<b class="sprite hide-icon small"></b>
								</a>
								<a class="show-view button small">
									<b class="sprite show-icon small"></b>
								</a>
							</div>

							<div class="view-list-chart buttons">
								<a class="view-list button small">
									<b class="sprite view-list small"></b>
								</a>
								<a class="chart-bar button small" data-chart-type="Bar">
									<b class="sprite chart-bar small"></b>
								</a>
								<a class="chart-line button small" data-chart-type="Line">
									<b class="sprite chart-line small"></b>
								</a>
								<a class="chart-pie button small" data-chart-type="Pie">
									<b class="sprite chart-pie small"></b>
								</a>
							</div>
						</div>

						<? if ($can_modify && user_can('modify', 'views')) { ?>
							<div class="view-settings buttons">
								<div class="view-setting setting-buttons">
									<a class="move-up button move">
										<b class="move-up sprite"></b>
									</a>
									<a class="move-down button move">
										<b class="sprite move-down"></b>
									</a>
									<a class="save-view button" data-loading="Saving..."><?= _l("Save"); ?></a>
									<a class="delete-view button remove" data-loading="Removing..."><?= _l("X"); ?></a>
								</div>
								<a class="edit-view small button">
									<b class="sprite edit small"></b>
								</a>

								<br/>
								<div class="view-setting choose-view-box">
									<?=
									build('select', array(
										'name'   => 'view_listing_id',
										'data'   => array('' => _l("(Select Listing)")) + $data_view_listings,
										'select' => $view['view_listing_id'],
										'key'    => false,
										'value'  => 'name',
									)); ?>
								</div>
								<div class="view-setting choose-view-size">
									<?=
									build('select', array(
										'name'   => 'settings[size]',
										'data'   => $data_view_sizes,
										'select' => $view['settings']['size'],
									)); ?>
								</div>
							</div>
						<? } ?>
					</div>

					<div class="listing">
						<? if ($view['show'] && $row !== '__ac_template__') { ?>
							<?= $view['controller']->$view['method']($view['params']); ?>
						<? } ?>
					</div>
				</div>
			</div>
		<? } ?>
	</div>

	<div class="buttons views-actions">
		<a class="add-view button"><?= _l("New View"); ?></a>

		<? if (user_can('modify', 'views')) { ?>
			<a class="create-view button"><?= _l("Create View"); ?></a>

			<div class="view-popup create-view-box">
				<form action="<?= site_url('block/widget/views/create', array('redirect' => $this->url->here())); ?>" method="post">
					<div class="description"><?= _l("Provide your own SELECT SQL Statement. The view will be created as a filterable / sortable table."); ?></div>
					<input type="hidden" name="group" value="<?= $group; ?>"/>
					<input type="text" name="name" value="<?= "View Name"; ?>"/>
					<br/>
					<textarea name="sql" placeholder="<?= _l("WHERE Status = 'Complete'"); ?>"></textarea>
					<button class="submit-view"><?= _l("Create View"); ?></button>
				</form>

				<a class="button close">X</a>
			</div>
		<? } ?>
	</div>

</div>

<script type="text/javascript">
	var listings = <?= json_encode($data_view_listings); ?>;

	$('.widget-view-list').find('.button.move').click(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		if ($this.hasClass('move-down')) {
			$view.next().after($view);
		} else {
			$view.prev().before($view);
		}

		var sort_order = {};

		$('.widget-view-list .widget-view').each(function (i, e) {
			sort_order[$(e).attr('data-view-id')] = i;
		});

		$.post("<?= site_url('block/widget/views/save_sort_order'); ?>", {'sort_order': sort_order}, function (response) {
			if (!response.success) {
				$('.widget-view-list').ac_msg(response);
			}
		}, 'json');
	});

	$('.edit-view').click(function () {
		var $this = $(this);
		$this.closest('.view-settings').toggleClass('active');
		$this.find('.sprite').toggleClass('cancel');
	});

	$('.create-view').click(function () {
		$('.create-view-box').addClass('show');
	});

	$('.hide-view').click(function () {
		$(this).closest('.widget-view').removeClass('show');
	});

	$('.show-view').click(function () {
		var $view = $(this).closest('.widget-view').addClass('show');
		var $hide = $view.find('.hide-view');
		var view_listing_id = $view.find('[name=view_listing_id]').val();

		if (!view_listing_id) {
			return alert("<?= _l("Please Choose a listing first for this view"); ?>");
		}

		var listing = listings[view_listing_id];

		$hide.loading();

		$view.find('.widget-listing').addClass('loading');

		var q = $view.attr('data-query');
		query = (q ? q + '&' : '') + listing.query;

		query += '&view_id=' + $view.attr('data-view-id');

		$view.find('.listing').load(listing.path + (query ? '?' + query : ''), function () {
			$hide.loading('stop');
		});
	});

	$('.view-list-chart .button').click(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		var chart_type = $this.attr('data-chart-type');

		$view.toggleClass('view-chart', chart_type ? true : false);

		if (chart_type) {
			var chart = $view.find('.widget-chart canvas').data('chart');

			if (chart && chart[chart_type]) {
				chart.chart.destroy();
				chart[chart_type](chart.data, chart.options);
			}
		}

	});

	<? if ($can_modify && user_can('modify', 'views')) { ?>
	$('.choose-view-box [name]').change(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		$view.find('.show-view').click();
	});

	$('.save-view').click(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');

		var query = $view.find('.refresh-listing').attr('href').replace(/^[^\?]*\?/, '');

		$view.attr('data-query', query);

		var settings = $view.find('[name*="settings["]').serializeObject();

		var data = {
			view_id: $view.attr('data-view-id'),
			group: $view.attr('data-group'),
			view_listing_id: $view.find('[name=view_listing_id]').val(),
			path: $view.find('[name=path]').val(),
			query: query,
			title: $view.find('.view-title').html(),
			show: $view.hasClass("show") ? 1 : 0
		}

		data = $.fn.extend({}, settings, data);

		$this.loading();

		$.post("<?= $save_view;?>", data, function (response) {
			$this.loading('stop');
			if (response.view_id) {
				$view.attr('data-view-id', response.view_id);
				response.view_id = null;
			}

			$view.ac_msg(response);
			$view.find('.edit-view').click();
		}, 'json');
	});

	$('.delete-view').click(function () {
		var $this = $(this);
		if (!$this.hasClass('confirm')) {
			setTimeout(function () {
				$this.removeClass('confirm').loading('stop');
			}, 2000);
			return $this.loading({text: "<?= _l("Confirm Delete"); ?>"}).addClass('confirm');
		}

		if (confirm("<?= _l("Are you sure you want to remove this view?"); ?>")) {
			var $this = $(this);
			var $view = $this.closest('.widget-view');

			$this.loading();

			$.post("<?= $remove_view; ?>", {view_id: $view.attr('data-view-id')}, function (response) {
				$view.remove();
			});
		}
	});

	var col_sizes = <?= json_encode($col_sizes); ?>;

	$('.choose-view-size select').change(function () {
		var $this = $(this);
		$this.closest('.widget-view').removeClass(all_col_sizes).addClass(col_sizes[$this.val()]);
	});

	function all_col_sizes() {
		var classes = '';
		var sizes = ['xs', 'sm', 'md', 'lg'];
		for (var s in sizes) {
			for (var i = 1; i <= 12; i++) {
				classes += sizes[s] + '-' + i + ' ';
			}
		}
		return classes;
	}
	<? } ?>

	$('.view-popup .close').click(function () {
		$(this).closest('.view-popup').removeClass('show');
	});

	$('.block-widget-view').ac_template('v-list', {defaults: <?= json_encode($views['__ac_template__']); ?>});

	$('.add-view').click(function () {
		var $vlist = $('.block-widget-view .widget-view-list').ac_template('v-list', 'add');
		if ($vlist.find('[name=view_listing_id]').val()) {
			$vlist.find('.show-view').click();
		}
	});
</script>