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

			$col_class .= ' ' . ($view['view_type'] ? 'view-chart' : '');
			?>

			<div class="widget-view <?= $view['show'] ? 'show' : ''; ?> col top left <?= $col_class; ?>" data-row="<?= $row; ?>" data-group="<?= $view['group']; ?>" data-query="<?= http_build_query($view['query']); ?>" data-view-id="<?= $view['view_id']; ?>">
				<div class="widget-view-box">
					<div class="view-header clearfix">
						<div class="view-title-col col xs-12 sm-6 left">
							<h3 class="view-title" <?= $can_modify ? 'contenteditable' : ''; ?>><?= $view['title']; ?></h3>
						</div>

						<div class="view-buttons col xs-12 sm-6 right">
							<div class="view-choices col auto right buttons">
								<div class="show-hide buttons">
									<a class="hide-view button small" data-loading="Showing...">
										<b class="sprite hide-icon small"></b>
									</a>
									<a class="show-view button small">
										<b class="sprite show-icon small"></b>
									</a>
								</div>

								<div class="view-list-chart buttons">
									<? foreach ($data_view_types as $key => $view_type) { ?>
										<a class="<?= $view_type; ?> button small <?= $view['view_type'] === $key ? 'active' : ''; ?>" data-view-type="<?= $key; ?>">
											<b class="sprite <?= $view_type; ?> small"></b>
										</a>
									<? } ?>
								</div>
							</div>

							<? if ($can_modify) { ?>
								<div class="view-settings col auto right padding-left buttons">
									<a class="edit-view button">
										<b class="fa fa-pencil"></b>
									</a>

									<div class="view-setting-box on-active">
										<div class="row view-setting-header">
											<div class="col xs-9 left">
												<h3>
													<i class="fa fa-cog col auto"></i>

													<div class="col auto text padding-horizontal left">{{View Settings}}</div>
												</h3>
											</div>
											<div class="col xs-3 right">
												<a class="cancel-view button">
													<b class="fa fa-close"></b>
												</a>
											</div>
										</div>

										<div class="view-setting-list row left">
											<div class="view-setting setting-buttons">
												<a class="move-up button move">
													<b class="move-up sprite"></b>
												</a>
												<a class="move-down button move">
													<b class="sprite move-down"></b>
												</a>
												<a class="save-view button" data-loading="Saving...">{{Save}}</a>
												<a class="delete-view button remove">
													<i class="fa fa-trash-o"></i>
												</a>
											</div>

											<div class="view-setting view-style">
												<div class="choose-view-box col auto">
													<?= build(array(
														'type'   => 'select',
														'name'   => 'view_listing_id',
														'data'   => array('' => _l("(Select Listing)")) + $data_view_listings,
														'select' => $view['view_listing_id'],
														'value'  => false,
														'label'  => 'name',
													)); ?>
												</div>

												<div class="choose-view-size col auto padding-left">
													<?= build(array(
														'type'   => 'select',
														'name'   => 'settings[size]',
														'data'   => $data_view_sizes,
														'select' => $view['settings']['size'] ? $view['settings']['size'] : 100,
													)); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							<? } ?>
						</div>
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
		<a class="add-view button">{{New View}}</a>

		<? if ($r->user->isTopAdmin()) { ?>
			<a class="create-view button">{{Create Listing}}</a>

			<div class="view-popup create-view-box">
				<form action="<?= site_url('block/widget/views/create', array('redirect' => $r->url->here())); ?>" method="post">
					<div class="description">{{Provide your own SELECT SQL Statement. The view will be created as a filterable / sortable table.}}</div>
					<input type="hidden" name="group" value="<?= $group; ?>"/>
					<input type="text" name="name" value="{{View Name}}"/>
					<br/>
					<textarea name="sql" placeholder="{{WHERE Status = 'Complete'}}"></textarea>
					<button class="submit-view" data-loading="{{Submitting...}}">{{Submit}}</button>
				</form>

				<a class="button close">X</a>
			</div>
		<? } ?>
	</div>

</div>

