<?php 
class ControllerSaleVoucherTheme extends Controller { 
	
   
  	public function index() {
		$this->load->language('sale/voucher_theme');
	
    	$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
              
  	public function insert() {
		$this->load->language('sale/voucher_theme');
	
    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      		$this->model_sale_voucher_theme->addVoucherTheme($_POST);
		  	
			$this->message->add('success', $this->_('text_success'));

			$url = '';
			
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
						
      		$this->redirect($this->url->link('sale/voucher_theme', $url));
		}
	
    	$this->getForm();
  	}

  	public function update() {
		$this->load->language('sale/voucher_theme');
	
    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	  		$this->model_sale_voucher_theme->editVoucherTheme($_GET['voucher_theme_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$url = '';
			
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->redirect($this->url->link('sale/voucher_theme', $url));
    	}
	
    	$this->getForm();
  	}

  	public function delete() {
		$this->load->language('sale/voucher_theme');
	
    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $voucher_theme_id) {
				$this->model_sale_voucher_theme->deleteVoucherTheme($voucher_theme_id);
			}
			      		
			$this->message->add('success', $this->_('text_success'));

			$url = '';
			
			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}
			
			$this->redirect($this->url->link('sale/voucher_theme', $url));
   		}
	
    	$this->getList();
  	}
    
  	private function getList() {
		$this->template->load('sale/voucher_theme_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'vtd.name';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher_theme', $url));

		$this->data['insert'] = $this->url->link('sale/voucher_theme/insert', $url);
		$this->data['delete'] = $this->url->link('sale/voucher_theme/delete', $url);	

		$this->data['voucher_themes'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$voucher_theme_total = $this->model_sale_voucher_theme->getTotalVoucherThemes();
	
		$results = $this->model_sale_voucher_theme->getVoucherThemes($data);
 
    	foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/voucher_theme/update', 'voucher_theme_id=' . $result['voucher_theme_id'] . $url)
			);
						
			$this->data['voucher_themes'][] = array(
				'voucher_theme_id' => $result['voucher_theme_id'],
				'name'             => $result['name'],
				'selected'         => isset($_POST['selected']) && in_array($result['voucher_theme_id'], $_POST['selected']),
				'action'           => $action
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('sale/voucher_theme', 'sort=name' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $voucher_theme_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('sale/voucher_theme', $url . '&page={page}');
			
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  
  	private function getForm() {
		$this->template->load('sale/voucher_theme_form');
		
		$this->document->addScript("image_manager.js");
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}
		
 		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = '';
		}
				
		$url = '';
			
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher_theme', $url));

		if (!isset($_GET['voucher_theme_id'])) {
			$this->data['action'] = $this->url->link('sale/voucher_theme/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/voucher_theme/update', 'voucher_theme_id=' . $_GET['voucher_theme_id'] . $url);
		}
		
		if (isset($_GET['voucher_theme_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      		$voucher_theme_info = $this->model_sale_voucher_theme->getVoucherTheme($_GET['voucher_theme_id']);
    	}
					
		$this->data['cancel'] = $this->url->link('sale/voucher_theme', $url);
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($_POST['voucher_theme_description'])) {
			$this->data['voucher_theme_description'] = $_POST['voucher_theme_description'];
		} elseif (isset($_GET['voucher_theme_id'])) {
			$this->data['voucher_theme_description'] = $this->model_sale_voucher_theme->getVoucherThemeDescriptions($_GET['voucher_theme_id']);
		} else {
			$this->data['voucher_theme_description'] = array();
		}
		
		if (isset($_POST['image'])) {
			$this->data['image'] = $_POST['image'];
		} elseif (!empty($voucher_theme_info)) {
			$this->data['image'] = $voucher_theme_info['image'];
		} else {
			$this->data['image'] = '';
		}

		if (isset($voucher_theme_info) && $voucher_theme_info['image'] && file_exists(DIR_IMAGE . $voucher_theme_info['image'])) {
			$this->data['thumb'] = $this->image->resize($voucher_theme_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->image->resize('no_image.jpg', 100, 100);
		}
		
		$this->data['no_image'] = $this->image->resize('no_image.jpg', 100, 100);
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());	
  	}
  	
	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}
	
    	foreach ($_POST['voucher_theme_description'] as $language_id => $value) {
      		if ((strlen($value['name']) < 3) || (strlen($value['name']) > 32)) {
        		$this->error['name'][$language_id] = $this->_('error_name');
      		}
    	}
		
		if (!$_POST['image']) {
			$this->error['image'] = $this->_('error_image');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

  	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
      		$this->error['warning'] = $this->_('error_permission');
    	}
		
		foreach ($_POST['selected'] as $voucher_theme_id) {
			$voucher_total = $this->model_sale_voucher->getTotalVouchersByVoucherThemeId($voucher_theme_id);
		
			if ($voucher_total) {
	  			$this->error['warning'] = sprintf($this->_('error_voucher'), $voucher_total);	
			}  
	  	}
		
		if (!$this->error) { 
	  		return true;
		} else {
	  		return false;
		}
  	}	  
}