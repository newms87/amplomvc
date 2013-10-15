<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_image; ?></td>
							<td><?= $this->builder->imageInput("image", $image); ?></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('errors', $errors); ?>
<?= $this->builder->js('translations', $translations); ?>

<?= $footer; ?>
