<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Flashsales Viewed Report"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $reset; ?>';" class="button"><?= _l("Reset"); ?></a>
			</div>
		</div>
		<div class="section">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Flashsale Title"); ?></td>
						<td class="left"><?= _l("Flashsale Start Date"); ?></td>
						<td class="left"><?= _l("Flashsale End Date"); ?></td>
						<td class="right"><?= _l("Viewed"); ?></td>
						<td class="right"><?= _l("Unique Users (By IP Address)"); ?></td>
						<td class="right"><?= _l("Unique Users (By session and ID)"); ?></td>
						<td class="right"><?= _l("Unique Users (By session, ID, and IP)"); ?></td>
						<td class="right"><?= _l("Percent"); ?></td>
					</tr>
				</thead>
				<tbody>
					<? if ($flashsales) { ?>
						<? foreach ($flashsales as $flashsale) { ?>
							<tr>
								<td class="left"><?= $flashsale['name']; ?></td>
								<td class="left"><?= $flashsale['date_start']; ?></td>
								<td class="left"><?= $flashsale['date_end']; ?></td>
								<td class="right"><?= $flashsale['viewed']; ?></td>
								<td class="right"><?= $flashsale['ip_total']; ?></td>
								<td class="right"><?= $flashsale['user_total']; ?></td>
								<td class="right"><?= $flashsale['ip_user_total']; ?></td>
								<td class="right"><?= $flashsale['percent']; ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="4"><?= _l("No results!"); ?></td>
						</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $this->call('common/footer'); ?>
