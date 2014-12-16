<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Mail Messages}}</h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button">{{Save}}</a>
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-mail-msgs">{{Mail Messages}}</a>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-mail-msgs">
					<table class="form">
						<tr>
							<td>{{Customer Registration Email:}}</td>
							<td class="mail_info">
								<label for="registration_subject">{{Subject:}}</label>
								<input id="registration_subject" type="text" name="mail_registration_subject" value="<?= $mail_registration_subject; ?>" size="100"/>
								<label for="registration_message">{{Body:}}</label>
								<textarea id="registration_message" class="ckedit" name="mail_registration_message"><?= $mail_registration_message; ?></textarea>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= build_js('ckeditor'); ?>

<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
