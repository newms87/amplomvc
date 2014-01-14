<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _l("Your Transactions"); ?></h1>

	<p><?= _l("Your current balance is:"); ?><b> <?= $total; ?></b>.</p>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?= _l("Date Added"); ?></td>
				<td class="left"><?= _l("Description"); ?></td>
				<td class="right"><?= _l("Amount (%s)", $amount); ?></td>
			</tr>
		</thead>
		<tbody>
			<? if ($transactions) { ?>
				<? foreach ($transactions as $transaction) { ?>
					<tr>
						<td class="left"><?= $transaction['date_added']; ?></td>
						<td class="left"><?= $transaction['description']; ?></td>
						<td class="right"><?= $transaction['amount']; ?></td>
					</tr>
				<? } ?>
			<? } else { ?>
				<tr>
					<td class="center" colspan="5"><?= _l("You do not have any transactions!"); ?></td>
				</tr>
			<? } ?>
		</tbody>
	</table>
	<div class="pagination"><?= $pagination; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= $content_bottom; ?>
</div>

<?= $footer; ?>
