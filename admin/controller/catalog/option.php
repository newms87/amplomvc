<?php
class Admin_Controller_Catalog_Option extends Controller
{
	public function index()
	{
		$this->language->load('catalog/option');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('catalog/option');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['option_id'])) {
				$this->Model_Catalog_Option->addOption($_POST);
			} //Update
			else {
				$this->Model_Catalog_Option->editOption($_GET['option_id'], $_POST);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/option'));
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/option');

		$this->document->setTitle($this->_('head_title'));

		if (isset($_GET['option_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Option->deleteOption($_GET['option_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/option'));
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('catalog/option');

		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_GET['selected'] as $option_id) {
					switch ($_GET['action']) {
						case 'delete':
							$this->Model_Catalog_Option->deleteOption($option_id);
							break;
					}

					if ($this->error) {
						break;
					}
				}
			}

			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/option', $this->url->getQueryExclude('action')));
			}
		}

		$this->getList();
	}


	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('catalog/option_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('catalog/option'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_name'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => $this->_('column_sort_order'),
			'filter'       => true,
			'sortable'     => true,
		);

		//Get Sorted /Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = isset($_GET['filter']) ? $_GET['filter'] : array();

		$option_total = $this->Model_Catalog_Option->getTotalOptions($filter);
		$options      = $this->Model_Catalog_Option->getOptions($sort + $filter);

		$url_query = $this->url->getQueryExclude('option_id');

		foreach ($options as &$option) {
			$option['actions'] = array(
				'edit'   => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/option/update', 'option_id=' . $option['option_id']),
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/option/delete', 'option_id=' . $option['option_id'] . '&' . $url_query),
				),
			);

		}
		unset($option);

		//Build The Table
		$tt_data = array(
			'row_id' => 'option_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($options);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);

		$this->data['batch_update'] = 'catalog/option/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();

		//Pagination
		$this->pagination->init();
		$this->pagination->total  = $option_total;
		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/option/update');
		$this->data['delete'] = $this->url->link('catalog/option/delete');

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
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('catalog/option_form');

		//Insert or Update
		$option_id = isset($_GET['option_id']) ? (int)$_GET['option_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('catalog/option'));

		if (!$option_id) {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('catalog/option/update'));
		} else {
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('catalog/option/update', 'option_id=' . $option_id));
		}

		//Action Buttons
		$this->data['save']   = $this->url->link('catalog/option/update', 'option_id=' . $option_id);
		$this->data['cancel'] = $this->url->link('catalog/option');

		//Load Information
		if ($option_id && !$this->request->isPost()) {
			$option_info = $this->Model_Catalog_Option->getOption($option_id);

			$option_values = $this->Model_Catalog_Option->getOptionValues($option_id);

			$option_values['__ac_template__'] = array(
				'option_value_id' => '',
				'value'           => '',
				'image'           => '',
				'sort_order'      => 0,
			);

			foreach ($option_values as &$option_value) {
				$option_value['translations'] = $this->Model_Catalog_Option->getOptionValueTranslations($option_value['option_value_id']);
			}
			unset($option_value);

			$option_info['option_values'] = $option_values;
		}

		//Load Values or Defaults
		$defaults = array(
			'name'          => '',
			'display_name'  => '',
			'type'          => '',
			'sort_order'    => '',
			'option_values' => array()
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($option_info[$key])) {
				$this->data[$key] = $option_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Product Options Template Defaults
		$this->data['option_values']['__ac_template__'] = array(
			'option_id'  => 0,
			'name'       => '',
			'image'      => '',
			'sort_order' => 0,
		);

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
		if (!$this->user->hasPermission('modify', 'catalog/option')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->validation->text($_POST['name'], 3, 45)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->validation->text($_POST['display_name'], 1, 128)) {
			$this->error['display_name'] = $this->_('error_display_name');
		}

		$multi_types = array('checkbox');

		$_POST['group_type'] = in_array($_POST['type'], $multi_types) ? 'multi' : 'single';

		if (!isset($_POST['option_value'])) {
			$this->error['warning'] = $this->_('error_type');
		} else {
			foreach ($_POST['option_value'] as $option_value_id => $option_value) {
				if (!$this->validation->text($option_value['value'], 1, 128)) {
					$this->error["option_value[$option_value_id][value]"] = $this->_('error_option_value');
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

		return $this->error ? false : true;
	}

	public function autocomplete()
	{
		//Sort / Filter
		$sort   = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'option_id';

		//Language
		$this->language->load('catalog/option');

		//Load Sorted / Filtered Data
		$options = $this->Model_Catalog_Option->getOptions($filter);

		foreach ($options as &$option) {
			$option['label'] = $option[$label];
			$option['value'] = $option[$value];

			$option['name'] = html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8');

			$option_values = $this->Model_Catalog_Option->getOptionValues($option['option_id']);

			$image_width  = $this->config->get('config_image_product_option_width');
			$image_height = $this->config->get('config_image_product_option_height');

			foreach ($option_values as &$option_value) {
				$option_value['thumb'] = $this->image->resize($option_value['image'], $image_width, $image_height);
				$option_value['value'] = html_entity_decode($option_value['value'], ENT_QUOTES, 'UTF-8');
			}
			unset($option_value);

			$option['option_values'] = $option_values;

		}
		unset($option);

		$options[] = array(
			'label' => $this->_("text_add_option_autocomplete"),
			'value' => false,
			'href'  => $this->url->link('catalog/option'),
		);

		//JSON response
		$this->response->setOutput(json_encode($options));
	}
}
