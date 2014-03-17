<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>
<div id="address_update" class="content">
	<? if ($errors && $this->request->isAjax()) { ?>
		<?= $this->builder->displayMessages(array('error' => $errors)); ?>
	<? } ?>

	<?= $this->breadcrumb->render(); ?>
	<?= $area_top; ?>

	<div class="box">
		<h2 class="box_heading">
			<? if ($address_id) { ?>
				<?= _l("Edit Address"); ?>
			<? } else { ?>
				<?= _l("Add Address"); ?>
			<? } ?>
		</h2>

		<div class="section">
			<form id="new_address_form" action="<?= $save; ?>" method="post" enctype="multipart/form-data">

				<div class="section left">
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
						<tr>
							<td><?= _l("Default Address:"); ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, "default", $default); ?></td>
						</tr>
					</table>
				</div>

				<div class="buttons">
					<? if (!empty($back)) { ?>
						<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
					<? } ?>
					<div class="right"><input type="submit" value="<?= _l("Save"); ?>" class="button"/></div>
				</div>
			</form>
		</div>
	</div>

	<?= $area_bottom; ?>
</div>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<? if ($this->request->isAjax()) { ?>
	<script type="text/javascript">
		$('#new_address_form').submit(function () {
			$.post($(this).attr('action'), $(this).serialize(), function (json) {
				if (!json || !json['success']) {
					$('#address_update').parent().html(json);
				} else {
					location.reload()
				}
			}, 'json');
			return false;
		});
	</script>
<? } ?>

<?= $this->builder->js('errors', $errors); ?>

<?= $common_footer; ?>
