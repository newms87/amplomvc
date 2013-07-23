<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
	<form id='contact_form' action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h3><?= $text_contact_us; ?></h3>
		<h3><?= $text_contact_info; ?></h3>
		<h2><?= $text_contact; ?></h2>
		<div class="section">
		<b><?= $entry_name; ?></b><br />
		<input type="text" name="name" value="<?= $name; ?>" />
		<br />
		<br />
		<b><?= $entry_email; ?></b><br />
		<input type="text" name="email" value="<?= $email; ?>" />
		<br />
		<br />
		<b><?= $entry_enquiry; ?></b><br />
		<textarea name="enquiry" cols="40" rows="10" style="width: 99%;"><?= $enquiry; ?></textarea>
		<br />
		<br />
		<b><?= $entry_captcha; ?></b><br />
		<input type="text" name="captcha" value="<?= $captcha; ?>" />
		<br />
		<img src="<?= $captcha_url; ?>" alt="" />
		</div>
		<div class="buttons">
			<div class="right"><input type="submit" value="<?= $button_submit; ?>" class="button" /></div>
		</div>
	</form>
	
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	
	<?= $content_bottom; ?>
</div>
	
<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>