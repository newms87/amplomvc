<?php
class Admin_Controller_Sale_VoucherTheme extends Controller
{
	public function index()
  	{
		$this->language->load('sale/voucher_theme');
	
		$this->getList();
  	}
	
  	public function update()
  	{
		$this->language->load('sale/voucher_theme');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['voucher_theme_id'])) {
				$this->Model_Sale_VoucherTheme->addVoucherTheme($_POST);
			}
			//Update
			else {
				$this->Model_Sale_VoucherTheme->editVoucherTheme($_GET['voucher_theme_id'], $_POST);
			}
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('sale/voucher_theme'));
			}
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->language->load('sale/voucher_theme');
		
		if (isset($_GET['voucher_theme_id']) && $this->validateDelete()) {
			$this->Model_Sale_VoucherTheme->deleteVoucherTheme($_GET['voucher_theme_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('sale/voucher_theme'));
			}
		}
	
		$this->getList();
  	}
	
  	private function getList()
  	{
  		//Page Title
  		$this->document->setTitle($this->_('heading_title'));
		
		//The Template
		$this->template->load('sale/voucher_theme_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher_theme'));
		
		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type' => 'image',
			'display_name' => $this->_('column_image'),
			'filter' => false,
			'sortable' => true,
			'sort_value' => '__image_sort__image',
		);
		
		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		//Get Sorted / Filtered Data
		$sort = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		$voucher_theme_total = $this->Model_Sale_VoucherTheme->getTotalVoucherThemes($filter);
		$voucher_themes = $this->Model_Sale_VoucherTheme->getVoucherThemes($sort + $filter);
 		
		$url_query = $this->url->getQueryExclude('voucher_theme_id');
		$image_width = $this->config->get('config_image_admin_list_width');
		$image_height = $this->config->get('config_image_admin_list_height');
		
		foreach ($voucher_themes as &$voucher_theme) {
			$voucher_theme['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme['voucher_theme_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('sale/voucher_theme/delete', 'voucher_theme_id=' . $voucher_theme['voucher_theme_id'] . '&' . $url_query)
				)
			);
			
			$voucher_theme['thumb'] = $this->image->resize($voucher_theme['image'], $image_width, $image_height);
		} unset($voucher_theme);
		
		//Build The Table
		$tt_data = array(
			'row_id'		=> 'voucher_theme_id',
		);
		
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($voucher_themes);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
		$this->data['batch_actions'] = array(
			'enable'	=> array(
				'label' => $this->_('text_enable'),
			),
			'disable'=>	array(
				'label' => $this->_('text_disable'),
			),
			'copy' => array(
				'label' => $this->_('text_copy'),
			),
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('sale/voucher_theme/batch_update', $url_query));
		
		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $voucher_theme_total;
		
		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('sale/voucher_theme/insert');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
  	}

  	private function getForm()
  	{
  		//Page Title
  		$this->document->setTitle($this->_('heading_title'));
		
		//The Template
		$this->template->load('sale/voucher_theme_form');
		
		//Insert or Update
		$voucher_theme_id = isset($_GET['voucher_theme_id']) ? (int)$_GET['voucher_theme_id'] : 0;
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher_theme'));
		
		if ($voucher_theme_id) {
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme_id));
		} else {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('sale/voucher_theme/update'));
		}
		
		//Load Information
		if ($voucher_theme_id && !$this->request->isPost()) {
			$voucher_theme_info = $this->Model_Sale_VoucherTheme->getVoucherTheme($voucher_theme_id);
		}
		
		//Load Values or Defaults
		$defaults = array(
			'name' => '',
			'image' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($voucher_theme_info[$key])) {
				$this->data[$key] = $voucher_theme_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Translations
		$this->data['translations'] = $this->Model_Sale_VoucherTheme->getVoucherThemeTranslations($voucher_theme_id);
		
		//Action Buttons
		$this->data['save'] = $this->url->link('sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme_id);
		$this->data['cancel'] = $this->url->link('sale/voucher_theme');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
  	}
  	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'], 3, 32)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (empty($_POST['image'])) {
			$this->error['image'] = $this->_('error_image');
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!empty($_POST['selected'])) {
			$voucher_theme_ids = $_POST['selected'];
		} else {
			$voucher_theme_ids = array();
		}
		
		if (!empty($_GET['voucher_theme_id'])) {
			$voucher_theme_ids[] = $_GET['voucher_theme_id'];
		}
		
		foreach ($voucher_theme_ids as $voucher_theme_id) {
			$in_use = $this->db->queryVar("SELECT COUNT(*) FROM ". DB_PREFIX . "voucher WHERE voucher_theme_id = " . (int)$voucher_theme_id);
			
			if ($in_use) {
				$this->error['warning'] = $this->_('error_voucher', $in_use);
				break;
			}
		}
		
		return $this->error ? false : true;
  	}
}