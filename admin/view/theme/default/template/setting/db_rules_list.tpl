<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("DB Rules"); ?></h1>

			<div class="buttons">
				<a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a>
				<a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><?= _l("Table:"); ?></a></td>
							<td class="left"><?= _l("Field Name (Column):"); ?></td>
							<td class="left"><?= _l("Escape Method:"); ?></td>
							<td class="left"><?= _l("Can Truncate?"); ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($db_rules) { ?>
							<? foreach ($db_rules as $db_rule) { ?>
								<tr>
									<td style="text-align: center;">
										<input type="checkbox" name="batch[]" value="<?= $db_rule['db_rule_id']; ?>" <?= $db_rule['selected'] ? "checked=\"checked\"" : ""; ?> />
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
								<td class="center" colspan="4"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<?= call('common/footer'); ?>
