<table class="list">
	<thead>
		<tr>
			<td><?= _l("First Name:"); ?></td>
			<td><?= _l("Last Name:"); ?></td>
			<td><?= _l("Company:"); ?></td>
			<td><?= _l("Email"); ?></td>
			<td><?= _l("Website"); ?></td>
			<td><?= _l("Phone"); ?></td>
			<td><?= _l("Address:"); ?></td>
			<td><?= _l("Contact Type: "); ?></td>
			<td></td>
		</tr>
	</thead>
	<? foreach ($contact_info as $row => $contact) { ?>
		<tbody id="contact-<?= $row; ?>">
			<tr>
				<td class="left"><input type="text" name="contact[<?= $row; ?>][first_name]" value="<?= $contact['first_name']; ?>"/></td>
				<td class="left"><input type="text" name="contact[<?= $row; ?>][last_name]" value="<?= $contact['last_name']; ?>"/></td>
				<td class="left"><input type="text" name="contact[<?= $row; ?>][company]" value="<?= $contact['company']; ?>"/>
				</td>
				<td class="left"><input type="text" name="contact[<?= $row; ?>][email]" value="<?= $contact['email']; ?>"/>
				</td>
				<td class="left"><input type="text" name="contact[<?= $row; ?>][website]" value="<?= $contact['website']; ?>"/>
				</td>
				<td class="left">
					<ul class="phone_list" row="<?= $row; ?>">
						<? if ($contact['phone']) {
							foreach ($contact['phone'] as $key => $phone) {
								?>
								<li>
									<input type="text" name="contact[<?= $row; ?>][phone][<?= $key; ?>][number]" value="<?= is_array($phone) ? $phone['number'] : $phone->number; ?>"/>
									<?= $this->builder->build('select', $phone_types, "contact[$row][phone][$key][type]", is_array($phone) ? $phone['type'] : $phone->type); ?>
									<a onclick="$(this).parent().remove()"><?= _l("Remove");_phone; ?></a>
								</li>
							<?
							}
						} ?>
					</ul>
					<a onclick="add_contact_phone(this);"><?= _l("Add Phone"); ?></a>
				</td>
				<td class="contact_address left">
					<div class="contact_street_1"><label for="street_1_<?= $row; ?>"><?= _l("Street Address: "); ?></label><input
							id="street_1_<?= $row; ?>" type="text" name="contact[<?= $row; ?>][street_1]" value="<?= $contact['street_1']; ?>"/></div>
					<div class="contact_street_2"><label for="street_2_<?= $row; ?>"><?= _l("Street Address 2: "); ?></label><input
							id="street_2_<?= $row; ?>" type="text" name="contact[<?= $row; ?>][street_2]" value="<?= $contact['street_2']; ?>"/></div>
					<div class="contact_city"><label for="city_<?= $row; ?>"><?= _l("City: "); ?></label><input
							id="city_<?= $row; ?>" type="text" name="contact[<?= $row; ?>][city]" value="<?= $contact['city']; ?>"/></div>
					<div class="contact_country">
						<label> <?= _l("Country: "); ?></label>
						<?= $this->builder->build('select', $countries, "contact[$row][country_id]", $contact['country_id']); ?>
					</div>
					<div class="contact_zone">
						<label>   <?= _l("Zone: "); ?></label>
						<select id="zone_id-<?= $row; ?>" data-zone_id="<?= $contact['zone_id']; ?>" name="contact[<?= $row; ?>][zone_id]"></select>
					</div>
					<div class="contact_postcode"><label for="postcode_<?= $row; ?>"><?= _l("Postal Code: "); ?></label><input
							id="postcode_<?= $row; ?>" type="text" maxlength="10" name="contact[<?= $row; ?>][postcode]" value="<?= $contact['postcode']; ?>"/></div>
				</td>
				<td
					class="left"><?= $this->builder->build('select', $contact_types, "contact[$row][contact_type]", $contact['contact_type']); ?></td>
				<td class="left"><a onclick="$('#contact-<?= $row; ?>').remove();" class="button"><?= _l("Remove"); ?></a>
				</td>
			</tr>
		</tbody>
	<? } ?>
	<tbody>
		<tr>
			<td class="left" colspan="4"></td>
			<td class="center"><a onclick="add_contact_entry(this);" class="button"><?= _l("Add Contact"); ?></a></td>
			<td class="left" colspan="3"></td>
		</tr>
	</tbody>
