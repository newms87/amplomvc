<?= $is_ajax ? '' : call('admin/header'); ?>

<section class="section table-form-tpl">
	<form action="<?= site_url($model['path'] . '/save', $model['value'] . '=' . $record[$model['value']]); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save table-form-form">
		<div class="row heading left">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
			</div>
		</div>

		<div class="row left section">
			<div class="col left top">
				<? foreach ($columns as $c => $column) { ?>
					<? if (isset($record[$c]) && $column['type'] !== 'pk-int' && $column['type'] !== 'pk') { ?>
						<?
						$column['#id']    = 'column-' . $c;
						$column['select'] = $record[$c];
						$column['name']   = $c;
						?>

						<div class="form-item">
							<label for="<?= $column['#id']; ?>" class="col xs-3 md-2 left"><?= $column['label']; ?></label>

							<div class="col xs-9 md-10 left value">
								<? if (!empty($column['build'])) { ?>
									<? $column = $column['build'] + $column; ?>
								<? } ?>


								<?= build($column); ?>
							</div>
						</div>
					<? } ?>
				<? } ?>
			</div>
		</div>
	</form>
</section>

<script type="text/javascript">
	$.ac_datepicker()
</script>
<?= $is_ajax ? '' : call('admin/footer'); ?>
