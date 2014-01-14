<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Coupon"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="document.getElementById('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'cd.name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Coupon Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Coupon Name"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.code') { ?>
									<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= _l("Code"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_code; ?>"><?= _l("Code"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'c.discount') { ?>
									<a href="<?= $sort_discount; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Discount"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_discount; ?>"><?= _l("Discount"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.date_start') { ?>
									<a href="<?= $sort_date_start; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Date Start"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_start; ?>"><?= _l("Date Start"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.date_end') { ?>
									<a href="<?= $sort_date_end; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Date End"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_end; ?>"><?= _l("Date End"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.status') { ?>
									<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= _l("Status"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_status; ?>"><?= _l("Status"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($coupons) { ?>
							<? foreach ($coupons as $coupon) { ?>
								<tr>
									<td style="text-align: center;"><? if ($coupon['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $coupon['coupon_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $coupon['coupon_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $coupon['name']; ?></td>
									<td class="left"><?= $coupon['code']; ?></td>
									<td class="right"><?= $coupon['discount']; ?></td>
									<td class="left"><?= $coupon['date_start']; ?></td>
									<td class="left"><?= $coupon['date_end']; ?></td>
									<td class="left"><?= $coupon['status']; ?></td>
									<td class="right"><? foreach ($coupon['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="8"><?= $text_no_results; ?></td>
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
