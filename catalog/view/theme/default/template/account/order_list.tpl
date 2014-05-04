<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content">
	<?= _area('top'); ?>
	<?= _breadcrumbs(); ?>

	<h1><?= _l("Order History"); ?></h1>

	<? if (!empty($orders)) { ?>
		<? foreach ($orders as $order) { ?>
			<div class="order_list">
				<div class="order_id"><b><?= _l("Order ID:"); ?></b> #<?= $order['order_id']; ?></div>
				<div class="order_status"><b><?= _l("Status:"); ?></b> <?= $order['order_status']['title']; ?></div>
				<div class="order_content">
					<div>
						<b><?= _l("Date Added:"); ?></b> <?= $order['date_added']; ?><br/>
						<b><?= _l("Products:"); ?></b> <?= $order['products']; ?>
					</div>
					<div>
						<b><?= _l("Customer:"); ?></b> <?= $order['name']; ?><br/>
						<b><?= _l("Total:"); ?></b> <?= $order['total']; ?>
					</div>
					<div class="order_info">
						<a class="view" href="<?= $order['href']; ?>">
							<img src="<?= theme_url('image/view.png'); ?>" alt="<?= _l("View"); ?>" title="<?= _l("View Order"); ?>"/>
						</a>
						<a class="reorder" href="<?= $order['reorder']; ?>">
							<img src="<?= theme_url('image/reorder.png'); ?>" alt="<?= _l("Reorder"); ?>" title="<?= _l("Reorder"); ?>"/>
						</a>
					</div>
				</div>
			</div>
		<? } ?>

		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="section"><?= _l("You have not made any previous orders!"); ?></div>
	<? } ?>

	<div class="buttons">
		<div class="right">
			<a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a>
		</div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
