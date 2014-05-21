<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="order-history-page" class="content">
	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<h1><?= _l("Order History"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="order-history row">
		<div class="wrap">
			<? if (!empty($orders)) { ?>
				<div class="order-list">

				<? foreach ($orders as $order) { ?>
					<div class="order-item info-list">
						<div class="order-entry">
							<div class="info-item date-added">
								<span class="label"><?= _l("Date:"); ?></span>
								<span class="info"><?= format('date', $order['date_added'], 'short'); ?></span>
							</div>
							<div class="info-item order-id">
								<span class="label"><?= _l("Order ID:"); ?></span>
								<span class="info"><?= $order['order_id']; ?></span>
							</div>
							<div class="info-item status">
								<span class="label"><?= _l("Status:"); ?></span>
								<span class="info"><?= $order['order_status']['title']; ?></span>
							</div>
						</div>

						<div class="order-content">
							<div class="info-item products">
								<span class="label"><?= _l("Products:"); ?></span>
								<span class="info"><?= $order['products']; ?></span>
							</div>
							<div class="info-item customer">
								<span class="label"><?= _l("Customer:"); ?></span>
								<span class="info"><?= $order['name']; ?></span>
							</div>
							<div class="info-item total">
								<span class="label"><?= _l("Total:"); ?></span>
								<span class="info"><?= format('currency', $order['total']); ?></span>
							</div>
							<div class="order-buttons">
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
			</div>

				<div class="pagination"><?= $pagination; ?></div>
			<? } else { ?>
				<div class="section"><?= _l("You have not made any previous orders!"); ?></div>
			<? } ?>
		</div>
	</div>

	<div class="button-row row">
		<div class="wrap">
			<div class="buttons">
				<div class="right">
					<a href="<?= site_url('account'); ?>" class="button"><?= _l("Continue"); ?></a>
				</div>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>

</section>

<?= call('common/footer'); ?>
