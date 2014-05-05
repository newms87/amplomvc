<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
	<div class="content">
		<?= _breadcrumbs(); ?>
		<?= _area('top'); ?>

		<h1><?= _l("Product Returns"); ?></h1>

		<div class="success-message"><?= _l("<p>Thank you for submitting your return request. Your request has been sent to the relevant department for processing.</p><p> You will be notified via e-mail as to the status of your request.</p>"); ?></div>

		<? if (!empty($returns)) { ?>
			<div class="rma-description"><?= _l("Please ship your products back to us with the following RMA number(s) included inside each package with the associated product."); ?></div>
			<ul class="return-success-list">
				<? foreach ($returns as $return) { ?>
					<li>
						<div class="product-name">
							<span class="label"><?= _l("Product Name:"); ?></span>
							<span class="value"><?= $return['product']['name']; ?></span>
						</div>
						<div class="product-model">
							<span class="label"><?= _l("Product Model:"); ?></span>
							<span class="value"><?= $return['product']['model']; ?></span>
						</div>
						<div class="rma">
							<span class="label"><?= _l("RMA #:"); ?></span>
							<span class="value"><?= $return['rma']; ?></span>
						</div>
					</li>
				<? } ?>
			</ul>
		<? } ?>

		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>


		<?= _area('bottom'); ?>
	</div>

<?= _call('common/footer'); ?>
