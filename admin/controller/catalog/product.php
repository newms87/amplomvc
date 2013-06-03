<?php
class Admin_Controller_Catalog_Product extends Controller 
{
  	public function index()
  	{
		$this->load->language('catalog/product');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert()
  	{
		$this->load->language('catalog/product');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Product->addProduct($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
	
			$this->getList();
			return;
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('catalog/product');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Product->editProduct($_GET['product_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
			
			$this->getList();
			return;
		}

		$this->getForm();
  	}

  	public function delete()
  	{
  		
		$this->load->language('catalog/product');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $product_id) {
				$this->Model_Catalog_Product->deleteProduct($product_id);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
		}

		$this->getList();
  	}

  	public function copy()
  	{
		$this->load->language('catalog/product');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateCopy()) {
			foreach ($_POST['selected'] as $product_id) {
				$this->Model_Catalog_Product->copyProduct($product_id);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
		}

		$this->getList();
 	}
	
	public function batch_update()
	{
		$this->load->language('catalog/product');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && isset($_GET['action']) && $this->validateCopy()) {
			foreach ($_POST['selected'] as $product_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Catalog_Product->updateProductValue($product_id, 'status',1);
						break;
					case 'disable':
						$this->Model_Catalog_Product->updateProductValue($product_id, 'status',0);
						break;
					case 'date_expires':
						$this->Model_Catalog_Product->updateProductValue($product_id,'date_expires',$_GET['action_value']);
						break;
					case 'is_final':
						$this->Model_Catalog_Product->updateProductValue($product_id,'is_final',$_GET['action_value']);
						break;
					case 'add_cat':
						$this->Model_Catalog_Product->updateProductCategory($product_id, 'add',$_GET['action_value']);
						break;
					case 'remove_cat':
						$this->Model_Catalog_Product->updateProductCategory($product_id, 'remove',$_GET['action_value']);
						break;
					case 'editable':
						$this->Model_Catalog_Product->updateProductValue($product_id,'editable',$_GET['action_value']);
						break;
					case 'ship_policy':
						$this->Model_Catalog_Product->updateProductDescriptions($product_id,'shipping_return', $_GET['action_value']);
						break;
					default:
						$this->error['warning'] = "Invalid Action Selected!";
						break;
				}
				if($this->error)
					break;
			}
			if (!$this->error) {
				if (!$this->message->error_set()) {
					$this->message->add('success',$this->_('text_success'));
				}
			}
		}

		$this->getList();
	}
	
  	private function getList()
  	{
		$this->template->load('catalog/product_list');
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/product'));
		
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
			'sort_value' => 'pd.name',
		);
		
		$columns['model'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_model'),
			'filter' => true,
			'sortable' => true,
			'sort_value' => 'p.model',
		);
		
		$columns['price'] = array(
			'type' => 'int',
			'display_name' => $this->_('column_price'),
			'filter' => true,
			'sortable' => true,
			'sort_value' => 'p.price',
		);
		
		$columns['cost'] = array(
			'type' => 'int',
			'display_name' => $this->_('column_cost'),
			'filter' => true,
			'sortable' => true,
			'sort_value' => 'p.cost',
		);
		
		$columns['categories'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_category'),
			'filter' => true,
			'build_config' => array('category_id' => 'name'),
			'build_data' => $this->Model_Catalog_Category->getCategories(),
			'sortable' => false,
		);
		
		/*
		$columns['manufacturer_id'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_manufacturer'),
			'filter' => true,
			'build_config' => array('manufacturer_id' => 'name'),
			'build_data' => $this->Model_Catalog_Manufacturer->getManufacturers(),
			'sortable' => true,
			'sort_value' => 'm.name',
		);
		*/
		
		$columns['quantity'] = array(
			'type' => 'int',
			'display_name' => $this->_('column_quantity'),
			'filter' => true,
			'sortable' => true,
			'sort_value' => 'p.quantity',
		);
		
