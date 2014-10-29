<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="not-found-page" class="content">
	<header class="top-row row">
		<div class="wrap">
			<?= IS_AJAX ? '' : breadcrumbs(); ?>
			<h1><?= !empty($page_title) ? $page_title : _l("Page Not Found"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="not-found row">
		<div class="wrap">
			<div class="text"><?= _l("The page you requested cannot be found."); ?></div>
			<div class="buttons">
				<a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>
