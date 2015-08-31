<?= $is_ajax ? '' : call('header'); ?>

<div id="maintenance-mode" class="content">
	<h1>
		{{We are currently performing some scheduled maintenance.}}<br/>
		{{We will be back as soon as possible. Please check back soon.}}
	</h1>
</div>

<?= $is_ajax ? '' : call('footer'); ?>
