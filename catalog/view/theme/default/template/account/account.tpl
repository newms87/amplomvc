<?= _call('common/header'); ?>
<?= _area('left') . _area('right'); ?>

<div id="account_manage" class="content">
	<?= _breadcrumbs(); ?>

	<?= _area('top'); ?>

	<h1><?= _l("Account Manager"); ?></h1>

	<div class="section left customer_info">
		<h2><?= _l("Customer Information"); ?></h2>

		<div class="name"><?= $customer['display_name']; ?></div>
		<div class="phone"><?= $customer['telephone']; ?></div>
		<div class="email"><?= $customer['email']; ?></div>
		<br/>

		<h2><?= _l("Default Shipping Address"); ?></h2>

		<div class="shipping_address"><?= $shipping_address['display']; ?></div>
		<br/>

		<h2><?= _l("Newsletter"); ?></h2>

		<div class="newsletter"><?= $newsletter_display; ?></div>
		<br/>

		<div class="center">
			<a class="button small account_edit" href="<?= $edit_account; ?>"><?= _l("Edit Information"); ?></a>
		</div>
	</div>

	<? if (!empty($data_subscriptions)) { ?>
		<div class="section right">
			<h2><?= _l("Subscriptions"); ?></h2>

			<div id="subscription_list">
				<? foreach ($data_subscriptions as $subscription) { ?>
					<? if ($subscription['status'] === Subscription::ACTIVE) { ?>
						<div class="subscription active">
							<div class="info">
								<div class="image left">
									<img src="<?= $subscription['thumb']; ?>"/>
								</div>
								<div class="info_text left">
									<div class="name"><?= $subscription['product']['name']; ?></div>
									<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
									<div class="price"><?= $subscription['total_display']; ?></div>
								</div>
							</div>
							<div class="buttons">
								<a href="<?= $subscription['choose_meals']; ?>" class="clear meals button"><?= _l("Choose Meals"); ?></a>
								<a href="<?= $subscription['edit']; ?>" class="clear update small button"><?= _l("Manage Subscription"); ?></a>
							</div>
						</div>
					<? } elseif ($subscription['status'] === Subscription::ON_HOLD) { ?>
						<div class="subscription on_hold">
							<div class="info">
								<div class="image left">
									<img src="<?= $subscription['thumb']; ?>"/>
								</div>
								<div class="info_text left">
									<div class="name"><?= $subscription['product']['name']; ?></div>
									<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
									<div class="price"><?= $subscription['total_display']; ?></div>
								</div>
							</div>
							<div class="clear on_hold_text"><?= _l("On Hold until %s", $subscription['resume_date']); ?></div>
							<div class="buttons">
								<a href="<?= $subscription['resume']; ?>" class="clear resume subscribe button"><?= _l("Resume"); ?></a>
								<a href="<?= $subscription['edit']; ?>" class="clear update small button"><?= _l("Manage Subscription"); ?></a>
							</div>
						</div>
					<? } ?>
				<? } ?>
			</div>
		</div>
	<? } ?>

	<div class="clear account_links clearfix">
		<div class="left"><a href="<?= $back; ?>" class="button medium"><?= _l("Home"); ?></a></div>
		<div class="right">
			<a href="<?= $url_order_history; ?>" class="button medium"><?= _l("View Order History"); ?></a>
			<a href="<?= $url_returns; ?>" class="button medium"><?= _l("Product Returns"); ?></a>
		</div>
	</div>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>
