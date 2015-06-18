<? $id = uniqid(); ?>

<div id="<?= $id; ?>" class="widget-listing" data-href="<?= site_url($listing_path, $_GET); ?>">
	<? if ($show_messages) { ?>
		<?= render_message(); ?>
	<? } ?>

	<? if ($show_limits === 'top') { ?>
		<div class="limits clearfix">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<? if ($show_controls) { ?>
		<div class="view-controls">
			<a class="refresh-listing" href="<?= site_url($listing_path, $_GET); ?>">
				<b class="sprite refresh small"></b>
			</a>

			<? if (user_can('r', 'block/widget/listing/export')) { ?>
				<a href="<?= site_url($listing_path, $_GET + array('export' => '')); ?>" class="button export-view small">
					<b class="sprite export small"></b>
				</a>
			<? } ?>

			<button class="modify-view small">
				<b class="sprite settings small"></b>
			</button>

			<div class="view-config" data-listing="#<?= $id; ?>">
				<div class="view-tabs htabs">
					<a href=".col-tab">{{Columns}}</a>
					<a href=".group-tab">{{Groups / Aggregate}}</a>
					<? if (user_can('w', 'admin/views')) { ?>
						<a href=".view-listing-tab">{{Settings}}</a>
					<? } ?>
				</div>

				<? if (!empty($extra_cols)) { ?>
					<div class="col-tab tab-content">
						<div class="select-cols">
							<?=
							build(array(
								'type'   => 'multiselect',
								'name'   => 'columns',
								'data'   => $extra_cols,
								'select' => array_keys($columns),
								'value'  => 'Field',
								'label'  => 'display_name',
							)); ?>

							<div class="buttons">
								<a class="save-view-cols button" data-loading="{{Applying...}}" href="<?= site_url($listing_path, _get_exclude('columns')); ?>">{{Apply}}</a>
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
							<label for="view-type-<?= $view_id; ?>">{{Default View Type}}</label>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'view_type',
								'data'   => $data_chart_types,
								'select' => $view_type,
								'#id'    => 'view-type-' . $view_id,
							)); ?>
						</div>

						<br/>

						<h2>{{Chart Settings}}</h2>

						<div class="form-item">
							<label for="chart-group-<?= $view_id; ?>">{{X axis (Group Column)}}</label>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'chart[group_by]',
								'data'   => $extra_cols,
								'select' => isset($chart['group_by']) ? $chart['group_by'] : null,
								'value'  => 'Field',
								'label'  => 'display_name',
								'#id'    => 'chart-group-' . $view_id,
							)); ?>
						</div>

						<div class="form-item">
							<label for="chart-data-<?= $view_id; ?>">{{Y axis (Data Column)}}</label>
							<?=
							build(array(
								'type'   => 'multiselect',
								'name'   => 'chart[data_cols]',
								'data'   => $extra_cols,
								'select' => isset($chart['data_cols']) ? $chart['data_cols'] : null,
								'value'  => 'Field',
								'label'  => 'display_name',
								'#id'    => 'chart-data-' . $view_id,
								'#class' => 'chart-data-cols',
							)); ?>
						</div>

						<div class="form-item submit buttons center">
							<button class="save-settings" data-loading="{{Saving...}}">{{Save Settings}}</button>
						</div>

					</div>
				<? } ?>
			</div>
		</div>
	<? } ?>

	<div class="view-types">
		<div class="listings view-type">
			<?= $listing; ?>
		</div>

		<? if ($show_charts) { ?>
			<div class="charts view-type">
				<? if (!empty($chart)) { ?>
					<?=
					block('widget/chart', null, array(
						'data'     => $records,
						'settings' => $chart,
						'type'     => $view_type,
					)); ?>
				<? } ?>
			</div>
		<? } ?>
	</div>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<? if ($show_limits === 'bottom') { ?>
		<div class="limits clearfix">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<script type="text/javascript">
		$('#<?= $id; ?>').list_widget(<?= (int)$view_id; ?>);
	</script>

</div>

