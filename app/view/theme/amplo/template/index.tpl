<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<section id="home-page" class="home-video content">
	<header class="row top-row">
		<div class="wrap">
			<h1>{{Welcome to Amplo MVC}}</h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="home-main row">
		<div class="wrap">
		</div>
	</div>

	<? if (show_area('bottom')) { ?>
		<div class="row area-bottom">
			<div class="wrap">
				<?= area('bottom'); ?>
			</div>
		</div>
	<? } ?>

</section>

<?= $is_ajax ? '' : call('footer'); ?>
