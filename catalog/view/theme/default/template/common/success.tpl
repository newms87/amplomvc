<?= $header; ?>
<?= $this->builder->display_errors($errors); ?>

<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<h1><?= $heading_title; ?></h1>
	<?= $content_top; ?>
	<?= $text_message; ?>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<?= $content_bottom; ?></div>
<?= $footer; ?>