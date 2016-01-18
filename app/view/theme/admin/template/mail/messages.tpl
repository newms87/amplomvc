<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

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
								<textarea id="registration_message" name="mail_registration_message"><?= $mail_registration_message; ?></textarea>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
