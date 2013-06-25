<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<div class="content"><?= $text_error; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<?= $content_bottom; ?>
</div>
<?= $footer; ?>