<div class="block-address-select form">
	<div class="address-list ">
		<? $build = array(
			'name' => 'address_id',
		   'data' => format_all('address', $addresses),
		   'select' => $address_id,
		   'key' => 'address_id',
		   'value' => 'formatted',
		); ?>

		<?= build('ac-radio', $build); ?>

		<? if ($add_address) { ?>
			<a class="add-address" href="<?= site_url("account/address/form"); ?>"><?= _l("Add Address"); ?></a>
		<? } ?>
	</div>
</div>
