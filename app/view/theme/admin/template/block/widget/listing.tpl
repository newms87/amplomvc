<div class="widget-listing">
	<? if ($show_messages) { ?>
		<?= render_message(); ?>
	<? } ?>

	<? if ($show_limits) { ?>
		<div class="limits clearfix">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<div class="view-controls">
		<a class="refresh-listing" href="<?= $refresh; ?>">
			<b class="sprite refresh small"></b>
		</a>

		<a href="<?= site_url($refresh, 'export'); ?>" class="button export-view small">
			<b class="sprite export small"></b>
		</a>

		<button class="modify-view small">
			<b class="sprite settings small"></b>
		</button>

		<div class="view-config">
			<button class="close">X</button>

			<div class="view-tabs htabs">
				<a href=".col-tab"><?= _l("Columns"); ?></a>
				<a href=".group-tab"><?= _l("Groups / Aggregate"); ?></a>
				<? if (user_can('w', 'admin/views')) { ?>
					<a href=".view-listing-tab"><?= _l("Settings"); ?></a>
				<? } ?>
			</div>

			<? if (!empty($extra_cols)) { ?>
				<div class="col-tab tab-content">
					<div class="select-cols">
						<?=
						build('multiselect', array(
							'name'   => 'columns',
							'data'   => $extra_cols,
							'select' => array_keys($columns),
							'key'    => 'Field',
							'value'  => 'display_name',
						)); ?>

						<div class="buttons">
							<a class="filter-cols button" data-loading="<?= _l("Applying..."); ?>" href="<?= site_url($listing_path, $this->url->getQueryExclude('columns')); ?>"><?= _l("Apply"); ?></a>
						</div>
					</div>
				</div>
			<? } ?>

			<div class="group-tab tab-content">
				Group By / Aggregate... Waiting to be implemented.
			</div>

			<? if (user_can('w', 'admin/views')) { ?>
				<div class="view-listing-tab tab-content form">
					<input type="hidden" name="view_id" value="<?= $view_id; ?>"/>
					<div class="form-item">
						<label for="view-type-<?= $view_id; ?>"><?= _l("Default View Type"); ?></label>
						<?=
						build('select', array(
							'name'   => 'view_type',
							'data'   => $data_chart_types,
							'select' => $view_type,
							'#id'    => 'view-type-' . $view_id,
						)); ?>
					</div>

					<br/>
					<h2><?= _l("Chart Settings"); ?></h2>

					<div class="form-item">
						<label for="chart-group-<?= $view_id; ?>"><?= _l("X axis (Group Column)"); ?></label>
						<?=
						build('select', array(
							'name'   => 'chart[group_by]',
							'data'   => $extra_cols,
							'select' => isset($chart['group_by']) ? $chart['group_by'] : null,
							'key'    => 'Field',
							'value'  => 'display_name',
							'#id'    => 'chart-group-' . $view_id,
						)); ?>
					</div>

					<div class="form-item">
						<label for="chart-data-<?= $view_id; ?>"><?= _l("Y axis (Data Column)"); ?></label>
						<?=
						build('multiselect', array(
							'name'   => 'chart[data_cols]',
							'data'   => $extra_cols,
							'select' => isset($chart['data_cols']) ? $chart['data_cols'] : null,
							'key'    => 'Field',
							'value'  => 'display_name',
							'#id'    => 'chart-data-' . $view_id,
							'#class' => 'chart-data-cols',
						)); ?>
					</div>

					<div class="form-item submit buttons center">
						<button class="save-settings" data-loading="<?= _l("Saving..."); ?>"><?= _l("Save Settings"); ?></button>
					</div>

				</div>
			<? } ?>
		</div>
	</div>

	<div class="view-types">
		<div class="listings view-type">
			<?= $listing; ?>
		</div>

		<div class="charts view-type">
			<? if (!empty($chart)) { ?>
				<?=
				block('widget/chart', null, array(
					'data'     => $rows,
					'settings' => $chart,
					'type'     => $view_type,
				)); ?>
			<? } ?>
		</div>
	</div>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<script type="text/javascript">
		var $list_widget = $('.widget-listing').use_once();

		$list_widget.find('.export-view').click(function() {
			if (confirm("<?= _l("Do you want to export the full data set?"); ?>")) {
				window.location = $(this).attr('href') + '&limit=0';
				return false;
			}
		});

		$list_widget.find('.view-tabs a').tabs();

		$list_widget.find('.modify-view').click(function () {
			$(this).siblings('.view-config').toggleClass('show');
		});

		$list_widget.find('.view-config .close').click(function () {
			$(this).closest('.view-config').removeClass('show');
		});

		$list_widget.find('.select-cols .multiselect-list').sortable();

		$list_widget.find('.filter-cols').click(function () {
			$(this).closest('.view-config').find('.close').click();
		});

		$list_widget.find('.pagination a, .sortable, .filter-button, .reset-button, .limits a, .refresh-listing, .filter-cols')
			.click(function () {
				var $this = $(this);

				if (!$this.attr('href')) {
					return false;
				}

				var $listing = $this.closest('.widget-listing');
				$listing.addClass("loading");
				$listing.find('.refresh-listing').addClass('refreshing');

				var data = {columns: {}};

				$this.closest('.select-cols').find(':checked').each(function (i, e) {
					data.columns[$(e).val()] = i;
				});

				data.view_id = <?= (int)$view_id; ?>;

				$.get($this.attr('href'), data, function (response) {
					var $parent = $listing.closest('.listing');
					$listing.siblings('.messages').remove();
					$listing.replaceWith(response);
					$parent.trigger('loaded');
				});

				return false;
			});

		$list_widget.find('.chart-data-cols .multiselect-list').sortable();

		$list_widget.find('.save-settings').click(function () {
			var $this = $(this);
			var $form = $this.closest('.form');
			var $widget = $this.closest('.widget-view');
			var view_type = $form.find('[name="view_type"]').val();

			$this.loading();

			$.post("<?= site_url('block/widget/listing/save-settings'); ?>", $form.find('[name]').serialize(),function (response) {
				$form.ac_msg(response);
				$this.closest('.view-config').removeClass('show');
				$widget.find('.refresh-listing').click();

				//Hack to show chart / listing view
				$widget.find('[data-view-type="' + view_type + '"]').click();

			}, 'json').always(function () {
				$this.loading('stop');
			});
		});
	</script>

</div>

