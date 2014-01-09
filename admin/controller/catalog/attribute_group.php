<?php
class Admin_Controller_Catalog_AttributeGroup extends Controller
{
	public function index()
	{
		$this->language->load('catalog/attribute_group');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('catalog/attribute_group');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['attribute_group_id'])) {
				$this->Model_Catalog_AttributeGroup->addAttributeGroup($_POST);
			} //Update
			else {
				$this->Model_Catalog_AttributeGroup->editAttributeGroup($_GET['attribute_group_id'], $_POST);
			}

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified attribute groups!"));

				$this->url->redirect('catalog/attribute_group');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/attribute_group');

		if (!empty($_GET['attribute_group_id']) && $this->validateDelete()) {
			$this->Model_Catalog_AttributeGroup->deleteAttributeGroup($_GET['attribute_group_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified attribute groups!"));

				$this->url->redirect('catalog/attribute_group');
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('catalog/attribute_group');

		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $attribute_group_id) {
				switch ($_GET['action']) {
					case 'delete':
						if ($this->validateDelete()) {
							$this->Model_Catalog_AttributeGroup->deleteAttributeGroup($attribute_group_id);
						}
						break;
				}

				if ($this->error) {
					break;
				}
			}

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified attribute groups!"));

				$this->url->redirect('catalog/attribute_group');
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Attribute Groups"));

		//The Template
		$this->template->load('catalog/attribute_group_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Attribute Groups"), $this->url->link('catalog/attribute_group'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Attribute Group Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['attribute_count'] = array(
			'type'         => 'int',
			'display_name' => _l("# of Attributes"),
			'filter'       => false,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => _l("Sort Order"),
			'filter'       => false,
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//This triggers the attribute_count to be added to the query
		if (empty($sort['attribute_count'])) {
			$sort['attribute_count'] = true;
		}

		$attribute_group_total = $this->Model_Catalog_AttributeGroup->getTotalAttributeGroups($filter);
		$attribute_groups      = $this->Model_Catalog_AttributeGroup->getAttributeGroups($sort + $filter);

		$url_query = $this->url->getQueryExclude('attribute_group_id');

		foreach ($attribute_groups as &$attribute_group) {
			$attribute_group['actions'] = array(
				'edit'   => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $attribute_group['attribute_group_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/attribute_group/delete', 'attribute_group_id=' . $attribute_group['attribute_group_id'])
				)
			);
		}
		unset($attribute_group);

		//Build The Table
		$tt_data = array(
			'row_id' => 'attribute_group_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($attribute_groups);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);

		$this->data['batch_update'] = 'catalog/attribute_group/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/attribute_group/update');

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $attribute_group_total;

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
		//Page Head
		$this->document->setTitle(_l("Attribute Groups"));

		//The Template
		$this->template->load('catalog/attribute_group_form');

		//Insert or Update
		$attribute_group_id = !empty($_GET['attribute_group_id']) ? $_GET['attribute_group_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Attribute Groups"), $this->url->link('catalog/attribute_group'));

		if (!$attribute_group_id) {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('catalog/attribute_group/update'));
		} else {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $attribute_group_id));
		}

		//Handle Post
		if ($attribute_group_id && !$this->request->isPost()) {
			$attribute_group_info = $this->Model_Catalog_AttributeGroup->getAttributeGroup($attribute_group_id);

			$attributes = $this->Model_Catalog_AttributeGroup->getAttributes($attribute_group_id);

			foreach ($attributes as &$attribute) {
				$count = $this->Model_Catalog_AttributeGroup->getAttributeProductCount($attribute['attribute_id']);

				if ($count) {
					$attribute['product_count'] = _l("Associated to %d Product(s)", $count);
				}
			}

			$attribute_group_info['attributes'] = $attributes;
		}

		//Load Values or Defaults
		$defaults = array(
			'name'       => '',
			'sort_order' => '',
			'attributes' => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($attribute_group_info[$key])) {
				$this->data[$key] = $attribute_group_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Translation for Attribute Group
		$translate_fields = array(
			'name',
		);

		$this->data['translations'] = $this->translation->getTranslations('attribute_group', $attribute_group_id, $translate_fields);

		//Translations for Attributes
		$translate_fields = array(
			'name',
		);

		foreach ($this->data['attributes'] as &$attribute) {
			$attribute['translations'] = $this->translation->getTranslations('attribute', $attribute['attribute_id'], $translate_fields);
		}
		unset($attribute);

		//Attribute Defaults
		$this->data['attributes']['__ac_template__'] = array(
			'attribute_id' => '',
			'name'         => '',
			'image'        => '',
			'sort_order'   => 0,
			'translations' => array(),
		);

		//Action Buttons
		$this->data['save']   = $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $attribute_group_id);
		$this->data['cancel'] = $this->url->link('catalog/attribute_group');

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
		if (!$this->user->can('modify', 'catalog/attribute_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify attribute groups!");
		}

		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Attribute Group Name must be between 3 and 64 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/attribute_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify attribute groups!");
		}

		$attribute_group_ids = array();

		if (!empty($_GET['attribute_group_id'])) {
			$attribute_group_ids[] = $_GET['attribute_group_id'];
		}

		if (!empty($_GET['selected'])) {
			$attribute_group_ids = array_merge($_GET['selected'], $attribute_group_ids);
		}

		foreach ($attribute_group_ids as $attribute_group_id) {
			if ($attribute_total = $this->Model_Catalog_AttributeGroup->hasProductAssociation($attribute_group_id)) {
				$attribute_group = $this->Model_Catalog_AttributeGroup->getAttributeGroup($attribute_group_id);

				$this->error['warning_' . $attribute_group_id] = _l("Th attribute group %s cannot be deleted as it is currently assigned to %s products!", $attribute_group['name'], $attribute_total);
			}
		}

		return $this->error ? false : true;
	}

	public function autocomplete()
	{
		//Language
		$this->language->load('catalog/attribute_group');

		//Sort / Filter
		$sort   = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'attribute_id';

		//Load Sorted / Filtered Data
		$attributes = $this->Model_Catalog_AttributeGroup->getAttributesFilter($sort + $filter);

		$image_width = $this->config->get('config_image_admin_thumb_width');
		$image_height = $this->config->get('config_image_admin_thumb_height');

		foreach ($attributes as &$attribute) {
			$attribute['label'] = $attribute[$label];
			$attribute['value'] = $attribute[$value];
			$attribute['thumb'] = $this->image->resize($attribute['image'], $image_width, $image_height);
		}
		unset($attribute);

		$attributes[] = array(
			'label' => _l(" + Add Attribute"),
			'value' => false,
			'href'  => $this->url->link('catalog/attribute_group'),
		);

		//JSON response
		$this->response->setOutput(json_encode($attributes));
	}
}
