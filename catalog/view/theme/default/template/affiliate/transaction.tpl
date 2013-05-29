<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs(); ?>
	<h1><?= $heading_title; ?></h1>
	<p><?= $text_balance; ?><b> <?= $balance; ?></b>.</p>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?= $column_date_added; ?></td>
				<td class="left"><?= $column_description; ?></td>
				<td class="right"><?= $column_amount; ?></td>
			</tr>
		</thead>
		<tbody>
			<? if ($transactions) { ?>
			<? foreach ($transactions	as $transaction) { ?>
			<tr>
				<td class="left"><?= $transaction['date_added']; ?></td>
				<td class="left"><?= $transaction['description']; ?></td>
				<td class="right"><?= $transaction['amount']; ?></td>
			</tr>
			<? } ?>
			<? } else { ?>
			<tr>
				<td class="center" colspan="5"><?= $text_empty; ?></td>
			</tr>
			<? } ?>
		</tbody>
	</table>
	<div class="pagination"><?= $pagination; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<?= $content_bottom; ?></div>
<?= $footer; ?>