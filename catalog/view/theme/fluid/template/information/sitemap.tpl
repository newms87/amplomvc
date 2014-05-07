<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
	<div class="content">
		<?= breadcrumbs(); ?>
		<?= area('top'); ?>

		<h1><?= _l("Site Map"); ?></h1>

		<div class="sitemap-info">
			<div class="left">
				<ul>
					<? foreach ($categories as $category_1) { ?>
						<li><a href="<?= $category_1['href']; ?>"><?= $category_1['name']; ?></a>
							<? if ($category_1['children']) { ?>
								<ul>
									<? foreach ($category_1['children'] as $category_2) { ?>
										<li><a href="<?= $category_2['href']; ?>"><?= $category_2['name']; ?></a>
											<? if ($category_2['children']) { ?>
												<ul>
													<? foreach ($category_2['children'] as $category_3) { ?>
														<li><a href="<?= $category_3['href']; ?>"><?= $category_3['name']; ?></a></li>
													<? } ?>
												</ul>
											<? } ?>
										</li>
									<? } ?>
								</ul>
							<? } ?>
						</li>
					<? } ?>
				</ul>
			</div>
			<div class="right">
				<ul>
					<li><a href="<?= $special; ?>"><?= _l("Special Offers"); ?></a></li>
					<li><a href="<?= $account; ?>"><?= _l("My Account"); ?></a>
						<ul>
							<li><a href="<?= $edit; ?>"><?= _l("Account Information"); ?></a></li>
							<li><a href="<?= $password; ?>"><?= _l("Password"); ?></a></li>
							<li><a href="<?= $address; ?>"><?= _l("Address Book"); ?></a></li>
							<li><a href="<?= $history; ?>"><?= _l("Order History"); ?></a></li>
							<li><a href="<?= $download; ?>"><?= _l("Downloads"); ?></a></li>
						</ul>
					</li>
					<li><a href="<?= $cart; ?>"><?= _l("Shopping Cart"); ?></a></li>
					<li><a href="<?= $checkout; ?>"><?= _l("Checkout"); ?></a></li>
					<li><a href="<?= $search; ?>"><?= _l("Search"); ?></a></li>
					<li><?= _l("Information"); ?>
						<ul>
							<? foreach ($informations as $information) { ?>
								<li><a href="<?= $information['href']; ?>"><?= $information['title']; ?></a></li>
							<? } ?>
							<li><a href="<?= $contact; ?>"><?= _l("Contact Us"); ?></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>

		<?= area('bottom'); ?>
	</div>

<?= call('common/footer'); ?>
