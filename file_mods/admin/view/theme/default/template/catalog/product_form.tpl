=====
				<tr>
					<td><?= $entry_category; ?></td>
					<? $this->builder->set_config('category_id', 'pathname'); ?>
					<td><?= $this->builder->build('multiselect', $data_categories, "product_categories", $product_categories); ?></td>
				</tr>
-----
>>>>> {html}
				<tr>
					<td><?= $entry_collection; ?></td>
					<? $this->builder->set_config('collection_id', 'name');?>
					<td><?= $this->builder->build('multiselect', $data_collections, "product_collection", $product_collection); ?></td>
				</tr>
-----