<?php
class Admin_Controller_Sale_Affiliate extends Controller 
{
	
  
  	public function index()
  	{
		$this->load->language('sale/affiliate');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert()
  	{
		$this->load->language('sale/affiliate');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
				$this->Model_Sale_Affiliate->addAffiliate($_POST);
			
			$this->message->add('success', $this->_('text_success'));
		
			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
						
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
			
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
		
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
							
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/affiliate', $url));
		}
		
		$this->getForm();
  	}
	
  	public function update()
  	{
		$this->load->language('sale/affiliate');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Affiliate->editAffiliate($_GET['affiliate_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
	
			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
					
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
		
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/affiliate', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('sale/affiliate');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $affiliate_id) {
				$this->Model_Sale_Affiliate->deleteAffiliate($affiliate_id);
			}
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
			
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
								
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
		
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->url->redirect($this->url->link('sale/affiliate', $url));
		}
	
		$this->getList();
  	}
		
	public function approve()
	{
		$this->load->language('sale/affiliate');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if (!$this->user->hasPermission('modify', 'sale/affiliate')) {
			$this->error['warning'] = $this->_('error_permission');
		} elseif (isset($_POST['selected'])) {
			$approved = 0;
			
			foreach ($_POST['selected'] as $affiliate_id) {
				$affiliate_info = $this->Model_Sale_Affiliate->getAffiliate($affiliate_id);
				
				if ($affiliate_info && !$affiliate_info['approved']) {
					$this->Model_Sale_Affiliate->approve($affiliate_id);
				
					$approved++;
				}
			}
			
			$this->message->add('success', sprintf($this->_('text_approved'), $approved));
			
			$url = '';
		
			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}
		
			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}
		
			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}
			
			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}
			
			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}
						
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}
		
			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}
							
			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
		
			$this->url->redirect($this->url->link('sale/affiliate', $url));
		}
		
		$this->getList();
	}
		
  	private function getList()
  	{
		$this->template->load('sale/affiliate_list');

		if (isset($_GET['filter_name'])) {
			$filter_name = $_GET['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($_GET['filter_email'])) {
			$filter_email = $_GET['filter_email'];
		} else {
			$filter_email = null;
		}
		
		if (isset($_GET['filter_status'])) {
			$filter_status = $_GET['filter_status'];
		} else {
			$filter_status = null;
		}
		
		if (isset($_GET['filter_approved'])) {
			$filter_approved = $_GET['filter_approved'];
		} else {
			$filter_approved = null;
		}
		
		if (isset($_GET['filter_date_added'])) {
			$filter_date_added = $_GET['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
			
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
						
		$url = '';

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
						
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
			
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
						
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
		
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/affiliate', $url));

		$this->data['approve'] = $this->url->link('sale/affiliate/approve', $url);
		$this->data['insert'] = $this->url->link('sale/affiliate/insert', $url);
		$this->data['delete'] = $this->url->link('sale/affiliate/delete', $url);

		$this->data['affiliates'] = array();

		$data = array(
			'filter_name'		=> $filter_name,
			'filter_email'		=> $filter_email,
			'filter_status'	=> $filter_status,
			'filter_approved'	=> $filter_approved,
			'filter_date_added' => $filter_date_added,
			'sort'				=> $sort,
			'order'				=> $order,
			'start'				=> ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'				=> $this->config->get('config_admin_limit')
		);
		
		$affiliate_total = $this->Model_Sale_Affiliate->getTotalAffiliates($data);
	
		$results = $this->Model_Sale_Affiliate->getAffiliates($data);
 
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/affiliate/update', 'affiliate_id=' . $result['affiliate_id'] . $url)
			);
			
			$this->data['affiliates'][] = array(
				'affiliate_id' => $result['affiliate_id'],
				'name'			=> $result['name'],
				'email'		=> $result['email'],
				'balance'		=> $this->currency->format($result['balance'], $this->config->get('config_currency')),
				'status'		=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'approved'	=> ($result['approved'] ? $this->_('text_yes') : $this->_('text_no')),
				'date_added'	=> $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'selected'	=> isset($_POST['selected']) && in_array($result['affiliate_id'], $_POST['selected']),
				'action'		=> $action
			);
		}
					
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$url = '';

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
			
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
			
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('sale/affiliate', 'sort=name' . $url);
		$this->data['sort_email'] = $this->url->link('sale/affiliate', 'sort=a.email' . $url);
		$this->data['sort_status'] = $this->url->link('sale/affiliate', 'sort=a.status' . $url);
		$this->data['sort_approved'] = $this->url->link('sale/affiliate', 'sort=a.approved' . $url);
		$this->data['sort_date_added'] = $this->url->link('sale/affiliate', 'sort=a.date_added' . $url);
		
		$url = '';

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
			
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $affiliate_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_email'] = $filter_email;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_approved'] = $filter_approved;
		$this->data['filter_date_added'] = $filter_date_added;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  
  	private function getForm()
  	{
		$this->template->load('sale/affiliate_form');

		if (isset($_GET['affiliate_id'])) {
			$this->data['affiliate_id'] = $_GET['affiliate_id'];
		} else {
			$this->data['affiliate_id'] = 0;
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

 		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
		
 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
 		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
		
 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
		if (isset($this->error['address_1'])) {
			$this->data['error_address_1'] = $this->error['address_1'];
		} else {
			$this->data['error_address_1'] = '';
		}
		
		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}
		
		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}
		
		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}

		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}
						
		$url = '';
		
		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}
		
		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}
		
		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}
		
		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}
		
		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}
						
		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}
		
		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/affiliate', $url));

		if (!isset($_GET['affiliate_id'])) {
			$this->data['action'] = $this->url->link('sale/affiliate/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/affiliate/update', 'affiliate_id=' . $_GET['affiliate_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('sale/affiliate', $url);

		if (isset($_GET['affiliate_id']) && !$this->request->isPost()) {
				$affiliate_info = $this->Model_Sale_Affiliate->getAffiliate($_GET['affiliate_id']);
		}
			
		if (isset($_POST['firstname'])) {
				$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($affiliate_info)) {
			$this->data['firstname'] = $affiliate_info['firstname'];
		} else {
				$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
				$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($affiliate_info)) {
			$this->data['lastname'] = $affiliate_info['lastname'];
		} else {
				$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
				$this->data['email'] = $_POST['email'];
		} elseif (!empty($affiliate_info)) {
			$this->data['email'] = $affiliate_info['email'];
		} else {
				$this->data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
				$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($affiliate_info)) {
			$this->data['telephone'] = $affiliate_info['telephone'];
		} else {
				$this->data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
				$this->data['fax'] = $_POST['fax'];
		} elseif (!empty($affiliate_info)) {
			$this->data['fax'] = $affiliate_info['fax'];
		} else {
				$this->data['fax'] = '';
		}

		if (isset($_POST['company'])) {
				$this->data['company'] = $_POST['company'];
		} elseif (!empty($affiliate_info)) {
			$this->data['company'] = $affiliate_info['company'];
		} else {
				$this->data['company'] = '';
		}
		
		if (isset($_POST['address_1'])) {
				$this->data['address_1'] = $_POST['address_1'];
		} elseif (!empty($affiliate_info)) {
			$this->data['address_1'] = $affiliate_info['address_1'];
		} else {
				$this->data['address_1'] = '';
		}
				
		if (isset($_POST['address_2'])) {
				$this->data['address_2'] = $_POST['address_2'];
		} elseif (!empty($affiliate_info)) {
			$this->data['address_2'] = $affiliate_info['address_2'];
		} else {
				$this->data['address_2'] = '';
		}

		if (isset($_POST['city'])) {
				$this->data['city'] = $_POST['city'];
		} elseif (!empty($affiliate_info)) {
			$this->data['city'] = $affiliate_info['city'];
		} else {
				$this->data['city'] = '';
		}

		if (isset($_POST['postcode'])) {
				$this->data['postcode'] = $_POST['postcode'];
		} elseif (!empty($affiliate_info)) {
			$this->data['postcode'] = $affiliate_info['postcode'];
		} else {
				$this->data['postcode'] = '';
		}
		
		if (isset($_POST['country_id'])) {
				$this->data['country_id'] = $_POST['country_id'];
		} elseif (!empty($affiliate_info)) {
			$this->data['country_id'] = $affiliate_info['country_id'];
		} else {
				$this->data['country_id'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();
				
		if (isset($_POST['zone_id'])) {
				$this->data['zone_id'] = $_POST['zone_id'];
		} elseif (!empty($affiliate_info)) {
			$this->data['zone_id'] = $affiliate_info['zone_id'];
		} else {
				$this->data['zone_id'] = '';
		}

		if (isset($_POST['code'])) {
				$this->data['code'] = $_POST['code'];
		} elseif (!empty($affiliate_info)) {
			$this->data['code'] = $affiliate_info['code'];
		} else {
				$this->data['code'] = uniqid();
		}
		
		if (isset($_POST['commission'])) {
				$this->data['commission'] = $_POST['commission'];
		} elseif (!empty($affiliate_info)) {
			$this->data['commission'] = $affiliate_info['commission'];
		} else {
				$this->data['commission'] = $this->config->get('config_commission');
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
																												
		if (isset($_POST['status'])) {
				$this->data['status'] = $_POST['status'];
		} elseif (!empty($affiliate_info)) {
			$this->data['status'] = $affiliate_info['status'];
		} else {
				$this->data['status'] = 1;
		}

		if (isset($_POST['password'])) {
			$this->data['password'] = $_POST['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($_POST['confirm'])) {
			$this->data['confirm'] = $_POST['confirm'];
		} else {
			$this->data['confirm'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
  	private function validateForm()
  	{
		if (!$this->user->hasPermission('modify', 'sale/affiliate')) {
				$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
				$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['email']) > 96) || (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email']))) {
				$this->error['email'] = $this->_('error_email');
		}
		
		$affiliate_info = $this->Model_Sale_Affiliate->getAffiliateByEmail($_POST['email']);
		
		if (!isset($_GET['affiliate_id'])) {
			if ($affiliate_info) {
				$this->error['warning'] = $this->_('error_exists');
			}
		} else {
			if ($affiliate_info && ($_GET['affiliate_id'] != $affiliate_info['affiliate_id'])) {
				$this->error['warning'] = $this->_('error_exists');
			}
		}
		
		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
				$this->error['telephone'] = $this->_('error_telephone');
		}

		if ($_POST['password'] || (!isset($_GET['affiliate_id']))) {
				if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
				}
	
			if ($_POST['password'] != $_POST['confirm']) {
				$this->error['confirm'] = $this->_('error_confirm');
			}
		}
		
		if ((strlen($_POST['address_1']) < 3) || (strlen($_POST['address_1']) > 128)) {
				$this->error['address_1'] = $this->_('error_address_1');
		}

		if ((strlen($_POST['city']) < 2) || (strlen($_POST['city']) > 128)) {
				$this->error['city'] = $this->_('error_city');
		}
		
		$country_info = $this->Model_Localisation_Country->getCountry($_POST['country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
			$this->error['postcode'] = $this->_('error_postcode');
		}
		
		if ($_POST['country_id'] == '') {
				$this->error['country'] = $this->_('error_country');
		}
		
		if ($_POST['zone_id'] == '') {
				$this->error['zone'] = $this->_('error_zone');
		}

		if (!$_POST['code']) {
				$this->error['code'] = $this->_('error_code');
		}
					
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'sale/affiliate')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
  	}

	public function transaction()
	{
		$this->template->load('sale/affiliate_transaction');
		$this->language->load('sale/affiliate');
		
		if ($this->request->isPost() && $this->user->hasPermission('modify', 'sale/affiliate')) {
			$this->Model_Sale_Affiliate->addTransaction($_GET['affiliate_id'], $_POST['description'], $_POST['amount']);
				
			$this->language->set('success', $this->_('text_success'));
		} else {
			$this->data['success'] = '';
		}

		if ($this->request->isPost() && !$this->user->hasPermission('modify', 'sale/affiliate')) {
			$this->language->set('error_warning', $this->_('error_permission'));
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['transactions'] = array();
			
		$results = $this->Model_Sale_Affiliate->getTransactions($_GET['affiliate_id'], ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'		=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short'))
			);
			}
		
		$this->data['balance'] = $this->currency->format($this->Model_Sale_Affiliate->getTransactionTotal($_GET['affiliate_id']), $this->config->get('config_currency'));
		
		$transaction_total = $this->Model_Sale_Affiliate->getTotalTransactions($_GET['affiliate_id']);
			
		$this->pagination->init();
		$this->pagination->total = $transaction_total;
		$this->data['pagination'] = $this->pagination->render();

		
		$this->response->setOutput($this->render());
	}
		
	public function autocomplete()
	{
		$affiliate_data = array();
		
		if (isset($_GET['filter_name'])) {
			$data = array(
				'filter_name' => $_GET['filter_name'],
				'start'		=> 0,
				'limit'		=> 20
			);
		
			$results = $this->Model_Sale_Affiliate->getAffiliates($data);
			
			foreach ($results as $result) {
				$affiliate_data[] = array(
					'affiliate_id' => $result['affiliate_id'],
					'name'			=> html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')
				);
			}
		}
		
		$this->response->setOutput(json_encode($affiliate_data));
	}
}