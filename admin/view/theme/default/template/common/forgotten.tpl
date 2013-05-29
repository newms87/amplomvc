<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#forgotten').submit();" class="button"><?= $button_reset; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="forgotten">
				<p><?= $text_email; ?></p>
				<table class="form">
					<tr>
						<td><?= $entry_email; ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>