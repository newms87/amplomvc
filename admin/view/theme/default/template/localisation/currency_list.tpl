<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'payment.png'; ?>" alt=""/> <?= _l("Currency"); ?></h1>

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
									<a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= _l("Currency Title"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_title; ?>"><?= _l("Currency Title"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'code') { ?>
									<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= _l("Code"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_code; ?>"><?= _l("Code"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'value') { ?>
									<a href="<?= $sort_value; ?>" class="<?= strtolower($order); ?>"><?= _l("Value"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_value; ?>"><?= _l("Value"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'date_modified') { ?>
									<a href="<?= $sort_date_modified; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Last Updated"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_modified; ?>"><?= _l("Last Updated"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($currencies) { ?>
							<? foreach ($currencies as $currency) { ?>
								<tr>
									<td style="text-align: center;"><? if ($currency['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $currency['currency_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $currency['currency_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $currency['title']; ?></td>
									<td class="left"><?= $currency['code']; ?></td>
									<td class="right"><?= $currency['value']; ?></td>
									<td class="left"><?= $currency['date_modified']; ?></td>
									<td class="right"><? foreach ($currency['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="6"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $common_footer; ?>
