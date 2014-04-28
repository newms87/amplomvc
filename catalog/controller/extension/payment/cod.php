<?php

class Catalog_Controller_Extension_Payment_Cod extends Controller
{
	public function index()
	{
		//Action Buttons
		$data['confirm'] = site_url('extension/payment/cod/confirm', 'order_id=' . $this->order->getId());

		//Render
		$this->render('extension/payment/cod', $data);
	}

	public function confirm()
	{
		if (!empty($_GET['order_id'])) {
			$this->order->confirmOrder($_GET['order_id']);

			redirect('checkout/success', 'order_id=' . $_GET['order_id']);
		}

		redirect('common/home');
	}
}
