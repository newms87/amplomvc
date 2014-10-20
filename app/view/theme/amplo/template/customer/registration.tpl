<?= call('common/header', array('disable_messages' => 1)); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="user-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= breadcrumbs(); ?>
			<h1><?= _l("Register Account"); ?></h1>

			<h3>
				<?= _l("If you already have an account with us, please login at the"); ?>
				<a href="<?= $login; ?>"><?= _l("login page"); ?></a>
			</h3>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="login-page row">
		<div class="wrap">
			<?= $this->message->render(); ?>

			<form class="login-form form" action="<?= $register; ?>" method="post">
				<div class="col xs-12 md-6 register-details">
					<div class="form-section">
						<h2><?= _l("Your Personal Details"); ?></h2>

						<div class="form-item required">
							<input type="text" placeholder="<?= _l("First Name"); ?>" name="firstname" value="<?= $firstname; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="<?= _l("Last Name"); ?>" name="lastname" value="<?= $lastname; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="<?= _l("Email"); ?>" autocomplete="off" name="email" value="<?= $email; ?>"/>
						</div>
					</div>

					<div class="form-section">
						<h2><?= _l("Create Password"); ?></h2>

						<div class="form-item required">
							<input type="password" autocomplete="off" placeholder="<?= _l("Password"); ?>" name="password"/>
						</div>
						<div class="form-item required">
							<input type="password" autocomplete="off" placeholder="<?= _l("Confirm Password"); ?>" name="confirm"/>
						</div>
					</div>

					<div class="form-section">
						<h2><?= _l("Newsletter"); ?></h2>

						<div class="form-item">
							<label><?= _l("Subscribe"); ?></label>
							<?= build('radio', array(
								'name'   => 'newsletter',
								'data'   => $data_yes_no,
								'select' => $newsletter
							)); ?>
						</div>
					</div>
				</div>

				<div class="col xs-12 md-6 top register-address">
					<div class="form-section">
						<h2><?= _l("Your Address"); ?> </h2>

						<div class="form-item required">
							<input type="text" placeholder="<?= _l("Address"); ?>" name="address_1" value="<?= $address_1; ?>"/>
						</div>
						<div class="form-item">
							<input type="text" placeholder="<?= _l("Address Line 2"); ?>" name="address_2" value="<?= $address_2; ?>"/>
						</div>
						<div class="form-item required">
							<input type="text" placeholder="<?= _l("City"); ?>" name="city" value="<?= $city; ?>"/>
						</div>
						<div class="form-item required">
							<?=
							build('select', array(
								'name'   => 'country_id',
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
							<input type="text" placeholder="<?= _l("Postal Code"); ?>" name="postcode" value="<?= $postcode; ?>"/>
						</div>
					</div>
				</div>

				<div class="col xs-12">
					<? if (!empty($agree_to)) { ?>
						<div class="form-item">
							<input id="agree-terms" type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : ''; ?> />
							<label for="agree_terms"><?= _l("I have read and agree to"); ?> <a href="<?= $agree_to; ?>"><?= $agree_title; ?></a></label>
						</div>
					<? } ?>

					<div class="form-item submit">
						<input type="submit" value="<?= _l("Continue"); ?>" class="button"/>
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

<?= call('common/footer'); ?>