</table>


<?= $this->builder->js('load_zones', '.contact_address', '.contact_country select', '.contact_zone select'); ?>

<script type="text/javascript">

	function build_phone_item(row, phonerow) {
		html = '<li>';
		html += '	<input type="text" name="contact[%row%][phone][%phonerow%][number]" />';
		html += "	<?= $this->builder->build('select',$phone_types,"contact[%row%][phone][%phonerow%][type]"); ?>";
		html += '	<a onclick="$(this).parent().remove()"><?= _l("Remove");_phone; ?></a>';
		html += '</li>';
		return html.replace(/%row%/g, row).replace(/%phonerow%/g, phonerow);
	}

	function add_contact_phone(context) {
		list = $(context).siblings('.phone_list');
		list.append(build_phone_item(list.attr('row'), list.children().length));
	}

	var contact_row = <?= count($contact_info); ?>;
	function add_contact_entry(context) {
		html = '<tbody id="contact-%row%">';
		html += '	<tr>';
		html += '			<td class="left"><input type="text" name="contact[%row%][first_name]" /></td>';
		html += '			<td class="left"><input type="text" name="contact[%row%][last_name]" /></td>';
		html += '			<td class="left"><input type="text" name="contact[%row%][company]" /></td>';
		html += '			<td class="left"><input type="text" name="contact[%row%][email]" /></td>';
		html += '			<td class="left"><input type="text" name="contact[%row%][website]" /></td>';
		html += '			<td class="left"><ul class="phone_list" row="%row%">' + build_phone_item(contact_row, 0) + '</ul><a onclick="add_contact_phone(this);"><?= _l("Add Phone"); ?></a></td>';
		html += '			<td class="contact_address left">';
		html += '				<div class="contact_street_1"><label for="street_1_%row%"><?= _l("Street Address: "); ?></label><input id="street_1_%row%" type="text" name="contact[%row%][street_1]" /></div>';
		html += '				<div class="contact_street_2"><label for="street_2_%row%"><?= _l("Street Address 2: "); ?></label><input id="street_2_%row%" type="text" name="contact[%row%][street_2]" /></div>';
		html += '				<div class="contact_city"><label for="city_%row%"><?= _l("City: "); ?></label><input id="city_%row%" type="text" name="contact[%row%][city]" /></div>';
		html += '				<div class="contact_country">';
		html += '						<label> <?= _l("Country: "); ?></label>';
		html += "<?= $this->builder->build('select',$countries, "contact[%row%][country_id]", $default_country, array(),true); ?>";
		html += '				</div>';
		html += '				<div class="contact_zone">';
		html += '						<label>	<?= _l("Zone: "); ?></label>';
		html += '						<select id="zone_id-%row%" name="contact[%row%][zone_id]"></select>';
		html += '				</div>';
		html += '				<div class="contact_postcode"><label for="postcode_%row%"><?= _l("Postal Code: "); ?></label><input id="postcode_%row%" type="text" maxlength="10" name="contact[%row%][postcode]" /></div>';
		html += '			</td>';
		html += '			<td class="left">' + "<?= $this->builder->build('select',$contact_types,"contact[%row%][contact_type]"); ?>" + '</td>';
		html += '			<td class="left"><a onclick="$(\'#contact-%row%\').remove();" class="button"><?= _l("Remove"); ?></a></td>';
		html += '	</tr>';
		html += '</tbody>';
		$(context).closest('tbody').before(html.replace(/%row%/g, contact_row));
		$('#contact-' + contact_row + ' .contact_country select').trigger('change');
		contact_row++;
	}
</script>
