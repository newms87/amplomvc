<?php
class Catalog_Controller_Account_Voucher extends Controller
{
	public function index()
	{
		//Page Title
		$this->document->setTitle(_l("Purchase a Gift Certificate"));


		//TODO: Move this to cart Library
		if (!$this->session->has('vouchers')) {
			$this->session->set('vouchers', array());
		}

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			$this->session->set('vouchers', array(
				rand() => array(
					'description'      => sprintf(_l("%s Gift Certificate for %s"), $this->currency->format($this->currency->convert($_POST['amount'], $this->currency->getCode(), $this->config->get('config_currency'))), $_POST['to_name']),
					'to_name'          => $_POST['to_name'],
					'to_email'         => $_POST['to_email'],
					'from_name'        => $_POST['from_name'],
					'from_email'       => $_POST['from_email'],
					'voucher_theme_id' => $_POST['voucher_theme_id'],
					'message'          => $_POST['message'],
					'amount'           => $this->currency->convert($_POST['amount'], $this->currency->getCode(), $this->config->get('config_currency'))
				)
			));

			$this->url->redirect('account/voucher/success');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account'));
		$this->breadcrumb->add(_l("Gift Voucher"), $this->url->link('account/voucher'));

		//Template Data
		$data['min_value'] = $this->currency->format(1, false, 1);
		$data['max_value'] = $this->currency->format(1000, false, 1);

		//Action Buttons
		$data['action'] = $this->url->link('account/voucher');

		$voucher_info = array();

		if ($this->request->isPost()) {
			$voucher_info = $_POST;
		} elseif ($this->customer->isLogged()) {
			$customer_info = $this->customer->info();

			$voucher_info['from_name']  = $customer_info('firstname') . ' ' . $customer_info('lastname');
			$voucher_info['from_email'] = $customer_info('email');
		}

		$defaults = array(
			'to_name'          => '',
			'to_email'         => '',
			'from_name'        => '',
			'from_email'       => '',
			'voucher_theme_id' => '',
			'message'          => '',
			'amount'           => '25.00',
			'agree'            => false,
		);

		$data += $voucher_info + $defaults;

		//Template Data
		$data['voucher_themes'] = $this->Model_Cart_VoucherTheme->getVoucherThemes();

		//Render
		$this->response->setOutput($this->render('account/voucher', $data));
	}

	public function success()
	{
		//Page Title
		$this->document->setTitle(_l("Purchase a Gift Certificate"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Purchase a Gift Certificate"), $this->url->link('account/voucher'));

		//Action Buttons
		$data['continue'] = $this->url->link('cart/cart');

		//Render
		$this->response->setOutput($this->render('common/success', $data));
	}

	private function validate()
	{
		if ((strlen($_POST['to_name']) < 1) || (strlen($_POST['to_name']) > 64)) {
			$this->error['to_name'] = _l("Recipient's Name must be between 1 and 64 characters!");
		}

		if ((strlen($_POST['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['to_email'])) {
			$this->error['to_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if ((strlen($_POST['from_name']) < 1) || (strlen($_POST['from_name']) > 64)) {
			$this->error['from_name'] = _l("Your Name must be between 1 and 64 characters!");
		}

		if ((strlen($_POST['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['from_email'])) {
			$this->error['from_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!isset($_POST['voucher_theme_id'])) {
			$this->error['theme'] = _l("You must select a theme!");
		}

		if (($_POST['amount'] < 1) || ($_POST['amount'] > 1000)) {
			$this->error['amount'] = sprintf(_l("Amount must be between %s and %s!"), $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1) . ' ' . $this->currency->getCode());
		}

		if (!isset($_POST['agree'])) {
			$this->error['warning'] = _l("Warning: You must agree that the gift certificates are non-refundable!");
		}

		return $this->error ? false : true;
	}
}
