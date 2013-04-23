#<?php
//=====
class ModelCatalogProduct extends Model {
//.....
	public function getProduct($product_id) {
//.....
	   if ($result->num_rows) {
//-----
//>>>>> {php}
			if($collection_name = $collection = $this->model_catalog_collection->get_name($product_id)){
				$result->row['name'] = $collection_name;
			}
//-----
//=====
		}
//.....
	}
//.....
}
//-----