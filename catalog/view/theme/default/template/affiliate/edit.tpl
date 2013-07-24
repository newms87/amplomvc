<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_details; ?></h2>
		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_firstname; ?></td>
					<td><input type="text" name="firstname" value="<?= $firstname; ?>" />
						<? if ($error_firstname) { ?>
						<span class="error"><?= $error_firstname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_lastname; ?></td>
					<td><input type="text" name="lastname" value="<?= $lastname; ?>" />
						<? if ($error_lastname) { ?>
						<span class="error"><?= $error_lastname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_email; ?></td>
					<td><input type="text" name="email" value="<?= $email; ?>" />
						<? if ($error_email) { ?>
						<span class="error"><?= $error_email; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_telephone; ?></td>
					<td><input type="text" name="telephone" value="<?= $telephone; ?>" />
						<? if ($error_telephone) { ?>
						<span class="error"><?= $error_telephone; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><?= $entry_fax; ?></td>
					<td><input type="text" name="fax" value="<?= $fax; ?>" /></td>
				</tr>
			</table>
		</div>
		<h2><?= $text_your_address; ?></h2>
		<div class="section">
			<table class="form">
				<tr>
					<td><?= $entry_company; ?></td>
					<td><input type="text" name="company" value="<?= $company; ?>" /></td>
				</tr>
				<tr>
					<td><?= $entry_website; ?></td>
					<td><input type="text" name="website" value="<?= $website; ?>" /></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_address_1; ?></td>
					<td><input type="text" name="address_1" value="<?= $address_1; ?>" />
						<? if ($error_address_1) { ?>
						<span class="error"><?= $error_address_1; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><?= $entry_address_2; ?></td>
					<td><input type="text" name="address_2" value="<?= $address_2; ?>" /></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_city; ?></td>
					<td><input type="text" name="city" value="<?= $city; ?>" />
						<? if ($error_city) { ?>
						<span class="error"><?= $error_city; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_postcode; ?></td>
					<td><input type="text" name="postcode" value="<?= $postcode; ?>" />
						<? if ($error_postcode) { ?>
						<span class="error"><?= $error_postcode; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_country; ?></td>
					<td>
						<?= $this->builder->set_config('country_id', 'name'); ?>
						<?= $this->builder->build('select', $countries, "country_id", $country_id, array('class'=>"country_select")); ?>
					</td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_zone; ?></td>
					<td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id; ?>"></select></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right">
				<input type="submit" value="<?= $button_continue; ?>" class="button" />
			</div>
		</div>
	</form>
	
	<?= $content_bottom; ?>
</div>
	
	<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>
	
<?= $footer; ?>