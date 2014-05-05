<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="account-update-page" class="content">
	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h1><?= _l("My Account Information"); ?></h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<form action="<?= $save; ?>" class="form labels" method="post" autocomplete="off" enctype="multipart/form-data">
		<div class="row account-update-form">
			<div class="wrap">

				<div class="col xs-12 md-6 account-details">
					<section class="form-section">
						<h2><?= _l("Your Personal Details"); ?></h2>

						<div class="form-item">
							<label for="firstname"><?= _l("First Name"); ?></label>
							<input id="firstname" type="text" name="firstname" value="<?= $firstname; ?>"/>
						</div>
						<div class="form-item">
							<label for="lastname"><?= _l("Last Name"); ?></label>
							<input id="lastname" type="text" name="lastname" value="<?= $lastname; ?>"/>
						</div>
						<div class="form-item">
							<label for="email"><?= _l("Email"); ?></label>
							<input id="email" type="text" name="email" value="<?= $email; ?>"/>
						</div>
						<div class="form-item">
							<label for="birthdate"><?= _l("Birth Date"); ?></label>
							<input id="birthdate" type="text" class="datepicker" autocomplete="off" name="metadata[birthdate]" value="<?= !empty($metadata['birthdate']) ? $metadata['birthdate'] : ''; ?>"/>
						</div>
					</section>

					<section class="form-section">
						<h2><?= _l("Change Password"); ?></h2>

						<div class="form-item">
							<label for="password"><?= _l("Password"); ?></label>
							<input id="password" autocomplete="off" type="password" name="password"/>
							<span class="help"><?= _l("Leave blank to keep the same."); ?></span>
						</div>
						<div class="form-item">
							<label for="password-confirm"><?= _l("Confirm"); ?></label>
							<input id="password-confirm" autocomplete="off" type="password" name="confirm"/>
						</div>
					</section>

					<section class="form-section">
						<h2><?= _l("Newsletter"); ?></h2>

						<label for="newsletter"><?= _l("Join our mailing list?"); ?></label>
						<input id="newsletter" type="checkbox" class="ac-checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked="checked"' : ''; ?> />
					</section>
				</div>

				<div class="col xs-12 md-6 account-address">
					<section class="form-section shipping-address">
						<h2><?= _l("Default Shipping Address:"); ?></h2>
						<? if (empty($data_addresses)) { ?>
							<h3><?= _l("You do not have an address registered with us."); ?></h3>
							<a href="<?= $add_address; ?>" class="button" onclick="return colorbox($(this));"><?= _l("Register New Address"); ?></a>
						<? } else { ?>
							<div class="address-list-box">
								<div class="address-list noselect">
									<? foreach ($data_addresses as $address) { ?>
										<div class="address <?= $address['default_shipping'] ? 'checked' : ''; ?>">
											<input id="shipaddress<?= $address['address_id']; ?>" type="radio" name="metadata[default_shipping_address_id]" value="<?= $address['address_id']; ?>" <?= $address['default_shipping'] ? 'checked="checked"' : ''; ?> />
											<label for="shipaddress<?= $address['address_id']; ?>"><?= $address['display']; ?></label>
											<a href="<?= $address['remove']; ?>" class="remove"></a>
										</div>
									<? } ?>
									<a href="<?= $add_address; ?>" class="address add-slide noradio" onclick="return colorbox($(this));"><?= _l("Add New Address"); ?></a>
								</div>
							</div>
						<? } ?>
					</section>

					<section class="form-section credit-card">
						<h2><?= _l("Default Credit Card:"); ?></h2>

						<div class="credit-card-list">
							<?= _block('account/card/select'); ?>
						</div>
					</section>
				</div>

			</div>
		</div>
		<div class="button-row row">
			<div class="wrap">
				<div class="col xs-10 sm-7 center">
					<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
					<div class="right">
						<button class="button"><?= _l("Save"); ?></button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<?= _area('bottom'); ?>
</section>

<script type="text/javascript">
	var addresses = $('.address_list').ac_radio();

	if (addresses.children().length > 2) {
		addresses.ac_slidelist({pad_y: -15, x_dir: -1});
	}

	$('.address_list .remove').click(function () {
		var address = $(this);
		$.get(address.attr('href'), {}, function (json) {
			if (json['error']) {
				show_msgs(json['error'], 'error');
			} else {
				location.reload();
			}
		});
		return false;
	});

	$.ac_datepicker({changeYear: true, yearRange: "c-150:c", changeMonth: true});
</script>

<?= _call('common/footer'); ?>