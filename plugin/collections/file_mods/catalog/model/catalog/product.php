#<?php
//=====
class Catalog_Model_Catalog_Product extends Model 
{
//.....
	public function getProduct($product_id)
	{
//.....
		if ($result->num_rows) {
//-----
//>>>>> {php}
			if ($collection_name = $collection = $this->Model_Catalog_Collection->get_name($product_id)) {
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