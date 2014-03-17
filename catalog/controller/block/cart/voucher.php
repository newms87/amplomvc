<?php
class Catalog_Controller_Block_Cart_Voucher extends Controller
{
	public function index($settings = null)
	{
		$this->view->load('block/cart/voucher');

		if (isset($_POST['voucher']) && $this->validateVoucher()) {
			$this->session->set('voucher', $_POST['voucher']);

			$this->message->add('success', _l("Success: Your gift voucher discount has been applied!"));
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
			$this->error['warning'] = _l("Warning: Gift Voucher is either invalid or the balance has been used up!");
		}

		return $this->error ? false : true;
	}
}
