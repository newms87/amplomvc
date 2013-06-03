<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="document.getElementById('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><? if ($sort == 'cd.name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
								<? } else { ?>
								<a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.code') { ?>
								<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= $column_code; ?></a>
								<? } else { ?>
								<a href="<?= $sort_code; ?>"><?= $column_code; ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'c.discount') { ?>
								<a href="<?= $sort_discount; ?>" class="<?= strtolower($order); ?>"><?= $column_discount; ?></a>
								<? } else { ?>
								<a href="<?= $sort_discount; ?>"><?= $column_discount; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.date_start') { ?>
								<a href="<?= $sort_date_start; ?>" class="<?= strtolower($order); ?>"><?= $column_date_start; ?></a>
								<? } else { ?>
								<a href="<?= $sort_date_start; ?>"><?= $column_date_start; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.date_end') { ?>
								<a href="<?= $sort_date_end; ?>" class="<?= strtolower($order); ?>"><?= $column_date_end; ?></a>
								<? } else { ?>
								<a href="<?= $sort_date_end; ?>"><?= $column_date_end; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.status') { ?>
								<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
								<? } else { ?>
								<a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($coupons) { ?>
						<? foreach ($coupons as $coupon) { ?>
						<tr>
							<td style="text-align: center;"><? if ($coupon['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $coupon['coupon_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $coupon['coupon_id']; ?>" />
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