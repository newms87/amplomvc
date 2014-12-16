<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Send Email}}</h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button">{{Send}}</a>
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form" style="width:50%; margin-left:10%">
					<tr>
						<td class="mail_info">
							<label for="mail_sender">{{Send From Display Name:}}</label>
							<input id="mail_sender" type="text" name="sender" value="<?= $sender; ?>" size="40"/>
							<label for="mail_from"><span class="required">*</span>{{From:}}</label>
							<input id="mail_from" type="text" name="from" value="<?= $from; ?>" size="100"/>
							<label for="mail_to" class="required">{{To:}}<span class="help">{{(comma separated list)}}</span></label>
							<input id="mail_to" type="text" name="to" value="<?= $to; ?>" size="100"/>
							<label for="mail_cc">{{Copy To:}}<span class="help">{{(comma separated list)}}</span></label>
							<input id="mail_cc" type="text" name="cc" value="<?= $cc; ?>" size="100"/>
							<label for="mail_bcc">{{Blind Copy To:}}<span class="help">{{(comma separated list)}}</span></label>
							<input id="mail_bcc" type="text" name="bcc" value="<?= $bcc; ?>" size="100"/>
							<label for="mail_subject"><span class="required">*</span>{{Subject:}}</label>
							<input id="mail_subject" type="text" name="subject" value="<?= $subject; ?>" size="100"/>
							<label for="mail_message"><span class="required">*</span>{{Message:}}</label>
							<textarea id="mail_message" rows="15" cols="120" name="message"><?= $message; ?></textarea>
							<label for="allow_html"><input type="checkbox" <?= $allow_html ? 'checked' : ''; ?> name="allow_html" id="allow_html"/>{{Allow HTML in message?}}
							</label>
							<label for="mail_attachment">{{Attachments:}}</label>
							<input id="mail_attachment" type="file" multiple name="attachment[]" value="<?= $attachment; ?>"
								size="100"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= build_js('ckeditor'); ?>

<script type="text/javascript">
	$('#allow_html').change(function () {
		message = $('#mail_message');

		if ($(this).is(':checked') && !message.hasClass('ckedit')) {
			message.addClass('ckedit');
			init_ckeditor_for($('#mail_message'));
		}
		else if (!$(this).is(':checked') && message.hasClass('ckedit')) {
			message.removeClass('ckedit');
			remove_ckeditor_for($('#mail_message'));
		}
	}).change();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
