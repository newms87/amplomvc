<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if (_l("Warning: Please check the form carefully for errors!")) { ?>
		<div class="message_box warning"><?= _l("Warning: Please check the form carefully for errors!"); ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Customer"); ?></h1>

			<div class="buttons"><a onclick="$('form').attr('action', '<?= $approve; ?>'); $('form').submit();"
					class="button"><?= _l("Approve"); ?></a><a onclick="location = '<?= $insert; ?>'"
					class="button"><?= _l("Insert"); ?></a><a onclick="$('form').attr('action', '<?= $delete; ?>'); $('form').submit();"
					class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Customer Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Customer Name"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.email') { ?>
									<a href="<?= $sort_email; ?>" class="<?= strtolower($order); ?>"><?= _l("E-Mail"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_email; ?>"><?= _l("E-Mail"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'customer_group') { ?>
									<a href="<?= $sort_customer_group; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Customer Group"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_customer_group; ?>"><?= _l("Customer Group"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.status') { ?>
									<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= _l("Status"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_status; ?>"><?= _l("Status"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.approved') { ?>
									<a href="<?= $sort_approved; ?>" class="<?= strtolower($order); ?>"><?= _l("Approved"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_approved; ?>"><?= _l("Approved"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.ip') { ?>
									<a href="<?= $sort_ip; ?>" class="<?= strtolower($order); ?>"><?= _l("IP"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_ip; ?>"><?= _l("IP"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'c.date_added') { ?>
									<a href="<?= $sort_date_added; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Date Added"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_added; ?>"><?= _l("Date Added"); ?></a>
								<? } ?></td>
							<td class="left"><?= _l("Login into Store"); ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<tr class="filter">
							<td></td>
							<td><input type="text" name="filter_name" value="<?= $filter_name; ?>"/></td>
							<td><input type="text" name="filter_email" value="<?= $filter_email; ?>"/></td>
							<td><select name="filter_customer_group_id">
									<option value="*"></option>
									<? foreach ($customer_groups as $customer_group) { ?>
										<? if ($customer_group['customer_group_id'] == $filter_customer_group_id) { ?>
											<option value="<?= $customer_group['customer_group_id']; ?>"
												selected="selected"><?= $customer_group['name']; ?></option>
										<? } else { ?>
											<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
							<td><select name="filter_status">
									<option value="*"></option>
									<? if ($filter_status) { ?>
										<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
									<? } else { ?>
										<option value="1"><?= _l("Enabled"); ?></option>
									<? } ?>
									<? if (!is_null($filter_status) && !$filter_status) { ?>
										<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
									<? } else { ?>
										<option value="0"><?= _l("Disabled"); ?></option>
									<? } ?>
								</select></td>
							<td><select name="filter_approved">
									<option value="*"></option>
									<? if ($filter_approved) { ?>
										<option value="1" selected="selected"><?= _l("Yes"); ?></option>
									<? } else { ?>
										<option value="1"><?= _l("Yes"); ?></option>
									<? } ?>
									<? if (!is_null($filter_approved) && !$filter_approved) { ?>
										<option value="0" selected="selected"><?= _l("No"); ?></option>
									<? } else { ?>
										<option value="0"><?= _l("No"); ?></option>
									<? } ?>
								</select></td>
							<td><input type="text" name="filter_ip" value="<?= $filter_ip; ?>"/></td>
							<td><input type="text" name="filter_date_added" value="<?= $filter_date_added; ?>" size="12"
									id="date"/></td>
							<td></td>
							<td align="right"><a onclick="filter();" class="button"><?= _l("Filter"); ?></a></td>
						</tr>
						<? if ($customers) { ?>
							<? foreach ($customers as $customer) { ?>
								<tr>
									<td style="text-align: center;"><? if ($customer['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $customer['customer_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $customer['customer_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $customer['name']; ?></td>
									<td class="left"><?= $customer['email']; ?></td>
									<td class="left"><?= $customer['customer_group']; ?></td>
									<td class="left"><?= $customer['status']; ?></td>
									<td class="left"><?= $customer['approved']; ?></td>
									<td class="left"><?= $customer['ip']; ?></td>
									<td class="left"><?= $customer['date_added']; ?></td>
									<td class="left">
										<? $this->builder->setConfig('store_id', 'name'); ?>
										<?= $this->builder->build('select', $data_stores, "store_login", '', array('onchange' => "if(this.value !== '')window.open(" <?= HTTP_ADMIN . "index.php?route=sale/customer/login"; ?>
										" + '&customer_id=$customer[customer_id]&store_id=' + this.value);"));?>
									</td>
									<td class="right"><? foreach ($customer['action'] as $action) { ?>
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
<script type="text/javascript"><
	!--
		function filter() {
			url = "<?= HTTP_ADMIN . "index.php?route=sale/customer"; ?>";

			var filter_name = $('input[name=\'filter_name\']').attr('value');

			if (filter_name) {
				url += '&filter_name=" + encodeURIComponent(filter_name);
			}

			var filter_email = $("input[name=\'filter_email\']').attr('value');

			if (filter_email) {
				url += '&filter_email=" + encodeURIComponent(filter_email);
			}

			var filter_customer_group_id = $("select[name=\'filter_customer_group_id\']').attr('value');

			if (filter_customer_group_id != '*') {
				url += '&filter_customer_group_id=" + encodeURIComponent(filter_customer_group_id);
			}

			var filter_status = $("select[name=\'filter_status\']').attr('value');

			if (filter_status != '*') {
				url += '&filter_status=" + encodeURIComponent(filter_status);
			}

			var filter_approved = $("select[name=\'filter_approved\']').attr('value');

			if (filter_approved != '*') {
				url += '&filter_approved=" + encodeURIComponent(filter_approved);
			}

			var filter_ip = $("input[name=\'filter_ip\']').attr('value');

			if (filter_ip) {
				url += '&filter_ip=" + encodeURIComponent(filter_ip);
			}

			var filter_date_added = $("input[name=\'filter_date_added\']').attr('value');

			if (filter_date_added) {
				url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
			}

			location = url;
		}
</script>
<script type="text/javascript"><
	!--
		$(document).ready(function () {
			$('#date').datepicker({dateFormat: 'yy-mm-dd'});
		});
</script>
<?= $footer; ?>
