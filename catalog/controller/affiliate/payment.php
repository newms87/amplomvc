<?php
class Catalog_Controller_Affiliate_Payment extends Controller 
{
	

	public function index()
	{
		$this->template->load('affiliate/payment');

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/payment');

			$this->url->redirect($this->url->link('affiliate/login'));
		}

		$this->language->load('affiliate/payment');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost()) {
			$this->Model_Affiliate_Affiliate->editPayment($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('affiliate/account'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('affiliate/payment'));

		$this->data['action'] = $this->url->link('affiliate/payment');

		if (!$this->request->isPost()) {
			$affiliate_info = $this->Model_Affiliate_Affiliate->getAffiliate($this->affiliate->getId());
		}

		if (isset($_POST['tax'])) {
			$this->data['tax'] = $_POST['tax'];
		} elseif (!empty($affiliate_info)) {
			$this->data['tax'] = $affiliate_info['tax'];
		} else {
			$this->data['tax'] = '';
		}
		
		if (isset($_POST['payment'])) {
			$this->data['payment'] = $_POST['payment'];
		} elseif (!empty($affiliate_info)) {
			$this->data['payment'] = $affiliate_info['payment'];
		} else {
			$this->data['payment'] = 'cheque';
		}

		if (isset($_POST['cheque'])) {
			$this->data['cheque'] = $_POST['cheque'];
		} elseif (!empty($affiliate_info)) {
			$this->data['cheque'] = $affiliate_info['cheque'];
		} else {
			$this->data['cheque'] = '';
		}

		if (isset($_POST['paypal'])) {
			$this->data['paypal'] = $_POST['paypal'];
		} elseif (!empty($affiliate_info)) {
			$this->data['paypal'] = $affiliate_info['paypal'];
		} else {
			$this->data['paypal'] = '';
		}

		if (isset($_POST['bank_name'])) {
			$this->data['bank_name'] = $_POST['bank_name'];
		} elseif (!empty($affiliate_info)) {
			$this->data['bank_name'] = $affiliate_info['bank_name'];
		} else {
			$this->data['bank_name'] = '';
		}

		if (isset($_POST['bank_branch_number'])) {
			$this->data['bank_branch_number'] = $_POST['bank_branch_number'];
		} elseif (!empty($affiliate_info)) {
			$this->data['bank_branch_number'] = $affiliate_info['bank_branch_number'];
		} else {
			$this->data['bank_branch_number'] = '';
		}

		if (isset($_POST['bank_swift_code'])) {
			$this->data['bank_swift_code'] = $_POST['bank_swift_code'];
		} elseif (!empty($affiliate_info)) {
			$this->data['bank_swift_code'] = $affiliate_info['bank_swift_code'];
		} else {
			$this->data['bank_swift_code'] = '';
		}

		if (isset($_POST['bank_account_name'])) {
			$this->data['bank_account_name'] = $_POST['bank_account_name'];
		} elseif (!empty($affiliate_info)) {
			$this->data['bank_account_name'] = $affiliate_info['bank_account_name'];
		} else {
			$this->data['bank_account_name'] = '';
		}
		
		if (isset($_POST['bank_account_number'])) {
			$this->data['bank_account_number'] = $_POST['bank_account_number'];
		} elseif (!empty($affiliate_info)) {
			$this->data['bank_account_number'] = $affiliate_info['bank_account_number'];
		} else {
			$this->data['bank_account_number'] = '';
		}
		
		$this->data['back'] = $this->url->link('affiliate/account');

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