<?php
class App_Controller_Block_Cart_Voucher extends Controller
{
	public function build($settings)
	{
		$settings += array(
			'voucher' => '',
		);

		//Render
		$this->response->setOutput($this->render('block/cart/voucher', $settings));
	}

	public function add_voucher()
	{
		if ($this->cart->addVoucher($_POST['voucher'])) {
			$this->message->add('success', _l("Your Voucher has been added to your order"));
		} else {
			$this->message->add('error', $this->cart->getError());
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('checkout/checkout');
		}
	}
}
