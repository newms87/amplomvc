<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= _l("My Account"); ?></h1>

		<div class="content_account content">
			<h2><?= _l("My Account"); ?></h2>
			<ul>
				<li><a href="<?= $update; ?>"><?= _l("Update your account information"); ?></a></li>
				<li><a href="<?= $password; ?>"><?= _l("Change your password"); ?></a></li>
				<li><a href="<?= $address; ?>"><?= _l("Modify your address book entries"); ?></a></li>
				<li><a href="<?= $wishlist; ?>"><?= _l("Modify your wish list"); ?></a></li>
			</ul>
		</div>
		<div class="content_account content">
			<h2><?= _l("My Orders"); ?></h2>
			<ul>
				<li><a href="<?= $order; ?>"><?= _l("View your order history"); ?></a></li>
				<li><a href="<?= $download; ?>"><?= _l("Downloads"); ?></a></li>
				<? if (!empty($reward)) { ?>
					<li><a href="<?= $reward; ?>"><?= _l("Your Reward Points"); ?></a></li>
				<? } ?>
				<li><a href="<?= $return_view; ?>"><?= _l("View your return requests"); ?></a></li>
				<li><a href="<?= $return_request; ?>"><?= _l("Return a product"); ?></a></li>
				<li><a href="<?= $transaction; ?>"><?= _l("Your Transactions"); ?></a></li>
			</ul>
		</div>
		<div class="content_account content">
			<h2><?= _l("Newsletter"); ?></h2>
			<ul>
				<li><a href="<?= $newsletter; ?>"><?= _l("Subscribe / unsubscribe to newsletter"); ?></a></li>
			</ul>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>