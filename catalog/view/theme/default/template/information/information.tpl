<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
	<?= $description; ?>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>