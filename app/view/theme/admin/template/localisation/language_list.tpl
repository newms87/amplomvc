<?= IS_AJAX ? '' : call('admin/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/language.png'); ?>" alt=""/> <?= _l("Language"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Language Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Language Name"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'code') { ?>
									<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= _l("Code"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_code; ?>"><?= _l("Code"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'sort_order') { ?>
									<a href="<?= $sort_sort_order; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Sort Order"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_sort_order; ?>"><?= _l("Sort Order"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($languages) { ?>
							<? foreach ($languages as $language) { ?>
								<tr>
									<td style="text-align: center;"><? if ($language['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $language['language_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $language['language_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $language['name']; ?></td>
									<td class="left"><?= $language['code']; ?></td>
									<td class="right"><?= $language['sort_order']; ?></td>
									<td class="right"><? foreach ($language['action'] as $action) { ?>
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
<?= IS_AJAX ? '' : call('admin/footer'); ?>
