<?= $is_ajax ? '' : call('admin/header'); ?>
	<div class="section">
		<?= $is_ajax ? '' : breadcrumbs(); ?>
		<? if ($error_warning) { ?>
			<div class="message warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/payment.png'); ?>" alt=""/> {{Currency}}</h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button">{{Save}}</a><a
						href="<?= $cancel; ?>" class="button">{{Cancel}}</a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> {{Currency Title:}}</td>
							<td><input type="text" name="title" value="<?= $title; ?>"/>
								<? if (_l("Currency Title must be between 3 and 32 characters!")) { ?>
									<span class="error">{{Currency Title must be between 3 and 32 characters!}}</span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Code:<br /><span class=\"help\">Do not change if this is your default currency. Must be valid <a href=\"http://www.xe.com/iso4217.php\" target=\"_blank\">ISO code</a>.</span>"); ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>"/>
								<? if (_l("Currency Code must contain 3 characters!")) { ?>
									<span class="error">{{Currency Code must contain 3 characters!}}</span>
								<? } ?></td>
						</tr>
						<tr>
							<td>{{Symbol Left:}}</td>
							<td><input type="text" name="symbol_left" value="<?= $symbol_left; ?>"/></td>
						</tr>
						<tr>
							<td>{{Symbol Right:}}</td>
							<td><input type="text" name="symbol_right" value="<?= $symbol_right; ?>"/></td>
						</tr>
						<tr>
							<td>{{Decimal Places:}}</td>
							<td><input type="text" name="decimal_place" value="<?= $decimal_place; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Value:<br /><span class=\"help\">Set to 1.00000 if this is your default currency.</span>"); ?></td>
							<td><input type="text" name="value" value="<?= $value; ?>"/></td>
						</tr>
						<tr>
							<td>{{Status:}}</td>
							<td><select name="status">
									<? if ($status) { ?>
										<option value="1" selected="selected">{{Enabled}}</option>
										<option value="0">{{Disabled}}</option>
									<? } else { ?>
										<option value="1">{{Enabled}}</option>
										<option value="0" selected="selected">{{Disabled}}</option>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
