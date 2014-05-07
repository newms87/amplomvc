<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
<div class="content">
	<?= area('top'); ?>
	<?= breadcrumbs(); ?>

	<h1><?= _l("Order History"); ?></h1>

	<? if (!empty($orders)) { ?>
		<? foreach ($orders as $order) { ?>
			<div class="order-list">
				<div class="order-id"><b><?= _l("Order ID:"); ?></b> #<?= $order['order_id']; ?></div>
				<div class="order-status"><b><?= _l("Status:"); ?></b> <?= $order['order_status']['title']; ?></div>
				<div class="order-content">
					<div>
						<b><?= _l("Date Added:"); ?></b> <?= $order['date_added']; ?><br/>
						<b><?= _l("Products:"); ?></b> <?= $order['products']; ?>
					</div>
					<div>
						<b><?= _l("Customer:"); ?></b> <?= $order['name']; ?><br/>
						<b><?= _l("Total:"); ?></b> <?= $order['total']; ?>
					</div>
					<div class="order-info">
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

	<?= area('bottom'); ?>
</div>

<?= call('common/footer'); ?>
