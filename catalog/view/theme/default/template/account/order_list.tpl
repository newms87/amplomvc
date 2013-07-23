<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	
	<h1><?= $heading_title; ?></h1>
	
	<? if (!empty($orders)) { ?>
		<? foreach ($orders as $order) { ?>
		<div class="order_list">
			<div class="order_id"><b><?= $text_order_id; ?></b> #<?= $order['order_id']; ?></div>
			<div class="order_status"><b><?= $text_status; ?></b> <?= $order['order_status']['title']; ?></div>
			<div class="order_content">
				<div>
					<b><?= $text_date_added; ?></b> <?= $order['date_added']; ?><br />
					<b><?= $text_products; ?></b> <?= $order['products']; ?>
				</div>
				<div>
					<b><?= $text_customer; ?></b> <?= $order['name']; ?><br />
					<b><?= $text_total; ?></b> <?= $order['total']; ?>
				</div>
				<div class="order_info">
					<a class="view" href="<?= $order['href']; ?>">
						<img src="<?= HTTP_THEME_IMAGE . 'info.png'; ?>" alt="<?= $button_view; ?>" title="<?= $button_view; ?>" />
					</a>
					<a class="reorder" href="<?= $order['reorder']; ?>">
						<img src="<?= HTTP_THEME_IMAGE . 'reorder.png'; ?>" alt="<?= $button_reorder; ?>" title="<?= $button_reorder; ?>" />
					</a>
				</div>
			</div>
		</div>
		<? } ?>
		
		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="section"><?= $text_empty; ?></div>
	<? } ?>
	
	<div class="buttons">
		<div class="right">
			<a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a>
		</div>
	</div>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>