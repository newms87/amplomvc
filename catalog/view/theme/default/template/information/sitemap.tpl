<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div id="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>

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
					<li><a href="<?= $special; ?>"><?= $text_special; ?></a></li>
					<li><a href="<?= $account; ?>"><?= $text_account; ?></a>
						<ul>
							<li><a href="<?= $edit; ?>"><?= $text_edit; ?></a></li>
							<li><a href="<?= $password; ?>"><?= $text_password; ?></a></li>
							<li><a href="<?= $address; ?>"><?= $text_address; ?></a></li>
							<li><a href="<?= $history; ?>"><?= $text_history; ?></a></li>
							<li><a href="<?= $download; ?>"><?= $text_download; ?></a></li>
						</ul>
					</li>
					<li><a href="<?= $cart; ?>"><?= $text_cart; ?></a></li>
					<li><a href="<?= $checkout; ?>"><?= $text_checkout; ?></a></li>
					<li><a href="<?= $search; ?>"><?= $text_search; ?></a></li>
					<li><?= $text_information; ?>
						<ul>
							<? foreach ($informations as $information) { ?>
								<li><a href="<?= $information['href']; ?>"><?= $information['title']; ?></a></li>
							<? } ?>
							<li><a href="<?= $contact; ?>"><?= $text_contact; ?></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>