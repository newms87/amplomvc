<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Failed Email Messages}}</h1>

			<div class="buttons">
				<a onclick="$('#mail_form').submit();" class="button">{{Send}}</a>
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="content failed_messages">
			<div class="left message_list">
				<div class="mail_error_messages">
					<? foreach ($messages as $msg) { ?>
						<div class="message_preview">
							<form action="<?= $resend_message; ?>" method="post" enctype="multipart/form-data">
								<div class="from">
									<span class="label">{{From: }}</span>
									<span class="value"><?= $msg['from']; ?></span>
								</div>
								<div class="to">
									<span class="label">{{To: }}</span>
									<span class="value"><?= $msg['to']; ?></span>
								</div>
								<? if ($msg['cc']) { ?>
									<div class="cc">
										<span class="label">{{CC: }}</span>
										<span class="value"><?= $msg['cc']; ?></span>
									</div>
								<? } ?>
								<? if ($msg['bcc']) { ?>
									<div class="bcc">
										<span class="label">{{BCC: }}</span>
										<span class="value"><?= $msg['bcc']; ?></span>
									</div>
								<? } ?>
								<div class="subject">
									<span class="label"><?= _l(""); ?></span>
									<span class="value"><?= $msg['subject']; ?></span>
								</div>
								<input type="hidden" name="mail_fail_id" value="<?= $msg['mail_fail_id']; ?>"/>
								<input type="hidden" name="to" value="<?= $msg['to']; ?>"/>
								<input type="hidden" name="cc" value="<?= $msg['cc']; ?>"/>
								<input type="hidden" name="bcc" value="<?= $msg['bcc']; ?>"/>
								<input type="hidden" name="from" value="<?= $msg['from']; ?>"/>
								<input type="hidden" name="sender" value="<?= $msg['sender']; ?>"/>
								<input type="hidden" name="subject" value="<?= $msg['subject']; ?>"/>
								<input type="hidden" name="allow_html" value="<?= !empty($msg['html']) ? 'class="html"' : ''; ?>"/>
								<? if (!empty($msg['attachments'])) { ?>
									<input type="hidden" name="_attachments" value="<?= implode(',', $msg['attachments']); ?>"/>
								<? } ?>
								<div class="action_buttons">
									<input type="submit" class="button" name="resend_message" value="{{Resend}}"/>
									<a class="edit_message">{{View / Edit Message}}</a>
									<a class="delete_message">{{Delete}}</a>
								</div>
							</form>
						</div>
					<? } ?>
				</div>
			</div>
			<div class="right resend_message">
				<form action="<?= $send_message; ?>" method="post" enctype="multipart/form-data" id="mail_form">
					<table class="form">
						<tr>
							<td class="mail_info">
								<label for="mail_sender">{{Send From Display Name:}}</label>
								<input id="mail_sender" type="text" name="sender" value="" size="40"/>
								<label for="mail_from"><span class="required"></span>{{From:}}</label>
								<input id="mail_from" type="text" name="from" value="" size="100"/>
								<label for="mail_to" class="required">{{To:}}<span class="help">{{(comma separated list)}}</span></label>
								<input id="mail_to" type="text" name="to" value="" size="100"/>
								<label for="mail_cc">{{Copy To:}}<span class="help">{{(comma separated list)}}</span></label>
								<input id="mail_cc" type="text" name="cc" value="" size="100"/>
								<label for="mail_bcc">{{Blind Copy To:}}<span class="help">{{(comma separated list)}}</span></label>
								<input id="mail_bcc" type="text" name="bcc" value="" size="100"/>
								<label for="mail_subject"><span class="required"></span>{{Subject:}}</label>
								<input id="mail_subject" type="text" name="subject" value="" size="100"/>
								<label for="mail_message"><span class="required"></span>{{Message:}}</label>
								<textarea id="mail_message" rows="15" cols="120" name="message"></textarea>
								<label for="allow_html"><input type="checkbox" checked="checked" name="allow_html"
										id="allow_html"/>{{Allow HTML in message?}}</label>
								<label for="mail_attachment">{{Attachments:}}</label>
								<input id="mail_attachment" type="file" multiple name="attachment[]" value="" size="100"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('a.edit_message').click(function () {
		message = $(this).closest('.message_preview');

		message.find('input[type=hidden]').not('[name=_attachment]').each(function (i, e) {
			$('#mail_form [name=' + $(e).attr("name") + ']').val($(e).val());
		});

		$.get('<?= $load_message; ?>', {mail_fail_id: message.find('[name=mail_fail_id]').val()}, function (html) {
			type = html.substring(0, 8);

			if (type == '__TEXT__') {
				html = html.substring(9);
				$('#mail_form [name=allow_html]').removeAttr('checked').change();
				$('#mail_form [name=message]').val(html);
			}
			else {
				$('#mail_form [name=allow_html]').attr('checked', 'checked').change();
				$('#cke_mail_message .cke_button_source').click();
				$('#cke_mail_message .cke_source').val(html);
			}
		});
	});

	$('a.delete_message').click(function () {
		var msg_preview = $(this).closest('.message_preview');

		mail_fail_id = msg_preview.find('input[name=mail_fail_id]').val();

		$.post('<?= $delete_message; ?>', {mail_fail_id: mail_fail_id}, function () {
			msg_preview.parent().show_msg("success", "{{Message deleted.}}");
			msg_preview.remove();
		});
	});
</script>



<?= $is_ajax ? '' : call('admin/footer'); ?>
