<div class="block-address-select form">
	<div class="addresses">
		<? $this->builder->setConfig('address_id', 'format'); ?>
		<?= $this->builder->build('ac-radio', $addresses, 'address_id', $address_id); ?>
	</div>
</div>
