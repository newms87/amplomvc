<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
					<tr>
						<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
						</td>
						<td class="right"><? if ($sort == 'r.return_id') { ?>
								<a href="<?= $sort_return_id; ?>"
								   class="<?= strtolower($order); ?>"><?= $column_return_id; ?></a>
							<? } else { ?>
								<a href="<?= $sort_return_id; ?>"><?= $column_return_id; ?></a>
							<? } ?></td>
						<td class="right"><? if ($sort == 'r.order_id') { ?>
								<a href="<?= $sort_order_id; ?>" class="<?= strtolower($order); ?>"><?= $column_order_id; ?></a>
							<? } else { ?>
								<a href="<?= $sort_order_id; ?>"><?= $column_order_id; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'customer') { ?>
								<a href="<?= $sort_customer; ?>" class="<?= strtolower($order); ?>"><?= $column_customer; ?></a>
							<? } else { ?>
								<a href="<?= $sort_customer; ?>"><?= $column_customer; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'r.product') { ?>
								<a href="<?= $sort_product; ?>" class="<?= strtolower($order); ?>"><?= $column_product; ?></a>
							<? } else { ?>
								<a href="<?= $sort_product; ?>"><?= $column_product; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'r.model') { ?>
								<a href="<?= $sort_model; ?>" class="<?= strtolower($order); ?>"><?= $column_model; ?></a>
							<? } else { ?>
								<a href="<?= $sort_model; ?>"><?= $column_model; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'status') { ?>
								<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
							<? } else { ?>
								<a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'r.date_added') { ?>
								<a href="<?= $sort_date_added; ?>"
								   class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
							<? } else { ?>
								<a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'r.date_modified') { ?>
								<a href="<?= $sort_date_modified; ?>"
								   class="<?= strtolower($order); ?>"><?= $column_date_modified; ?></a>
							<? } else { ?>
								<a href="<?= $sort_date_modified; ?>"><?= $column_date_modified; ?></a>
							<? } ?></td>
						<td class="right"><?= $column_action; ?></td>
					</tr>
					</thead>
					<tbody>
					<tr class="filter">
						<td></td>
						<td align="right"><input type="text" name="filter_return_id" value="<?= $filter_return_id; ?>"
						                         size="4" style="text-align: right;"/></td>
						<td align="right"><input type="text" name="filter_order_id" value="<?= $filter_order_id; ?>" size="4"
						                         style="text-align: right;"/></td>
						<td><input type="text" name="filter_customer" value="<?= $filter_customer; ?>"/></td>
						<td><input type="text" name="filter_product" value="<?= $filter_product; ?>"/></td>
						<td><input type="text" name="filter_model" value="<?= $filter_model; ?>"/></td>
						<td>
							<? $this->builder->set_config(false, 'title'); ?>
							<?= $this->builder->build('select', $data_return_statuses, 'filter_return_status_id', $filter_return_status_id); ?>
						</td>
						<td><input type="text" name="filter_date_added" value="<?= $filter_date_added; ?>" size="12"
						           class="datepicker"/></td>
						<td><input type="text" name="filter_date_modified" value="<?= $filter_date_modified; ?>" size="12"
						           class="datepicker"/></td>
						<td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
					</tr>
					<? if ($returns) { ?>
						<? foreach ($returns as $return) { ?>
							<tr>
								<td style="text-align: center;"><? if ($return['selected']) { ?>
										<input type="checkbox" name="selected[]" value="<?= $return['return_id']; ?>"
										       checked="checked"/>
									<? } else { ?>
										<input type="checkbox" name="selected[]" value="<?= $return['return_id']; ?>"/>
									<? } ?></td>
								<td class="right"><?= $return['return_id']; ?></td>
								<td class="right"><?= $return['order_id']; ?></td>
								<td class="left"><?= $return['customer']; ?></td>
								<td class="left"><?= $return['product']; ?></td>
								<td class="left"><?= $return['model']; ?></td>
								<td class="left"><?= $return['status']; ?></td>
								<td class="left"><?= $return['date_added']; ?></td>
								<td class="left"><?= $return['date_modified']; ?></td>
								<td class="right"><? foreach ($return['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="10"><?= $text_no_results; ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
	function filter() {
		url = "<?= HTTP_ADMIN . "index.php?route=sale/return"; ?>";

		var filter_return_id = $('input[name=\'filter_return_id\']').attr('value');

		if (filter_return_id) {
			url += '&filter_return_id=' + encodeURIComponent(filter_return_id);
		}

		var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');

		if (filter_order_id) {
			url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
		}

		var filter_customer = $('input[name=\'filter_customer\']').attr('value');

		if (filter_customer) {
			url += '&filter_customer=' + encodeURIComponent(filter_customer);
		}

		var filter_product = $('input[name=\'filter_product\']').attr('value');

		if (filter_product) {
			url += '&filter_product=' + encodeURIComponent(filter_product);
		}

		var filter_model = $('input[name=\'filter_model\']').attr('value');

		if (filter_model) {
			url += '&filter_model=' + encodeURIComponent(filter_model);
		}

		var filter_return_status_id = $('select[name=\'filter_return_status_id\']').attr('value');

		if (filter_return_status_id != '*') {
			url += '&filter_return_status_id=' + encodeURIComponent(filter_return_status_id);
		}

		var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');

		if (filter_date_added) {
			url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
		}

		var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');

		if (filter_date_modified) {
			url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
		}

		location = url;
	}
//--></script>
<script type="text/javascript"><!--
	$.widget('custom.catcomplete', $.ui.autocomplete, {
		_renderMenu: function (ul, items) {
			var self = this, currentCategory = '';

			$.each(items, function (index, item) {
				if (item.category != currentCategory) {
					ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');

					currentCategory = item.category;
				}

				self._renderItem(ul, item);
			});
		}
	});

	$('input[name=\'filter_customer\']').catcomplete({
		delay: 0,
		source: function (request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/autocomplete"; ?>" + '&filter_name=' + encodeURIComponent(request.term),
				dataType: 'json',
				success: function (json) {
					response($.map(json, function (item) {
						return {
							category: item.customer_group,
							label: item.name,
							value: item.customer_id
						}
					}));
				}
			});
		},
		select: function (event, ui) {
			$('input[name=\'filter_customer\']').val(ui.item.label);

			return false;
		}
	});
//--></script>
<script type="text/javascript"><!--
	$(document).ready(function () {
		$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	});
//--></script>
<?= $footer; ?>