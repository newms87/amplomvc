<?php
class Admin_Controller_Mail_Newsletter extends Controller 
{
	
	public function index()
	{
		$this->load->language('mail/newsletter');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('mail/newsletter');
		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$newsletter_id = $this->Model_Mail_Newsletter->addNewsletter($_POST);

			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
			
			$this->url->redirect($this->url->link('mail/newsletter/update', 'newsletter_id=' . $newsletter_id));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('mail/newsletter');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Mail_Newsletter->editNewsletter($_GET['newsletter_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
		}

		$this->getForm();
	}

	public function batch_update()
	{
		$this->load->language('mail/newsletter');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && isset($_GET['action']) && $this->validateModify()) {
			foreach ($_POST['selected'] as $newsletter_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Mail_Newsletter->editNewsletter($newsletter_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Mail_Newsletter->editNewsletter($newsletter_id, array('status' => 0));
						break;
					case 'copy':
						$this->Model_Mail_Newsletter->copyNewsletter($newsletter_id);
						break;
					case 'delete':
						$this->Model_Mail_Newsletter->deleteNewsletter($newsletter_id);
					default:
						$this->error['warning'] = "Invalid Action Selected!";
						break;
				}
				if($this->error)
					break;
			}
			if (!$this->error) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('mail/newsletter', $this->url->get_query()));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('mail/newsletter_list');
		$this->language->load('mail/newsletter');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('mail/newsletter'));

		//The Table Columns
		$columns = array();
		
		$columns['name'] = array(
			'display_name' => $this->_('column_name'),
			'type' => 'text',
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['send_date'] = array(
			'display_name' => $this->_('column_send_date'),
			'type' => 'datetime',
			'filter' => true,
			'sortable' => false,
		);
		
		$columns['status'] = array(
			'display_name' => $this->_('column_status'),
			'type' => 'select',
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
		);
		
		//The Sort / filter data
		$data = array();
		
		$sort_page = array(
			'sort' => 'name',
			'order' => 'ASC',
			'limit' => $this->config->get('config_admin_limit'),
			'page' => 1,
		);
		
		foreach ($sort_page as $key => $default) {
			$data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($page - 1) * $limit;
		
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		$data += $filter_values;
		
		$newsletter_total = $this->Model_Mail_Newsletter->getTotalNewsletters($data);
		$results = $this->Model_Mail_Newsletter->getNewsletters($data);
		
		$newsletters = array();
		
		foreach ($results as $result) {
			$action = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('mail/newsletter/update', 'newsletter_id=' . $result['newsletter_id'])
			);
			
			$result['action'] = $action;
			
			$newsletters[] = $result;
		}
		
		$table->set_table_data($newsletters);
		
		$this->data['newsletter_view'] = $table->render();
		
		
		$url = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->data['update_actions'] = array('enable'=>'Enable','disable'=>'Disable');
		
		$this->data['batch_update'] = $this->url->link('mail/newsletter/batch_update', $url . '&action=%action%');
		
		$this->data['insert'] = $this->url->link('mail/newsletter/insert', $url);
		$this->data['copy'] = $this->url->link('mail/newsletter/copy', $url);
		$this->data['delete'] = $this->url->link('mail/newsletter/delete', $url);
		
		$url = $this->url->get_query('filter', 'sort', 'order');
		
		$this->pagination->init();
		$this->pagination->total = $newsletter_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['download_email_list'] = $this->url->link('mail/newsletter/email_list');
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->language->load('mail/newsletter');
		
		$this->template->load('mail/newsletter_form');

		$newsletter_id = $this->data['newsletter_id'] = isset($_GET['newsletter_id'])?$_GET['newsletter_id']:0;
		$store_id = $this->config->get('config_default_store');
		
		if ($newsletter_id) {
			$this->data['url_active'] = $this->url->store($store_id, "newsletter/newsletter", 'newsletter_id=' . $newsletter_id);
		}
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('mail/newsletter'));
		
		
		//Action buttons
		if (!$newsletter_id) {
			$this->data['action'] = $this->url->link('mail/newsletter/insert');
		} else {
			$this->data['action'] = $this->url->link('mail/newsletter/update', 'newsletter_id=' . $newsletter_id);
		}
	
		$this->data['preview'] = $this->url->link('mail/newsletter/preview', 'newsletter_id=' . $newsletter_id);
		
		$this->data['cancel'] = $this->url->link('mail/newsletter');
		
		
		//The Data
		if ($newsletter_id && (!$this->request->isPost())) {
			$newsletter_info = $this->Model_Mail_Newsletter->getNewsletter($newsletter_id);
		}
		
