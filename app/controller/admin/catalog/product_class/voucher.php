<?php
class App_Controller_Admin_Catalog_ProductClass_Voucher extends App_Controller_Admin_Catalog_Product
{
	public function update()
	{
		$_POST['product_class'] = 'voucher';
		$_POST['shipping'] = 0;

		parent::update();
	}

	protected function getForm()
	{
		parent::getForm();
	}
}
