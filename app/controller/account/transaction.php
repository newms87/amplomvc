<?php
class App_Controller_Account_Transaction extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/transaction'));

			redirect('customer/login');
		}

		$this->document->setTitle(_l("Your Transactions"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Your Transactions"), site_url('account/transaction'));

		$data['amount'] = option('config_currency');

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$transaction_total = $this->Model_Account_Transaction->getTotalTransactions($data);

		$results = $this->Model_Account_Transaction->getTransactions($data);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], option('config_currency')),
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], 'short'),
			);
		}

		$this->pagination->init();
		$this->pagination->total  = $transaction_total;
		$data['pagination'] = $this->pagination->render();

		$data['total'] = $this->currency->format($this->customer->getBalance());

		$data['continue'] = site_url('account');

		$this->response->setOutput($this->render('account/transaction', $data));
	}
}
