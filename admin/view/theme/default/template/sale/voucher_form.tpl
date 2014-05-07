<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/payment.png'); ?>" alt=""/> <?= _l("Gift Voucher"); ?></h1>

			<div class="buttons">
				<? if ($voucher_id) { ?>
					<a onclick="send_voucher()" class="button"><?= _l("Send"); ?></a>
				<? } ?>
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
				<? if ($voucher_id) { ?>
					<a href="#tab-history"><?= _l("Voucher History"); ?></a>
				<? } ?>
			</div>

			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Code:<br /><span class=\"help\">The code the customer enters to activate the voucher.</span>"); ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("From Name:"); ?></td>
							<td><input type="text" name="from_name" value="<?= $from_name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("From E-Mail:"); ?></td>
							<td><input type="text" name="from_email" value="<?= $from_email; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("To Name:"); ?></td>
							<td><input type="text" name="to_name" value="<?= $to_name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("To E-Mail:"); ?></td>
							<td><input type="text" name="to_email" value="<?= $to_email; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Theme:"); ?></td>
							<td>
								<? $this->builder->setConfig('voucher_theme_id', 'name'); ?>
								<?= $this->builder->build('select', $data_voucher_themes, 'voucher_theme_id', $voucher_theme_id); ?>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Message:"); ?></td>
							<td><textarea name="message" cols="40" rows="5"><?= $message; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Amount:"); ?></td>
							<td><input type="text" name="amount" value="<?= $amount; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', $status); ?></td>
						</tr>
					</table>
				</div>

				<? if ($voucher_id) { ?>
					<div id="tab-history">
						<div id="history"></div>
					</div>
				<? } ?>

			</form>
		</div>
	</div>

	<? if ($voucher_id) { ?>
		<script type="text/javascript">
			$('#history .pagination a').live('click', function () {
				$('#history').load(this.href);

				return false;
			});

			$('#history').load("<?= $url_history; ?>");

			function send_voucher() {
				$.get('<?= $send; ?>', {}, function (json) {
					if (typeof json == 'string') {
						show_msg('warning', json);
					} else {
						show_msgs(json);
					}
				});
			}
		</script>
	<? } ?>

	<script type="text/javascript">
		$('#tabs a').tabs();
	</script>

	<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

	<?= call('common/footer'); ?>
