<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<? if ($voucher_id) { ?>
					<a onclick="send_voucher()" class="button"><?= $button_send; ?></a>
				<? } ?>
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
				<? if ($voucher_id) { ?>
					<a href="#tab-history"><?= $tab_voucher_history; ?></a>
				<? } ?>
			</div>

			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_code; ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_from_name; ?></td>
							<td><input type="text" name="from_name" value="<?= $from_name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_from_email; ?></td>
							<td><input type="text" name="from_email" value="<?= $from_email; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_to_name; ?></td>
							<td><input type="text" name="to_name" value="<?= $to_name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_to_email; ?></td>
							<td><input type="text" name="to_email" value="<?= $to_email; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_theme; ?></td>
							<td>
								<? $this->builder->setConfig('voucher_theme_id', 'name'); ?>
								<?= $this->builder->build('select', $data_voucher_themes, 'voucher_theme_id', $voucher_theme_id); ?>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_message; ?></td>
							<td><textarea name="message" cols="40" rows="5"><?= $message; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_amount; ?></td>
							<td><input type="text" name="amount" value="<?= $amount; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
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

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
