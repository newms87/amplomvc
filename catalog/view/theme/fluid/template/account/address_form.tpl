<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="address-form" class="content">

	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h2>
				<? if ($address_id) { ?>
					<?= _l("Edit Address"); ?>
				<? } else { ?>
					<?= _l("Add Address"); ?>
				<? } ?>
			</h2>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="address-row row">
		<div class="wrap">
			<form id="address-form" class="form" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
				<div class="form-item first-name">
					<input type="text" placeholder="<?= _l("First Name"); ?>" name="firstname" value="<?= $firstname; ?>"/>
				</div>
				<div class="form-item last-name">
					<input type="text" placeholder="<?= _l("Last Name"); ?>" name="lastname" value="<?= $lastname; ?>"/>
				</div>
				<div class="form-item company">
					<input type="text" placeholder="<?= _l("Company"); ?>" name="company" value="<?= $company; ?>"/>
				</div>
				<div class="form-item address">
					<input type="text" placeholder="<?= _l("Address"); ?>" name="address_1" value="<?= $address_1; ?>"/>
				</div>
				<div class="form-item address-line-2">
					<input type="text" placeholder="<?= _l("Address Line 2"); ?>" name="address_2" value="<?= $address_2; ?>"/>
				</div>
				<div class="form-item city">
					<input type="text" placeholder="<?= _l("City"); ?>" name="city" value="<?= $city; ?>"/>
				</div>
				<div class="form-item postcode">
					<input type="text" placeholder="<?= _l("Post Code"); ?>" name="postcode" value="<?= $postcode; ?>"/>
				</div>
				<div class="form-item country-select">
					<? $build = array(
						'data'      => $data_countries,
						'name'      => 'country_id',
						'value'     => $country_id,
						'key_id'    => 'country_id',
						'key_value' => 'name',
					); ?>

					<?= build('select', $build); ?>
				</div>
				<div class="form-item zone-select">
					<select name="zone_id" data-zone-id="<?= $zone_id; ?>"></select>
				</div>
				<div class="form-item default-address">
					<div class="text"><?= _l("Set as Default Address?"); ?></div>
					<? $build = array(
						'data'  => $data_yes_no,
						'name'  => 'default',
						'value' => $default,
					); ?>

					<?= build('ac-radio', $build); ?>
				</div>

				<div class="form-item submit">
					<div class="left">
						<a href="<?= site_url('account/address'); ?>" class="button"><?= _l("Cancel"); ?></a>
					</div>
					<div class="right">
						<button><?= _l("Save"); ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?= _area('bottom'); ?>

</section>

<script type="text/javascript">
	$('#address-form [name=zone_id]').ac_zoneselect({listen: '#address-form [name=country_id]'});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= _call('common/footer'); ?>
