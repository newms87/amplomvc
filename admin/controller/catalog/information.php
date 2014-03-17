<?php
class Admin_Controller_Catalog_Information extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function insert()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Information->addInformation($_POST);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified information!"));

				$this->url->redirect('catalog/information');
			}
		}

		$this->getForm();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Information->editInformation($_GET['information_id'], $_POST);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified information!"));

				$this->url->redirect('catalog/information');
			}
		}

		$this->getForm();
	}

	public function copy()
	{
		if (isset($_GET['information_id']) && $this->validateCopy()) {
			$this->Model_Catalog_Information->copyInformation($_GET['information_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified information!"));

				$this->url->redirect('catalog/information', $this->url->getQueryExclude('information_id'));
			}
		}

		$this->getList();
	}

	public function delete()
	{
		if (isset($_GET['information_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Information->deleteInformation($_GET['information_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified information!"));

				$this->url->redirect('catalog/information', $this->url->getQueryExclude('information_id'));
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_GET['selected'] as $information_id) {
					switch ($_GET['action']) {
						case 'enable':
							$this->Model_Catalog_Information->updateField($information_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Catalog_Information->updateField($information_id, array('status' => 0));
							break;
						case 'delete':
							$this->Model_Catalog_Information->deleteInformation($information_id);
							break;
						case 'copy':
							$this->Model_Catalog_Information->copyInformation($information_id);
							break;
					}

					if ($this->error) {
						break;
					}
				}
			}

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified information!"));

				$this->url->redirect('catalog/information', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Information"));

		//The Template
		$this->view->load('catalog/information_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Information"), $this->url->link('catalog/information'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Information Title"),
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
		$sort   = $this->sort->getQueryDefaults('title', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$information_total = $this->Model_Catalog_Information->getTotalInformations($filter);
		$informations      = $this->Model_Catalog_Information->getInformations($sort + $filter);

		$url_query = $this->url->getQueryExclude('information_id');

		foreach ($informations as &$information) {
			$information['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('catalog/information/update', 'information_id=' . $information['information_id'])
				),
				'copy'   => array(
					'text' => _l("Copy"),
					'href' => $this->url->link('catalog/information/copy', 'information_id=' . $information['information_id'] . '&' . $url_query)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('catalog/information/delete', 'information_id=' . $information['information_id'] . '&' . $url_query)
				),
			);

			$information['stores'] = $this->Model_Catalog_Information->getInformationStores($information['information_id']);
		}
		unset($information);

		//Build The Table
		$tt_data = array(
			'row_id' => 'information_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($informations);
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
				'label' => _l("Copy"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$this->data['batch_update'] = 'catalog/information/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/information/insert');

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $information_total;

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
		$this->document->setTitle(_l("Information"));

		$this->view->load('catalog/information_form');

		$information_id = !empty($_GET['information_id']) ? $_GET['information_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Information Pages"), $this->url->link('catalog/information'));
		$this->breadcrumb->add(_l("Information"), $this->url->link('catalog/information', 'information_id=' . $information_id));

		//Saved Data
		if ($information_id && !$this->request->isPost()) {
			$information_info            = $this->Model_Catalog_Information->getInformation($_GET['information_id']);
			$information_info['stores']  = $this->Model_Catalog_Information->getInformationStores($information_id);
			$information_info['layouts'] = $this->Model_Catalog_Information->getInformationLayouts($information_id);
		}

		//Initialize data and default data
		$defaults = array(
			'title'       => '',
			'description' => '',
			'alias'       => '',
			'stores'      => array($this->config->get('config_default_store')),
			'layouts'     => array(),
			'sort_order'  => 0,
			'status'      => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($information_info[$key])) {
				$this->data[$key] = $information_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Data Lists
		$this->data['data_stores']  = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts'] = array('' => _l(" --- None --- ")) + $this->Model_Design_Layout->getLayouts();

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		if ($information_id) {
			$this->data['action'] = $this->url->link('catalog/information/update', 'information_id=' . $information_id);
		} else {
			$this->data['action'] = $this->url->link('catalog/information/insert');
		}

		$this->data['cancel'] = $this->url->link('catalog/information');

		//Translations
		$translate_fields = array(
			'title',
			'description',
		);

		$this->data['translations'] = $this->translation->getTranslations('information', $information_id, $translate_fields);

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/information')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify information!");
		}

		if (!$this->validation->text($_POST['title'], 3, 128)) {
			$this->error['title'] = _l("Information Title must be between 3 and 128 characters!");
		}

		if (!$this->validation->text($_POST['description'], 3)) {
			$this->error['description'] = _l("Description must be more than 3 characters!");
		}

		if (!empty($_POST['translations'])) {
			foreach ($_POST['translations'] as $field => $translation) {
				foreach ($translation as $language_id => $text) {
					if (empty($text)) {
						continue;
					} //blank translations will revert to Default language

					if ($field === 'title' && !$this->validation->text($text, 3, 128)) {
						$this->error["translations[$field][$language_id]"] = _l("Information Title must be between 3 and 128 characters for the language %s!", $this->language->info('name', $language_id));
					}

					if ($field === 'description' && !$this->validation->text($text, 3)) {
						$this->error["translations[$field][$language_id]"] = _l("Description must be more than 3 characters for the language %s!", $this->language->info('name', $language_id));
					}
				}
			}
		}

		return $this->error ? false : true;
	}

	private function validateCopy()
	{
		if (!$this->user->can('modify', 'catalog/information')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify information!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/information')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify information!");
		}

		$informations_ids = array();

		if (!empty($_GET['selected'])) {
			$information_ids = $_GET['selected'];
		} elseif (!empty($_GET['information_id'])) {
			$information_ids[] = $_GET['information_id'];
		}

		foreach ($information_ids as $information_id) {
			if ($this->config->get('config_account_terms_info_id') == $information_id) {
				$this->error['warning' . $information_id] = _l("Warning: This information page cannot be deleted as it is currently assigned as the store account terms!");
			}

			if ($this->config->get('config_checkout_terms_info_id') == $information_id) {
				$this->error['warning' . $information_id] = _l("Warning: This information page cannot be deleted as it is currently assigned as the store checkout terms!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByInformationId($information_id);

			if ($store_total) {
				$this->error['warning' . $information_id] = sprintf(_l("Warning: This information page cannot be deleted as its currently used by %s stores!"), $store_total);
			}
		}

		return $this->error ? false : true;
	}
}
