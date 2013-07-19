<?= $header; ?><?= $column_left; ?><?= $column_right; ?>

<div id="content">
	<?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	
	<h1><?= $heading_title; ?></h1>
	<? if (!empty($returns)) { ?>
		<? foreach ($returns as $return) { ?>
		<div class="return-list">
			<div class="return-id"><b><?= $text_rma_number; ?></b> #<?= $return['rma']; ?></div>
			<div class="return-status"><b><?= $text_status; ?></b> <?= $return['status']['title']; ?></div>
			<div class="return-content">
				<div><b><?= $text_date_added; ?></b> <?= $return['date_added']; ?><br />
					<b><?= $text_order_id; ?></b> <?= $return['order_id']; ?></div>
				<div><b><?= $text_customer; ?></b> <?= $return['name']; ?></div>
				<div class="return-info"><a href="<?= $return['href']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'info.png'; ?>" alt="<?= $button_view; ?>" title="<?= $button_view; ?>" /></a></div>
			</div>
		</div>
		<? } ?>
		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="content"><?= $text_empty; ?></div>
	<? } ?>
	
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>