		$defaults = array(
			'name' => 'New Newsletter ' . $this->date->format(null, 'short'),
			'send_date' => $this->date->now(),
			'newsletter' => array(),
			'status' => 0,
		);
		
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (!empty($newsletter_info[$d])) {
				$this->data[$d] = $newsletter_info[$d];
			} elseif (!$newsletter_id) {
				$this->data[$d] = $value;
			}
		}
		
		if (empty($this->data['newsletter'])) {
			$this->data['newsletter']['featured']['designer']['designer_id'] = 0;
			$this->data['newsletter']['featured']['designer']['name'] = 'Designer Name';
			$this->data['newsletter']['featured']['designer']['title'] = '';
			$this->data['newsletter']['featured']['designer']['description'] = '';
			$this->data['newsletter']['featured']['designer']['article'] = '';
			$this->data['newsletter']['featured']['designer']['image'] = '';
			$this->data['newsletter']['featured']['designer']['width'] = 110;
			$this->data['newsletter']['featured']['designer']['height'] = 175;
			
			$this->data['newsletter']['featured']['product']['product_id'] = 0;
			$this->data['newsletter']['featured']['product']['name'] = 'Product Name';
			$this->data['newsletter']['featured']['product']['image'] = '';
			$this->data['newsletter']['featured']['product']['width'] = 400;
			$this->data['newsletter']['featured']['product']['height'] = 280;
		}
		
		$featured_designer = & $this->data['newsletter']['featured']['designer'];
		$featured_product = & $this->data['newsletter']['featured']['product'];
		
		if (isset($featured_designer['image'])) {
			$featured_designer['thumb'] = $this->image->resize($featured_designer['image'], $this->config->get('config_image_admin_thumb_width'), $this->config->get('config_image_admin_thumb_height'));
		}
		
		if (isset($featured_product['image'])) {
			$featured_product['thumb'] = $this->image->resize($featured_product['image'], $this->config->get('config_image_admin_thumb_width'), $this->config->get('config_image_admin_thumb_height'));
		}
		
		$m_data = array(
			'status' => 1,
			'sort' => 'name',
			'order' => 'ASC',
		);
	
		$this->data['data_designers'] = $this->Model_Catalog_Manufacturer->getManufacturers($m_data, 'manufacturer_id, name, image');
		
		if (empty($featured_designer['designer_id'])) {
			array_unshift($this->data['data_designers'], $this->_('text_select'));
			$this->data['data_designer_products'] =  array('' => $this->_('text_select'));
		}
		else {
			$this->data['data_designer_products'] =  array($featured_product['product_id'] => $featured_product['name']);
		}
	
		
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function preview()
	{
		$store_id = $this->config->get('config_default_store');
		
		$this->language->load('mail/newsletter');
		
		if ($this->request->isPost()) {
			if ($this->validateForm()) {
			}
			else {
				$this->message->add('warning', $this->_('error_newsletter_preview'));
				$this->message->add('warning', $this->error);
				$this->url->redirect($this->url->link('error/not_found'));
			}
			
			$this->data = $_POST;
		}
		else {
			$newsletter_id = isset($_GET['newsletter_id'])?$_GET['newsletter_id']:0;
			
			if (!$newsletter_id) {
				$this->message->add('warning', $this->_('error_newsletter_preview'));
				$this->url->redirect($this->url->link('error/not_found'));
				return;
			}
			
			$newsletter_info = $this->Model_Mail_Newsletter->getNewsletter($newsletter_id);
			
			$this->data += $newsletter_info;
		}

		$this->template->load('newsletter/betty-v2');
		
		$this->data['send_date'] = $this->date->format($this->data['send_date'], 'F d, Y');
		
		//Featured Designer
		$featured_designer = & $this->data['newsletter']['featured']['designer'];
		
			//The name text image
		$this->draw->set_canvas('designer_name', 385, 26);
		$this->draw->set_background('#EC227B');
		$this->draw->font_format('chaletpariseighty.ttf', 20, '#000000', 0, false);
		$this->draw->write_text($featured_designer['name']);
		$this->draw->render(DIR_GENERATED_IMAGE . 'newsletter/' . preg_replace("/[^A-Z0-9_]/i",'',$featured_designer['name']) . '.png');
		$featured_designer['name_image'] = $this->draw->get_image_url();
		
			//The title text image
		$this->draw->set_canvas('designer_title', 366, 100);
		$this->draw->set_background('#E8E8E8');
		$this->draw->font_format('chaletpariseighty.ttf', 29, '#000000', 0, false);
		$this->draw->write_text($featured_designer['title']);
		$this->draw->render(DIR_GENERATED_IMAGE . 'newsletter/' . preg_replace("/[^A-Z0-9_]/i",'',$featured_designer['title']) . '.png');
		$featured_designer['title_image'] = $this->draw->get_image_url();
		
		$featured_designer['href'] = $this->url->store($store_id, 'designers/designers', 'designer_id=' . $featured_designer['designer_id']);
		
		$featured_designer['description'] = html_entity_decode($featured_designer['description'], ENT_QUOTES, 'UTF-8');
		
		if (!empty($featured_designer['image'])) {
			$featured_designer['thumb'] = $this->image->resize($featured_designer['image'], $featured_designer['width'],$featured_designer['height']);
		}
		
		//Featured Product
		$featured_product = & $this->data['newsletter']['featured']['product'];
		
		if (!empty($featured_product['image'])) {
			$featured_product['thumb'] = $this->image->resize($featured_product['image'], $featured_product['width'],$featured_product['height'], '#FFFFFF');
		}
		
		$featured_product['href'] = $this->url->store($store_id, 'product/product', 'product_id=' . $featured_product['product_id']);
		
		$result = $this->Catalog_Model_Catalog_Product->getProduct($featured_product['product_id']);
		
		if ($result) {
			if ($result['special']) {
				$result['retail'] = $result['price'];
				$result['price'] = $result['special'];
			}
			else {
				$result['retail'] = $result['price'];
			}
			
			$result['price'] = $this->currency->format($result['price']);
			$result['retail'] = $this->currency->format($result['retail']);
			
			$featured_product += $result;
		}
		
		
		//The Product List
		if (!empty($this->data['newsletter']['products'])) {
			foreach ($this->data['newsletter']['products'] as $key => &$product) {
				$result = $this->Catalog_Model_Catalog_Product->getProduct($product['product_id']);
				
				if (!$result) {
					unset($this->data['newsletter']['products'][$key]);
					continue;
				}
				
				$result['name'] = $product['name'];
				
				$result['description'] = html_entity_decode($result['description']);
				
				if ($result['special']) {
					$result['retail'] = (int)$result['price'];
					$result['price'] = $result['special'];
				}
				else {
					$result['retail'] = (int)$result['price'];
				}
				
				$result['price'] = $this->currency->format($result['price']);
				
				if (!$result['image']) {
					$result['image'] = 'no_image.png';
				}

				$result['thumb'] = $this->image->resize($result['image'], 155, 121);
				
				$result['href'] = $this->url->store($store_id, 'product/product','product_id=' . $result['product_id']);
				
				$product = $result;
			}
		}unset($product);
		
		
		
		//The Designer List
		if (!empty($this->data['newsletter']['designers'])) {
			foreach ($this->data['newsletter']['designers'] as &$designer) {
				$result = $this->Model_Catalog_Manufacturer->getManufacturer($designer['designer_id']);
				
				if (!$result) {
					$result['teaser'] = "This Designer is not active";
					$result['manufacturer_id'] = 0;
					$result['image'] = 'no_image.png';
				}
				
				$result['name'] = $designer['name'];
				
				$result['thumb'] = $this->image->resize($result['image'], 154, 164);
				
				$result['href'] = $this->url->store($store_id, "designers/designers", 'designer_id=' . $result['manufacturer_id']);
				
				$designer = $result;
			}
		}unset($designer);

		if (!empty($this->data['newsletter']['featured']['articles'])) {
			foreach ($this->data['newsletter']['featured']['articles'] as &$article) {
				$article['thumb'] = $this->image->resize($article['image'], 94, 155);
			}
		}
		
		if (empty($this->data['newsletter']['articles_image'])) {
			$this->data['newsletter']['articles_image'] = 'no_image.png';
		}
		
		$this->data['newsletter']['articles_image'] = $this->image->resize($this->data['newsletter']['articles_image'], 225, 136);
		
		//Extra Data
		$this->data['link_see_all_sales'] = $this->url->store($store_id, 'sales/flashsale', '');
		
		$this->response->setOutput($this->render());
	}
	
	public function email_list()
	{
		
		$customers = $this->Model_Mail_Newsletter->getEmailList();
		
		$columns = array(
			'firstname' => "First Name",
			'lastname'  => "Last Name",
			'email'	=> "Email",
		);
		
		$this->export->generate_csv($columns, $customers);
		
		$file = "email_list_" . $this->date->format(null, 'm-d-Y') . '.csv';
		
		$this->export->download_contents_as('csv', $file);
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'mail/newsletter')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'],1, 32)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->validation->datetime($_POST['send_date'])) {
			$this->error['send_date'] = $this->_('error_send_date');
		}
					
		return $this->error ? false : true;
	}

	private function validateModify()
	{
		if (!$this->user->hasPermission('modify', 'mail/newsletter')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}
