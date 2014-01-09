<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= _l("Product Returns"); ?></h1>

		<div class="success_message"><?= _l("<p>Thank you for submitting your return request. Your request has been sent to the relevant department for processing.</p><p> You will be notified via e-mail as to the status of your request.</p>"); ?></div>

		<? if (!empty($returns)) { ?>
			<div class="rma_description"><?= _l("Please ship your products back to us with the following RMA number(s) included inside each package with the associated product."); ?></div>
			<ul class="return_success_list">
				<? foreach ($returns as $return) { ?>
					<li>
						<div class="product_name">
							<span class="label"><?= _l("Product Name:"); ?></span>
							<span class="value"><?= $return['product']['name']; ?></span>
						</div>
						<div class="product_model">
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


		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>