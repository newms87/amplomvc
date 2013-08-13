<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_alias; ?></td>
							<td><input type="text" name="alias" value="<?= $alias; ?>" size="40" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_path; ?></td>
							<td><input type="text" name="path" value="<?= $path; ?>" size="40" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_query; ?></td>
							<td><input type="text" name="query" value="<?= $query; ?>" size="40" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_redirect; ?></td>
							<td><input type="text" name="redirect" value="<?= $redirect; ?>" size="40" /></td>
						</tr>
						<tr>
							<td><?= $entry_store; ?></td>
							<td>
								<? $this->builder->set_config('store_id', 'name'); ?>
								<?= $this->builder->build('select', $data_stores, 'store_id', $store_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$statuses, 'status',$status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>