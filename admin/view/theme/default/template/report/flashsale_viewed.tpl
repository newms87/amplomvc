<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $reset; ?>';" class="button"><?= $button_reset; ?></a></div>
		</div>
		<div class="content">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="left"><?= $column_date_start; ?></td>
						<td class="left"><?= $column_date_end; ?></td>
						<td class="right"><?= $column_viewed; ?></td>
						<td class="right"><?= $column_ip_views; ?></td>
						<td class="right"><?= $column_user_views; ?></td>
						<td class="right"><?= $column_ip_user_views; ?></td>
						<td class="right"><?= $column_percent; ?></td>
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