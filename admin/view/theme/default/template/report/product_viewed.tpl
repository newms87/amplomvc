<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Products Viewed Report"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $reset; ?>';" class="button"><?= _l("Reset"); ?></a>
			</div>
		</div>
		<div class="section">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Product Name"); ?></td>
						<td class="left"><?= _l("Model"); ?></td>
						<td class="right"><?= _l("Viewed"); ?></td>
						<td class="right"><?= _l("Unique Users (By IP Address)"); ?></td>
						<td class="right"><?= _l("Unique Users (By session and ID)"); ?></td>
						<td class="right"><?= _l("Unique Users (By session, ID, and IP)"); ?></td>
						<td class="right"><?= _l("Percent"); ?></td>
					</tr>
				</thead>
				<tbody>
					<? if ($products) { ?>
						<? foreach ($products as $product) { ?>
							<tr>
								<td class="left"><?= $product['name']; ?></td>
								<td class="left"><?= $product['model']; ?></td>
								<td class="right"><?= $product['viewed']; ?></td>
								<td class="right"><?= $product['ip_total']; ?></td>
								<td class="right"><?= $product['user_total']; ?></td>
								<td class="right"><?= $product['ip_user_total']; ?></td>
								<td class="right"><?= $product['percent']; ?></td>
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
<?= _call('common/footer'); ?>
