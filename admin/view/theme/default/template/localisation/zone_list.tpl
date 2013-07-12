<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
	<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'country.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><? if ($sort == 'c.name') { ?>
								<a href="<?= $sort_country; ?>" class="<?= strtolower($order); ?>"><?= $column_country; ?></a>
								<? } else { ?>
								<a href="<?= $sort_country; ?>"><?= $column_country; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'z.name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
								<? } else { ?>
								<a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'z.code') { ?>
								<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= $column_code; ?></a>
								<? } else { ?>
								<a href="<?= $sort_code; ?>"><?= $column_code; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($zones) { ?>
						<? foreach ($zones as $zone) { ?>
						<tr>
							<td style="text-align: center;"><? if ($zone['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $zone['zone_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $zone['zone_id']; ?>" />
								<? } ?></td>
							<td class="left"><?= $zone['country']; ?></td>
							<td class="left"><?= $zone['name']; ?></td>
							<td class="left"><?= $zone['code']; ?></td>
							<td class="right"><? foreach ($zone['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="5"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $footer; ?>