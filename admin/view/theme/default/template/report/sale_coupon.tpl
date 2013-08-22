<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= $head_title; ?></h1>
			</div>
			<div class="content">
				<table class="form">
					<tr>
						<td><?= $entry_date_start; ?>
							<input type="text" name="filter_date_start" value="<?= $filter_date_start; ?>" id="date-start"
							       size="12"/></td>
						<td><?= $entry_date_end; ?>
							<input type="text" name="filter_date_end" value="<?= $filter_date_end; ?>" id="date-end"
							       size="12"/></td>
						<td style="text-align: right;"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
					</tr>
				</table>
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="left"><?= $column_code; ?></td>
						<td class="right"><?= $column_orders; ?></td>
						<td class="right"><?= $column_total; ?></td>
						<td class="right"><?= $column_action; ?></td>
					</tr>
					</thead>
					<tbody>
					<? if ($coupons) { ?>
						<? foreach ($coupons as $coupon) { ?>
							<tr>
								<td class="left"><?= $coupon['name']; ?></td>
								<td class="left"><?= $coupon['code']; ?></td>
								<td class="right"><?= $coupon['orders']; ?></td>
								<td class="right"><?= $coupon['total']; ?></td>
								<td class="right"><? foreach ($coupon['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="6"><?= $text_no_results; ?></td>
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
			url = "<?= HTTP_ADMIN . "index.php?route=report/sale_order"; ?>";

			var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

			if (filter_date_start) {
				url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
			}

			var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');

			if (filter_date_end) {
				url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
			}

			location = url;
		}
//--></script>
	<script type="text/javascript"><!--
		$(document).ready(function () {
			$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

			$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
		});
//--></script>
<?= $footer; ?>