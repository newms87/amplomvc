<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("URL Aliases"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("URL Alias:"); ?></td>
							<td><input type="text" name="alias" value="<?= $alias; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Path:"); ?></td>
							<td><input type="text" name="path" value="<?= $path; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Query:"); ?></td>
							<td><input type="text" name="query" value="<?= $query; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Redirect:"); ?></td>
							<td><input type="text" name="redirect" value="<?= $redirect; ?>" size="40"/></td>
						</tr>
						<tr>
							<td><?= _l("Store:"); ?></td>
							<td>
								<? $this->builder->setConfig('store_id', 'name'); ?>
								<?= $this->builder->build('select', $data_stores, 'store_id', $store_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', $status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $this->call('common/footer'); ?>
