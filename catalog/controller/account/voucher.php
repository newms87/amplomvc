<?php
class Catalog_Controller_Account_Voucher extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('account/voucher');

		$this->language->load('account/voucher');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}
	
		if (($this->request->isPost()) && $this->validate()) {
			$this->session->data['vouchers'][rand()] = array(
				'description'		=> sprintf($this->_('text_for'), $this->currency->format($this->currency->convert($_POST['amount'], $this->currency->getCode(), $this->config->get('config_currency'))), $_POST['to_name']),
				'to_name'			=> $_POST['to_name'],
				'to_email'			=> $_POST['to_email'],
				'from_name'		=> $_POST['from_name'],
				'from_email'		=> $_POST['from_email'],
				'voucher_theme_id' => $_POST['voucher_theme_id'],
				'message'			=> $_POST['message'],
				'amount'			=> $this->currency->convert($_POST['amount'], $this->currency->getCode(), $this->config->get('config_currency'))
			);
			
			$this->url->redirect($this->url->link('account/voucher/success'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('text_voucher'), $this->url->link('account/voucher'));

		$this->language->format('entry_amount', $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1));
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['to_name'])) {
			$this->data['error_to_name'] = $this->error['to_name'];
		} else {
			$this->data['error_to_name'] = '';
		}
		
		if (isset($this->error['to_email'])) {
			$this->data['error_to_email'] = $this->error['to_email'];
		} else {
			$this->data['error_to_email'] = '';
		}
				
		if (isset($this->error['from_name'])) {
			$this->data['error_from_name'] = $this->error['from_name'];
		} else {
			$this->data['error_from_name'] = '';
		}
		
		if (isset($this->error['from_email'])) {
			$this->data['error_from_email'] = $this->error['from_email'];
		} else {
			$this->data['error_from_email'] = '';
		}
		
		if (isset($this->error['theme'])) {
			$this->data['error_theme'] = $this->error['theme'];
		} else {
			$this->data['error_theme'] = '';
		}
						
		if (isset($this->error['amount'])) {
			$this->data['error_amount'] = $this->error['amount'];
		} else {
			$this->data['error_amount'] = '';
		}
					
		$this->data['action'] = $this->url->link('account/voucher');
								
		if (isset($_POST['to_name'])) {
			$this->data['to_name'] = $_POST['to_name'];
		} else {
			$this->data['to_name'] = '';
		}
		
		if (isset($_POST['to_email'])) {
			$this->data['to_email'] = $_POST['to_email'];
		} else {
			$this->data['to_email'] = '';
		}
				
		if (isset($_POST['from_name'])) {
			$this->data['from_name'] = $_POST['from_name'];
		} elseif ($this->customer->isLogged()) {
			$this->data['from_name'] = $this->customer->info('firstname') . ' '  . $this->customer->info('lastname');
		} else {
			$this->data['from_name'] = '';
		}
		
		if (isset($_POST['from_email'])) {
			$this->data['from_email'] = $_POST['from_email'];
		} elseif ($this->customer->isLogged()) {
			$this->data['from_email'] = $this->customer->info('email');
		} else {
			$this->data['from_email'] = '';
		}
			
 		$this->data['voucher_themes'] = $this->Model_Cart_VoucherTheme->getVoucherThemes();

		if (isset($_POST['voucher_theme_id'])) {
				$this->data['voucher_theme_id'] = $_POST['voucher_theme_id'];
		} else {
				$this->data['voucher_theme_id'] = '';
		}
					
		if (isset($_POST['message'])) {
			$this->data['message'] = $_POST['message'];
		} else {
			$this->data['message'] = '';
		}
				
		if (isset($_POST['amount'])) {
			$this->data['amount'] = $_POST['amount'];
		} else {
			$this->data['amount'] = '25.00';
		}
				
		if (isset($_POST['agree'])) {
			$this->data['agree'] = $_POST['agree'];
		} else {
			$this->data['agree'] = false;
		}

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
	
  	public function success()
  	{
		$this->template->load('common/success');

		$this->language->load('account/voucher');

		$this->document->setTitle($this->_('heading_title'));
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/voucher'));

		$this->data['continue'] = $this->url->link('cart/cart');

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
	
	private function validate()
	{
		if ((strlen($_POST['to_name']) < 1) || (strlen($_POST['to_name']) > 64)) {
				$this->error['to_name'] = $this->_('error_to_name');
		}
		
		if ((strlen($_POST['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['to_email'])) {
				$this->error['to_email'] = $this->_('error_email');
		}
		
		if ((strlen($_POST['from_name']) < 1) || (strlen($_POST['from_name']) > 64)) {
				$this->error['from_name'] = $this->_('error_from_name');
		}
		
		if ((strlen($_POST['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['from_email'])) {
				$this->error['from_email'] = $this->_('error_email');
		}
		
		if (!isset($_POST['voucher_theme_id'])) {
				$this->error['theme'] = $this->_('error_theme');
		}
				
		if (($_POST['amount'] < 1) || ($_POST['amount'] > 1000)) {
				$this->error['amount'] = sprintf($this->_('error_amount'), $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1) . ' ' . $this->currency->getCode());
		}
				
		if (!isset($_POST['agree'])) {
				$this->error['warning'] = $this->_('error_agree');
		}
									
		return $this->error ? false : true;
	}
}