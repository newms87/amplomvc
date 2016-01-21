<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="not-found-page" class="content">
	<header class="top-row row">
		<div class="wrap">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><?= !empty($page_title) ? $page_title : "{{Page Not Found}}"; ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="not-found row">
		<div class="wrap">
			<div class="text">{{The page you requested cannot be found.}}</div>
			<div class="buttons">
				<a href="<?= $continue; ?>" class="button">{{Continue}}</a>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
