<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'measurement.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons"><a onclick="location="<?= $insert; ?>"" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'title') { ?>
									<a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= _l("Title"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_title; ?>"><?= _l("Title"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'unit') { ?>
									<a href="<?= $sort_unit; ?>" class="<?= strtolower($order); ?>"><?= _l("Unit"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_unit; ?>"><?= _l("Unit"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($measurement_classes) { ?>
							<? foreach ($measurement_classes as $measurement_class) { ?>
								<tr>
									<td style="align: center;"><? if ($measurement_class['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $measurement_class['measurement_class_id']; ?>" checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $measurement_class['measurement_class_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $measurement_class['title']; ?></td>
									<td class="left"><?= $measurement_class['unit']; ?></td>
									<td class="right"><? foreach ($measurement_class['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
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
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
