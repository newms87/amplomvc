=====
						<tr>
							<td><?= $entry_show_category_description; ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, 'config_show_category_description', $config_show_category_description); ?></td>
						</tr>
-----
>>>>> {html}
						<tr>
							<td><?= $entry_show_collection_image; ?></td>
							<td><?= $this->builder->build('select', $data_yes_no, 'config_show_collection_image', $config_show_collection_image); ?></td>
						</tr>
						<tr>
							<td><?= $entry_show_collection_description; ?></td>
							<td><?= $this->builder->build('select', $data_yes_no, 'config_show_collection_description', $config_show_collection_description); ?></td>
						</tr>
-----
=====
						<tr>
							<td class="required"> <?= $entry_image_category; ?></td>
							<td><input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>" size="3" />
								x
								<input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>" size="3" />
						</tr>
-----
>>>>> {html}
						<tr>
							<td class="required"> <?= $entry_image_collection; ?></td>
							<td><input type="text" name="config_image_collection_width" value="<?= $config_image_collection_width; ?>" size="3" />
								x
								<input type="text" name="config_image_collection_height" value="<?= $config_image_collection_height; ?>" size="3" />
						</tr>
-----