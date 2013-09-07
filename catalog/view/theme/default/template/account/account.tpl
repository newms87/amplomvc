<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>

		<div class="content_account content">
			<h2><?= $text_my_account; ?></h2>
			<ul>
				<li><a href="<?= $update; ?>"><?= $text_update; ?></a></li>
				<li><a href="<?= $password; ?>"><?= $text_password; ?></a></li>
				<li><a href="<?= $address; ?>"><?= $text_address; ?></a></li>
				<li><a href="<?= $wishlist; ?>"><?= $text_wishlist; ?></a></li>
			</ul>
		</div>
		<div class="content_account content">
			<h2><?= $text_my_orders; ?></h2>
			<ul>
				<li><a href="<?= $order; ?>"><?= $text_order; ?></a></li>
				<li><a href="<?= $download; ?>"><?= $text_download; ?></a></li>
				<? if ($reward) { ?>
					<li><a href="<?= $reward; ?>"><?= $text_reward; ?></a></li>
				<? } ?>
				<li><a href="<?= $return_view; ?>"><?= $text_return_view; ?></a></li>
				<li><a href="<?= $return_request; ?>"><?= $text_return_request; ?></a></li>
				<li><a href="<?= $transaction; ?>"><?= $text_transaction; ?></a></li>
			</ul>
		</div>
		<div class="content_account content">
			<h2><?= $text_my_newsletter; ?></h2>
			<ul>
				<li><a href="<?= $newsletter; ?>"><?= $text_newsletter; ?></a></li>
			</ul>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>