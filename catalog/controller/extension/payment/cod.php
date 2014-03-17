<?php

class Catalog_Controller_Extension_Payment_Cod extends Controller
{
	public function index()
	{
		//Action Buttons
		$this->data['confirm'] = $this->url->link('extension/payment/cod/confirm', 'order_id=' . $this->order->getId());

		//The Template
		$this->view->load('extension/payment/cod');

		//Render
		$this->render();
	}

	public function confirm()
	{
		if (!empty($_GET['order_id'])) {
			$this->order->confirmOrder($_GET['order_id']);

			$this->url->redirect('checkout/success', 'order_id=' . $_GET['order_id']);
		}

		$this->url->redirect('common/home');
	}
}
