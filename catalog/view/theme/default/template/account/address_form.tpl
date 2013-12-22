<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="address_update" class="content">
	<? if ($errors && $this->request->isAjax()) { ?>
		<?= $this->builder->displayMessages(array('error' => $errors)); ?>
	<? } ?>

	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>

	<div class="box">
		<h2 class="box_heading"><?= $text_edit_address; ?></h2>

		<div class="section">
			<form id="new_address_form" action="<?= $save; ?>" method="post" enctype="multipart/form-data">

				<div class="section left">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_firstname; ?></td>
							<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_lastname; ?></td>
							<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_company; ?></td>
							<td><input type="text" name="company" value="<?= $company; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_address_1; ?></td>
							<td><input type="text" name="address_1" value="<?= $address_1; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_address_2; ?></td>
							<td><input type="text" name="address_2" value="<?= $address_2; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_city; ?></td>
							<td><input type="text" name="city" value="<?= $city; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_postcode; ?></td>
							<td><input type="text" name="postcode" value="<?= $postcode; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_country; ?></td>
							<td>
								<?= $this->builder->setConfig('country_id', 'name'); ?>
								<?= $this->builder->build('select', $data_countries, "country_id", $country_id, array('class' => "country_select")); ?>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_zone; ?></td>
							<td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id; ?>"></select></td>
						</tr>
						<tr>
							<td><?= $entry_default; ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, "default", $default); ?></td>
						</tr>
					</table>
				</div>

				<div class="buttons">
					<? if (!empty($back)) { ?>
						<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
					<? } ?>
					<div class="right"><input type="submit" value="<?= $button_save; ?>" class="button"/></div>
				</div>
			</form>
		</div>
	</div>

	<?= $content_bottom; ?>
</div>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<? if ($this->request->isAjax()) { ?>
<script type="text/javascript">
$('#new_address_form').submit(function(){
	$.post($(this).attr('action'), $(this).serialize(), function(html){
		if (html) {
			$('#address_update').parent().html(html);
		} else {
			location.reload()
		}
	});
	return false;
});
</script>
<? } ?>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
