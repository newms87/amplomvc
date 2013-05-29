<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'total.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="low_order_fee_total" value="<?= $low_order_fee_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_fee; ?></td>
						<td><input type="text" name="low_order_fee_fee" value="<?= $low_order_fee_fee; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_tax_class; ?></td>
						<td><select name="low_order_fee_tax_class_id">
									<option value="0"><?= $text_none; ?></option>
									<? foreach ($tax_classes as $tax_class) { ?>
									<? if ($tax_class['tax_class_id'] == $low_order_fee_tax_class_id) { ?>
									<option value="<?= $tax_class['tax_class_id']; ?>" selected="selected"><?= $tax_class['title']; ?></option>
									<? } else { ?>
									<option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
									<? } ?>
									<? } ?>
								</select></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="low_order_fee_status">
								<? if ($low_order_fee_status) { ?>
								<option value="1" selected="selected"><?= $text_enabled; ?></option>
								<option value="0"><?= $text_disabled; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_enabled; ?></option>
								<option value="0" selected="selected"><?= $text_disabled; ?></option>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="low_order_fee_sort_order" value="<?= $low_order_fee_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 