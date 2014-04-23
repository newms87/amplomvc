<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'payment.png'; ?>" alt=""/> <?= _l("Voucher Themes"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Voucher Theme Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Image:"); ?></td>
						<td><input type="text" class="imageinput" name="image" value="<?= $image; ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.imageinput').ac_imageinput();
</script>
<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>
<?= $this->builder->js('translations', $translations); ?>

<?= $this->call('common/footer'); ?>
