<?= $is_ajax ? '' : call('header'); ?>

<div id="maintenance-mode" class="row content">
	<div class="col xs-12 center">
		<div class="align-middle"></div>
		<h1>
			{{We are currently performing some scheduled maintenance.}}<br/>
			{{We will be back as soon as possible. Please check back soon.}}
		</h1>
	</div>
</div>

<?= $is_ajax ? '' : call('footer'); ?>
