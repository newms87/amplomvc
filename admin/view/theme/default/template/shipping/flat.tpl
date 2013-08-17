<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="content shipping_flat">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td><?= $entry_title; ?></td>
							<td><input type="text" name="flat_title" value="<?= $flat_title; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'flat_status', $flat_status); ?></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="flat_sort_order" value="<?= $flat_sort_order; ?>" size="1"/></td>
						</tr>
						<tr>
							<td valign="top">
								<?= $entry_flat_rates; ?><br/><br/>
								<a id="add_flat_rate"><?= $button_add_rate; ?></a>
							</td>
							<td>
								<? $flat_rates['template_row'] = array(
									'method'       => "%method%",
									'title'        => "%title%",
									'cost'         => "%cost%",
									'rule'         => array(
										'type'  => "%rule_type%",
										'value' => "%rule_value%"
									),
									'tax_class_id' => "%tax_class_id%",
									'geo_zone_id'  => "%geo_zone_id%",
								); ?>

								<div id="flat_rates">
									<? $rate_row = 0; ?>
									<? foreach ($flat_rates as $key => $rate) { ?>
										<? $row = $key == "template_row" ? "%rate_row%" : $rate_row; ?>

										<table class="form rate <?= $key; ?>">
											<tr>
												<td><?= $entry_method_title; ?></td>
												<td>
													<input type="hidden" name="flat_rates[<?= $row; ?>][method]"
													       value="<?= $rate['method']; ?>"/>
													<input type="text" name="flat_rates[<?= $row; ?>][title]"
													       value="<?= $rate['title']; ?>"/>
												</td>
											</tr>
											<tr>
												<td><?= $entry_cost; ?></td>
												<td><input type="text" name="flat_rates[<?= $row; ?>][cost]"
												           value="<?= $rate['cost']; ?>"/></td>
											</tr>
											<tr>
												<td><?= $entry_rule; ?></td>
												<td>
													<?= $this->builder->build('select', $data_rule_types, "flat_rates[$row][rule][type]", $rate['rule']['type']); ?>
													<input type="text" name="flat_rates[<?= $row; ?>][rule][value]"
													       value="<?= $rate['rule']['value']; ?>"/>
												</td>
											</tr>
											<tr>
												<td><?= $entry_tax_class; ?></td>
												<td>
													<? $this->builder->set_config('tax_class_id', 'title'); ?>
													<?= $this->builder->build('select', $data_tax_classes, "flat_rates[$row][tax_class_id]", $rate['tax_class_id']); ?>
												</td>
											</tr>
											<tr>
												<td><?= $entry_geo_zone; ?></td>
												<td>
													<? $this->builder->set_config('geo_zone_id', 'name'); ?>
													<?= $this->builder->build('select', $data_geo_zones, "flat_rates[$row][geo_zone_id]", $rate['geo_zone_id']); ?>
												</td>
											</tr>
											<tr>
												<td colspan="2"><a class="delete"
												                   onclick="$(this).closest('.rate').remove();"><?= $button_delete; ?></a>
												</td>
											</tr>
										</table>
										<? $rate_row++; ?>
									<? } ?>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">//<!--
		var list_template = $('#flat_rates').find('.template_row');
		var flat_rate_template = list_template.html();
		list_template.remove();

		var rate_row = <?= $rate_row; ?>;

		$('#add_flat_rate').click(function () {
			template = flat_rate_template
				.replace(/%rate_row%/g, rate_row++)
				.replace(/%method%/g, '')
				.replace(/%title%/g, '')
				.replace(/%cost%/g, '')
				.replace(/%rule_type%/g, '')
				.replace(/%rule_value%/g, '')
				.replace(/%tax_class_id%/g, '')
				.replace(/%geo_zone_id%/g, '');

			$('#flat_rates').append($('<table class="form rate" />').append(template));
		});
		//--></script>

<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>