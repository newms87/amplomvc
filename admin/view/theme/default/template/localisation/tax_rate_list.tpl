<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<? if ($success) { ?>
			<div class="message_box success"><?= $success; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= _l("Tax Rates"); ?></h1>

				<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="list">
						<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'tr.name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Tax Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Tax Name"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'tr.rate') { ?>
									<a href="<?= $sort_rate; ?>" class="<?= strtolower($order); ?>"><?= _l("Tax Rate"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_rate; ?>"><?= _l("Tax Rate"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.type') { ?>
									<a href="<?= $sort_type; ?>" class="<?= strtolower($order); ?>"><?= _l("Type"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_type; ?>"><?= _l("Type"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'gz.name') { ?>
									<a href="<?= $sort_geo_zone; ?>"
									   class="<?= strtolower($order); ?>"><?= _l("Geo Zone"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_geo_zone; ?>"><?= _l("Geo Zone"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.date_added') { ?>
									<a href="<?= $sort_date_added; ?>"
									   class="<?= strtolower($order); ?>"><?= _l("Date Added"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_added; ?>"><?= _l("Date Added"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.date_modified') { ?>
									<a href="<?= $sort_date_modified; ?>"
									   class="<?= strtolower($order); ?>"><?= _l("Date Modified"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_modified; ?>"><?= _l("Date Modified"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
						</thead>
						<tbody>
						<? if ($tax_rates) { ?>
							<? foreach ($tax_rates as $tax_rate) { ?>
								<tr>
									<td style="text-align: center;"><? if ($tax_rate['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $tax_rate['tax_rate_id']; ?>"
											       checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $tax_rate['tax_rate_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $tax_rate['name']; ?></td>
									<td class="right"><?= $tax_rate['rate']; ?></td>
									<td class="left"><?= $tax_rate['type']; ?></td>
									<td class="left"><?= $tax_rate['geo_zone']; ?></td>
									<td class="left"><?= $tax_rate['date_added']; ?></td>
									<td class="left"><?= $tax_rate['date_modified']; ?></td>
									<td class="right"><? foreach ($tax_rate['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="9"><?= $text_no_results; ?></td>
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