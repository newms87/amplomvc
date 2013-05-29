//=====
<?php
class ControllerProductProduct extends Controller 
{
//.....
	public function index()
	{
//-----
//<<<<< {php}
		$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
//-----
//>>>>>
		$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('designers/designers', 'designer_id=' . $product_info['manufacturer_id']));
//-----
//=====
	}
//-----
//=====
}
//-----