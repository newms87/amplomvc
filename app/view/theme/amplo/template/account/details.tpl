<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<? $sprite = theme_sprite('sprite@1x.png'); ?>

<section class="account-page content">
	<header class="row account-details section-style-a">
		<div class="wrap">
			<h1>{{Account Details}}</h1>

			<h2>{{Your scopes are as follows}}</h2>
		</div>
	</header>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
