<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="account-manage-page" class="content">

	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h1><?= _l("Account Manager"); ?></h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="customer-info row">
		<div class="wrap">
			<div class="section col xs-12 <?= !empty($data_subscriptions) ? 'md-6' : 'md-12'; ?>">
				<h2><?= _l("Customer Information"); ?></h2>

				<div class="name"><?= $customer['display_name']; ?></div>
				<div class="phone"><?= $customer['telephone']; ?></div>
				<div class="email"><?= $customer['email']; ?></div>
				<br/>

				<h2><?= _l("Default Shipping Address"); ?></h2>

				<div class="shipping-address"><?= $shipping_address['display']; ?></div>
				<br/>

				<h2><?= _l("Newsletter"); ?></h2>

				<div class="newsletter"><?= $newsletter_display; ?></div>
				<br/>

				<div class="center">
					<a class="button small account-edit" href="<?= $edit_account; ?>"><?= _l("Edit Information"); ?></a>
				</div>
			</div>
		</div>

	</div>

	<? if (!empty($data_subscriptions)) { ?>
		<div class="customer-subscription row">
			<div class="wrap">
				<div class="section col xs-12 md-6">
					<h2><?= _l("Subscriptions"); ?></h2>

					<div id="subscription-list">
						<? foreach ($data_subscriptions as $subscription) { ?>
							<? if ($subscription['status'] === Subscription::ACTIVE) { ?>
								<div class="subscription active">
									<div class="info">
										<div class="image left">
											<img src="<?= $subscription['thumb']; ?>"/>
										</div>
										<div class="info-text left">
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
								<div class="subscription on-hold">
									<div class="info">
										<div class="image left">
											<img src="<?= $subscription['thumb']; ?>"/>
										</div>
										<div class="info-text left">
											<div class="name"><?= $subscription['product']['name']; ?></div>
											<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
											<div class="price"><?= $subscription['total_display']; ?></div>
										</div>
									</div>
									<div class="clear on-hold_text"><?= _l("On Hold until %s", $subscription['resume_date']); ?></div>
									<div class="buttons">
										<a href="<?= $subscription['resume']; ?>" class="clear resume subscribe button"><?= _l("Resume"); ?></a>
										<a href="<?= $subscription['edit']; ?>" class="clear update small button"><?= _l("Manage Subscription"); ?></a>
									</div>
								</div>
							<? } ?>
						<? } ?>
					</div>
				</div>
			</div>
		</div>
	<? } ?>

	<div class="account-links row">
		<div class="wrap">
			<div class="col xs-12 md-8 center">
				<div class="left"><a href="<?= $back; ?>" class="button medium"><?= _l("Home"); ?></a></div>
				<div class="right">
					<a href="<?= $url_order_history; ?>" class="button medium"><?= _l("View Order History"); ?></a>
					<a href="<?= $url_returns; ?>" class="button medium"><?= _l("Product Returns"); ?></a>
				</div>
			</div>
		</div>
	</div>

	<?= _area('bottom'); ?>
</section>

<?= _call('common/footer'); ?>
