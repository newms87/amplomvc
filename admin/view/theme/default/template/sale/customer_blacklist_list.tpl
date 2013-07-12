<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
	<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><? if ($sort == 'ip') { ?>
								<a href="<?= $sort_ip; ?>" class="<?= strtolower($order); ?>"><?= $column_ip; ?></a>
								<? } else { ?>
								<a href="<?= $sort_ip; ?>"><?= $column_ip; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_customer; ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($customer_blacklists) { ?>
						<? foreach ($customer_blacklists as $customer_blacklist) { ?>
						<tr>
							<td style="text-align: center;"><? if ($customer_blacklist['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $customer_blacklist['customer_ip_blacklist_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $customer_blacklist['customer_ip_blacklist_id']; ?>" />
								<? } ?></td>
							<td class="left"><?= $customer_blacklist['ip']; ?></td>
							<td class="right"><? if ($customer_blacklist['total']) { ?>
								<a href="<?= $customer_blacklist['customer']; ?>"><?= $customer_blacklist['total']; ?></a>
								<? } else { ?>
								<?= $customer_blacklist['total']; ?>
								<? } ?></td>
							<td class="right"><? foreach ($customer_blacklist['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="10"><?= $text_no_results; ?></td>
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