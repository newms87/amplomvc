<?php
class Admin_Controller_Catalog_Product extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['product_id'])) {
				$this->Model_Catalog_Product->addProduct($_POST);
			} //Update
			else {
				$this->Model_Catalog_Product->editProduct($_GET['product_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified products!"));

				redirect('catalog/product');
			}
		}

		$this->getForm();
	}

	public function change_class()
	{
		if (!empty($_GET['product_id']) && $this->request->isPost() && $this->user->can('modify', 'catalog/product')) {
			$this->Model_Catalog_Product->editProduct($_GET['product_id'], $_POST, true);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("The Product's Class has been changed!"));
			}
		}

		redirect('catalog/product/update', $this->url->getQuery());
	}

	public function delete()
	{
		$this->document->setTitle(_l("Products"));

		if (!empty($_GET['product_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Product->deleteProduct($_GET['product_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified products!"));

				redirect('catalog/product');
			}
		}

		$this->getList();
	}

	public function copy()
	{
		$this->document->setTitle(_l("Products"));

		if (!empty($_GET['product_id']) && $this->validateCopy()) {
			$this->Model_Catalog_Product->copyProduct($_GET['product_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified products!"));

				redirect('catalog/product');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && !empty($_GET['action'])) {
			if (($_GET['action'] === 'copy' && !$this->validateCopy()) ||
				($_GET['action'] === 'delete' && !$this->validateDelete())
			) {
				//Action not allowed
				$this->message->add("warning", _l("The requested action failed."));
			} else {
				foreach ($_GET['selected'] as $product_id) {
					$data = array();

					switch ($_GET['action']) {
						case 'enable':
							$data['status'] = 1;
							break;
						case 'disable':
							$data['status'] = 0;
							break;
						case 'product_class_id':
						case 'date_expires':
						case 'shipping_policy_id':
						case 'return_policy_id':
							$data[$_GET['action']] = $_GET['action_value'];
							break;
						case 'add_cat':
						case 'remove_cat':
							$categories = $this->Model_Catalog_Product->getProductCategories($product_id);

							if ($_GET['action'] === 'add_cat') {
								$categories[]             = $_GET['action_value'];
								$data['product_category'] = $categories;
							} else {
								$data['product_category'] = array_diff($categories, array($_GET['action_value']));
							}
							break;
						case 'copy':
							$this->Model_Catalog_Product->copyProduct($product_id);
							break;
						case 'delete':
							$this->Model_Catalog_Product->deleteProduct($product_id);
							break;
						default:
							$this->message->add('warning', _l("The requested action failed."));
							break 2; //break the for loop
					}

					if ($data) {
						$this->Model_Catalog_Product->editProduct($product_id, $data, true);
					}
				}
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified products!"));
			}
		}

		redirect('catalog/product', $this->url->getQueryExclude('action', 'action_value'));
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Products"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Products"), site_url('catalog/product'));

		//The Table Columns
		$categories      = $this->Model_Catalog_Category->getCategoriesWithParents();
		$product_classes = $this->Model_Catalog_ProductClass->getProductClasses();

		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => _l("Image"),
			'filter'       => false,
			'sortable'     => true,
			'sort_value'   => '__image_sort__image',
		);

		$columns['product_class_id'] = array(
			'type'         => 'select',
			'display_name' => _l("Class"),
			'filter'       => true,
			'build_config' => array(
				'product_class_id',
				'name'
			),
			'build_data'   => $product_classes,
			'sortable'     => true,
			'sort_value'   => 'pc.name',
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Name"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.name',
		);

		$columns['model'] = array(
			'type'         => 'text',
			'display_name' => _l("Model ID"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.model',
		);

		$columns['price'] = array(
			'type'         => 'int',
			'display_name' => _l("Price"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.price',
		);

		$columns['cost'] = array(
			'type'         => 'int',
			'display_name' => _l("Cost"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.cost',
		);

		$columns['categories'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Categories"),
			'filter'       => true,
			'build_config' => array(
				'category_id',
				'pathname'
			),
			'build_data'   => $categories,
			'sortable'     => false,
		);

		$columns['quantity'] = array(
			'type'         => 'int',
			'display_name' => _l("Qty"),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.quantity',
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
			'sort_value'   => 'p.status',
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('p.name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);
		$products      = $this->Model_Catalog_Product->getProducts($sort + $filter);

		$url_query = $this->url->getQueryExclude('product_id');

		foreach ($products as &$product) {
			$product['actions'] = array(
				'edit' => array(
					'text' => _l("Edit"),
					'href' => site_url('catalog/product/update', 'product_id=' . $product['product_id'])
				),
				'copy' => array(
					'text' => _l("Copy"),
					'href' => site_url('catalog/product/copy', 'product_id=' . $product['product_id'])
				)
			);

			if (!$this->order->productInConfirmedOrder($product['product_id'])) {
				$product['actions']['delete'] = array(
					'text' => _l("Delete"),
					'href' => site_url('catalog/product/delete', 'product_id=' . $product['product_id'] . '&' . $url_query),
				);
			}

			$product['thumb'] = $this->image->resize($product['image'], option('config_image_admin_list_width'), option('config_image_admin_list_height'));

			$product['categories'] = $this->Model_Catalog_Product->getProductCategories($product['product_id']);

			$product['price'] = $this->currency->format($product['price']);
			$product['cost']  = $this->currency->format($product['cost']);

			$special            = $this->Model_Catalog_Product->getProductActiveSpecial($product['product_id']);
			$product['special'] = !empty($special) ? $this->currency->format($special['price']) : '';

			if ($product['special']) {
				$product['price'] = "<span class =\"product_retail\">$product[price]</span><br /><span class=\"product_special\">$product[special]</span>";
			}

			//The # in front of the key signifies we want to output the raw string for the value when rendering the table
			if ($product['date_expires'] === DATETIME_ZERO) {
				$product['#date_expires'] = _l("No Expiration");
			}

			if (!(int)$product['subtract']) {
				$product['quantity'] = _l("Unlimited");
			}
		}
		unset($product);

		//Build The Table
		$tt_data = array(
			'row_id' => 'product_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($products);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		//Template Data
		$data = array();

		$data['list_view'] = $this->table->render();

		if (count($product_classes) > 1) {
			foreach ($product_classes as &$product_class) {
				$product_class['insert'] = site_url('catalog/product/update', 'product_class_id=' . $product_class['product_class_id']);
			}
			unset($product_class);

			$data['product_classes'] = $product_classes;
		}

		//Batch actions
		$data['batch_actions'] = array(
			'enable'             => array(
				'label' => _l("Enable"),
			),

			'disable'            => array(
				'label' => _l("Disable"),
			),

			'product_class_id'   => array(
				'label'        => "Change Class",
				'type'         => 'select',
				'build_config' => array(
					'product_class_id',
					'name'
				),
				'build_data'   => $product_classes,
				'default'      => '',
			),

			'date_expires'       => array(
				'label'   => "Product Expiration Date",
				'type'    => 'datetime',
				'default' => $this->date->add('30 days'),
			),

			'shipping_policy_id' => array(
				'label'        => _l("Shipping Policy"),
				'type'         => 'select',
				'build_data'   => $this->cart->getShippingPolicies(),
				'build_config' => array(
					false,
					'title'
				),
				'default'      => option('config_default_shipping_policy'),
			),

			'return_policy_id'   => array(
				'label'        => _l("Return Policy"),
				'type'         => 'select',
				'build_data'   => $this->cart->getReturnPolicies(),
				'build_config' => array(
					false,
					'title'
				),
				'default'      => option('config_default_return_policy'),
			),

			'add_cat'            => array(
				'label'        => "Add Category",
				'type'         => 'select',
				'build_data'   => $categories,
				'build_config' => array(
					'category_id',
					'name'
				),
			),

			'remove_cat'         => array(
				'label'        => "Remove Category",
				'type'         => 'select',
				'build_data'   => $categories,
				'build_config' => array(
					'category_id',
					'name'
				),
			),

			'copy'               => array(
				'label' => _l("Copy"),
			),

			'delete'             => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_update'] = 'catalog/product/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $product_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = site_url('catalog/product/update');

		//Render
		$this->response->setOutput($this->render('catalog/product_list', $data));
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Products"));

		//Insert or Update
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

		$data = array(
			'product_id' => $product_id,
		);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Products"), site_url('catalog/product'));

		if (!$product_id) {
			$this->breadcrumb->add(_l("Add"), site_url('catalog/product/update'));
		} else {
			$this->breadcrumb->add(_l("Edit"), site_url('catalog/product/update', 'product_id=' . $product_id));
		}

		//Load Information
		$product_info = array();

		if ($this->request->isPost()) {
			$product_info = $_POST;
		} elseif ($product_id) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);

			$product_info['product_stores']     = $this->Model_Catalog_Product->getProductStores($product_id);
			$product_info['product_attributes'] = $this->Model_Catalog_Product->getProductAttributes($product_id);
			$product_info['product_options']    = $this->Model_Catalog_Product->getProductOptions($product_id);
			$product_info['product_discounts']  = $this->Model_Catalog_Product->getProductDiscounts($product_id);
			$product_info['product_specials']   = $this->Model_Catalog_Product->getProductSpecials($product_id);
			$product_info['product_images']     = $this->Model_Catalog_Product->getProductImages($product_id);
			$product_info['product_categories'] = $this->Model_Catalog_Product->getProductCategories($product_id);
			$product_info['product_downloads']  = $this->Model_Catalog_Product->getProductDownloads($product_id);
			$product_info['product_rewards']    = $this->Model_Catalog_Product->getProductRewards($product_id);
			$product_info['product_layouts']    = $this->Model_Catalog_Product->getProductLayouts($product_id);
			$product_info['product_templates']  = $this->Model_Catalog_Product->getProductTemplates($product_id);
			$product_info['product_related']    = $this->Model_Catalog_Product->getProductRelated($product_id);
			$product_info['product_tags']       = $this->Model_Catalog_Product->getProductTags($product_id);
		}

		//Apply Product Class
		$product_classes          = $this->Model_Catalog_ProductClass->getProductClasses();
		$default_product_class_id = isset($_GET['product_class_id']) ? $_GET['product_class_id'] : option('config_default_product_class_id');

		//Set Values or Defaults
		$defaults = array(
			'product_class_id'   => $default_product_class_id,
			'model'              => '',
			'sku'                => '',
			'upc'                => '',
			'location'           => '',
			'alias'              => '',
			'name'               => '',
			'description'        => '',
			'teaser'             => '',
			'information'        => '',
			'meta_keywords'      => '',
			'meta_description'   => '',
			'image'              => '',
			'manufacturer_id'    => 0,
			'shipping'           => 1,
			'price'              => '',
			'cost'               => '',
			'return_policy_id'   => option('config_default_return_policy'),
			'shipping_policy_id' => option('config_default_shipping_policy'),
			'tax_class_id'       => option('config_tax_default_id'),
			'date_available'     => $this->date->now(),
			'date_expires'       => '',
			'editable'           => 1,
			'quantity'           => 1,
			'minimum'            => 1,
			'subtract'           => 1,
			'sort_order'         => 1,
			'stock_status_id'    => option('config_stock_status_id'),
			'status'             => 1,
			'weight'             => '',
			'weight_class_id'    => option('config_weight_class_id'),
			'length'             => '',
			'width'              => '',
			'height'             => '',
			'length_class_id'    => option('config_length_class_id'),
			'product_stores'     => array(option('config_default_store_id')),
			'product_options'    => array(),
			'product_discounts'  => array(),
			'product_specials'   => array(),
			'product_attributes' => array(),
			'product_images'     => array(),
			'product_downloads'  => array(),
			'product_categories' => array(),
			'product_related'    => array(),
			'product_tags'       => array(),
			'points'             => '',
			'product_rewards'    => array(),
			'product_layouts'    => array(),
			'product_templates'  => array(),
		);

		$data += $product_info + $defaults;

		//TODO: Make tags into a list of tag inputs (with js)
		if (is_string($data['product_tags'])) {
			$data['product_tags'] = explode(',', $data['product_tags']);
		}

		//Format Data
		foreach ($data['product_related'] as $key => &$related) {
			$related_product = $this->Model_Catalog_Product->getProduct($related);

			if (!$related_product) {
				unset($data['product_related'][$key]);
			} else {
				$related = $related_product;
			}
		}
		unset($related);

		//Template Data
		$data['data_product_classes']   = $product_classes;
		$data['data_manufacturers']     = array('' => _l(" --- None --- ")) + $this->Model_Catalog_Manufacturer->getManufacturers(array('sort' => 'name'));
		$data['data_tax_classes']       = array('' => _l(" --- None --- ")) + $this->Model_Localisation_TaxClass->getTaxClasses();
		$data['data_weight_classes']    = $this->Model_Localisation_WeightClass->getWeightClasses();
		$data['data_length_classes']    = $this->Model_Localisation_LengthClass->getLengthClasses();
		$data['data_customer_groups']   = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		$data['data_downloads']         = $this->Model_Catalog_Download->getDownloads();
		$data['data_categories']        = $this->Model_Catalog_Category->getCategoriesWithParents();
		$data['data_stores']            = $this->Model_Setting_Store->getStores();
		$data['data_layouts']           = array('' => '') + $this->Model_Design_Layout->getLayouts();
		$data['data_templates']         = $this->theme->getTemplatesFrom('product', false, '');
		$data['data_shipping_policies'] = $this->cart->getShippingPolicies();
		$data['data_return_policies']   = $this->cart->getReturnPolicies();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$data['help_email'] = _l("mailto:%s?subject=New Product Option Request", option('config_email'));

		//TODO: do we really need ths here?
		$data['no_image'] = $this->image->resize('no_image.png', option('config_image_admin_thumb_width'), option('config_image_admin_thumb_height'));

		//Translations
		$data['translations'] = $this->Model_Catalog_Product->getProductTranslations($product_id);

		//Product Attribute Template Defaults
		$data['product_attributes']['__ac_template__'] = array(
			'attribute_id' => '',
			'name'         => '',
			'image'        => '',
			'text'         => '',
			'sort_order'   => 0,
		);

		//Product Options Unused Option Values
		foreach ($data['product_options'] as &$product_option) {
			if (!empty($product_option['product_option_values'])) {
				$filter = array(
					'!option_value_ids' => array_column($product_option['product_option_values'], 'option_value_id'),
				);
			} else {
				$filter = array();
			}

			$option_values = $this->Model_Catalog_Option->getOptionValues($product_option['option_id'], $filter);

			$product_option['unused_option_values'] = $option_values;

		}
		unset($product_option);

		//Product Options Template Defaults
		$data['product_options']['__ac_template__'] = array(
			'product_option_id'     => 0,
			'option_id'             => '',
			'name'                  => '',
			'display_name'          => '',
			'type'                  => '',
			'group_type'            => '',
			'required'              => 1,
			'sort_order'            => 0,
			'unused_option_values'  => array(
				'__ac_template__' => array(
					'option_value_id' => '',
					'value'           => '',
				)
			),
			'product_option_values' => array(
				'__ac_template__' => array(
					'product_option_value_id' => 0,
					'option_value_id'         => '',
					'default'                 => 0,
					'value'                   => '',
					'display_value'           => '',
					'image'                   => '',
					'quantity'                => 1,
					'subtract'                => 0,
					'cost'                    => 0,
					'price'                   => 0,
					'points'                  => 0,
					'weight'                  => 0,
					'sort_order'              => 0,
					'restrictions'            => array(
						'__ac_template__' => array(
							'product_option_value_id'  => 0,
							'restrict_option_value_id' => 0,
							'quantity'                 => 0,
						)
					),

				)
			),
		);

		//Product Discount Template Defaults
		$data['product_discounts']['__ac_template__'] = array(
			'customer_group_id' => option('config_customer_group_id'),
			'quantity'          => 0,
			'priority'          => 0,
			'price'             => 0,
			'date_start'        => $this->date->now(),
			'date_end'          => $this->date->add('30 days'),
		);

		//Product Special Template Defaults
		$data['product_specials']['__ac_template__'] = array(
			'customer_group_id' => option('config_customer_group_id'),
			'priority'          => 0,
			'price'             => 0,
			'date_start'        => $this->date->now(),
			'date_end'          => $this->date->add('30 days'),
		);

		//Product Additional Images Template Defaults
		$data['product_images']['__ac_template__'] = array(
			'image'      => '',
			'thumb'      => '',
			'sort_order' => 0,
		);

		//Product Related Template Defaults
		$data['product_related']['__ac_template__'] = array(
			'product_id' => '',
			'name'       => '',
		);


		//Ajax Urls
		$data['url_generate_url']           = site_url('catalog/product/generate_url');
		$data['url_generate_model']         = site_url('catalog/product/generate_model');
		$data['url_autocomplete']           = site_url('catalog/product/autocomplete');
		$data['url_attribute_autocomplete'] = site_url('catalog/attribute_group/autocomplete');
		$data['url_option_autocomplete']    = site_url('catalog/option/autocomplete');

		//Action Buttons
		$data['save']                = site_url('catalog/product/update', 'product_id=' . $product_id);
		$data['cancel']              = site_url('catalog/product');
		$data['change_class']        = site_url('catalog/product/change_class', 'product_id=' . $product_id);
		$data['add_shipping_policy'] = site_url('setting/shipping_policy');
		$data['add_return_policy']   = site_url('setting/return_policy');

		//The Template
		$template = $this->Model_Catalog_ProductClass->getTemplate($data['product_class_id']);

		if (!$this->theme->findFile($template)) {
			$product_class = array_search_key('product_class_id', $data['product_class_id'], $product_classes);
			$this->message->add('warning', _l("The %s Class template file %s could not be found!", $product_class['name'], $template));

			$template = 'catalog/product_form';
		}

		//Render
		$this->response->setOutput($this->render($template, $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/product')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify products!");
		}

		if (!$this->validation->text($_POST['name'], 3, 254)) {
			$this->error['name'] = _l("Product Name must be greater than 3 and less than 255 characters!");
		}

		//We do not allow changing the Model ID for products that have been ordered
		//A new product must be created
		if (!empty($_GET['product_id']) && $this->order->productInConfirmedOrder($_GET['product_id'])) {
			$model = $this->db->queryVar("SELECT model FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$_GET['product_id']);

			if ($model !== $_POST['model']) {
				$this->error['model'] = _l("Product Model ID cannot be changed after a product is associated with a confirmed order! You may create a copy of the product and deactivate this product if you wish to change the model #.");
				$_POST['model']       = $model;
			}
		} else {
			if (!$_POST['model']) {
				$_POST['model'] = $this->Model_Catalog_Product->generateModel(0, $_POST['name']);
			}

			if (!$this->validation->text($_POST['model'], 1, 64)) {
				$this->error['model'] = _l("Product Model ID must be greater than 3 and less than 64 characters!");
			}

			$product_id = isset($_GET['product_id']) ? "AND product_id != '" . (int)$_GET['product_id'] . "'" : '';

			$exists = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($_POST['model']) . "' $product_id");

			if ($exists) {
				$this->error['model'] = _l("Your Product Model ID is already in our system. Please make the Model ID unique or use the \"Generate Model ID\" button next to the Model ID field.");
			}
		}

		if (empty($_POST['alias']) && option('config_seo_url')) {
			$_POST['alias'] = $this->Model_Setting_UrlAlias->getUniqueAlias($_POST['name'], 'product/product', 'product_id=' . $product_id);
		}

		if (isset($_POST['product_images'])) {
			foreach ($_POST['product_images'] as $key => $image) {
				if (strtolower($image['image']) == 'data/no_image.png' || !$image['image']) {
					unset($_POST['product_images'][$key]);
				}
			}
		}

		//validate the quantities
		if (!empty($_POST['product_options'])) {
			foreach ($_POST['product_options'] as $option_id => &$product_option) {
				if (empty($product_option['product_option_values'])) {
					$this->error["option_value$option_id"] = _l("You must specify at least 1 Option Value for %s!", $product_option['name']);
					continue;
				}

				//Validate Product Option Value Restrictions
				foreach ($product_option['product_option_values'] as $product_option_value_id => &$product_option_value) {
					if (!isset($product_option_value['subtract'])) {
						$product_option_value['subtract'] = 0;
					}

					if (!empty($product_option_value['restrictions'])) {
						foreach ($product_option_value['restrictions'] as $r_key => $restriction) {
							if ($restriction['restrict_option_value_id'] == $product_option_value_id) {
								$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = _l("You cannot restrict a Product Option Value with itself!");
							}

							foreach ($product_option_value['restrictions'] as $r_key2 => $restriction2) {
								if ($r_key != $r_key2) {
									if ($restriction['restrict_option_value_id'] == $restriction2['restrict_option_value_id']) {
										$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = _l("You have duplicate restriction values on 1 Product Option Value!");
									}
								}
							}

							if (isset($product_option['product_option_values'][$restriction['restrict_option_value_id']])) {
								$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = sprintf(_l("You cannot restrict a Product Option Value with another Product Option value of the same Option Category For Type '%s'!"), ucfirst($product_option['type']));
							}
						}
					}
				}
				unset($product_option_value);
			}
			unset($product_option);
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/product')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify products!");
		}

		$product_ids = array();

		if (!empty($_GET['selected'])) {
			$product_ids = $_GET['selected'];
		}

		if (!empty($_GET['product_id'])) {
			$product_ids[] = $_GET['product_id'];
		}

		foreach ($product_ids as $product_id) {
			if ($this->order->productInConfirmedOrder($product_id)) {
				$this->error['product' . $product_id] = _l("The product %s cannot be deleted after it is associated with a confirmed order! Please deactivate this product instead of deleting it.", $this->Model_Catalog_Product->getProductField($product_id, 'name'));
			}
		}

		return empty($this->error);
	}

	private function validateCopy()
	{
		if (!$this->user->can('modify', 'catalog/product')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify products!");
		}

		return empty($this->error);
	}

	public function select()
	{
		if (!isset($_POST['filter'])) {
			return;
		}

		$filter     = $_POST['filter'];
		$select     = isset($_POST['select']) ? $_POST['select'] : array();
		$field_keys = isset($_POST['fields']) ? $_POST['fields'] : array();

		$fields = array();
		foreach (explode(',', $field_keys) as $key) {
			$fields[$key] = 1;
		}

		$products = $this->Model_Catalog_Product->getProducts($filter);

		$html = '';

		foreach ($products as $product) {
			$data[$product['product_id']] = array_intersect_key($product, $fields);

			$selected = $product['product_id'] == $select ? 'selected="selected"' : '';

			$html .= "<option value=\"$product[product_id]\" " . $selected . ">$product[name]</option>";
		}

		$json = array(
			'option_data' => $data,
			'html'        => $html,
		);

		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete()
	{
		//Sort
		$sort = $this->sort->getQueryDefaults('name', 'ASC', option('config_autocomplete_limit'));

		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'product_id';

		//Load Sorted / Filtered Data
		$products = $this->Model_Catalog_Product->getProducts($sort + $filter);

		foreach ($products as &$product) {
			$product['label'] = $product[$label];
			$product['value'] = $product[$value];

			$product['name']  = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');
			$product['thumb'] = $this->image->resize($product['image'], 100, 100);

			$product_options = $this->Model_Catalog_Product->getProductOptions($product['product_id']);

			foreach ($product_options as &$product_option) {
				foreach ($product_option['product_option_values'] as &$product_option_value) {
					$product_option_value['price'] = $this->currency->format($product_option_value['price']);
				}
			}
			unset($product_option);

		}
		unset($product);

		//JSON response
		$this->response->setOutput(json_encode($products));
	}

	public function generate_url()
	{
		if (!empty($_POST['name'])) {
			$product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

			$url = $this->Model_Setting_UrlAlias->getUniqueAlias($_POST['name'], 'product/product', 'product_id=' . $product_id);
		} else {
			$url = '';
		}

		$this->response->setOutput($url);
	}

	public function generate_model()
	{
		if (!empty($_POST['name'])) {
			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : 0;

			$this->response->setOutput($this->Model_Catalog_Product->generateModel($product_id, $_POST['name']));
		}
	}
}





