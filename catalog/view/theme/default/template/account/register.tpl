<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content">
	<?= _breadcrumbs(); ?>

	<div class="section">
		<h1><?= _l("Register Account"); ?></h1>

		<?= _area('top'); ?>

		<p><?= _l("If you already have an account with us, please login at the"); ?> <a href="<?= $login; ?>"><?= _l("login page"); ?></a></p>

		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">

				<div class="customer section left">
					<h2><?= _l("Your Personal Details"); ?></h2>

					<table class="form">
						<tr>
							<td class="required"> <?= _l("First Name:"); ?></td>
							<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Last Name:"); ?></td>
							<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("E-Mail:"); ?></td>
							<td><input type="text" name="email" value="<?= $email; ?>"/></td>
						</tr>
					</table>

					<h2><?= _l("Create Password"); ?></h2>

					<table class="form">
						<tr>
							<td class="required"> <?= _l("Password:"); ?></td>
							<td><input type="password" autocomplete="off" name="password" value="<?= $password; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Password Confirm:"); ?></td>
							<td><input type="password" autocomplete="off" name="confirm" value="<?= $confirm; ?>"/></td>
						</tr>
					</table>

					<h2><?= _l("Newsletter"); ?></h2>

					<table class="form">
						<tr>
							<td><?= _l("Subscribe:"); ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, 'newsletter', $newsletter); ?></td>
						</tr>
					</table>
				</div>

				<div class="address section right">
					<h2><?= _l("Your Address"); ?> </h2>

					<table class="form">
						<tr>
							<td><?= _l("Company:"); ?></td>
							<td><input type="text" name="company" value="<?= $company; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Address 1:"); ?></td>
							<td><input type="text" name="address_1" value="<?= $address_1; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Address 2:"); ?></td>
							<td><input type="text" name="address_2" value="<?= $address_2; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("City:"); ?></td>
							<td><input type="text" name="city" value="<?= $city; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Post Code:"); ?></td>
							<td><input type="text" name="postcode" value="<?= $postcode; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Country:"); ?></td>
							<td>
								<?= $this->builder->setConfig('country_id', 'name'); ?>
								<?= $this->builder->build('select', $data_countries, "country_id", $country_id, array('class' => "country_select")); ?>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Region / State:"); ?></td>
							<td><select name="zone_id" class="zone_select" data-zone_id="<?= $zone_id; ?>"></select></td>
						</tr>
					</table>
				</div>

				<div class="clear buttons">
					<div class="right">
						<? if (!empty($agree_to)) { ?>
							<input id="agree_terms" type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : ''; ?> />
							<label for="agree_terms"><?= _l("I have read and agree to"); ?> <a href="<?= $agree_to; ?>"><?= $agree_title; ?></a></label>
						<? } ?>

						<input type="submit" value="<?= _l("Continue"); ?>" class="button"/>
					</div>
				</div>
			</form>
		</div>

		<?= _area('bottom'); ?>
	</div>
</div>

<script type="text/javascript">
	$('.table.form .zone_select').ac_zoneselect({listen: '.table.form .country_select'});
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= _call('common/footer'); ?>
