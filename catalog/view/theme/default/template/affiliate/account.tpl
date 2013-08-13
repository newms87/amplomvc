<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>
	<h2><?= $text_my_account; ?></h2>
	<div class="section">
		<ul>
			<li><a href="<?= $edit; ?>"><?= $text_edit; ?></a></li>
			<li><a href="<?= $password; ?>"><?= $text_password; ?></a></li>
			<li><a href="<?= $payment; ?>"><?= $text_payment; ?></a></li>
		</ul>
	</div>
	<h2><?= $text_my_tracking; ?></h2>
	<div class="section">
		<ul>
			<li><a href="<?= $tracking; ?>"><?= $text_tracking; ?></a></li>
		</ul>
	</div>
	<h2><?= $text_my_transactions; ?></h2>
	<div class="section">
		<ul>
			<li><a href="<?= $transaction; ?>"><?= $text_transaction; ?></a></li>
		</ul>
	</div>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>