<?php
class Admin_Controller_Catalog_Manufacturer extends Controller
{
	public function index()
	{
		$this->language->load('catalog/manufacturer');

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('catalog/manufacturer');

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Manufacturer->addManufacturer($_POST);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified manufacturers!"));
			}

			$this->url->redirect('catalog/manufacturer');
		}

		$this->getForm();
	}

	public function update()
	{
		$this->language->load('catalog/manufacturer');

		if ($this->request->isPost() && $this->validateForm()) {
			$manufacturer_id = isset($_GET['manufacturer_id']) ? $_GET['manufacturer_id'] : 0;

			$this->Model_Catalog_Manufacturer->editManufacturer($_GET['manufacturer_id'], $_POST);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified manufacturers!"));
				$this->url->redirect('catalog/manufacturer');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/manufacturer');

		if (isset($_GET['manufacturer_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Manufacturer->deleteManufacturer($_GET['manufacturer_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified manufacturers!"));
				$this->url->redirect('catalog/manufacturer', $this->url->getQueryExclude('manufacturer_id'));
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('catalog/manufacturer');

		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_GET['selected'] as $manufacturer_id) {
					switch ($_GET['action']) {
						case 'enable':
							$this->Model_Catalog_Manufacturer->updateField($manufacturer_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Catalog_Manufacturer->updateField($manufacturer_id, array('status' => 0));
							break;
						case 'delete':
							$this->Model_Catalog_Manufacturer->deleteManufacturer($manufacturer_id);
							break;
						case 'copy':
							$this->Model_Catalog_Manufacturer->copyManufacturer($manufacturer_id);
							break;
					}

					if ($this->error) {
						break;
					}
				}
			}

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified manufacturers!"));

				$this->url->redirect('catalog/manufacturer', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Manufacturer"));

		//The Template
		$this->template->load('catalog/manufacturer_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Manufacturer"), $this->url->link('catalog/manufacturer'));

		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => _l("Image"),
			'filter'       => false,
			'sortable'     => true,
			'sort_value'   => '__image_sort__image',
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Manufacturer Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['vendor_id'] = array(
			'type'         => 'text',
			'display_name' => _l("Vendor ID"),
			'filter'       => true,
			'sortable'     => true,
		);


		$columns['date_active'] = array(
			'type'         => 'datetime',
			'display_name' => _l("Active On"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['date_expires'] = array(
			'type'         => 'datetime',
			'display_name' => _l("Expires On"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['stores'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Stores"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $this->Model_Setting_Store->getStores(),
			'sortable'     => false,
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
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$manufacturer_total = $this->Model_Catalog_Manufacturer->getTotalManufacturers($filter);
		$manufacturers      = $this->Model_Catalog_Manufacturer->getManufacturers($sort + $filter);

		$url_query = $this->url->getQueryExclude('manufacturer_id');

		foreach ($manufacturers as &$manufacturer) {
			$manufacturer['actions'] = array(
				'edit'   => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/manufacturer/delete', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . $url_query)
				)
			);

			if ($manufacturer['date_active'] === DATETIME_ZERO) {
				$manufacturer['date_active'] = _l("No Activation Date");
			} else {
				$manufacturer['date_active'] = $this->date->format($manufacturer['date_active'], 'datetime_format_long');
			}

			if ($manufacturer['date_expires'] === DATETIME_ZERO) {
				$manufacturer['date_expires'] = _l("No Expiration Date");
			} else {
				$manufacturer['date_expires'] = $this->date->format($manufacturer['date_active'], 'datetime_format_long');
			}

			$manufacturer['thumb'] = $this->image->resize($manufacturer['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));

			$manufacturer['stores'] = $this->Model_Catalog_Manufacturer->getManufacturerStores($manufacturer['manufacturer_id']);
		}
		unset($manufacturer);

		//Build The Table
		$tt_data = array(
			'row_id' => 'manufacturer_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($manufacturers);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
			'enable'  => array(
				'label' => _l("Enable")
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'copy'    => array(
				'label' => $this->_('text_copy'),
			),
			'delete'  => array(
				'label' => $this->_('text_delete'),
			),
		);

		$this->data['batch_update'] = 'catalog/manufacturer/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Action buttons
		$this->data['insert'] = $this->url->link('catalog/manufacturer/insert');

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $manufacturer_total;

		$this->data['pagination'] = $this->pagination->render();

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
		$this->document->setTitle(_l("Manufacturer"));

		$this->template->load('catalog/manufacturer_form');

		$manufacturer_id = $this->data['manufacturer_id'] = isset($_GET['manufacturer_id']) ? (int)$_GET['manufacturer_id'] : null;

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Manufacturer"), $this->url->link('catalog/manufacturer'));

		if (!$manufacturer_id) {
			$this->data['action'] = $this->url->link('catalog/manufacturer/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/manufacturer/update', 'manufacturer_id=' . $manufacturer_id);
		}

		$this->data['cancel'] = $this->url->link('catalog/manufacturer');

		if ($manufacturer_id && !$this->request->isPost()) {
			$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);

			$manufacturer_info['stores'] = $this->Model_Catalog_Manufacturer->getManufacturerStores($manufacturer_id);
		}

		$defaults = array(
			'name'            => '',
			'alias'           => '',
			'image'           => '',
			'date_active'     => $this->date->now(),
			'date_expires'    => $this->date->add(null, '30 days'),
			'description'     => '',
			'teaser'          => '',
			'shipping_return' => '',
			'stores'          => array(1),
			'sort_order'      => 0,
			'status'          => 0,
		);


		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($manufacturer_info[$key])) {
				$this->data[$key] = $manufacturer_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Ajax Urls
		$this->data['url_generate_url'] = $this->url->link('catalog/manufacturer/generate_url');
		$this->data['url_autocomplete'] = $this->url->link('catalog/manufacturer/autocomplete');

		$translate_fields = array(
			'name',
			'description',
			'teaser',
			'shipping_return',
		);

		$this->data['translations'] = $this->translation->getTranslations('manufacturer', $manufacturer_id, $translate_fields);

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/manufacturer')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify manufacturers!");
		}

		if (!$this->validation->text($_POST['name'], 3, 128)) {
			$this->error['name'] = _l("Manufacturer Name must be between 3 and 64 characters!");
		}

		if (empty($_POST['alias'])) {
			$_POST['alias'] = $this->tool->getSlug($_POST['name']);
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/manufacturer')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify manufacturers!");
		}

		$manufacturer_ids = array();

		if (isset($_GET['selected'])) {
			$manufacturer_ids = $_GET['selected'];
		}

		if (isset($_GET['manufacturer_id'])) {
			$manufacturer_ids[] = $_GET['manufacturer_id'];
		}

		foreach ($manufacturer_ids as $manufacturer_id) {
			$data = array(
				'manufacturer_id' => $manufacturer_id,
			);

			$product_count = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_count) {
				$this->error['manufacturer' . $manufacturer_id] = _l("Warning: This manufacturer cannot be deleted as it is currently assigned to %s products!", $product_count);
			}
		}

		return $this->error ? false : true;
	}

	public function generate_url()
	{
		if (!empty($_POST['name'])) {
			$manufacturer_id = !empty($_POST['manufacturer_id']) ? (int)$_POST['manufacturer_id'] : 0;

			$url = $this->Model_Setting_UrlAlias->getUniqueAlias('catalog/manufacturer', 'manufacturer_id=' . $manufacturer_id, $_POST['name']);
		} else {
			$url = '';
		}

		$this->response->setOutput($url);
	}

	public function autocomplete()
	{
		//Sort
		$sort = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));

		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'manufacturer_id';

		//Load Sorted / Filtered Data
		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers($sort + $filter);

		foreach ($manufacturers as &$manufacturer) {
			$manufacturer['label'] = $manufacturer[$label];
			$manufacturer['value'] = $manufacturer[$value];
		}
		unset($manufacturer);

		//JSON response
		$this->response->setOutput(json_encode($manufacturers));
	}
}
