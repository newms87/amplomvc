<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Affiliate Commission Report"); ?></h1>
			</div>
			<div class="section">
				<table class="form">
					<tr>
						<td><?= _l("Date Start:"); ?>
							<input type="text" name="filter_date_start" value="<?= $filter_date_start; ?>" id="date-start"
							       size="12"/></td>
						<td><?= _l("Date End:"); ?>
							<input type="text" name="filter_date_end" value="<?= $filter_date_end; ?>" id="date-end"
							       size="12"/></td>
						<td style="text-align: right;"><a onclick="filter();" class="button"><?= _l("Filter"); ?></a></td>
					</tr>
				</table>
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= _l("Affiliate Name"); ?></td>
						<td class="left"><?= _l("E-Mail"); ?></td>
						<td class="left"><?= _l("Status"); ?></td>
						<td class="right"><?= _l("Commission"); ?></td>
						<td class="right"><?= _l("No. Orders"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
						<td class="right"><?= _l("Action"); ?></td>
					</tr>
					</thead>
					<tbody>
					<? if ($affiliates) { ?>
						<? foreach ($affiliates as $affiliate) { ?>
							<tr>
								<td class="left"><?= $affiliate['affiliate']; ?></td>
								<td class="left"><?= $affiliate['email']; ?></td>
								<td class="left"><?= $affiliate['status']; ?></td>
								<td class="right"><?= $affiliate['commission']; ?></td>
								<td class="right"><?= $affiliate['orders']; ?></td>
								<td class="right"><?= $affiliate['total']; ?></td>
								<td class="right"><? foreach ($affiliate['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="7"><?= _l("No results!"); ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>
	<script type="text/javascript"><!--
		function filter() {
			url = "<?= $url_affiliate_commission; ?>";

			var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

			if (filter_date_start) {
				url += '&filter_date_start=" + encodeURIComponent(filter_date_start);
			}

			var filter_date_end = $("input[name=\'filter_date_end\']').attr('value');

			if (filter_date_end) {
				url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
			}

			location = url;
		}
</script>
	<script type="text/javascript"><!--
		$(document).ready(function () {
			$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

			$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
		});
</script>
<?= $footer; ?>
