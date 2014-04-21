<?php

class Admin_Controller_Catalog_Option extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['option_id'])) {
				$this->Model_Catalog_Option->addOption($_POST);
			} //Update
			else {
				$this->Model_Catalog_Option->editOption($_GET['option_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified options!"));

				$this->url->redirect('catalog/option');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Options"));

		if (isset($_GET['option_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Option->deleteOption($_GET['option_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified options!"));

				$this->url->redirect('catalog/option');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
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

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified options!"));

				$this->url->redirect('catalog/option', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Options"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Options"), $this->url->link('catalog/option'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Option Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => _l("Sort Order"),
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
					'text' => _l("Edit"),
					'href' => $this->url->link('catalog/option/update', 'option_id=' . $option['option_id']),
				),
				'delete' => array(
					'text' => _l("Delete"),
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

		$data['list_view'] = $this->table->render();

		//Batch Actions
		$data['batch_actions'] = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_update'] = 'catalog/option/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total  = $option_total;
		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = $this->url->link('catalog/option/update');
		$data['delete'] = $this->url->link('catalog/option/delete');

		//Render
		$this->response->setOutput($this->render('catalog/option_list', $data));
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Options"));

		//Insert or Update
		$option_id = isset($_GET['option_id']) ? (int)$_GET['option_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Options"), $this->url->link('catalog/option'));

		if (!$option_id) {
			$this->breadcrumb->add(_l("Add"), $this->url->link('catalog/option/update'));
		} else {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('catalog/option/update', 'option_id=' . $option_id));
		}

		//Action Buttons
		$data['save']   = $this->url->link('catalog/option/update', 'option_id=' . $option_id);
		$data['cancel'] = $this->url->link('catalog/option');

		//Load Information
		$option_info = array();

		if ($this->request->isPost()) {
			$option_info = $_POST;
		} elseif ($option_id) {
			$option_info = $this->Model_Catalog_Option->getOption($option_id);

			$option_values = $this->Model_Catalog_Option->getOptionValues($option_id);

			$thumb_width  = $this->config->get('config_image_admin_thumb_width');
			$thumb_height = $this->config->get('config_image_admin_thumb_height');

			foreach ($option_values as &$option_value) {
				$option_value['thumb']        = $this->image->resize($option_value['image'], $thumb_width, $thumb_height);
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

		$data += $option_info + $defaults;

		//Template Data
		$data['data_option_types'] = array(
			'#optgroup1' => _l('Choose'),
			'select'     => _l('Select'),
			'radio'      => _l('Radio'),
			'checkbox'   => _l('Checkbox'),
			'image'      => _l('Image'),
			'#optgroup2' => _l('Input'),
			'text'       => _l('Text'),
			'textarea'   => _l('Textarea'),
			'#optgroup4' => _l('Date'),
			'date'       => _l('Date'),
			'datetime'   => _l('Date &amp; Time'),
			'time'       => _l('Time'),
		);

		//Product Options Template Defaults
		$data['option_values']['__ac_template__'] = array(
			'option_id'       => 0,
			'option_value_id' => '',
			'name'            => '',
			'value'           => '',
			'display_value'   => '',
			'image'           => '',
			'thumb'           => '',
			'sort_order'      => 0,
		);

		//Render
		$this->response->setOutput($this->render('catalog/option_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/option')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify options!");
		}

		if (!$this->validation->text($_POST['name'], 3, 45)) {
			$this->error['name'] = _l("Option Name must be between 3 and 45 characters!");
		}

		if (!$this->validation->text($_POST['display_name'], 1, 128)) {
			$this->error['display_name'] = _l("Option Display Name must be between 1 and 128 characters!");
		}

		$multi_types = array('checkbox');

		$_POST['group_type'] = in_array($_POST['type'], $multi_types) ? 'multi' : 'single';

		if (!isset($_POST['option_value'])) {
			$this->error['warning'] = _l("Warning: Option Values required!");
		} else {
			foreach ($_POST['option_value'] as $option_value_id => $option_value) {
				if (!$this->validation->text($option_value['value'], 1, 128)) {
					$this->error["option_value[$option_value_id][value]"] = _l("Option Value must be between 1 and 128 characters!");
				}
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/option')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify options!");
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
			'label' => _l(" + Add Option"),
			'value' => false,
			'href'  => $this->url->link('catalog/option'),
		);

		//JSON response
		$this->response->setOutput(json_encode($options));
	}
}