		/*
		$columns['date_expires'] = array(
			'type' => 'datetime',
			'display_name' => $this->_('column_date_expires'),
			'filter' => true,
			'sortable' => true,
		);
		*/
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
			'sort_value' => 'p.status',
		);

  		//The Sort data
		$data = array();
		
		$sort_defaults = array(
			'sort' => 'name',
			'order' => 'ASC',
			'limit' => $this->config->get('config_admin_limit'),
			'page' => 1,
		);
		
		foreach ($sort_defaults as $key => $default) {
			$data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($page - 1) * $limit;
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$data += $filter_values;
		}
		
		$url = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$product_total = $this->Model_Catalog_Product->getTotalProducts($data);
		$products = $this->Model_Catalog_Product->getProducts($data);
		
		foreach ($products as &$product) {
			$product['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/product/update', 'product_id=' . $product['product_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/product/delete', 'product_id=' . $product['product_id'] . '&' . $url)
				)
			);
			
			$product['thumb'] = $this->image->resize($product['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));
			
			$product['categories'] = $this->Model_Catalog_Product->getProductCategories($product['product_id']);
			
			$product['collections'] = $this->Model_Catalog_Collection->getCollectionsForProduct($product['product_id']);
			
			$product['price'] = $this->currency->format($product['price']);
			$product['cost'] = $this->currency->format($product['cost']);
			
			$special = $this->Model_Catalog_Product->getProductActiveSpecial($product['product_id']);
			$product['special'] = !empty($special) ? $this->currency->format($special['price']) : '';
			
			if ($product['special']) {
				$product['price'] = "<span class =\"product_retail\">$product[price]</span><br /><span class=\"product_special\">$product[special]</span>";
			}
			
			//The # in front of the key signifies we want to output the raw string for the value when rendering the table
			if ($product['date_expires'] == DATETIME_ZERO) {
				$product['#date_expires'] = $this->_('text_no_expiration');
			}
		}
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'product_id',
			'route'		=> 'catalog/product',
			'sort'		=> $sort,
			'order'		=> $order,
			'page'		=> $page,
			'sort_url'	=> $this->url->link('catalog/product', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $products,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->mytable->init();
		$this->mytable->set_template('table/list_view');
		$this->mytable->set_template_data($tt_data);
		$this->mytable->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->mytable->build();
		
		$categories = $this->Model_Catalog_Category->getCategories(null);
		
		//Batch actions
		$this->data['batch_actions'] = array(
			'enable'=> array(
				'label' => "Enable",
			),
			
			'disable' => array(
				'label' => "Disable",
			),
			
			'date_expires' => array(
				'label'	=> "Product Expiration Date",
				'type'	=>'datetime',
				'default'=> DATETIME_ZERO,
			),
			
			'is_final' => array(
				'label' => "Final Sale",
				'type' => 'select',
				'default' => 1,
				'build_data' => $this->_('data_yes_no'),
			),
			
			'add_cat' => array(
				'label' => "Add Category",
				'type' => 'select',
				'build_data' => $categories,
				'build_config' => array('category_id' => 'name'),
			),
			
			'remove_cat' => array(
				'label' => "Remove Category",
				'type' => 'select',
				'build_data' => $categories,
				'build_config' => array('category_id' => 'name'),
			),
			
			'editable' => array(
				'label' => "Allow Designer Edits",
				'type' => 'select',
				'default' => 1,
				'build_data' => $this->_('data_yes_no'),
			),
			
			'ship_policy' => array(
				'label' => "Update Shipping / Return Policy",
				'type' => 'ckedit',
				'default' => $this->_('shipping_return_policy'),
			)
		);
		
		$url = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->data['batch_update'] = $this->url->link('catalog/product/batch_update', $url);
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/product/insert', $url);
		
		//Pagination
		$url = $this->url->get_query('filter', 'sort', 'order');
		
		$this->pagination->init();
		$this->pagination->total = $product_total;
		$this->data['pagination'] = $this->pagination->render();
		
		//Child Templates
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
  	}

  	private function getForm()
  	{
		$this->template->load('catalog/product_form');

  		$product_id = $this->data['product_id'] = isset($_GET['product_id'])?$_GET['product_id']:false;
		
		$url = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/product'));
		
		if (!$product_id) {
			$this->data['action'] = $this->url->link('catalog/product/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/product/update', 'product_id=' . $product_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/product', $url);

		if ($product_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		}
		
		$default_english = array('shipping_return'=>$this->_('shipping_return_policy'), 'description'=>'','meta_description'=>'','meta_keyword'=>'','name'=>'','blurb'=>'');
		
		$defaults = array(
			'model'=>'',
			'sku'=>'',
			'upc'=>'',
			'location'=>'',
			'keyword'=>'',
			'product_store'=>array(0,1,2),
			'product_description'=>array(1=>$default_english),
			'product_tag'=>array(),
			'image'=>'',
			'manufacturer_id' => 0,
			'shipping'=>1,
			'price'=>'',
			'cost'=>'',
			'is_final'=>0,
			'tax_class_id'=>$this->config->get('config_tax_default_id'),
			'date_available'=>date_format(new DateTime(),"Y-m-d H:i:s"),
			'date_expires'=>'',
			'editable' => 1,
			'quantity'=>1,
			'minimum'=>1,
			'subtract'=>1,
			'sort_order'=>1,
			'stock_status_id'=>$this->config->get('config_stock_status_id'),
			'status'=>1,
			'weight'=>'',
			'weight_class_id'=>$this->config->get('config_weight_class_id'),
			'length'=>'',
			'width'=>'',
			'height'=>'',
			'length_class_id'=>$this->config->get('config_length_class_id'),
			'product_options'=>array(),
			'product_discounts'=>array(),
			'product_specials'=>array(),
			'product_attributes'=>array(), //changed product_attribute -> product_attributes for the POST in the template
			'product_images'=>array(), //changed product_image -> product_images for the POST in the template
			'product_download'=>array(),
			'product_category'=>array(),
			'product_related'=>array(),
			'points'=>'',
			'product_reward'=>array(),
			'product_layout'=>array(),
			'product_template' => array(),
			);

		foreach($defaults as $d=>$default)
{
			if (isset($_POST[$d]))
				$this->data[$d] = $_POST[$d];
			elseif (isset($product_info[$d]))
				$this->data[$d] = $product_info[$d];
			elseif(!$product_id)
				$this->data[$d] = $default;
		}
		
		if (isset($this->data['keyword'])) {
			$this->data['keyword'] = preg_replace("/^[\/]?product[\/]?/i","",$this->data['keyword']);
		}
		else {
			$this->data['keyword'] = '';
		}
		
		if (!isset($this->data['product_description'])) {
			$this->data['product_description'] = $this->Model_Catalog_Product->getProductDescriptions($product_id);
			
			if (!empty($this->data['product_description'][$this->config->get('config_language_id')]['name'])) {
				$this->breadcrumb->add($this->data['product_description'][$this->config->get('config_language_id')]['name'], $this->url->link('catalog/product/update', 'product_id=' . $product_id));
			}
		}
		
		if (!isset($this->data['product_store'])) {
			$this->data['product_store'] = $this->Model_Catalog_Product->getProductStores($product_id);
		}
		
		if (!isset($this->data['product_tag'])) {
			$this->data['product_tag'] = $this->Model_Catalog_Product->getProductTags($product_id);
		}
		
		if (!isset($this->data['date_available'])) {
			$this->data['date_available'] = date('Y-m-d', strtotime($product_info['date_available']));
		}
		
		$this->data['data_languages'] = $this->Model_Localisation_Language->getLanguageList();
		
		
		$thumb = ($this->data['image'] && file_exists(DIR_IMAGE . $this->data['image']))?$this->data['image']:'no_image.png';
		$this->data['thumb'] = $this->image->resize($thumb, 100, 100);
		
		$this->data['manufacturers'] = array(0=>$this->_('text_none'));
		
		$m_data = array(
			'sort' => 'name'
		);
		
		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers($m_data);
		
		foreach ($manufacturers as $man) {
			$this->data['manufacturers'][$man['manufacturer_id']] = $man['name'];
		}
		
		$this->data['tax_classes'] = array_merge(array(0=>'--- None ---'),$this->Model_Localisation_TaxClass->getTaxClasses());
	
		$this->data['stock_statuses'] = $this->Model_Localisation_StockStatus->getStockStatuses();
		
		
		$this->data['weight_classes'] = $this->Model_Localisation_WeightClass->getWeightClasses();
		
		
		$this->data['length_classes'] = $this->Model_Localisation_LengthClass->getLengthClasses();
		
		//Get Product Attributes
		if (!isset($this->data['product_attributes'])) {
			$this->data['product_attributes'] = $this->Model_Catalog_Product->getProductAttributes($product_id);
		}
		
		
		/**
		* NOTE to clarify options / product options:
		*
		* An option contains a set of 1 or more option_values
		*
		* A product_option is associated to a product and contains product_option_values (a subset option_values from an option),
		* A product_option_value has a reference to an option_value and contains additional data.
		*
		*/
		
		//Get Product Options with product_option_values
		if (!isset($this->data['product_options'])) {
			$product_options = $this->Model_Catalog_Product->getProductOptions($product_id);
			
			$this->data['product_options'] = array();
			
			foreach ($product_options as $product_option) {
				$this->data['product_options'][$product_option['product_option_id']] = $product_option;
				
				$product_option_value_data = array();
				
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$pov_id = $product_option_value['product_option_value_id'];
					
					$product_option_value_data[$pov_id] = $product_option_value;
					
					//Add restrictions to the product_option_value
					if (isset($restrictions[$pov_id])) {
						$product_option_value_data[$pov_id]['restrictions'] = $restrictions[$pov_id];
					}
				}
				
				$this->data['product_options'][$product_option['product_option_id']]['product_option_value'] = $product_option_value_data;
			}
		}
		
		//Ensure that the index 'product_option_value' is set for each option
		foreach ($this->data['product_options'] as &$po) {
			if (!isset($po['product_option_value'])) {
				$po['product_option_value'] = array();
			}
		}
		
		//Get All option values currently assigned to this product in flat list (for option restrictions)
		$this->data['all_product_option_values'] = array();
		
		foreach ($this->data['product_options'] as $key=>$product_option) {
			foreach ($product_option['product_option_value'] as $product_option_value) {
				$this->data['all_product_option_values'][$product_option_value['option_value_id']] = $product_option_value;
			}
		}
		
		//Get Set of Option Values
		$this->data['option_values'] = array();
		
		foreach ($this->data['product_options'] as $product_option) {
			if (!isset($this->data['option_values'][$product_option['option_id']])) {
				$this->data['option_values'][$product_option['option_id']] = $this->Model_Catalog_Option->getOptionValues($product_option['option_id']);
			}
		}
		
		//Find set of option values that are not associated to a product_option
		$this->data['unused_option_values'] = $this->data['option_values'];
		
		foreach ($this->data['unused_option_values'] as $option_id=>$option_value) {
			foreach ($this->data['product_options'] as $product_option) {
				if($product_option['option_id'] != $option_id)continue;
				foreach ($product_option['product_option_value'] as $product_option_value) {
					foreach ($option_value as $key=>$ov) {
						if ($product_option_value['option_value_id'] == $ov['option_value_id']) {
							unset($this->data['unused_option_values'][$option_id][$key]);
						}
					}
				}
			}
		}
		
		$this->language->format('text_option_help', $this->config->get('config_email'));
		$this->language->format('text_not_editable', $this->data['product_description'][$this->config->get('config_language_id')]['name'], $this->config->get('config_email'));
		
		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		
		if (!isset($this->data['product_discounts'])) {
			$this->data['product_discounts'] = $this->Model_Catalog_Product->getProductDiscounts($product_id);
		}

		if (!isset($this->data['product_specials'])) {
			$this->data['product_specials'] = $this->Model_Catalog_Product->getProductSpecials($product_id);
		}
		
		if (!isset($this->data['product_images'])) {
			$this->data['product_images'] = $this->Model_Catalog_Product->getProductImages($product_id);
		}
		
		foreach ($this->data['product_images'] as &$product_image) {
			if (!$product_image['image'] || !file_exists(DIR_IMAGE . $product_image['image'])) {
				$product_image['image'] = 'no_image.png';
			}
			
			$product_image['thumb'] = $this->image->resize($product_image['image'], 100, 100);
		}
		
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);

		$this->data['data_downloads'] = $this->Model_Catalog_Download->getDownloads();
		
		if (!isset($this->data['product_download'])) {
			$this->data['product_download'] = $this->Model_Catalog_Product->getProductDownloads($product_id);
		}
		
		$this->data['data_categories'] = $this->Model_Catalog_Category->getCategories(0);
		
		if (!isset($this->data['product_category'])) {
			$this->data['product_category'] = $this->Model_Catalog_Product->getProductCategories($product_id);
		}
		
		if (!isset($this->data['product_related'])) {
			$products = $this->Model_Catalog_Product->getProductRelated($product_id);
			$this->data['product_related'] = array();
		
			foreach ($products as $product_id) {
				$related_info = $this->Model_Catalog_Product->getProduct($product_id);
				
				if ($related_info) {
					$this->data['product_related'][] = array(
						'product_id' => $related_info['product_id'],
						'name'		=> $related_info['name']
					);
				}
			}
		}
						
		if (!isset($this->data['product_reward'])) {
			$this->data['product_reward'] = $this->Model_Catalog_Product->getProductRewards($product_id);
		}
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		if (!isset($this->data['product_layout'])) {
			$this->data['product_layout'] = $this->Model_Catalog_Product->getProductLayouts($product_id);
		}
		
		$this->data['layouts'] = array(0 => '') +	$this->Model_Design_Layout->getLayouts();
		
		if (!isset($this->data['product_template'])) {
			$this->data['product_template'] = $this->Model_Catalog_Product->getProductTemplates($product_id);
		}
		
		$this->data['templates'] = $this->Model_Design_Template->getTemplatesFor('product', true);
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm()
  	{
  		
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['product_description'] as $language_id => $value) {
			if ((strlen($value['name']) < 1) || (strlen($value['name']) > 255)) {
			$this->error['product_description['.$language_id.'][name]'] = $this->_('error_name');
			}
		}
		
		if ((strlen($_POST['model']) < 1) || (strlen($_POST['model']) > 64)) {
			$this->error['model'] = $this->_('error_model');
		}
		elseif (!isset($_GET['product_id'])) {
			$dup = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($_POST['model']) . "'");
			if($dup->row['count'] > 0)
				$this->error['model'] = $this->_('error_dup_model');
		}
		
		if ((strlen($_POST['keyword']) < 1) || (strlen($_POST['keyword']) > 255)) {
				$this->error['keyword'] = $this->_('error_keyword');
		}
		
		if (isset($_POST['product_images'])) {
			foreach ($_POST['product_images']  as $key=>$image) {
				if (strtolower($image['image']) == 'data/no_image.png' || !$image['image']) {
					unset($_POST['product_images'][$key]);
				}
			}
		}
		
		$product_options = isset($_POST['product_options']) ? $_POST['product_options'] : false;
		//validate the quantities
		if ($product_options) {
			$po_quantity = array();
			
			foreach ($product_options as $option_id=>$product_option) {
				if (!isset($product_option['product_option_value'])) {
					$this->error["option-value$option_id"] = $this->language->format('error_no_option_value', $product_option['name']);
					continue;
				}
				
				if(!$product_option['required']) continue;
				
				if ((string)$po_quantity != 'INF') {
					$po_quantity[$option_id] = 0;
				}
				
				foreach ($product_option['product_option_value'] as $option_value_id=>$product_option_value) {
					if (!$product_option_value['subtract']) {
						$po_quantity = 'INF';
						continue;
					}
					
					if ((string)$po_quantity != 'INF') {
						$po_quantity[$option_id] += (int)$product_option_value['quantity'];
					}
					
					if (isset($product_option_value['restrictions'])) {
						$restrict_quantity = 0;
						
						foreach ($product_option_value['restrictions'] as $r_key=>$restriction) {
							$restrict_quantity += (int)$restriction['quantity'];
						}
						
						if ($restrict_quantity > (int)$product_option_value['quantity']) {
							$this->error["product_options[$option_id][product_option_value][$option_value_id][quantity]"] = $this->language->format('error_restrict_quantity', $product_option_value['quantity'], $restrict_quantity);
						}
					}
				}
			}
			
			if ($po_quantity != 'INF' && min($po_quantity) < (int)$_POST['quantity']) {
				$this->error['quantity'] = $this->language->format('error_po_quantity', $_POST['quantity'], min($po_quantity));
			}
		}
		
		//validate the option restrictions
		if ($product_options) {
			foreach ($product_options as $option_id=>$product_option) {
				if (!isset($product_option['product_option_value'])) {
					continue;
				}
				
				foreach ($product_option['product_option_value'] as $option_value_id=>$product_option_value) {
					if (isset($product_option_value['restrictions'])) {
						foreach ($product_option_value['restrictions'] as $r_key=>$restriction) {
							if ($restriction['restrict_option_value_id'] == $option_value_id) {
								$this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_pov_restrict_same');
							}
							
							foreach ($product_option_value['restrictions'] as $r_key2=>$restriction2) {
								if ($r_key != $r_key2) {
									if ($restriction['restrict_option_value_id'] == $restriction2['restrict_option_value_id']) {
										$this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_dup_restrict');
									}
								}
							}
							
							if (isset($product_option['product_option_value'][$restriction['restrict_option_value_id']])) {
								$this->error["product_options[$option_id][product_option_value][$option_value_id][restrictions][$r_key][restrict_option_value_id]"] = sprintf($this->_('error_restrict_same_option_id'), ucfirst($product_option['type']));
							}
						}
					}
				}
			}
		}
		
		return $this->error ? false : true;
  	}
	
  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/product') && !$this->user->hasPermission('access','user/user_permission')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
  	}
  	
  	private function validateCopy()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
  	}
	
	public function select()
	{
		if(!isset($_POST['filter'])) return;
		
		$filter = $_POST['filter'];
		$select = isset($_POST['select']) ? $_POST['select'] : array();
		$field_keys = isset($_POST['fields']) ? $_POST['fields'] : array();
		
		$fields = array();
		foreach (explode(',',$field_keys) as $key) {
			$fields[$key] = 1;
		}
		
		$products = $this->Model_Catalog_Product->getProducts($filter);
		
		$html = '';
		
		foreach ($products as $product) {
			$data[$product['product_id']] = array_intersect_key($product, $fields);
			
			$selected = $product['product_id'] == $select ? 'selected="selected"' : '';
			
			$html .= "<option value='$product[product_id]' $selected>$product[name]</option>";
		}
		echo json_encode(array('option_data' => $data, 'html' =>$html));
		exit();
	}
	
	public function autocomplete()
	{
		$json = array();
		
		$query_args = array(
			'start' => 0,
			'limit' => 20,
		);
		
		$data = array();
		
		foreach ($query_args as $key => $default) {
			if (isset($_GET[$key])) {
				$data[$key] = $_GET[$key];
			} elseif (!is_null($default)) {
				$data[$key] = $default;
			}
		}
		
		$data += !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		$results = $this->Model_Catalog_Product->getProducts($data);
		
		foreach ($results as $result) {
			$option_data = array();
			
			$product_options = $this->Model_Catalog_Product->getProductOptions($result['product_id']);
			
			foreach ($product_options as $product_option) {
				$option_value_data = array();
				
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'			=> $product_option_value['option_value_id'],
						'name'						=> $product_option_value['name'],
						'price'						=> (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
					);
				}
			
				$option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'			=> $product_option['option_id'],
					'name'				=> $product_option['name'],
					'type'				=> $product_option['type'],
					'option_value'		=> $option_value_data,
					'required'			=> $product_option['required']
				);
			}
			
			$json[] = array(
				'product_id' => $result['product_id'],
				'name'		=> html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				'model'		=> $result['model'],
				'option'	=> $option_data,
				'image'		=> $result['image'],
				'thumb'		=> $this->image->resize($result['image'], 100,100),
				'price'		=> $result['price']
			);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function generate_url()
	{
		$name = isset($_POST['name'])?$_POST['name']:'';
		$product_id = isset($_POST['product_id'])?$_POST['product_id']:'';
		if(!$name)return;
		
		echo json_encode($this->Model_Catalog_Product->generate_url($product_id,$name));
		exit;
	}
	
	public function generate_model()
	{
		$name = isset($_POST['name'])?$_POST['name']:'';
		if(!$name)return;
		
		echo json_encode($this->Model_Catalog_Product->generate_model($name));
		exit;
	}

	public function fill_shipping_return_policy()
	{
		$id = isset($_POST['manufacturer_id'])?$_POST['manufacturer_id']:'';
		if(!$id)return;
		
		$desc = $this->Model_Catalog_Manufacturer->getManufacturerDescriptions($id);
		echo json_encode($desc);
		exit;
	}
}
