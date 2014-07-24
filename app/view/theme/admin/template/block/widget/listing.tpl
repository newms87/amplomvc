<div class="widget-listing">
	<? if ($show_messages) { ?>
		<?= $this->message->render(); ?>
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

		<button class="modify-view small">
			<b class="sprite settings small"></b>
		</button>

		<div class="view-config">
			<button class="close">X</button>

			<div class="view-tabs htabs">
				<a href=".col-tab"><?= _l("Columns"); ?></a>
				<a href=".group-tab"><?= _l("Groups / Aggregate"); ?></a>
				<? if (user_can('modify', 'views')) { ?>
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

			<? if (user_can('modify', 'views')) { ?>
				<div class="view-listing-tab tab-content">
					<?=
					build('select', array(
						'name'   => 'chart[type]',
						'data'   => $data_chart_types,
						'select' => isset($chart['type']) ? $chart['type'] : null,
					)); ?>
				</div>
			<? } ?>
		</div>
	</div>

	<div class="listings">
		<?= $listing; ?>
	</div>

	<? if (!empty($chart)) { ?>
		<?= block('widget/chart', null, array('data'     => $rows,
		                                      'settings' => $chart
		)); ?>
	<? } ?>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<script type="text/javascript">
		var $list_widget = $('.widget-listing').not('.activated').addClass('activated');

		$list_widget.find('.view-tabs a').tabs();

		$list_widget.find('.modify-view').click(function () {
			$(this).siblings('.view-config').toggleClass('show');
		});

		$list_widget.find('.view-config .close').click(function () {
			$(this).closest('.view-config').removeClass('show');
		});

		$list_widget.find('.select-cols .scrollbox').sortable();

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
					init_ajax();
					$parent.trigger('loaded');
				});

				return false;
			});
	</script>

</div>

