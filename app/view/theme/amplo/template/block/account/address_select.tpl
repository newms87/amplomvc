<div class="block-address-select form">
	<div class="address-list ">
		<?= build(array(
			'type'   => 'radio',
			'name'   => 'address_id',
			'data'   => format_all('address', $addresses),
			'select' => $address_id,
			'value'  => 'address_id',
			'label'  => 'formatted',
		)); ?>

		<? if ($add_address) { ?>
			<a class="add-address" href="<?= site_url("account/address/form"); ?>">{{Add Address}}</a>
		<? } ?>
	</div>
</div>
