<?= call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/length.png'); ?>" alt=""/> <?= _l("Length Class"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'title') { ?>
									<a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= _l("Length Title"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_title; ?>"><?= _l("Length Title"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'unit') { ?>
									<a href="<?= $sort_unit; ?>" class="<?= strtolower($order); ?>"><?= _l("Length Unit"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_unit; ?>"><?= _l("Length Unit"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'value') { ?>
									<a href="<?= $sort_value; ?>" class="<?= strtolower($order); ?>"><?= _l("Value"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_value; ?>"><?= _l("Value"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($length_classes) { ?>
							<? foreach ($length_classes as $length_class) { ?>
								<tr>
									<td style="text-align: center;"><? if ($length_class['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $length_class['length_class_id']; ?>" checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $length_class['length_class_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $length_class['title']; ?></td>
									<td class="left"><?= $length_class['unit']; ?></td>
									<td class="right"><?= $length_class['value']; ?></td>
									<td class="right"><? foreach ($length_class['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="5"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= call('admin/common/footer'); ?>
