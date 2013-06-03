<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if($errors){?>
		<div class="message_box warning">
		<? $br=false; foreach($errors as $e){ echo ($br?'<br>':'') . $e; $br=true;}?>
		</div>
	<? }?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left">
								<a href="<?= $sort_name; ?>" <?= $sort=='name'?'class="'.strtolower($order).'"':''; ?>><?= $column_name; ?></a>
							</td>
							<td class="left">
								<a href="<?= $sort_vendor_id; ?>" <?= $sort=='vendor_id'?'class="'.strtolower($order).'"':''; ?>><?= $column_vendor_id; ?></a>
							</td>
							<td class="left">
								<a href="<?= $sort_date_active; ?>" <?= $sort=='date_active'?'class="'.strtolower($order).'"':''; ?>><?= $column_date_active; ?></a>
							</td>
							<td class="left">
								<a href="<?= $sort_date_expires; ?>" <?= $sort=='date_expires'?'class="'.strtolower($order).'"':''; ?>><?= $column_date_expires; ?></a>
							</td>
							<td class="left">
								<a href="<?= $sort_status; ?>" <?= $sort=='status'?'class="'.strtolower($order).'"':''; ?>><?= $column_status; ?></a>
							</td>
							<td class="left">
								<a href="<?= $sort_sort_order; ?>" <?= $sort=='sort_order'?'class="'.strtolower($order).'"':''; ?>><?= $column_sort_order; ?></a>
							</td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($manufacturers) { ?>
						<? foreach ($manufacturers as $manufacturer) { ?>
						<tr>
							<td style="text-align: center;"><? if ($manufacturer['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $manufacturer['manufacturer_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $manufacturer['manufacturer_id']; ?>" />
								<? } ?></td>
							<td class="left"><?= $manufacturer['name']; ?></td>
							<td class="left"><?= $manufacturer['vendor_id']; ?></td>
							<td class="right"><?= $manufacturer['date_active']; ?></td>
							<td class="right"><?= $manufacturer['date_expires']; ?></td>
							<td class="right"><?= $manufacturer['status']?$text_enabled:$text_disabled; ?></td>
							<td class="right"><?= $manufacturer['sort_order']; ?></td>
							<td class="right"><? foreach ($manufacturer['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="4"><?= $text_no_results; ?></td>
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