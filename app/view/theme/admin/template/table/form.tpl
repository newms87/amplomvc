<?= $is_ajax ? '' : call('admin/header'); ?>

<section class="section table-form-tpl">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url($model['path'] . '/save', $model['value'] . '=' . $record[$model['value']]); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save table-form-form">
		<div class="row heading left">
			<h1>
				<i class="fa fa-cog"></i>
				{{<?= $model['title']; ?>}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>

				<a href="<?= site_url($model['path']); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>

		<div class="row left section">
			<div class="col left top">
				<? foreach ($columns as $c => $column) { ?>
					<? if (isset($record[$c]) && $column['type'] !== 'pk-int') { ?>
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
