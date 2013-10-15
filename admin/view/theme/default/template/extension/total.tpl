<?= $header; ?>

<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').trigger('saving').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="extension_settings">
					<table class="form">
						<tr>
							<td><?= $entry_title; ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>" /></td>
						</tr>
						<? if (!empty($extend_settings)) { ?>
							<tr>
								<td colspan="2"><?= $extend_settings; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
