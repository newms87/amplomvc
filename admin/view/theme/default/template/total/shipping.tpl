<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'total.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="content">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td><?= $entry_estimator; ?></td>
							<td><select name="shipping_estimator">
									<? if ($shipping_estimator) { ?>
										<option value="1" selected="selected"><?= $text_enabled; ?></option>
										<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
										<option value="1"><?= $text_enabled; ?></option>
										<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="shipping_status">
									<? if ($shipping_status) { ?>
										<option value="1" selected="selected"><?= $text_enabled; ?></option>
										<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
										<option value="1"><?= $text_enabled; ?></option>
										<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="shipping_sort_order" value="<?= $shipping_sort_order; ?>" size="1"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>