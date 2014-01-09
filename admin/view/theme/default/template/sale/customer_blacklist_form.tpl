<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Customer IP Blacklist"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("IP:"); ?></td>
							<td><input type="text" name="ip" value="<?= $ip; ?>"/>
								<? if (_l("IP must be between 1 and 15 characters!")) { ?>
									<span class="error"><?= _l("IP must be between 1 and 15 characters!"); ?></span>
								<? } ?></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>