<?php
class Catalog_Controller_Account_Transaction extends Controller
{
	public function index()
	{
		$this->template->load('account/transaction');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/transaction');
			
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$this->language->load('account/transaction');

		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('text_transaction'), $this->url->link('account/transaction'));

		$this->_('column_amount', $this->config->get('config_currency'));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['transactions'] = array();
		
		$data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		
		$transaction_total = $this->Model_Account_Transaction->getTotalTransactions($data);
	
		$results = $this->Model_Account_Transaction->getTransactions($data);
 		
		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'		=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
		}

		$this->pagination->init();
		$this->pagination->total = $transaction_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['total'] = $this->currency->format($this->customer->getBalance());
		
		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
						
		$this->response->setOutput($this->render());
	}
}