<?php
class Catalog_Controller_Affiliate_Transaction extends Controller 
{
	public function index()
	{
		$this->template->load('affiliate/transaction');

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/transaction');
			
			$this->url->redirect($this->url->link('affiliate/login'));
		}
		
		$this->language->load('affiliate/transaction');

		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_transaction'), $this->url->link('affiliate/transaction'));

		$this->_('column_amount', $this->config->get('config_currency'));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['transactions'] = array();
		
		$data = array(
			'sort'  => 't.date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);
		
		$transaction_total = $this->Model_Affiliate_Transaction->getTotalTransactions($data);
	
		$results = $this->Model_Affiliate_Transaction->getTransactions($data);
 		
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
		
		$this->data['balance'] = $this->currency->format($this->Model_Affiliate_Transaction->getBalance());
		
		$this->data['continue'] = $this->url->link('affiliate/account');

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