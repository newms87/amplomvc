<? set_page_info('title', _l("Tracescope Invoices")); ?>

<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section admin-client-invoice">
	<div class="box invoice-form">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<form id="generate-invoice" action="<?= site_url('admin/invoice/create'); ?>" method="post">
					<button><?= _l("Generate Invoice"); ?></button>
					<input type="hidden" name="customer_id" value="<?= $customer['customer_id']; ?>"/>
					<input type="hidden" name="meta_type" value="ts_order"/>

					<div class="batch xs-hidden"></div>
				</form>
			</div>
		</div>
		<div class="client-orders">
			<h1>
				<img src="<?= image(theme_dir(Tracescope::$scope . '/icon.png'), 18, 18); ?>"/>
				<?= _l("Generate Invoice for "); ?>
				<div class="client-id">
					<input id="customer-autocomplete" type="text" name="client" data-autocomplete value="<?= $customer['username']; ?>"/>
				</div>
			</h1>

			<?=
			block('widget/views', null, array(
				'path'  => 'admin/order/listing',
				'query' => $customer['customer_id'] ? array(
					'filter' => array(
						'client_id' => $customer['customer_id'],
						'invoiced'  => 0,
					)
				) : '',
				'group' => 'Client Orders',
			)); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var load_count = 0;
	var $form = $('.invoice-form');

	$('#customer-autocomplete').autocomplete({
		delay:  500,
		source: function (request, response) {
			filter = {username: request.term};
			var $this = $('#customer-autocomplete');

			load_count++;
			$this.addClass('loading');

			$.get($ac.site_url + 'admin/client/autocomplete', {filter: filter}, function (e) {
				if (load_count-- <= 1) {
					$this.removeClass('loading');
				}
				response(e);
			}, 'json');
		},
		select: customer_callback
	});

	function customer_callback(event, data) {
		$('#customer-autocomplete').val(data.item.username);
		filter_customer_id(data.item.client_id);

		return false;
	}

	function filter_customer_id(customer_id) {
		var $widget = $('.widget-listing');
		var $filter = $widget.find('.column-filter.client_id');

		$filter.find('.filter-type').removeClass('not').addClass('equals');
		$filter.find('[name]').val(customer_id);
		$form.find('[name=customer_id]').val(customer_id);
		$widget.find('.filter-button:first').click();
	}

	<? if (!empty($customer['customer_id'])) { ?>
	filter_customer_id(<?= (int)$customer['customer_id']; ?>)
	<? } ?>

	$('#generate-invoice').submit(function () {
		$(this).find('.batch').append($('[name="batch[]"]:checked').clone());
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
