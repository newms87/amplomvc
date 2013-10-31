<?= $header; ?>

<div class="section clear">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#forgotten').submit();" class="button"><?= $button_reset; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="forgotten">
				<p><?= $text_email; ?></p>
				<table class="form">
					<tr>
						<td><?= $entry_email; ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $footer; ?>
