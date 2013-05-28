=====
				<tr>
							<td><?= $entry_category; ?></td>
							<? $this->builder->set_config('category_id', 'name');?>
							<td><?= $this->builder->build('multiselect', $data_categories, "product_category", $product_category);?></td>
						</tr>
-----
>>>>> {html}
				<tr>
							<td><?= $entry_collection; ?></td>
							<? $this->builder->set_config('collection_id', 'name');?>
							<td><?= $this->builder->build('multiselect', $data_collections, "product_collection", $product_collection);?></td>
						</tr>
-----