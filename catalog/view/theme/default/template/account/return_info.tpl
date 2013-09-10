<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

	<div class="content">
		<?= $content_top; ?>
		<?= $this->breadcrumb->render(); ?>

		<h1><?= $head_title; ?></h1>
		<table class="list">
			<thead>
			<tr>
				<td class="left" colspan="2"><?= $text_return_detail; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left" style="width: 50%;"><b><?= $text_rma_number; ?></b> #<?= $rma; ?><br/>
					<b><?= $text_date_added; ?></b> <?= $date_added; ?></td>
				<td class="left" style="width: 50%;"><b><?= $text_order_id; ?></b> #<?= $order_id; ?><br/>
					<b><?= $text_date_ordered; ?></b> <?= $date_ordered; ?></td>
			</tr>
			</tbody>
		</table>
		<h2><?= $text_product; ?></h2>
		<table class="list">
			<thead>
			<tr>
				<td class="left"><?= $column_product; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><?= $product['name']; ?></td>
				<td class="left"><?= $product['model']; ?></td>
				<td class="right"><?= $quantity; ?></td>
			</tr>
			</tbody>
		</table>
		<table class="list">
			<thead>
			<tr>
				<td class="left"><?= $column_reason; ?></td>
				<td class="left"><?= $column_opened; ?></td>
				<td class="left"><?= $column_action; ?></td>
				<td class="left"><?= $column_status; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><?= $reason['title']; ?></td>
				<td class="left"><?= $opened; ?></td>
				<td class="left"><?= $action; ?></td>
				<td class="left"><?= $return_status['title']; ?></td>
			</tr>
			</tbody>
		</table>
		<table class="list">
			<? if ($comment) { ?>
			<thead>
			<tr>
				<td class="left"><?= $text_comment; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><?= $comment; ?></td>
			</tr>
			</tbody>
		</table>
	<? } ?>
		<? if ($histories) { ?>
			<h2><?= $text_history; ?></h2>
			<table class="list">
				<thead>
				<tr>
					<td class="left" style="width: 33.3%;"><?= $column_date_added; ?></td>
					<td class="left" style="width: 33.3%;"><?= $column_status; ?></td>
					<td class="left" style="width: 33.3%;"><?= $column_comment; ?></td>
				</tr>
				</thead>
				<tbody>
				<? foreach ($histories as $history) { ?>
					<tr>
						<td class="left"><?= $history['date_added']; ?></td>
						<td class="left"><?= $history['status']; ?></td>
						<td class="left"><?= $history['comment']; ?></td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		<? } ?>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>