<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>

<div class="content">
	<?= $this->area->render('top'); ?>
	<?= $this->breadcrumb->render(); ?>

	<h1><?= _l("Return Information"); ?></h1>
	<table class="list">
		<thead>
			<tr>
				<td class="left" colspan="2"><?= _l("Return Details"); ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left" style="width: 50%;"><b><?= _l("RMA #:"); ?></b> #<?= $rma; ?><br/>
					<b><?= _l("Date Added:"); ?></b> <?= $date_added; ?></td>
				<td class="left" style="width: 50%;"><b><?= _l("Order ID:"); ?></b> #<?= $order_id; ?><br/>
					<b><?= _l("Order Date:"); ?></b> <?= $date_ordered; ?></td>
			</tr>
		</tbody>
	</table>
	<h2><?= _l("Product Information &amp; Reason for Return"); ?></h2>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?= _l("Product Name"); ?></td>
				<td class="left"><?= _l("Model"); ?></td>
				<td class="right"><?= _l("Quantity"); ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left"><?= $subscription['name']; ?></td>
				<td class="left"><?= $subscription['model']; ?></td>
				<td class="right"><?= $quantity; ?></td>
			</tr>
		</tbody>
	</table>
	<table class="list">
		<thead>
			<tr>
				<td class="left"><?= _l("Reason"); ?></td>
				<td class="left"><?= _l("Opened"); ?></td>
				<td class="left"><?= _l("Action"); ?></td>
				<td class="left"><?= _l("Status"); ?></td>
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
				<td class="left"><?= _l("Return Comments"); ?></td>
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
		<h2><?= _l("Return History"); ?></h2>
		<table class="list">
			<thead>
				<tr>
					<td class="left" style="width: 33.3%;"><?= _l("Date Added"); ?></td>
					<td class="left" style="width: 33.3%;"><?= _l("Status"); ?></td>
					<td class="left" style="width: 33.3%;"><?= _l("Comment"); ?></td>
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
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->call('common/footer'); ?>
