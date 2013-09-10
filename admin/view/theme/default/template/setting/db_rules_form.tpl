<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div id="tab-general">
						<table class="form">
							<tr>
								<td class="required"> <?= $entry_table; ?></td>
								<td><input type="text" name="table" value="<?= $table; ?>" size="40"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_column; ?></td>
								<td><input type="text" name="column" value="<?= $column; ?>" size="40"/>
							</tr>
							<tr>
								<td><?= $entry_escape_type; ?></td>
								<td><?= $this->builder->build('select', $data_escape_types, 'escape_type', (int)$escape_type); ?></td>
							</tr>
							<tr>
								<td><?= $entry_truncate; ?></td>
								<td><?= $this->builder->build('select', $data_yes_no, 'truncate', (int)$truncate); ?></td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>