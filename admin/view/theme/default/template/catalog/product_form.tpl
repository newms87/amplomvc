<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'product.png'; ?>" alt=""/> <?= $head_title; ?></h1>

		<div class="buttons">
			<? if (count($data_product_classes) > 1) { ?>
				<form id="product_class_form" action="<?= $change_class; ?>" method="post">
					<? $this->builder->setConfig('product_class_id', 'name'); ?>
					<?= $this->builder->build('select', $data_product_classes, 'product_class_id', $product_class_id); ?>
					<input type="submit" class="button" value="<?= $button_change_class; ?>"/>
				</form>
			<? } ?>
			<a onclick="$('#product_form').submit();" class="button"><?= $button_save; ?></a>
			<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
		</div>
	</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
				<a href="#tab-data"><?= $tab_data; ?></a>
				<a href="#tab-shipping-return"><?= $tab_shipping_return; ?></a>
				<a href="#tab-links"><?= $tab_links; ?></a>
				<a href="#tab-attribute"><?= $tab_attribute; ?></a>
				<a href="#tab-option"><?= $tab_option; ?></a>
				<a href="#tab-discount"><?= $tab_discount; ?></a>
				<a href="#tab-special"><?= $tab_special; ?></a>
				<a href="#tab-image"><?= $tab_image; ?></a>
				<a href="#tab-reward"><?= $tab_reward; ?></a>
				<a href="#tab-design"><?= $tab_design; ?></a>
			</div>

			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="product_form">
				<input type="hidden" name="product_class_id" value="<?= $product_class_id; ?>" />

				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" size="100" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_meta_description; ?></td>
							<td><textarea name="meta_description" cols="40" rows="5"><?= $meta_description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_meta_keywords; ?></td>
							<td><textarea name="meta_keywords" cols="40" rows="5"><?= $meta_keywords; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_teaser; ?></td>
							<td><textarea name="teaser" class="ckedit"><?= $teaser; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_description; ?></td>
							<td><textarea name="description" class="ckedit"><?= $description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_information; ?></td>
							<td><textarea name="information" class="ckedit"><?= $information; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_tag; ?></td>
							<td><input type="text" name="product_tags" value="<?= implode(',', $product_tags); ?>" size="80"/></td>
						</tr>
					</table>
				</div><!-- /tab-general -->

				<div id="tab-data">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_model; ?></td>
							<td>
								<input type="text" name="model" value="<?= $model; ?>"/>
								<a class='gen_url' onclick='generate_model(this)'><?= $button_generate_model; ?></a>
							</td>
						</tr>
						<tr>
							<td class="required"><?= $entry_alias; ?></td>
							<td>
								<input type="text" onfocus="$(this).display_error('<?= $warning_generate_url; ?>', 'gen_url');" name="alias" value="<?= $alias; ?>"/>
								<a class='gen_url' onclick='generate_url(this)'><?= $button_generate_url; ?></a>
							</td>
						</tr>
						<tr>
							<td><?= $entry_upc; ?></td>
							<td><input type="text" name="upc" value="<?= $upc; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_location; ?></td>
							<td><input type="text" name="location" value="<?= $location; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_price; ?></td>
							<td><input type="text" name="price" value="<?= $price; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_cost; ?></td>
							<td><input type="text" name="cost" value="<?= $cost; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_tax_class; ?></td>
							<td>
								<? $this->builder->setConfig('tax_class_id', 'title'); ?>
								<?= $this->builder->build('select', $data_tax_classes, 'tax_class_id', $tax_class_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_quantity; ?></td>
							<td><input type="text" name="quantity" value="<?= $quantity; ?>" size="2"/></td>
						</tr>
						<tr>
							<td><?= $entry_minimum; ?></td>
							<td><input type="text" name="minimum" value="<?= $minimum; ?>" size="2"/></td>
						</tr>
						<tr>
							<td><?= $entry_subtract; ?></td>
							<td>
								<?= $this->builder->build('select', $data_yes_no, "subtract", (int)$subtract); ?>
								<input type='hidden' name='stock_status_id' value='<?= $stock_status_id; ?>'/>
							</td>
						</tr>
						<tr>
							<td><?= $entry_image; ?></td>
							<td>
								<?= $this->builder->setBuilderTemplate('click_image'); ?>
								<?= $this->builder->imageInput("image", $image); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_date_available; ?></td>
							<td>
								<input type="text" name="date_available" value="<?= $date_available; ?>" size="12" class="datetimepicker"/>
							</td>
						</tr>
						<tr>
							<td><?= $entry_date_expires; ?></td>
							<td>
								<input type="text" name="date_expires" value="<?= $date_expires; ?>" size="12" class="datetimepicker"/>
							</td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $statuses, "status", $status); ?></td>
						</tr>
						<tr>
							<td><?= $entry_editable; ?></td>
							<td><?= $this->builder->build('select', $data_yes_no, 'editable', $editable); ?></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="2"/></td>
						</tr>
					</table>
				</div><!-- /tab-data -->

				<div id="tab-shipping-return">
					<table class="form">
						<tr>
							<td><?= $entry_return_policy; ?></td>
							<td>
								<? if (!empty($data_return_policies)) { ?>
									<? $this->builder->setConfig(false, 'title'); ?>
									<?= $this->builder->build('select', $data_return_policies, 'return_policy_id', $return_policy_id); ?>
								<? } ?>
								<p><?= $text_add_return_policy; ?></p>
							</td>
						</tr>
						<tr>
							<td><?= $entry_shipping; ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, "shipping", (int)$shipping); ?></td>
						</tr>
					</table>
					<table class="form" id="shipping_details">
						<tr>
							<td><?= $entry_shipping_policy; ?></td>
							<td>
								<? if (!empty($data_shipping_policies)) { ?>
									<? $this->builder->setConfig(false, 'title'); ?>
									<?= $this->builder->build('select', $data_shipping_policies, 'shipping_policy_id', $shipping_policy_id); ?>
								<? } ?>
								<p><?= $text_add_shipping_policy; ?></p>
							</td>
						</tr>
						<tr>
							<td><?= $entry_dimension; ?></td>
							<td>
								<input type="text" name="length" value="<?= $length; ?>" size="4"/>
								<input type="text" name="width" value="<?= $width; ?>" size="4"/>
								<input type="text" name="height" value="<?= $height; ?>" size="4"/>
							</td>
						</tr>
						<tr>
							<td><?= $entry_length; ?></td>
							<td>
								<? $this->builder->setConfig('length_class_id', 'title'); ?>
								<?= $this->builder->build('select', $data_length_classes, 'length_class_id', $length_class_id); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_weight; ?></td>
							<td><input type="text" name="weight" value="<?= $weight; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_weight_class; ?></td>
							<td>
								<? $this->builder->setConfig('weight_class_id', 'title'); ?>
								<?= $this->builder->build('select', $data_weight_classes, 'weight_class_id', $weight_class_id); ?>
							</td>
						</tr>
					</table>
				</div><!-- tab-shipping-return -->

				<div id="tab-links">
					<table class="form">
						<tr>
							<td><?= $entry_manufacturer; ?></td>
							<? $this->builder->setConfig('manufacturer_id', 'name'); ?>
							<td><?= $this->builder->build('select', $data_manufacturers, 'manufacturer_id', (int)$manufacturer_id); ?></td>
						</tr>
						<tr>
							<td><?= $entry_category; ?></td>
							<? $this->builder->setConfig('category_id', 'pathname'); ?>
							<td><?= $this->builder->build('multiselect', $data_categories, "product_categories", $product_categories); ?></td>
						</tr>
						<tr>
							<td><?= $entry_store; ?></td>
							<td>
								<? $this->builder->setConfig('store_id', 'name'); ?>
								<?= $this->builder->build('multiselect', $data_stores, "product_stores", $product_stores); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_download; ?></td>
							<? $this->builder->setConfig('download_id', 'name'); ?>
							<td><?= $this->builder->build('multiselect', $data_downloads, "product_downloads", $product_downloads); ?></td>
						</tr>
						<tr>
							<td>
								<?= $entry_related; ?>
								<div class="left">
									<input type="text" id="related_autocomplete" value=""/>
									<div class="help center">(<?= $text_autocomplete; ?>)</div>
								</div>
							</td>
							<td>
								<div id="product_related_list" class="scrollbox">
									<? foreach ($product_related as $row => $related) { ?>
										<div class="product_related" data-row="<?= $row; ?>">
											<input type="hidden" name="product_related[]" value="<?= $related['product_id']; ?>"/>
											<span class="related_name"><?= $related['name']; ?></span>
											<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" class="delete" onclick="$(this).closest('.product_related').remove();"/>
										</div>
									<? } ?>
								</div>
							</td>
						</tr>
					</table>
				</div><!-- /tab-links -->

				<div id="tab-option">
					<div id="vtab-option" class="vtabs">
						<div id="option_tab_list">
							<? foreach ($product_options as $row => $product_option) { ?>
								<a href="#tab-option-<?= $row; ?>" class="option_tab_button" data-row="<?= $row; ?>">
									<span class="option_tab_title"><?= $product_option['name']; ?></span>
									<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="return remove_option($(this));"/>
								</a>
							<? } ?>
						</div>

						<div id="option-add">
							<input id="product_option_autocomplete" value=""/>
							<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="<?= $button_add_option; ?>" title="<?= $button_add_option; ?>"/>
						</div>
						<div class='help'><?= $text_option_help; ?></div>
					</div>

					<div id="product_option_list">
						<? foreach ($product_options as $row => $product_option) { ?>
							<div id="tab-option-<?= $row; ?>" class="product_option vtabs-content" data-row="<?= $row; ?>" data-id="<?= $product_option['option_id']; ?>">
								<input type="hidden" name="product_options[<?= $row; ?>][product_option_id]" value="<?= $product_option['product_option_id']; ?>"/>
								<input type="hidden" name="product_options[<?= $row; ?>][name]" value="<?= $product_option['name']; ?>"/>
								<input type="hidden" name="product_options[<?= $row; ?>][display_name]" value="<?= $product_option['display_name']; ?>"/>
								<input type="hidden" name="product_options[<?= $row; ?>][option_id]" value="<?= $product_option['option_id']; ?>"/>
								<input type="hidden" name="product_options[<?= $row; ?>][type]" value="<?= $product_option['type']; ?>"/>
								<input type="hidden" name="product_options[<?= $row; ?>][group_type]" value="<?= $product_option['group_type']; ?>"/>

								<div class="product_option_name"><?= $product_option['display_name']; ?></div>
								<table class="form">
									<tr>
										<td><?= $entry_required; ?></td>
										<td><?= $this->builder->build('select', $data_yes_no, "product_options[$row][required]", (int)$product_option['required']); ?></td>
									</tr>
									<tr>
										<td><?= $entry_sort_order; ?></td>
										<td><input type="text" name="product_options[<?= $row; ?>][sort_order]" value="<?= $product_option['sort_order']; ?>"/></td>
									</tr>
									<tr>
										<td><?= $entry_option_value_list; ?></td>
										<td>
											<div class='scrollbox unused_option_value_list clickable'>
												<? foreach ($product_option['unused_option_values'] as $uov_row => $option_value) { ?>
													<div class="unused_option_value" data-row="<?= $uov_row; ?>" data-id="<?= $option_value['option_value_id']; ?>" onclick="add_option_value($(this));">
														<span class="uov_label"><?= $option_value['value']; ?></span>
														<img src="<?= HTTP_THEME_IMAGE . "add.png"; ?>"/>
														<script type="text/javascript">//<!--
															$('#tab-option-<?= $row; ?> .unused_option_value[data-id=<?= $option_value['option_value_id']; ?>]').data('option_value', <?= json_encode($option_value); ?>);
														//--></script>
													</div>
												<? } ?>
											</div>
										</td>
									</tr>
								</table>
								<table class="list">
									<thead>
										<tr>
											<td class="center"><?= $entry_option_value; ?></td>
											<td class="center"><?= $entry_default; ?></td>
											<td class="center"><?= $entry_image; ?></td>
											<td class="center"><?= $entry_display_value; ?></td>
											<td class="center"><?= $entry_quantity; ?></td>
											<td class="center"><?= $entry_pricing; ?></td>
											<td class="center"><?= $entry_weight; ?></td>
											<td class="center"><?= $entry_sort_order; ?></td>
											<td class="center"><?= $entry_option_value_restriction; ?></td>
											<td></td>
										</tr>
									</thead>
									<tbody class='product_option_value_list'>
										<? if (!empty($product_option['product_option_values'])) { ?>
											<? foreach ($product_option['product_option_values'] as $pov_row => $product_option_value) { ?>
												<? $product_option_value_row = "product_options[$row][product_option_values][$pov_row]"; ?>
												<tr class="product_option_value" data-row="<?= $pov_row; ?>">
													<td class="center">
														<input type="hidden" name="<?= $product_option_value_row; ?>[product_option_value_id]" value="<?= $product_option_value['product_option_value_id']; ?>"/>
														<input type="hidden" name="<?= $product_option_value_row; ?>[option_value_id]" value="<?= $product_option_value['option_value_id']; ?>"/>
														<input type="hidden" name="<?= $product_option_value_row; ?>[value]" value="<?= $product_option_value['value']; ?>"/>
														<span class='option_value_label'><?= $product_option_value['value']; ?></span>
													</td>
													<td class="center default_option">
														<? $type = $product_option['type'] === 'checkbox' ? 'checkbox' : 'radio'; ?>
														<input type="<?= $type; ?>" name="<?= $product_option_value_row; ?>[default]" value="1" <?= $product_option_value['default'] ? 'checked="checked"' : ''; ?> />
													</td>
													<td class="center">
														<? $this->builder->setBuilderTemplate('click_image_small'); ?>
														<?= $this->builder->imageInput($product_option_value_row . '[image]', $product_option_value['image'], null, null, $this->config->get('config_image_product_option_width'), $this->config->get('config_image_product_option_height')); ?>
													</td>
													<td class="center">
														<input type="text" size="50" name="<?= $product_option_value_row; ?>[display_value]" value="<?= $product_option_value['display_value']; ?>" />
													</td>
													<td class="center">
														<input type="text" name="<?= $product_option_value_row; ?>[quantity]" value="<?= $product_option_value['quantity']; ?>" size="3"/><br />
														<? $checked = $product_option_value['subtract'] ? 'checked="checked"' : ''; ?>
														<input id="subtractstock<?= "$row-$pov_row"; ?>" type="checkbox" <?= $checked; ?> name="<?= $product_option_value_row . "[subtract]"; ?>" value="1" />
														<label for="subtractstock<?= "$row-$pov_row"; ?>" class="subtract_stock"><?= $entry_subtract; ?></label>
													</td>
													<td class="center">
														<label for="cost<?= "$row-$pov_row"; ?>"><?= $entry_cost; ?></label>
														<input id="cost<?= "$row-$pov_row"; ?>" type="text" name="<?= $product_option_value_row; ?>[cost]" value="<?= $product_option_value['cost']; ?>" size="5"/><br/>
														<label for="price<?= "$row-$pov_row"; ?>"><?= $entry_price; ?></label>
														<input id="price<?= "$row-$pov_row"; ?>" type="text" name="<?= $product_option_value_row; ?>[price]" value="<?= $product_option_value['price']; ?>" size="5"/><br/>
														<label for="points<?= "$row-$pov_row"; ?>"><?= $entry_option_points; ?></label>
														<input id="points<?= "$row-$pov_row"; ?>" type="text" name="<?= $product_option_value_row; ?>[points]" value="<?= $product_option_value['points']; ?>" size="5"/>
													</td>
													<td class="center">
														<input type="text" name="<?= $product_option_value_row; ?>[weight]" value="<?= $product_option_value['weight']; ?>" size="5"/>
													</td>
													<td class="center">
														<input type="text" class="sort_order" name="<?= $product_option_value_row; ?>[sort_order]" value="<?= $product_option_value['sort_order']; ?>" size="5"/>
													</td>
													<td class="center">
														<? /** ?>
														<!--<table class='list'>
															<thead>
															<tr>
																<td class="center"><?= $entry_restriction_option_value; ?></td>
																<td class="center"><?= $entry_restriction_quantity; ?></td>
																<td></td>
															</tr>
															</thead>
															<tbody class="product_option_value_restriction_list">
															<? if (!empty($product_option_value['restrictions'])) { ?>
																<? foreach ($product_option_value['restrictions'] as $r_row => $restriction) { ?>
																	<? $restriction_row = $product_option_value_row . "[restrictions][$r_row]"; ?>
																	<tr class="product_option_value_restriction" data-row="<?= $r_row; ?>">
																		<td class="center">
																			<? // $this->builder->setConfig('product_option_value_id', 'name'); ?>
																			<? // $this->builder->build('select', $all_product_option_values, $restriction_row."[restrict_option_value_id]", $restriction['restrict_option_value_id']); ?>
																		</td>
																		<td class="center"><input type="text" size='3' name="<?= $restriction_row; ?>[quantity]" value="<?= $restriction['quantity']; ?>"/></td>
																		<td class="center"><a onclick="$(this).closest('tr').remove()" class="button_remove"></a></td>
																	</tr>
																<? } ?>
															<? } ?>
															</tbody>
															<tfoot>
															<tr>
																<td colspan='2'></td>
																<td class="center"><a onclick="return add_restriction_value($(this))" class="button_add"></a></td>
															</tr>
															</tfoot>
														</table>
														-->
														*/ ?>
													</td>
													<td class="left">
														<span onclick="remove_option_value($(this))" class="button"><?= $button_remove; ?></span>
														<script type="text/javascript">//<!--
															$('#tab-option-<?= $row; ?> .product_option_value[data-row=<?= $pov_row; ?>]').data('option_value', <?= json_encode($product_option_value); ?>);
														//--></script>
													</td>
												</tr>
											<? } ?>
										<? } ?>
									</tbody>
								</table>
								<script type="text/javascript">//<!--
									$('#tab-option-<?= $row; ?>').data('option', <?= json_encode($product_option); ?>);
								//--></script>
							</div>
						<? } ?>
					</div>
				</div><!-- /tab-option -->

				<div id="tab-attribute">
					<div class="add_attribute">
						<span class="entry"><?= $entry_add_attribute; ?></span>

						<div>
							<input type="text" id="product_attribute_autocomplete" value=""/>
							<span class="help">(<?= $text_autocomplete; ?>)</span>
						</div>
					</div>

					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_attribute; ?></td>
								<td class="left"><?= $entry_image; ?></td>
								<td class="left"><?= $entry_text; ?></td>
								<td class="left"><?= $entry_sort_order; ?></td>
								<td></td>
							</tr>
						</thead>
						<tbody id="product_attribute_list">
							<? foreach ($product_attributes as $row => $product_attribute) { ?>
								<tr class="attribute" data-row="<?= $row; ?>" data-id="<?= $product_attribute['attribute_id']; ?>">
									<td class="left">
										<input type="hidden" name="product_attributes[<?= $row; ?>][attribute_id]" value="<?= $product_attribute['attribute_id']; ?>"/>
										<span class="attribute_name"><?= $product_attribute['name']; ?></span>
									</td>
									<td class="left">
										<div class="image">
											<? $this->builder->setBuilderTemplate('browse_clear'); ?>
											<?= $this->builder->imageInput("product_attributes[$row][image]", $product_attribute['image']); ?>
										</div>
									</td>
									<td class="left">
										<textarea name="product_attributes[<?= $row; ?>][text]" cols="40" rows="5"><?= $product_attribute['text']; ?></textarea>
									</td>
									<td>
										<input type="text" size="1" class="sort_order" name="product_attributes[<?= $row; ?>][sort_order]" value="<?= $product_attribute['sort_order']; ?>" />
									</td>
									<td class="left"><a onclick="$(this).closest('.attribute').remove()" class="button"><?= $button_remove; ?></a></td>
								</tr>
							<? } ?>
						</tbody>
					</table>
				</div><!-- /tab-attribute -->

				<div id="tab-discount">
					<table id="discount" class="list">
						<thead>
						<tr>
							<td class="left"><?= $entry_customer_group; ?></td>
							<td class="right"><?= $entry_quantity; ?></td>
							<td class="right"><?= $entry_priority; ?></td>
							<td class="right"><?= $entry_price; ?></td>
							<td class="left"><?= $entry_date_start; ?></td>
							<td class="left"><?= $entry_date_end; ?></td>
							<td></td>
						</tr>
						</thead>
						<tbody id="product_discount_list">
						<? foreach ($product_discounts as $row => $product_discount) { ?>
							<tr class="product_discount" data-row="<?= $row; ?>">
								<td class="left">
									<? $this->builder->setConfig('customer_group_id', 'name'); ?>
									<?= $this->builder->build('select', $data_customer_groups, "product_discounts[$row][customer_group_id]", $product_discount['customer_group_id']); ?>
								</td>
								<td class="right"><input type="text" name="product_discounts[<?= $row; ?>][quantity]" value="<?= $product_discount['quantity']; ?>" size="2"/></td>
								<td class="right"><input type="text" name="product_discounts[<?= $row; ?>][priority]" value="<?= $product_discount['priority']; ?>" size="2"/></td>
								<td class="right"><input type="text" name="product_discounts[<?= $row; ?>][price]" value="<?= $product_discount['price']; ?>"/></td>
								<td class="left"><input type="text" name="product_discounts[<?= $row; ?>][date_start]" value="<?= $product_discount['date_start']; ?>" class="datetimepicker"/></td>
								<td class="left"><input type="text" name="product_discounts[<?= $row; ?>][date_end]" value="<?= $product_discount['date_end']; ?>" class="datetimepicker"/></td>
								<td class="left"><a onclick="$(this).closest('.product_discount').remove();"
								                    class="button"><?= $button_remove; ?></a></td>
							</tr>
						<? } ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="6"></td>
							<td class="left"><a id="add_product_discount" class="button"><?= $button_add_discount; ?></a></td>
						</tr>
						</tfoot>
					</table>
				</div><!-- /tab-discount -->

				<div id="tab-special">
					<table id="special" class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_customer_group; ?></td>
								<td class="right"><?= $entry_priority; ?></td>
								<td class="right"><?= $entry_price; ?></td>
								<td class="left"><?= $entry_date_start; ?></td>
								<td class="left"><?= $entry_date_end; ?></td>
								<td></td>
							</tr>
						</thead>
						<tbody id="product_special_list">
							<? foreach ($product_specials as $row => $product_special) { ?>
								<tr class="product_special" data-row="<?= $row; ?>">
									<td class="left">
										<? $this->builder->setConfig('customer_group_id', 'name'); ?>
										<?= $this->builder->build('select', $data_customer_groups, "product_specials[$row][customer_group_id]", $product_special['customer_group_id']); ?>
									</td>
									<td class="right"><input type="text" name="product_specials[<?= $row; ?>][priority]" value="<?= $product_special['priority']; ?>" size="2"/></td>
									<td class="right"><input type="text" name="product_specials[<?= $row; ?>][price]" value="<?= $product_special['price']; ?>"/></td>
									<td class="left"><input type="text" name="product_specials[<?= $row; ?>][date_start]" value="<?= $product_special['date_start']; ?>" class="datetimepicker"/></td>
									<td class="left"><input type="text" name="product_specials[<?= $row; ?>][date_end]" value="<?= $product_special['date_end']; ?>" class="datetimepicker"/></td>
									<td class="left"><a onclick="$(this).closest('.product_special').remove();" class="button"><?= $button_remove; ?></a></td>
								</tr>
							<? } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5"></td>
								<td class="left"><a id="add_product_special" class="button"><?= $button_add_special; ?></a></td>
							</tr>
						</tfoot>
					</table>
				</div><!-- /tab-special -->

				<div id="tab-image">
					<table id="images" class="list">
						<thead>
							<tr>
								<td class="center"><?= $entry_image; ?></td>
								<td class="center"><?= $entry_sort_order; ?></td>
								<td></td>
							</tr>
						</thead>
						<tbody id="product_image_list">
							<? foreach ($product_images as $row => $product_image) { ?>
								<tr class="product_image" data-row="<?= $row; ?>">
									<td class="center">
										<?= $this->builder->imageInput("product_images[$row][image]", $product_image['image']); ?>
									</td>
									<td class="center"><input class="sort_order" type="text" name="product_images[<?= $row; ?>][sort_order]" value="<?= $product_image['sort_order']; ?>" size="2"/></td>
									<td class="left"><a onclick="$(this).closest('.product_image').remove();"
											class="button"><?= $button_remove; ?></a></td>
								</tr>
							<? } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2"></td>
								<td><a class="button" onclick="add_product_image()"><?= $button_add_image; ?></a></td>
							</tr>
						</tfoot>
					</table>
				</div> <!-- /tab-image -->

				<div id="tab-reward">
					<table class="form">
						<tr>
							<td><?= $entry_points; ?></td>
							<td><input type="text" name="points" value="<?= $points; ?>"/></td>
						</tr>
					</table>
					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_customer_group; ?></td>
								<td class="right"><?= $entry_reward; ?></td>
							</tr>
						</thead>
						<tbody>
							<? foreach ($data_customer_groups as $customer_group) { ?>
								<tr>
									<td class="left"><?= $customer_group['name']; ?></td>
									<td class="right"><input type="text" name="product_rewards[<?= $customer_group['customer_group_id']; ?>][points]" value="<?= isset($product_rewards[$customer_group['customer_group_id']]) ? $product_rewards[$customer_group['customer_group_id']]['points'] : ''; ?>"/>
									</td>
								</tr>
							<? } ?>
						</tbody>
					</table>
				</div> <!-- /tab-reward -->

				<div id="tab-design">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= $entry_store; ?></td>
						<td class="left"><?= $entry_layout; ?></td>
						<td class="left"><?= $entry_template; ?></td>
					</tr>
					</thead>
					<tbody>
					<? foreach ($data_stores as $store) { ?>
						<tr>
							<td class="left"><?= $store['name']; ?></td>
							<td class="left">
								<? $this->builder->setConfig('layout_id', 'name'); ?>
								<?= $this->builder->build('select', $data_layouts, "product_layouts[$store[store_id]][layout_id]", isset($product_layouts[$store['store_id']]) ? $product_layouts[$store['store_id']] : ''); ?>
							</td>
							<td class="left">
								<? foreach ($data_templates as $theme => $template) { ?>
									<label class="product_template">
										<div><?= $theme; ?></div>
										<?= $this->builder->build('select', $template, "product_templates[$store[store_id]][$theme][template]", isset($product_templates[$store['store_id']][$theme]['template']) ? $product_templates[$store['store_id']][$theme]['template'] : ''); ?>
									</label>
								<? } ?>
							</td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</div><!-- /tab-design -->

			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">//<!--
var related_list = $('#product_related_list');
related_list.ac_template('related_list', {unique: 'product_id'});

$('#related_autocomplete').autocomplete({
	delay: 0,
	source: function (request, response) {
		filter = {name: request.term};
		$.get("<?= $url_autocomplete; ?>", {filter: filter}, response, 'json');
	},
	select: function (event, data) {
		if (data.item.value && (related_row = $.ac_template('related_list', 'add', data.item))) {
			related_row.find('.related_name').html(data.item.name);
		}

		$(this).val('');

		return false;
	}
});
//--></script>

<script type="text/javascript">//<!--
$('.product_option_value_list [type=radio]').change(function(){
	//We need this check for AC_template validation (changes input type, originally always radio)
	if ($(this).is('input[type=radio]')) {
		$(this).closest('.product_option_value_list').find('[type=radio]').prop('checked',false);
		$(this).prop('checked', true);
	}
});

$.ac_datepicker();

function generate_url(context) {
	$.clear_errors('gen_url');

	name = $('input[name=name]').val();

	if (!name) {
		alert("Please make a name for this product before generating the URL");
	}
	else {
		data = {product_id:<?= (int)$product_id; ?>, name: name};
		$(context).fade_post("<?= $url_generate_url; ?>", data, function (json) {
			$('input[name="alias"]').val(json);
		});
	}
}
function generate_model(context) {
	name = $('input[name=name]').val();

	if (!name) {
		alert("Please make a name for this product before generating the Model ID");
	} else {
		data = {product_id:<?= $product_id; ?>, name: name};
		$(context).fade_post("<?= $url_generate_model; ?>", data, function (json) {
			$('input[name="model"]').val(json);
		});
	}
}
//--></script>

<script type="text/javascript">//<!--
$('[name=shipping]').change(function () {
	if ($(this).is(':checked')) {
		if ($(this).val() === '1') {
			$('#shipping_details').show();
		} else {
			$('#shipping_details').hide();
		}
	}
}).change();
//--></script>

<script type="text/javascript">//<!--
var attribute_list = $('#product_attribute_list');
attribute_list.ac_template('attribute_list', {unique: 'attribute_id'});

$('#product_attribute_autocomplete').autocomplete({
	delay: 0,
	source: function (request, response) {
		filter = {name: request.term};
		$.get("<?= $url_attribute_autocomplete; ?>", {filter: filter}, response, 'json');
	},
	select: function (event, data) {
		if (data.item.value && (attribute_row = $.ac_template('attribute_list', 'add', data.item))) {
			attribute_row.find('.attribute_name').html(data.item.name);

			if (data.item.thumb) {
				attribute_row.find('.image .iu_thumb').attr('src', data.item.thumb);
			}
		}

		$(this).val('');

		attribute_list.update_index('.sort_order');

		return false;
	}
});

attribute_list.sortable({cursor:'move', stop: function(){
	attribute_list.update_index('.sort_order');
}});
//--></script>

<script type="text/javascript">//<!--
var po_list = $('#product_option_list');
//po_list.find('.product_option_value_restriction_list').ac_template('povr_list');
po_list.find('.unused_option_value_list').ac_template('uov_list', {unique: 'option_value_id'});
po_list.find('.product_option_value_list').ac_template('pov_list', {defaults: <?= json_encode($product_options['__ac_template__']['product_option_values']['__ac_template__']); ?>});
$('#option_tab_list').ac_template('option_tabs');
po_list.ac_template('po_list', {unique: 'option_id', defaults: <?= json_encode($product_options['__ac_template__']); ?>});

$('#product_option_autocomplete').autocomplete({
	delay: 0,
	source: function (request, response) {
		filter = {name: request.term};
		$.get("<?= $url_option_autocomplete; ?>", {filter: filter}, response, 'json');
	},
	select: autocomplete_callback_product_option
});

function autocomplete_callback_product_option(event, data) {
	$(this).val('');

	if (!data.item.value) return false;

	product_option = $.ac_template('po_list', 'add', data.item);

	product_option.data('option', data.item);

	if (!product_option) {
		//If false, the product option already exists
		if (product_option === false) {
			//Click tab for this product option
			$('[href=#' + $('.product_option[data-id=' + data.item.option_id + ']').attr('id') + ']').click();
		}

		return false;
	}

	tab = $.ac_template('option_tabs', 'add');

	tab.attr('href', '#tab-option-' + tab.attr('data-row'));
	tab.find('.option_tab_title').html(data.item.name);

	product_option.attr('id', 'tab-option-' + tab.attr('data-row'));
	product_option.find('.product_option_value_list').sortable({stop: function () {
		$(this).update_index('.sort_order')
	} });
	product_option.find('.product_option_name').html(data.item.display_name);

	option_value_list = product_option.find('.unused_option_value_list');

	for (ov in data.item.option_values) {
		option_value = data.item.option_values[ov];

		ov_row = option_value_list.ac_template('uov_list', 'add', option_value);

		ov_row.data('option_value', option_value);
		ov_row.find('.uov_label').html(option_value.value);
	}

	$('#option_tab_list a').tabs();

	tab.click();

	return false;
}

function add_option_value(option_value) {
	var product_option = option_value.closest('.product_option');
	var pov_list = product_option.find('.product_option_value_list');
	var ov_data = option_value.data('option_value');

	row = pov_list.ac_template('pov_list', 'add', ov_data);
	row.find('.option_value_label').html(ov_data.value);

	if (ov_data.image) {
		row.find('.image .iu_thumb').attr('src', ov_data.thumb);
	}

	//Handle the default box
	var input_type = '';

	switch (product_option.data('option').type) {
		case 'checkbox':
			input_type = 'checkbox';
			break;
		default:
			input_type = 'radio';
			break;
	}

	row.find('.default_option input').attr('type', input_type);

	row.data('option_value', ov_data);
	pov_list.update_index();
	option_value.remove();
}

function add_restriction_value(context) {
	console.log(context.closest('table').find('.product_option_value_restriction_list'));

	console.log(context.closest('table').find('.product_option_value_restriction_list'));

	context.closest('table').find('.product_option_value_restriction_list').ac_template('povr_list', 'add');
	console.log('rstrict');
	console.log(context);
}

function remove_option(context) {
	tab = context.closest('a');
	$(tab.attr('href')).remove();
	tab.remove();

	$('#option_tab_list a:first').click();

	context.closest('.product_option_value_list').update_index();

	update_ov_entries_select();

	return false;
}

function remove_option_value(context) {
	var product_option = context.closest('.product_option');
	var row = context.closest('.product_option_value');
	var ov_data = row.data('option_value');

	//save image
	ov_data.image = row.find('.image .iu_image').val();
	ov_data.thumb = row.find('.image .iu_thumb').attr('src');

	row.remove();

	uov_row = product_option.find('.unused_option_value_list').ac_template('uov_list', 'add', ov_data);

	uov_row.data('option_value', ov_data);
	uov_row.find('.uov_label').html(ov_data.value);

	update_ov_entries_select();
}

$('.product_option_value_list').sortable({stop: function () {
	$(this).update_index('.sort_order');
}});

<? //TODO: Finish the product restrictions for new templating style ?>
function update_ov_entries_select() {
	new_options = '';
	$('.option_value_entries > tr').each(function (i, e) {
		name = $(e).find('.ov_entry_name').val();
		id = $(e).find('.ov_entry_option_value_id').val();
		new_options += '<option value="' + id + '">' + name + '</option>';
	});

	$('#all_product_option_values select, .restrict_entries select.restrict_option_values').each(function (i, e) {
		select = $(e).val();
		$(e).html(new_options);
		$(e).val(select);
	});
}
//--></script>

<script type="text/javascript">//<!--
$('#product_image_list').ac_template('image_list', {defaults: <?= json_encode($product_images['__ac_template__']); ?>});

function add_product_image() {
	$.ac_template('image_list', 'add');

	$('#product_image_list').update_index('.sort_order');
}
;

$('#product_image_list').sortable({cursor: 'move', stop: function () {
	$(this).update_index('.sort_order');
} });
//--></script>

<script type="text/javascript">//<!--
$('#product_discount_list').ac_template('discount_list', {defaults: <?= json_encode($product_discounts['__ac_template__']); ?>});

$('#add_product_discount').click(function () {
	$.ac_template('discount_list', 'add');
});
//--></script>

<script type="text/javascript">//<!--
$('#product_special_list').ac_template('special_list', {defaults: <?= json_encode($product_specials['__ac_template__']); ?>});

$('#add_product_special').click(function () {
	$.ac_template('special_list', 'add');
});
//--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
$('#option_tab_list > a').tabs();
//--></script>

<?= $this->builder->js('translations', $translations); ?>
<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
