<?php
class Catalog_Controller_Block_Cart_Voucher extends Controller
{
	public function index($settings = null)
	{
		$this->template->load('block/cart/voucher');

		$this->language->load('block/cart/shipping');

		if (isset($_POST['voucher']) && $this->validateVoucher()) {
			$this->session->set('voucher', $_POST['voucher']);

			$this->message->add('success', $this->_('text_voucher'));
		}

		$defaults = array(
			'voucher' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($this->session->data[$key])) {
				$this->data[$key] = $this->session->data[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Render
		$this->response->setOutput($this->render());
	}

	private function validateVoucher()
	{
		$voucher_info = $this->System_Model_Voucher->getVoucherByCode($_POST['voucher']);

		if (!$voucher_info) {
			$this->error['warning'] = $this->_('error_voucher');
		}

		return $this->error ? false : true;
	}
}
