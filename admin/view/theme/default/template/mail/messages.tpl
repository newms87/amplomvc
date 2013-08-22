<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="content">
				<div id="tabs" class="htabs">
					<a href='#tab-mail-msgs'><?= $tab_mail_msgs; ?></a>
				</div>
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div id="tab-mail-msgs">
						<table class="form">
							<tr>
								<td><?= $entry_mail_registration; ?></td>
								<td class='mail_info'>
									<label for='registration_subject'><?= $entry_mail_subject; ?></label>
									<input id='registration_subject' type='text' name='mail_registration_subject' value='<?= $mail_registration_subject; ?>' size='100'/>
									<label for='registration_message'><?= $entry_mail_message; ?></label>
									<textarea id='registration_message' class='ckedit' name='mail_registration_message'><?= $mail_registration_message; ?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('ckeditor'); ?>

	<script type="text/javascript">//<!--
		$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>