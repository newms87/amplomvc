<?php
class Admin_Controller_Catalog_Flashsale extends Controller 
{
	
 
	public function index()
	{
		$this->load->language('catalog/flashsale');
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('catalog/flashsale');
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Flashsale->addFlashsale($_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('catalog/flashsale'));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('catalog/flashsale');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Flashsale->editFlashsale($_GET['flashsale_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('catalog/flashsale'));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/flashsale');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateModify()) {
			foreach ($_POST['selected'] as $flashsale_id) {
				$this->Model_Catalog_Flashsale->deleteFlashsale($flashsale_id);
			}

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('catalog/flashsale'));
		}

		$this->getList();
	}
	public function list_update()
	{
		$this->load->language('catalog/flashsale');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && isset($_GET['action']) && $this->validateModify()) {
			foreach ($_POST['selected'] as $flashsale_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Catalog_Flashsale->updateStatus($flashsale_id, 1);
						break;
					case 'disable':
						$this->Model_Catalog_Flashsale->updateStatus($flashsale_id, 0);
						break;
					default:
						$this->error['warning'] = "Invalid Action Selected!";
						break;
				}
				if($this->error)
					break;
			}
			if (!$this->error) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/flashsale', $this->url->get_query()));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('catalog/flashsale_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/flashsale'));
		
		$sort_list = array(
			'sort'  =>'name',
			'order' =>'ASC',
			'page'  =>1
		);
		foreach ($sort_list as $key=>$default) {
			if (isset($_GET[$key])) {
				$data[$key] = $$key = $_GET[$key];
			}
			else {
				$data[$key] = $$key = $default;
			}
		}
		
		$filter_list = array(
			'filter'				=>'',
			'filter_name'		=>'',
			'filter_date_start' =>'',
			'date_start_prefix' =>'',
			'filter_date_end'	=>$this->tool->format_datetime(),
			'date_end_prefix'	=>'>',
			'filter_status'	=>''
		);
		
		$filter_set = isset($_GET['filter']);
		
		foreach ($filter_list as $key=>$default) {
			$d_key = str_replace("filter_",'', $key);
			if (isset($_GET[$key])) {
				$data[$d_key] = $this->data[$key] = $this->url->decodeURIcomponent($_GET[$key]);
			}
			else {
				$data[$d_key] = $this->data[$key] = $filter_set ? '' : $default;
			}
		}
		
		if(!$data['date_start'])
			$data['date_start_prefix'] = '';
		if(!$data['date_end'])
			$data['date_end_prefix'] = '';
		
		$this->data['filter_list'] = array_keys($filter_list);
		
		$this->data['flashsales'] = array();
		
		$data['limit'] = $this->config->get('config_admin_limit');
		$data['start'] = ($data['page'] - 1) * $data['limit'];
		
		$flashsale_total = $this->Model_Catalog_Flashsale->getTotalFlashsales($data);
		$results = $this->Model_Catalog_Flashsale->getFlashsales($data);
		
		foreach ($results as $result) {
			$action = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/flashsale/update', 'flashsale_id=' . $result['flashsale_id'])
			);
			
			$discount = $result['discount_type']=='percent'?"-$result[discount]%":"-".$this->currency->format($result['discount']);
			
			$url_alias = $this->Model_Setting_UrlAlias->getUrlAliasByRouteQuery('sales/flashsale', 'flashsale_id='.$result['flashsale_id']);
			
			$this->data['flashsales'][] = array(
				'flashsale_id' => $result['flashsale_id'],
				'name'		=> $result['name'],
				'keyword'		=> isset($url_alias['keyword'])?$url_alias['keyword']:'(NO URL)',
				'image'		=> $this->image->resize($result['image'],80,80),
				'designers'	=> $this->Model_Catalog_Flashsale->getFlashsaleDesigners($result['flashsale_id']),
				'discount'		=> $discount,
				'date_start'		=> $result['date_start'],
				'date_end'		=> $result['date_end'],
				'status'		=> $result['status'],
				'selected'	=> isset($_POST['selected']) && in_array($result['flashsale_id'], $_POST['selected']),
				'action'		=> $action
			);
		}
		
		$url = $this->url->get_query(array_merge(array_keys($sort_list), array_keys($filter_list)));
		
		$this->data['update_actions'] = array('enable'=>'Enable','disable'=>'Disable');
		
		$this->data['list_update'] = $this->url->link('catalog/flashsale/list_update', 'action=%action%' . $url);
		
		
		
		$this->data['insert'] = $this->url->link('catalog/flashsale/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/flashsale/delete', $url);
		
		
		$url = $this->url->get_query(array_merge(array('page'), array_keys($filter_list)));
		
		$url .= $order == 'ASC'? '&order=DESC':'&order=ASC';
		
		$sort_by = array(
			'name'=>'name',
			'keyword'=>'keyword',
			'discount'=>'discount',
			'date_start'=>'date_start',
			'date_end'=>'date_end',
			'status'=>'status'
		);
		foreach ($sort_by as $key=>$s) {
			$this->data['sort_'.$s] = $this->url->link('catalog/flashsale', 'sort=' . $key . '&' . $url);
		}
		
		$url = $this->url->get_query(array_merge(array('sort', 'order'), array_keys($filter_list)));

		$this->pagination->init();
		$this->pagination->total = $flashsale_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = strtolower($order);
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('catalog/flashsale_form');

		$flashsale_id = $this->data['flashsale_id'] = isset($_GET['flashsale_id'])?$_GET['flashsale_id']:0;
		
		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();
		$this->data['designer_list'] = array(0=>'(Select)');
		foreach($manufacturers as $m)
			$this->data['designer_list'][$m['manufacturer_id']] = $m['name'];
		
		$this->data['autofill_url'] = $this->url->link('catalog/flashsale/get_designer_info');
		
		$cgs = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		
		$this->data['section_attrs'] = array(0=>'( None )');
		$attrs = $this->Model_Catalog_AttributeGroup->getAttributeGroups();
		foreach($attrs as $a)
			$this->data['section_attrs'][$a['attribute_group_id']] = $a['name'];
		
		$this->data['customer_groups'] = array();
		foreach($cgs as $cg)
			$this->data['customer_groups'][$cg['customer_group_id']] = $cg['name'];
		
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/flashsale'));
		
		if (!$flashsale_id) {
			$this->data['action'] = $this->url->link('catalog/flashsale/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/flashsale/update', 'flashsale_id=' . $flashsale_id);
		}
		
		if ($flashsale_id) {
			$this->data['preview'] = $this->url->store($this->config->get('config_default_store'), 'sales/flashsale', 'flashsale_id=' . $flashsale_id . '&preview_flashsale=1');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/flashsale');

		$flashsale_info = null;
		if ($flashsale_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
				$flashsale_info = $this->Model_Catalog_Flashsale->getFlashsale($flashsale_id);
		}
		
		if (!empty($flashsale_info) && $flashsale_info['image'] && file_exists(DIR_IMAGE . $flashsale_info['image'])) {
			$this->data['thumb'] = $this->image->resize($flashsale_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->image->resize('no_image.png', 100, 100);
		}
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);
		
		
		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'extend_flashsale'=>0,
			'status'=>0,
			'name'=>'',
			'blurb'=>'',
			'teaser'=>'',
			'image'=>'no_image.png',
			'keyword'=>'',
			'date_start'=>'',
			'date_end'=>'',
			'discount'=>50,
			'discount_type'=>'percent',
			'products'=>array(),
			'articles'=>array(),
			'designers'=>array(),
			'section_attr'=>0,
			'customer_group_id'=>8 //this is the "Default" customer_group_id
		);

		
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (!empty($flashsale_info)) {
				$this->data[$d] = $flashsale_info[$d];
			} else {
				$this->data[$d] = $value;
			}
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}


	function get_designer_info()
	{
		if(!isset($_POST['designer_id']) || !$_POST['designer_id'])return;
		$designer_id = $_POST['designer_id'];
		$designer = $this->Model_Catalog_Manufacturer->getManufacturer($designer_id);
		$description = $this->Model_Catalog_Manufacturer->getManufacturerDescription($designer_id);
		
		$p_data = array(
			'filter_manufacturer_id' => $designer_id
		);
		$products = $this->Model_Catalog_Product->getProducts($p_data);
		
		$designer_info = array('designer_id'=>$designer_id,
									'products'=>$products,
									'name'=>$designer['name'],
									'image'=>$designer['image'],
									'thumb'=>$this->image->resize($designer['image']?$designer['image']:'no_image.png',100,100),
									'description'=>$description
									);
		echo json_encode($designer_info);
		exit;
	}
	
	public function generate_url()
	{
		$name = isset($_POST['name'])?$_POST['name']:'';
		$flashsale_id= isset($_POST['flashsale_id'])?$_POST['flashsale_id']:0;
		if(!$name)return;
		
		echo json_encode($this->Model_Catalog_Flashsale->generate_url($flashsale_id,$name));
		exit;
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/flashsale')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		if(!$_POST['name'])
			$this->error['name'] = $this->_('error_name');
		
		if(!$_POST['keyword'])
			$this->error['keyword'] = $this->_('error_keyword');
		
		if(!$_POST['date_start'])
			$this->error['date_start'] = $this->_('error_date');
		if(!$_POST['date_end'])
			$this->error['date_end'] = $this->_('error_date');
		
		$sort_order = 0;
		foreach ($_POST['products'] as &$product) {
			$product['sort_order'] = $sort_order++;
		}
		
		return $this->error ? false : true;
	}

	private function validateModify()
	{
		if (!$this->user->hasPermission('modify', 'catalog/flashsale')) {
			$this->error['warning'] = $this->_('error_permission');
		}
 
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
