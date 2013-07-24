<?php
class Admin_Controller_Catalog_Option extends Controller
{
	

	public function index()
	{
		$this->load->language('catalog/option');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('catalog/option');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Option->addOption($_POST);
			
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
			
			$this->getList();
			return;
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('catalog/option');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Option->editOption($_GET['option_id'], $_POST);
			
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
			
			$this->getList();
			return;
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/option');

		$this->document->setTitle($this->_('heading_title'));
 		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $option_id) {
				$this->Model_Catalog_Option->deleteOption($option_id);
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
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('catalog/option_list');

		$defaults = array('sort'=>'od.name','order'=>'ASC','page'=>1);
		foreach ($defaults as $key=>$default) {
			$$key = isset($_GET[$key])?$_GET[$key]:$default;
		}
			
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/option'));
				
		$this->data['insert'] = $this->url->link('catalog/option/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/option/delete', $url);
		
		$this->data['options'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$option_total = $this->Model_Catalog_Option->getTotalOptions();
		
		$results = $this->Model_Catalog_Option->getOptions($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/option/update', 'option_id=' . $result['option_id'] . $url)
			);

			$this->data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'		=> $result['name'],
				'sort_order' => $result['sort_order'],
				'selected'	=> isset($_POST['selected']) && in_array($result['option_id'], $_POST['selected']),
				'action'	=> $action
			);
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
		
		$this->data['sort_name'] = $this->url->link('catalog/option', 'sort=od.name' . $url);
		$this->data['sort_sort_order'] = $this->url->link('catalog/option', 'sort=o.sort_order' . $url);
		
		$url = $this->get_url(array('sort','order'));

		$this->pagination->init();
		$this->pagination->total = $option_total;
		$this->data['pagination'] = $this->pagination->render();
		
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
		$this->template->load('catalog/option_form');

		$option_id = isset($_GET['option_id'])?$_GET['option_id']:false;
		
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/option'));
		
		if (!$option_id) {
			$this->data['action'] = $this->url->link('catalog/option/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/option/update', 'option_id=' . $option_id . $url);
		}

		$this->data['cancel'] = $this->url->link('catalog/option', $url);

		if ($option_id && !$this->request->isPost()) {
			$option_info = $this->Model_Catalog_Option->getOption($option_id);
		}
		
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		$defaults = array(
			'option_description'=>array(),
			'type'=>'',
			'sort_order'=>'',
			'option_values'=>array()
		);
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($option_info[$d])) {
				$this->data[$d] = $option_info[$d];
			} elseif (!$option_id) {
				$this->data[$d] = $value;
			}
		}
		
		if (!isset($this->data['option_description'])) {
			$this->data['option_description'] = $this->Model_Catalog_Option->getOptionDescriptions($option_id);
		}
		
		if (!isset($this->data['option_values'])) {
			$option_values = $this->Model_Catalog_Option->getOptionValueDescriptions($option_id);
			
			foreach ($option_values as &$option_value) {
				if ($option_value['image'] && file_exists(DIR_IMAGE . $option_value['image'])) {
					$image = $option_value['image'];
				} else {
					$image = 'no_image.png';
				}
				
				$option_value['image'] = $image;
				$option_value['thumb'] = $this->image->resize($image, 100, 100);
			}
			
			$this->data['option_values'] = $option_values;
		}
		
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/option')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['option_description'] as $language_id => $value) {
			if ((strlen($value['name']) < 1) || (strlen($value['name']) > 128)) {
				$this->error["option_description[$language_id][name]"] = $this->_('error_name');
			}
			if ((strlen($value['display_name']) < 1) || (strlen($value['display_name']) > 128)) {
				$this->error["option_description[$language_id][display_name]"] = $this->_('error_display_name');
			}
		}

		if (!isset($_POST['option_value'])) {
			$this->error['warning'] = $this->_('error_type');
		}
		else {
			foreach ($_POST['option_value'] as $option_value_id => $option_value) {
				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					if ((strlen($option_value_description['name']) < 1) || (strlen($option_value_description['name']) > 128)) {
						$this->error['option_value'.$option_value_id] = $this->_('error_option_value');
					}
				}
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/option')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $option_id) {
			$data = array (
				'options' => array($option_id),
			);
			
			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->_('error_product'), $product_total);
			}
		}

		return $this->error ? false : true;
	}
	
	public function autocomplete()
	{
		$json = array();
		
		if (isset($_GET['filter_name'])) {
			$this->load->language('catalog/option');
			
			$data = array(
				'filter_name' => $_GET['filter_name'],
				'start'		=> 0,
				'limit'		=> 20
			);
			
			$options = $this->Model_Catalog_Option->getOptions($data);
			
			foreach ($options as $option) {
				$option_value_data = array();
				
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_values = $this->Model_Catalog_Option->getOptionValues($option['option_id']);
					
					foreach ($option_values as $option_value) {
						if ($option_value['image'] && file_exists(DIR_IMAGE . $option_value['image'])) {
							$image = $this->image->resize($option_value['image'], 50, 50);
						} else {
							$image = '';
						}
													
						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'				=> html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8'),
							'image'			=> $image
						);
					}
					
					$sort_order = array();
				
					foreach ($option_value_data as $key => $value) {
						$sort_order[$key] = $value['name'];
					}
			
					array_multisort($sort_order, SORT_ASC, $option_value_data);
				}
				
				$type = '';
				
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$type = $this->_('text_choose');
				}
				
				if ($option['type'] == 'text' || $option['type'] == 'textarea') {
					$type = $this->_('text_input');
				}
				
				if ($option['type'] == 'file') {
					$type = $this->_('text_file');
				}
				
				if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$type = $this->_('text_date');
				}
												
				$json[] = array(
					'option_id'	=> $option['option_id'],
					'name'			=> html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8'),
					'category'	=> $type,
					'type'			=> $option['type'],
					'option_value' => $option_value_data
				);
			}
		}

		$sort_order = array();
	
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);
				
		$this->response->setOutput(json_encode($json));
	}
	
	private function get_url()
	{
		$url = '';
		$filters = array('sort', 'order', 'page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}
