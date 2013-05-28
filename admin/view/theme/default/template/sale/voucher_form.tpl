<?= $header; ?>
<div class="content">
<?= $this->builder->display_breadcrumbs();?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a>
			<? if ($voucher_id) { ?>
			<a href="#tab-history"><?= $tab_voucher_history; ?></a>
			<? } ?>
		</div>
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<div id="tab-general">
				<table class="form">
					<tr>
						<td><span class="required"></span> <?= $entry_code; ?></td>
						<td><input type="text" name="code" value="<?= $code; ?>" />
							<? if ($error_code) { ?>
							<span class="error"><?= $error_code; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_from_name; ?></td>
						<td><input type="text" name="from_name" value="<?= $from_name; ?>" />
							<? if ($error_from_name) { ?>
							<span class="error"><?= $error_from_name; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_from_email; ?></td>
						<td><input type="text" name="from_email" value="<?= $from_email; ?>" />
							<? if ($error_from_email) { ?>
							<span class="error"><?= $error_from_email; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_to_name; ?></td>
						<td><input type="text" name="to_name" value="<?= $to_name; ?>" />
							<? if ($error_to_name) { ?>
							<span class="error"><?= $error_to_name; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_to_email; ?></td>
						<td><input type="text" name="to_email" value="<?= $to_email; ?>" />
							<? if ($error_to_email) { ?>
							<span class="error"><?= $error_to_email; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_theme; ?></td>
						<td><select name="voucher_theme_id">
								<? foreach ($voucher_themes as $voucher_theme) { ?>
								<? if ($voucher_theme['voucher_theme_id'] == $voucher_theme_id) { ?>
								<option value="<?= $voucher_theme['voucher_theme_id']; ?>" selected="selected"><?= $voucher_theme['name']; ?></option>
								<? } else { ?>
								<option value="<?= $voucher_theme['voucher_theme_id']; ?>"><?= $voucher_theme['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<tr>
						<td><span class="required"></span> <?= $entry_message; ?></td>
						<td><textarea name="message" cols="40" rows="5"><?= $message; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_amount; ?></td>
						<td><input type="text" name="amount" value="<?= $amount; ?>" />
							<? if ($error_amount) { ?>
							<span class="error"><?= $error_amount; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="status">
								<? if ($status) { ?>
								<option value="1" selected="selected"><?= $text_enabled; ?></option>
								<option value="0"><?= $text_disabled; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_enabled; ?></option>
								<option value="0" selected="selected"><?= $text_disabled; ?></option>
								<? } ?>
							</select></td>
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
<script type="text/javascript"><!--
$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});

$('#history').load("<?= HTTP_ADMIN . "index.php?route=sale/voucher/history"; ?>" + '&voucher_id=<?= $voucher_id; ?>');
//--></script>
<? } ?>
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>
<?= $footer; ?>