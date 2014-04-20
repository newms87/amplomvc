<?= $this->call('common/header'); ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Customer Group"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Customer Group Name:"); ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/>
								<? if (_l("Customer Group Name must be between 3 and 64 characters!")) { ?>
									<span class="error"><?= _l("Customer Group Name must be between 3 and 64 characters!"); ?></span>
								<? } ?></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $this->call('common/footer'); ?>
