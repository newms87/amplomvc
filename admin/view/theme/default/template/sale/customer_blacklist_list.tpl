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
			<h1><img src="<?= theme_url('image/customer.png'); ?>" alt=""/> <?= _l("Customer IP Blacklist"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'ip') { ?>
									<a href="<?= $sort_ip; ?>" class="<?= strtolower($order); ?>"><?= _l("IP"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_ip; ?>"><?= _l("IP"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Customers"); ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($customer_blacklists) { ?>
							<? foreach ($customer_blacklists as $customer_blacklist) { ?>
								<tr>
									<td style="text-align: center;"><? if ($customer_blacklist['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $customer_blacklist['customer_ip_blacklist_id']; ?>" checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $customer_blacklist['customer_ip_blacklist_id']; ?>"/>
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
								<td class="center" colspan="10"><?= _l("There are no results to display."); ?></td>
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
