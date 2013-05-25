=====
<?php
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
//-----
//<<<<<
				$url_alias = array(
					'route' => 'product/manufacturer/product',
					'query' => 'manufacturer_id=' . (int)$manufacturer_id,
					'keyword' => $data['keyword'],
					'status' => $data['status'],
				);
//-----
//>>>>> {php}
				$url_alias = array(
					'route' => 'designers/designers',
					'query' => 'designer_id=' . (int)$manufacturer_id,
					'keyword' => $data['keyword'],
					'status' => $data['status'],
				);
//-----
//=====
	}
//.....
	public function editManufacturer($manufacturer_id, $data) {
//.....
				$this->model_setting_url_alias->deleteUrlAliasByRouteQuery('product/manufacturer/product', "manufacturer_id=$manufacturer_id");
//-----
//>>>>> {php}
				$this->model_setting_url_alias->deleteUrlAliasByRouteQuery('designers/designers', "designer_id=$manufacturer_id");
//-----
//<<<<<
				$url_alias = array(
					'route' => 'product/manufacturer/product',
					'query' => 'manufacturer_id=' . (int)$manufacturer_id,
					'keyword' => $data['keyword'],
					'status' => $data['status'],
				);
//-----
//>>>>> {php}
				$url_alias = array(
					'route' => 'designers/designers',
					'query' => 'designer_id=' . (int)$manufacturer_id,
					'keyword' => $data['keyword'],
					'status' => $data['status'],
				);
//-----
//=====
	}
//.....
	public function deleteManufacturer($manufacturer_id) {
//.....
		$this->model_setting_url_alias->deleteUrlAliasByRouteQuery('product/manufacturer/product', "manufacturer_id=$manufacturer_id");
//-----
//>>>>> {php}
		$this->model_setting_url_alias->deleteUrlAliasByRouteQuery('designers/designers', "designer_id=$manufacturer_id");
//-----
//=====
	}
//.....
}
//-----