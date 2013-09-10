<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>

		<div class="success_message"><?= $text_message; ?></div>

		<? if (!empty($returns)) { ?>
			<div class="rma_description"><?= $text_rma_description; ?></div>
			<ul class="return_success_list">
				<? foreach ($returns as $return) { ?>
					<li>
						<div class="product_name">
							<span class="label"><?= $text_product_name; ?></span>
							<span class="value"><?= $return['product']['name']; ?></span>
						</div>
						<div class="product_model">
							<span class="label"><?= $text_product_model; ?></span>
							<span class="value"><?= $return['product']['model']; ?></span>
						</div>
						<div class="rma">
							<span class="label"><?= $text_rma_number; ?></span>
							<span class="value"><?= $return['rma']; ?></span>
						</div>
					</li>
				<? } ?>
			</ul>
		<? } ?>

		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>


		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>