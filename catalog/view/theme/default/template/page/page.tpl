<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>

<div class="content">
	<?= $this->breadcrumb->render(); ?>

	<style><?= $css; ?></style>

	<div class="section">
		<? if (!empty($display_title)) { ?>
			<h1><?= $title; ?></h1>
		<? } ?>

		<?= $this->area->render('top'); ?>

		<div class="page_content"><?= $content; ?></div>
	</div>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->call('common/footer'); ?>
