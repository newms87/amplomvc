<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $reset; ?>';" class="button"><?= $button_reset; ?></a></div>
		</div>
		<div class="content">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="left"><?= $column_model; ?></td>
						<td class="right"><?= $column_viewed; ?></td>
						<td class="right"><?= $column_ip_views; ?></td>
						<td class="right"><?= $column_user_views; ?></td>
						<td class="right"><?= $column_ip_user_views; ?></td>
						<td class="right"><?= $column_percent; ?></td>
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
						<td class="center" colspan="4"><?= $text_no_results; ?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $footer; ?>