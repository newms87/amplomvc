<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a>
				<a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><?= $column_table; ?></a></td>
							<td class="left"><?= $column_column; ?></td>
							<td class="left"><?= $column_escape_type; ?></td>
							<td class="left"><?= $column_truncate; ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($db_rules) { ?>
						<? foreach ($db_rules as $db_rule) { ?>
						<tr>
							<td style="text-align: center;">
								<input type="checkbox" name="selected[]" value="<?= $db_rule['db_rule_id']; ?>" <?= $db_rule['selected']?"checked='checked'":""; ?> />
							</td>
							<td class="left"><?= $db_rule['table']; ?></td>
							<td class="left"><?= $db_rule['column']; ?></td>
							<td class="left"><?= $data_escape_types[(int)$db_rule['escape_type']]; ?></td>
							<td class="left"><?= $data_yes_no[(int)$db_rule['truncate']]; ?></td>
							<td class="right">
								[ <a href="<?= $db_rule['action']['href']; ?>"><?= $db_rule['action']['text']; ?></a> ]
							</td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="4"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 