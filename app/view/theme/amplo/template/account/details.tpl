<div class="account-info">
	<h1>{{My Details}}</h1>

	<div class="forms">
		<form action="<?= site_url('account/save'); ?>" method="post" class="section form-editor read col xs-12 left">
			<div class="heading">
				<h3 class="col xs-7 left top">{{Name &amp; Email}}</h3>

				<div class="buttons col xs-5 right top">
					<a class="edit-form reading">{{Edit}}</a>
					<a class="editing cancel-form">{{Cancel}}</a>
				</div>
			</div>

			<div class="display col xs-12 left reading">
				<div class="field name">
					<span class="field first_name"><?= $customer['first_name']; ?></span>
					<span class="field last_name"><?= $customer['last_name']; ?></span>
				</div>

				<div class="field email"><?= $customer['email']; ?></div>
				<div class="field phone"><?= $customer['phone']; ?></div>
			</div>

			<div class="input editing col xs-12 left">
				<div class="input-item">
					<input type="text" name="first_name" value="<?= $customer['first_name']; ?>" placeholder="{{First Name}}"/>
				</div>
				<div class="input-item">
					<input type="text" name="last_name" value="<?= $customer['last_name']; ?>" placeholder="{{Last Name}}"/>
				</div>
				<div class="input-item">
					<input type="text" name="email" value="<?= $customer['email']; ?>" placeholder="{{Email}}"/>
				</div>
				<div class="input-item">
					<input type="text" name="phone" value="<?= $customer['phone']; ?>" placeholder="{{Phone #}}"/>
				</div>
			</div>

			<div class="buttons submit editing col no-whitespace-hack xs-12 right">
				<div class="col xs-12 md-4">
					<button data-loading="{{Saving...}}">{{Save}}</button>
				</div>
			</div>
		</form>

		<div class="section col xs-12 left">
			<div class="form-editor read new-address-box">
				<div class="heading">
					<h3 class="col xs-7 left top">{{Addresses}}</h3>

					<div class="buttons col xs-5 right top">
						<a class="edit-form reading">{{Add Address}}</a>
						<a class="editing cancel-form">{{Cancel}}</a>
					</div>
				</div>

				<form action="<?= site_url('account/address/save'); ?>" data-reload="true" class="input editing col xs-12 left">
					<div class="input-item">
						<input type="text" name="name" value="<?= $customer['first_name'] . ' ' . $customer['last_name']; ?>" placeholder="{{Company}}"/>
					</div>
					<div class="input-item">
						<input type="text" name="address" value="<?= _post('address'); ?>" placeholder="{{Street Address}}"/>
					</div>
					<div class="input-item">
						<input type="text" name="address_2" value="<?= _post('address_2'); ?>" placeholder="{{Apt # / P.O Box}}"/>
					</div>
					<div class="input-item">
						<input type="text" name="city" value="<?= _post('city'); ?>" placeholder="{{City}}"/>
					</div>
					<div class="input-item">
						<?= build(array(
							'type'   => 'select',
							'name'   => 'zone_id',
							'data'   => array('' => _l("(Select State)")) + $data_zones,
							'select' => _post('zone_id'),
							'label'  => 'name',
							'value'  => 'zone_id',
						)); ?>
					</div>
					<div class="input-item">
						<input type="text" name="postcode" value="<?= _post('postcode'); ?>" placeholder="{{Zip Code}}"/>
					</div>

					<div class="submit buttons">
						<button data-loading="{{Saving...}}">{{Add New Address}}</button>
					</div>
				</form>
			</div>

			<div class="display address-list">
				<? foreach ($addresses as $address) { ?>
					<div class="address form-editor read" data-address-id="<?= $address['address_id']; ?>">
						<div class="formatted col xs-8 left top">
							<?= format('address', $address); ?>
						</div>
						<div class="buttons col xs-4 right top">
							<a href="<?= site_url('account/address/form', 'address_id=' . $address['address_id']); ?>" class="edit-form edit-address reading">{{Edit}}</a>
							<a href="<?= site_url('account/address/remove', 'address_id=' . $address['address_id']); ?>" class="remove-address reading">{{Remove}}</a>
							<a class="cancel-form cancel-address editing">{{Cancel}}</a>
						</div>
					</div>
				<? } ?>
			</div>
		</div>

		<form action="<?= site_url('account/save'); ?>" method="post" class="section form-editor read col xs-12 left">
			<div class="heading">
				<h3 class="col xs-7 left top">{{Password}}</h3>

				<div class="buttons col xs-5 right top">
					<a class="edit-form reading">{{Edit}}</a>
					<a class="editing cancel-form">{{Cancel}}</a>
				</div>
			</div>

			<div class="field password-field reading">XXXXXXXX</div>

			<div class="input editing col xs-12 left">
				<div class="input-item">
					<input type="password" name="password" placeholder="{{Password}}"/>
				</div>
				<div class="input-item">
					<input type="password" name="confirm" placeholder="{{Confirm Password}}"/>
				</div>
			</div>

			<div class="buttons submit editing col no-whitespace-hack xs-12 right">
				<div class="col xs-12 md-4">
					<button data-loading="{{Saving...}}">{{Save}}</button>
				</div>
			</div>
		</form>

	</div>
</div>

<script type="text/javascript">
	var addresses = <?= json_encode($addresses); ?>;

	$('.edit-address').click(function () {
		var $editor = $(this).closest('.form-editor');
		var address_id = $editor.attr('data-address-id');

		if (!$editor.find('form').length) {
			var $new_form = $('.new-address-box form').clone(true);
			$new_form.attr('action', $new_form.attr('action') + '?address_id=' + address_id);
			$editor.append($new_form);
		}
	});
</script>
