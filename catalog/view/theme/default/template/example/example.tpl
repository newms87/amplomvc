<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

<div class="content">
	<?= $this->breadcrumb->render(); ?>

	<div class="section">

		<h1><?= $title; ?></h1>

		<?= $content_top; ?>

		<div class="page_content"><?= $content; ?></div>
	</div>

	<?= $content_bottom; ?>
</div>

<?= $footer; ?>
