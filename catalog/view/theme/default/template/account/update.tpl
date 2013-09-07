<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_details; ?></h2>

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
					<td class="required"> <?= $entry_email; ?></td>
					<td><input type="text" name="email" value="<?= $email; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_birthdate; ?></td>
					<td><input type="text" class="datepicker" name="metadata[birthdate]" value="<?= !empty($metadata['birthdate']) ? $metadata['birthdate'] : ''; ?>"/></td>
				</tr>
			</table>
		</div>

		<div class="section right">
			<h3><?= $text_ship_to; ?></h3>
			<div id="address_list">
				<? foreach ($data_addresses as $address) { ?>
					<? $checked = ($address['address_id'] == $address_id) ? 'checked="checked"' : ''; ?>
					<div class="address <?= $checked ? 'checked' : ''; ?>">
						<input type="radio" name="address_id" value="<?= $address['address_id']; ?>" <?= $checked; ?> />
						<?= $address['display']; ?>
					</div>
				<? } ?>
			</div>
			<a href="<?= $add_address; ?>" class="add_address" onclick="return colorbox($(this).attr('href', '<?= $ajax_add_address; ?>'));"><?= $button_add_address; ?></a>
		</div>

		<div class="clear buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right">
				<input type="submit" value="<?= $button_save; ?>" class="button"/>
			</div>
		</div>
	</form>

	<?= $content_bottom; ?>
</div>

<script type="text/javascript">//<!--
$('.address input[type=radio]').hide();

$('#address_list .address').click(function(){
	$('.address').removeClass('checked').find('input[type=radio]').prop('checked', false);

	$(this).addClass("checked").find('input[type=radio]').prop('checked', true);
});

$.ac_datepicker({changeYear: true, yearRange: "c-150:c", changeMonth: true});
//--></script>
<?= $footer; ?>