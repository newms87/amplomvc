<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">

			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Customer"); ?></h1>

				<div class="buttons">
					<button class="button"><?= _l("Save"); ?></button>
					<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
				</div>
			</div>

			<div class="section">
				<div id="htabs" class="htabs">
					<a href="#tab-general"><?= _l("General"); ?></a>
					<? if ($customer_id) { ?>
						<a href="#tab-transaction"><?= _l("Transactions"); ?></a>
						<a href="#tab-reward"><?= _l("Reward Points"); ?></a>
					<? } ?>
					<a href="#tab-ip"><?= _l("IP Addresses"); ?></a>
				</div>

				<div id="tab-general">
					<div id="vtabs" class="vtabs"><a href="#tab-customer"><?= _l("General"); ?></a>
						<? $address_row = 1; ?>
						<? foreach ($addresses as $address) { ?>
							<a href="#tab-address-<?= $address_row; ?>"
							   id="address-<?= $address_row; ?>"><?= _l("Address") . ' ' . $address_row; ?>&nbsp;<img
									src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$('#vtabs a:first').trigger('click'); $('#address-<?= $address_row; ?>').remove(); $('#tab-address-<?= $address_row; ?>').remove(); return false;"/></a>
							<? $address_row++; ?>
						<? } ?>
						<span id="address-add"><?= _l("Add Address"); ?>
							&nbsp;<img src="<?= URL_THEME_IMAGE . 'add.png'; ?>" alt="" onclick="addAddress();"/></span></div>
					<div id="tab-customer" class="vtabs-content">
						<table class="form">
							<tr>
								<td class="required"> <?= _l("First Name:"); ?></td>
								<td><input type="text" name="firstname" value="<?= $firstname; ?>"/>
									<? if (_l("First Name must be between 1 and 32 characters!")) { ?>
										<span class="error"><?= _l("First Name must be between 1 and 32 characters!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td class="required"> <?= _l("Last Name:"); ?></td>
								<td><input type="text" name="lastname" value="<?= $lastname; ?>"/>
									<? if (_l("Last Name must be between 1 and 32 characters!")) { ?>
										<span class="error"><?= _l("Last Name must be between 1 and 32 characters!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td class="required"> <?= _l("E-Mail:"); ?></td>
								<td><input type="text" name="email" value="<?= $email; ?>"/>
									<? if (_l("E-Mail Address does not appear to be valid!")) { ?>
										<span class="error"><?= _l("E-Mail Address does not appear to be valid!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td class="required"> <?= _l("Telephone:"); ?></td>
								<td><input type="text" name="telephone" value="<?= $telephone; ?>"/>
									<? if (_l("Telephone must be between 3 and 32 characters!")) { ?>
										<span class="error"><?= _l("Telephone must be between 3 and 32 characters!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td><?= _l("Fax:"); ?></td>
								<td><input type="text" name="fax" value="<?= $fax; ?>"/></td>
							</tr>
							<tr>
								<td><?= _l("Password:"); ?></td>
								<td><input type="password" autocomplete="off" name="password" value="<?= $password; ?>"/>
									<br/>
									<? if (_l("Password must be between 4 and 20 characters!")) { ?>
										<span class="error"><?= _l("Password must be between 4 and 20 characters!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td><?= _l("Confirm:"); ?></td>
								<td><input type="password" autocomplete="off" name="confirm" value="<?= $confirm; ?>"/>
									<? if (_l("Password and password confirmation do not match!")) { ?>
										<span class="error"><?= _l("Password and password confirmation do not match!"); ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td><?= _l("Newsletter:"); ?></td>
								<td><select name="newsletter">
										<? if ($newsletter) { ?>
											<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
											<option value="0"><?= _l("Disabled"); ?></option>
										<? } else { ?>
											<option value="1"><?= _l("Enabled"); ?></option>
											<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
										<? } ?>
									</select></td>
							</tr>
							<tr>
								<td><?= _l("Customer Group:"); ?></td>
								<td><select name="customer_group_id">
										<? foreach ($customer_groups as $customer_group) { ?>
											<? if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
												<option value="<?= $customer_group['customer_group_id']; ?>"
												        selected="selected"><?= $customer_group['name']; ?></option>
											<? } else { ?>
												<option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
											<? } ?>
										<? } ?>
									</select></td>
							</tr>
							<tr>
								<td><?= _l("Status:"); ?></td>
								<td><select name="status">
										<? if ($status) { ?>
											<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
											<option value="0"><?= _l("Disabled"); ?></option>
										<? } else { ?>
											<option value="1"><?= _l("Enabled"); ?></option>
											<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
										<? } ?>
									</select></td>
							</tr>
						</table>
					</div>
					<? $address_row = 1; ?>
					<? foreach ($addresses as $address) { ?>
						<div id="tab-address-<?= $address_row; ?>" class="vtabs-content">
							<input type="hidden" name="address[<?= $address_row; ?>][address_id]" value="<?= $address['address_id']; ?>"/>
							<table class="form">
								<tr>
									<td class="required"> <?= _l("First Name:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][firstname]" value="<?= $address['firstname']; ?>"/>
										<? if (isset($error_address_firstname[$address_row])) { ?>
											<span class="error"><?= $error_address_firstname[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td class="required"> <?= _l("Last Name:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][lastname]" value="<?= $address['lastname']; ?>"/>
										<? if (isset($error_address_lastname[$address_row])) { ?>
											<span class="error"><?= $error_address_lastname[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td><?= _l("Company:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][company]" value="<?= $address['company']; ?>"/>
									</td>
								</tr>
								<tr>
									<td class="required"> <?= _l("Address 1:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][address_1]" value="<?= $address['address_1']; ?>"/>
										<? if (isset($error_address_address_1[$address_row])) { ?>
											<span class="error"><?= $error_address_address_1[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td><?= _l("Address 2:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][address_2]" value="<?= $address['address_2']; ?>"/>
									</td>
								</tr>
								<tr>
									<td class="required"> <?= _l("City:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][city]" value="<?= $address['city']; ?>"/>
										<? if (isset($error_address_city[$address_row])) { ?>
											<span class="error"><?= $error_address_city[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td class="required"> <?= _l("Postcode:"); ?></td>
									<td>
										<input type="text" name="address[<?= $address_row; ?>][postcode]" value="<?= $address['postcode']; ?>"/>
									</td>
								</tr>
								<tr>
									<td class="required"> <?= _l("Country:"); ?></td>
									<td>
										<?= $this->builder->setConfig('country_id', 'name'); ?>
										<?= $this->builder->build('select', $countries, "address[$address_row][country_id]", $address['country_id'], array('class' => 'country_select')); ?>
										<? if (isset($error_address_country[$address_row])) { ?>
											<span class="error"><?= $error_address_country[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td class="required"> <?= _l("Region / State:"); ?></td>
									<td>
										<select name="address[<?= $address_row; ?>][zone_id]" data-zone_id="<?= $address['zone_id']; ?>"
										        class="zone_select"></select>
										<? if (isset($error_address_zone[$address_row])) { ?>
											<span class="error"><?= $error_address_zone[$address_row]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td><?= _l("Default Address:"); ?></td>
									<td><? if (($address['address_id'] == $address_id) || !$addresses) { ?>
										<input type="radio" name="address[<?= $address_row; ?>][default]" value="<?= $address_row; ?>"
										       checked="checked"/></td>
									<? } else { ?>
										<input type="radio" name="address[<?= $address_row; ?>][default]" value="<?= $address_row; ?>"/>
										</td>
									<? } ?>
								</tr>
							</table>
						</div>
						<? $address_row++; ?>
					<? } ?>
				</div>
				<? if ($customer_id) { ?>
					<div id="tab-transaction">
						<table class="form">
							<tr>
								<td><?= _l("Description:"); ?></td>
								<td><input type="text" name="description" value=""/></td>
							</tr>
							<tr>
								<td><?= _l("Amount:"); ?></td>
								<td><input type="text" name="amount" value=""/></td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: right;">
									<a id="button-reward" class="button" onclick="addTransaction();"><span><?= _l("Add Transaction"); ?></span></a>
								</td>
							</tr>
						</table>
						<div id="transaction"></div>
					</div>
					<div id="tab-reward">
						<table class="form">
							<tr>
								<td><?= _l("Description:"); ?></td>
								<td><input type="text" name="description" value=""/></td>
							</tr>
							<tr>
								<td><?= _l("Points:<br /><span class=\"help\">Use minus to remove points</span>"); ?></td>
								<td><input type="text" name="points" value=""/></td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: right;">
									<a id="button-reward" class="button" onclick="addRewardPoints();"><span><?= _l("Add Reward Points"); ?></span></a>
								</td>
							</tr>
						</table>
						<div id="reward"></div>
					</div>
				<? } ?>
				<div id="tab-ip">
					<table class="list">
						<thead>
						<tr>
							<td class="left"><?= _l("IP"); ?></td>
							<td class="right"><?= _l("Total Accounts"); ?></td>
							<td class="left"><?= _l("Date Added"); ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
						</thead>
						<tbody>
						<? if ($ips) { ?>
							<? foreach ($ips as $ip) { ?>
								<tr>
									<td class="left">
										<a onclick="window.open('http://www.geoiptool.com/en/?IP=<?= $ip['ip']; ?>');"><?= $ip['ip']; ?></a>
									</td>
									<td class="right">
										<a onclick="window.open('<?= $ip['filter_ip']; ?>');"><?= $ip['total']; ?></a></td>
									<td class="left"><?= $ip['date_added']; ?></td>
									<td class="right"><? if ($ip['blacklist']) { ?>
											<b>[</b>
											<a id="<?= str_replace('.', '-', $ip['ip']); ?>" onclick="removeBlacklist('<?= $ip['ip']; ?>');"><?= _l("Remove Blacklist"); ?></a>
											<b>]</b>
										<? } else { ?>
											<b>[</b>
											<a id="<?= str_replace('.', '-', $ip['ip']); ?>" onclick="addBlacklist('<?= $ip['ip']; ?>');"><?= _l("Add Blacklist"); ?></a>
											<b>]</b>
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="3"><?= _l("No results!"); ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript"><
	!--
	var address_row = <?= $address_row; ?>;

	function addAddress() {
		html = '<div id="tab-address-' + address_row + '" class="vtabs-content" style="display: none;">';
		html += '	<input type="hidden" name="address[' + address_row + '][address_id]" value="" />';
		html += '	<table class="form">';
		html += '		<tr>';
		html += '		<td><?= _l("First Name:"); ?></td>';
		html += '		<td><input type="text" name="address[' + address_row + '][firstname]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Last Name:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][lastname]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Company:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][company]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Address 1:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][address_1]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Address 2:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][address_2]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("City:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][city]" value="" /></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Postcode:"); ?></td>';
		html += '			<td><input type="text" name="address[' + address_row + '][postcode]" value="" /></td>';
		html += '		</tr>';
		html += '			<td><?= _l("Country:"); ?></td>';
		html += '			<td><select name="address[' + address_row + '][country_id]" class="country_select">';
		html += '				<option value=""><?= _l(" --- Please Select --- "); ?></option>';
		<? foreach ($countries as $country) { ?>
		html += '				<option value="<?= $country['country_id']; ?>"><?= addslashes($country['name']); ?></option>';
		<? } ?>
		html += '			</select></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Region / State:"); ?></td>';
		html += '			<td><select name="address[' + address_row + '][zone_id]" class="zone_select"><option value="false"><?= _l(" --- None --- "); ?></option></select></td>';
		html += '		</tr>';
		html += '		<tr>';
		html += '			<td><?= _l("Default Address:"); ?></td>';
		html += '			<td><input type="radio" name="address[' + address_row + '][default]" value="1" /></td>';
		html += '		</tr>';
		html += '	</table>';
		html += '</div>';

		$('#tab-general').append(html);

		$('#address-add').before('<a href="#tab-address-' + address_row + '" id="address-' + address_row + '"><?= _l("Address"); ?> ' + address_row + '&nbsp;<img src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$(\'#vtabs a:first\').trigger(\'click\'); $(\'#address-' + address_row + '\').remove(); $(\'#tab-address-' + address_row + '\').remove(); return false;" /></a>');

		$('.vtabs a').tabs();

		$('#address-' + address_row).trigger('click');

		address_row++;
	}
</script>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<script type="text/javascript">
	$('#transaction .pagination a').live('click', function () {
		$('#transaction').load(this.href);

		return false;
	});

	$('#transaction').load("<?= $url_transaction; ?>");

	function addTransaction() {
		$.ajax({
			url:      "<?= $url_transaction; ?>",
			type:     'post',
			dataType: 'html',
			data:     'description=" + encodeURIComponent($("#tab-transaction input[name=\'description\']').val()
	)
		+'&amount=" + encodeURIComponent($("#tab-transaction input[name=\'amount\']'
	).
		val()
	),
		beforeSend: function () {
			$('.success, .warning').remove();
			$('#button-transaction').attr('disabled', true);
			$('#transaction').before('<div class="attention"><img src="<?= URL_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= _l("Please Wait!"); ?></div>');
		}
	,
		complete: function () {
			$('#button-transaction').attr('disabled', false);
			$('.attention').remove();
		}
	,
		success: function (html) {
			$('#transaction').html(html);

			$('#tab-transaction input[name=\'amount\']').val('');
			$('#tab-transaction input[name=\'description\']').val('');
		}
	}
	)
	;
	}
</script>
<script type="text/javascript">
	$('#reward .pagination a').live('click', function () {
		$('#reward').load(this.href);

		return false;
	});

	$('#reward').load("<?= $url_reward; ?>");

	function addRewardPoints() {
		$.ajax({
			url:      "<?= $url_reward; ?>",
			type:     'post',
			dataType: 'html',
			data:     'description=" + encodeURIComponent($("#tab-reward input[name=\'description\']').val()
	)
		+'&points=" + encodeURIComponent($("#tab-reward input[name=\'points\']'
	).
		val()
	),
		beforeSend: function () {
			$('.success, .warning').remove();
			$('#button-reward').attr('disabled', true);
			$('#reward').before('<div class="attention"><img src="<?= URL_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= _l("Please Wait!"); ?></div>');
		}
	,
		complete: function () {
			$('#button-reward').attr('disabled', false);
			$('.attention').remove();
		}
	,
		success: function (html) {
			$('#reward').html(html);

			$('#tab-reward input[name=\'points\']').val('');
			$('#tab-reward input[name=\'description\']').val('');
		}
	}
	)
	;
	}

	function addBlacklist(ip) {
		$.ajax({
			url:        "<?= $url_blacklist; ?>",
			type:       'post',
			dataType:   'json',
			data: 'ip=" + encodeURIComponent(ip),
			beforeSend: function () {
				$(".success, .warning').remove();

				$('.box').before('<div class="attention"><img src="<?= URL_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> Please wait!</div>');
			},
			complete:   function () {
				$('.attention').remove();
			},
			success:    function (json) {
				if (json['error']) {
					$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
					$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');

					$('.success').fadeIn('slow');

					$('#' + ip.replace(/\./g, '-')).replaceWith('<a id="' + ip.replace(/\./g, '-') + '" onclick="removeBlacklist(\'' + ip + '\');"><?= _l("Remove Blacklist"); ?></a>');
				}
			}
		});
	}

	function removeBlacklist(ip) {
		$.ajax({
			url:        "<?= $url_remove_blacklist; ?>",
			type:       'post',
			dataType:   'json',
			data: 'ip=" + encodeURIComponent(ip),
			beforeSend: function () {
				$(".success, .warning').remove();

				$('.box').before('<div class="attention"><img src="<?= URL_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> Please wait!</div>');
			},
			complete:   function () {
				$('.attention').remove();
			},
			success:    function (json) {
				if (json['error']) {
					$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');

					$('.warning').fadeIn('slow');
				}

				if (json['success']) {
					$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');

					$('.success').fadeIn('slow');

					$('#' + ip.replace(/\./g, '-')).replaceWith('<a id="' + ip.replace(/\./g, '-') + '" onclick="addBlacklist(\'' + ip + '\');"><?= _l("Add Blacklist"); ?></a>');
				}
			}
		});
	}
	;
</script>
<script type="text/javascript">
	$('.htabs a').tabs();
	$('.vtabs a').tabs();
</script>
<?= $common_footer; ?>
