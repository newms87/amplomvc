<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
	<div class="content">
		<?= _breadcrumbs(); ?>
		<?= _area('top'); ?>

		<h1><?= _l("Your Reward Points"); ?></h1>

		<p><?= _l("Your total number of reward points is:"); ?><b> <?= $total; ?></b>.</p>
		<table class="list">
			<thead>
				<tr>
					<td class="left"><?= _l("Date Added"); ?></td>
					<td class="left"><?= _l("Description"); ?></td>
					<td class="right"><?= _l("Points"); ?></td>
				</tr>
			</thead>
			<tbody>
				<? if ($rewards) { ?>
					<? foreach ($rewards as $reward) { ?>
						<tr>
							<td class="left"><?= $reward['date_added']; ?></td>
							<td class="left"><? if ($reward['order_id']) { ?>
									<a href="<?= $reward['href']; ?>"><?= $reward['description']; ?></a>
								<? } else { ?>
									<?= $reward['description']; ?>
								<? } ?></td>
							<td class="right"><?= $reward['points']; ?></td>
						</tr>
					<? } ?>
				<? } else { ?>
					<tr>
						<td class="center" colspan="5"><?= _l("You do not have any reward points!"); ?></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
		<div class="pagination"><?= $pagination; ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= _area('bottom'); ?>
	</div>

<?= _call('common/footer'); ?>
