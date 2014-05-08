<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="account-manage-page" class="content">

	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<h1><?= _l("Account Manager"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="customer-info row">
		<div class="wrap">
			<div class="section col xs-12 <?= !empty($subscriptions) ? 'md-6' : 'md-12'; ?>">
				<h2><?= _l("Customer Information"); ?></h2>

				<div class="name"><?= $customer['firstname'] . ' ' . $customer['lastname']; ?></div>
				<div class="phone"><?= $customer['telephone']; ?></div>
				<div class="email"><?= $customer['email']; ?></div>
				<br/>

				<h2><?= _l("Default Shipping Address"); ?></h2>

				<div class="shipping-address"><?= format('address', $shipping_address); ?></div>
				<br/>

				<h2><?= _l("Newsletter"); ?></h2>

				<div class="newsletter">
					<? if ($customer['newsletter']) { ?>
						<?= _l("Send me weekly updates from %s!", option('config_name')); ?>
					<? } else { ?>
						<?= _l("Do not send me any emails."); ?>
					<? } ?>
				</div>
				<br/>

				<div class="center">
					<a class="button small account-edit" href="<?= $edit_account; ?>"><?= _l("Edit Information"); ?></a>
				</div>
			</div>

			<? if (!empty($subscriptions)) { ?>
				<div class="customer-subscription col xs-12 md-6">
					<h2><?= _l("Subscriptions"); ?></h2>

					<div id="subscription-list">
						<? foreach ($subscriptions as $subscription) { ?>
							<? if ($subscription['status'] === Subscription::ACTIVE) { ?>
								<div class="subscription active">
									<div class="info">
										<div class="image left">
											<img src="<?= image($subscription['product']['image'], 240, 240); ?>"/>
										</div>
										<div class="info-text left">
											<div class="name"><?= $subscription['product']['name']; ?></div>
											<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
											<div class="price"><?= $subscription['total_display']; ?></div>
										</div>
									</div>
									<div class="buttons">
										<a href="<?= site_url('this-week'); ?>" class="button"><?= _l("This Week's Smoothie"); ?></a>
										<a href="<?= site_url('account/subscription', 'subscription_id=' . $subscription['customer_subscription_id']); ?>" class="update small button"><?= _l("Manage"); ?></a>
									</div>
								</div>
							<? } elseif ($subscription['status'] === Subscription::ON_HOLD) { ?>
								<div class="subscription on-hold">
									<div class="info">
										<div class="image left">
											<img src="<?= image($subscription['product']['image'], 240, 240); ?>"/>
										</div>
										<div class="info-text left">
											<div class="name"><?= $subscription['product']['name']; ?></div>
											<div class="teaser"><?= $subscription['product']['teaser']; ?></div>
											<div class="price"><?= $subscription['total_display']; ?></div>
										</div>
									</div>
									<div class="clear on-hold_text"><?= _l("On Hold until %s", format('date', $subscription['resume_date'], 'm/d/Y')); ?></div>
									<div class="buttons">
										<a href="<?= site_url('account/subscription/resume'); ?>" class="clear resume subscribe button"><?= _l("Resume"); ?></a>
										<a href="<?= site_url('account/subscription', 'subscription_id=' . $subscription['customer_subscription_id']); ?>" class="clear update small button"><?= _l("Manage"); ?></a>
									</div>
								</div>
							<? } ?>
						<? } ?>
					</div>
				</div>
			<? } ?>
		</div>

	</div>

	<div class="account-links row">
		<div class="wrap">
			<div class="col xs-12 md-8 center">
				<div class="left"><a href="<?= site_url('common/home'); ?>" class="button medium"><?= _l("Home"); ?></a></div>
				<div class="right">
					<a href="<?= site_url('account/order'); ?>" class="button medium"><?= _l("View Order History"); ?></a>
					<a href="<?= site_url('account/return'); ?>" class="button medium"><?= _l("Product Returns"); ?></a>
				</div>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>
