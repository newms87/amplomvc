<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->area->render('top'); ?>

	<h1><?= _l("Purchase a Gift Certificate"); ?></h1>

	<p><?= _l("This gift certificate will be emailed to the recipient after your order has been paid for."); ?></p>

	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<table class="form">
			<tr>
				<td class="required"> <?= _l("Recipient's Name:"); ?></td>
				<td><input type="text" name="to_name" value="<?= $to_name; ?>"/>
					<? if (_l("Recipient's Name must be between 1 and 64 characters!")) { ?>
						<span class="error"><?= _l("Recipient's Name must be between 1 and 64 characters!"); ?></span>
					<? } ?></td>
			</tr>
			<tr>
				<td class="required"> <?= _l("Recipient's Email:"); ?></td>
				<td><input type="text" name="to_email" value="<?= $to_email; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= _l("Your Name:"); ?></td>
				<td><input type="text" name="from_name" value="<?= $from_name; ?>"/>
					<? if (_l("Your Name must be between 1 and 64 characters!")) { ?>
						<span class="error"><?= _l("Your Name must be between 1 and 64 characters!"); ?></span>
					<? } ?></td>
			</tr>
			<tr>
				<td class="required"> <?= _l("Your Email:"); ?></td>
				<td><input type="text" name="from_email" value="<?= $from_email; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= _l("Gift Certificate Theme:"); ?></td>
				<td><? foreach ($voucher_themes as $voucher_theme) { ?>
						<? if ($voucher_theme['voucher_theme_id'] == $voucher_theme_id) { ?>
							<input type="radio" name="voucher_theme_id" value="<?= $voucher_theme['voucher_theme_id']; ?>"
								id="voucher-<?= $voucher_theme['voucher_theme_id']; ?>" checked="checked"/>
							<label
								for="voucher-<?= $voucher_theme['voucher_theme_id']; ?>"><?= $voucher_theme['name']; ?></label>
						<? } else { ?>
							<input type="radio" name="voucher_theme_id" value="<?= $voucher_theme['voucher_theme_id']; ?>"
								id="voucher-<?= $voucher_theme['voucher_theme_id']; ?>"/>
							<label
								for="voucher-<?= $voucher_theme['voucher_theme_id']; ?>"><?= $voucher_theme['name']; ?></label>
						<? } ?>
						<br/>
					<? } ?>
					<? if (_l("You must select a theme!")) { ?>
						<span class="error"><?= _l("You must select a theme!"); ?></span>
					<? } ?></td>
			</tr>
			<tr>
				<td><?= _l("Message:<br /><span class=\"help\">(Optional)</span>"); ?></td>
				<td><textarea name="message" cols="40" rows="5"><?= $message; ?></textarea></td>
			</tr>
			<tr>
				<td class="required">
					<?= _l("Amount:"); ?>
					<span class="help"><?= _l("(Value must be between %s and %s)", $max_value, $min_value); ?></span>
				</td>
				<td><input type="text" name="amount" value="<?= $amount; ?>" size="5"/></td>
			</tr>
		</table>
		<div class="buttons">
			<div class="right"><?= _l("I understand that gift certificates are non-refundable."); ?>
				<? if ($agree) { ?>
					<input type="checkbox" name="agree" value="1" checked="checked"/>
				<? } else { ?>
					<input type="checkbox" name="agree" value="1"/>
				<? } ?>
				<input type="submit" value="<?= _l("Continue"); ?>" class="button"/>
			</div>
		</div>
	</form>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $this->call('common/footer'); ?>