<script type="text/javascript">
	var listings = <?= json_encode($data_view_listings); ?>;

	$('.widget-view-list').find('.button.move').click(function() {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		if ($this.hasClass('move-down')) {
			$view.next().after($view);
		} else {
			$view.prev().before($view);
		}

		var sort_order = {};

		$('.widget-view-list .widget-view').each(function(i, e) {
			sort_order[$(e).attr('data-view-id')] = i;
		});

		var data = {
			'sort_order': sort_order,
			page_path:    '<?= $page_path; ?>'
		}

		$.post($ac.site_url + 'block/widget/views/save-sort-order', data, function(response) {
			if (!response.success) {
				$('.widget-view-list').show_msg(response);
			}
		}, 'json');
	});

	$('.edit-view').click(function() {
		$(this).closest('.view-settings').addClass('is-active');
	});
	$('.cancel-view').click(function() {
		$(this).closest('.view-settings').removeClass('is-active');
	});

	$('.create-view').click(function() {
		$('.create-view-box').addClass('show');
	});

	$('.hide-view').click(function() {
		$(this).closest('.widget-view').removeClass('show');
	});

	$('.show-view').click(function() {
		var $view = $(this).closest('.widget-view').addClass('show');
		var $hide = $view.find('.hide-view');
		var view_listing_id = $view.find('[name=view_listing_id]').val();

		if (!view_listing_id) {
			return $.ampAlert("{{Please Choose a listing first for this view}}");
		}

		var listing = listings[view_listing_id];

		$hide.loading();

		$view.find('.widget-listing').addClass('loading');

		var q = $view.attr('data-query');
		query = (q ? q + '&' : '') + listing.query;

		query += '&view_id=' + $view.attr('data-view-id');

		$view.find('.listing').load(listing.path + (query ? '?' + query : ''), function() {
			$hide.loading('stop');
		});
	});

	$('.view-list-chart .button').click(function() {
		var $this = $(this);

		if ($this.hasClass('active')) {
			return;
		}

		var $view = $this.closest('.widget-view');
		var chart_type = $this.attr('data-view-type');

		$view.toggleClass('view-chart', chart_type ? true : false);

		if (chart_type) {
			var $canvas = $view.find('.widget-chart canvas');
			var chart = $canvas.data('chart');

			$canvas.renderChart(chart_type);
		}

		$this.closest('.view-list-chart').find('.active').removeClass('active');
		$this.addClass('active');
	});

	<? if ($can_modify) { ?>
	$('.choose-view-box [name]').change(function() {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		$view.find('.show-view').click();
	});

	$('.save-view').click(function() {
		var $this = $(this);
		var $view = $this.closest('.widget-view');

		var query = $view.find('.refresh-listing').attr('href').replace(/^[^\?]*\?/, '');

		$view.attr('data-query', query);

		var settings = $view.find('[name*="settings["]').serializeObject();

		var data = {
			view_id:         $view.attr('data-view-id'),
			group:           $view.attr('data-group'),
			view_listing_id: $view.find('[name=view_listing_id]').val(),
			path:            $view.find('[name=path]').val(),
			query:           query,
			title:           $view.find('.view-title').html(),
			view_type:       $view.find('[data-view-type].active').attr('data-view-type'),
			show:            $view.hasClass("show") ? 1 : 0
		}

		data = $.fn.extend({}, settings, data);

		data.page_path = '<?= $page_path; ?>';

		$this.loading();

		$.post($ac.site_url + 'block/widget/views/save-view', data, function(response) {
			$this.loading('stop');
			if (response.data && response.data.view_id) {
				$view.attr('data-view-id', response.data.view_id);
			}

			$view.show_msg(response);
			$view.find('.cancel-view').click();
		}, 'json');
	});

	$('.delete-view').click(function() {
		var $this = $(this);
		if (!$this.hasClass('confirm')) {
			setTimeout(function() {
				$this.removeClass('confirm').loading('stop');
			}, 2000);
			return $this.loading({text: "{{Confirm}}"}).addClass('confirm');
		}

		$.ampConfirm({
			title:     "{{Remove View?}}",
			text:      "{{Are you sure you want to remove this view?}}",
			onConfirm: function() {
				var $view = $this.closest('.widget-view');

				$this.loading({text: "Removing..."});

				$.post($ac.site_url + 'block/widget/views/remove-view', {
					view_id:   $view.attr('data-view-id'),
					page_path: '<?= $page_path; ?>'
				}, function() {
					$view.remove();
				});
			}
		})
	});

	var col_sizes = <?= json_encode($col_sizes); ?>;

	$('.choose-view-size select').change(function() {
		var $this = $(this);
		$this.closest('.widget-view').removeClass(all_col_sizes).addClass(col_sizes[$this.val()]);
		window.dispatchEvent(new Event('resize'));
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

	$('.view-popup .close').click(function() {
		$(this).closest('.view-popup').removeClass('show');
	});

	$('.block-widget-view').ac_template('v-list', {defaults: <?= json_encode($views['__ac_template__']); ?>});

	var $add_view = $('.add-view').click(function() {
		var $vlist = $('.block-widget-view .widget-view-list').ac_template('v-list', 'add');
		if ($vlist.find('[name=view_listing_id]').val()) {
			$vlist.find('.show-view').click();
		}
	})

	<? if (count($views) === 1) { ?>
	$(document).on('ac_template', function() {
		$add_view.click();
	});
	<? } ?>
</script>
