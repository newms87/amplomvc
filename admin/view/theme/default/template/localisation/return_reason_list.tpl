<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
	<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><? if ($sort == 'name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
								<? } else { ?>
								<a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($return_reasons) { ?>
						<? foreach ($return_reasons as $return_reason) { ?>
						<tr>
							<td style="text-align: center;"><? if ($return_reason['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $return_reason['return_reason_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $return_reason['return_reason_id']; ?>" />
								<? } ?></td>
							<td class="left"><?= $return_reason['name']; ?></td>
							<td class="right"><? foreach ($return_reason['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="3"><?= $text_no_results; ?></td>
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