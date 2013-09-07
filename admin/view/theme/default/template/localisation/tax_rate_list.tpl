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
				<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="list">
						<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'tr.name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'tr.rate') { ?>
									<a href="<?= $sort_rate; ?>" class="<?= strtolower($order); ?>"><?= $column_rate; ?></a>
								<? } else { ?>
									<a href="<?= $sort_rate; ?>"><?= $column_rate; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.type') { ?>
									<a href="<?= $sort_type; ?>" class="<?= strtolower($order); ?>"><?= $column_type; ?></a>
								<? } else { ?>
									<a href="<?= $sort_type; ?>"><?= $column_type; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'gz.name') { ?>
									<a href="<?= $sort_geo_zone; ?>"
									   class="<?= strtolower($order); ?>"><?= $column_geo_zone; ?></a>
								<? } else { ?>
									<a href="<?= $sort_geo_zone; ?>"><?= $column_geo_zone; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.date_added') { ?>
									<a href="<?= $sort_date_added; ?>"
									   class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'tr.date_modified') { ?>
									<a href="<?= $sort_date_modified; ?>"
									   class="<?= strtolower($order); ?>"><?= $column_date_modified; ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_modified; ?>"><?= $column_date_modified; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
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