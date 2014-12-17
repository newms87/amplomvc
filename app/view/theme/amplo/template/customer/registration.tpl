<?= call('header', array('disable_messages' => 1)); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="user-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= $is_ajax ? '' : breadcrumbs(); ?>
			<h1>{{Register Account}}</h1>

			<h3>
				{{If you already have an account with us, please login at the}}
				<a href="<?= $login; ?>">{{login page}}</a>
			</h3>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="login-page row">
		<div class="wrap">
			<?= render_message(); ?>

			<form class="login-form form" action="<?= $register; ?>" method="post">
				<div class="col xs-12 md-6 register-details">
					<div class="form-section">
						<h2>{{Your Personal Details}}</h2>

						<div class="form-item required">
							<input type="text" placeholder="{{First Name}}" name="firstname" value="<?= $firstname; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="{{Last Name}}" name="lastname" value="<?= $lastname; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="{{Email}}" autocomplete="off" name="email" value="<?= $email; ?>"/>
						</div>
					</div>

					<div class="form-section">
						<h2>{{Create Password}}</h2>

						<div class="form-item required">
							<input type="password" autocomplete="off" placeholder="{{Password}}" name="password"/>
						</div>
						<div class="form-item required">
							<input type="password" autocomplete="off" placeholder="{{Confirm Password}}" name="confirm"/>
						</div>
					</div>

					<div class="form-section">
						<h2>{{Newsletter}}</h2>

						<div class="form-item">
							<label>{{Subscribe}}</label>
							<?= build(array(
								'type' => 'radio',
								'name'  => 'newsletter',
								'data'   => $data_yes_no,
								'select' => $newsletter
							)); ?>
						</div>
					</div>
				</div>

				<div class="col xs-12 md-6 top register-address">
					<div class="form-section">
						<h2>{{Your Address}} </h2>

						<div class="form-item required">
							<input type="text" placeholder="{{Address}}" name="address_1" value="<?= $address_1; ?>"/>
						</div>
						<div class="form-item">
							<input type="text" placeholder="{{Address Line 2}}" name="address_2" value="<?= $address_2; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="{{City}}" name="city" value="<?= $city; ?>"/>
						</div>
						<div class="form-item required">
							<?=
							build(array(
								'type' => 'select',
								'name'  => 'country_id',
								'data'   => $data_countries,
								'select' => $country_id,
								'key'    => 'country_id',
								'value'  => 'name',
							)); ?>
						</div>
						<div class="form-item required">
							<select name="zone_id" class="zone-select" data-zone_id="<?= $zone_id; ?>"></select>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="{{Postal Code}}" name="postcode" value="<?= $postcode; ?>"/>
						</div>
					</div>
				</div>

				<div class="col xs-12">
					<? if (!empty($agree_to)) { ?>
						<div class="form-item">
							<input id="agree-terms" type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : ''; ?> />
							<label for="agree_terms">{{I have read and agree to}} <a href="<?= $agree_to; ?>"><?= $agree_title; ?></a></label>
						</div>
					<? } ?>

					<div class="form-item submit">
						<input type="submit" value="{{Continue}}" class="button"/>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<script type="text/javascript">
	$('#user-login .zone-select').ac_zoneselect({listen: '#user-login [name=country_id]', select: '<?= $zone_id; ?>'});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('footer'); ?>
