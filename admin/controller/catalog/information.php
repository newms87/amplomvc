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

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified information!"));

				redirect('catalog/information');
			}
		}

		$this->getForm();
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Information->editInformation($_GET['information_id'], $_POST);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified information!"));

				redirect('catalog/information');
			}
		}

		$this->getForm();
	}

	public function copy()
	{
		if (isset($_GET['information_id']) && $this->validateCopy()) {
			$this->Model_Catalog_Information->copyInformation($_GET['information_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified information!"));

				redirect('catalog/information', $this->url->getQueryExclude('information_id'));
			}
		}

		$this->getList();
	}

	public function delete()
	{
		if (isset($_GET['information_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Information->deleteInformation($_GET['information_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified information!"));

				redirect('catalog/information', $this->url->getQueryExclude('information_id'));
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

			if (!$this->error && !$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified information!"));

				redirect('catalog/information', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Information"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Information"), site_url('catalog/information'));

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
					'href' => site_url('catalog/information/update', 'information_id=' . $information['information_id'])
				),
				'copy'   => array(
					'text' => _l("Copy"),
					'href' => site_url('catalog/information/copy', 'information_id=' . $information['information_id'] . '&' . $url_query)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('catalog/information/delete', 'information_id=' . $information['information_id'] . '&' . $url_query)
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

		$data['list_view'] = $this->table->render();

		//Batch Actions
		$data['batch_actions'] = array(
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

		$data['batch_update'] = 'catalog/information/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Action Buttons
		$data['insert'] = site_url('catalog/information/insert');

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $information_total;

		$data['pagination'] = $this->pagination->render();

		//Render
		$this->response->setOutput($this->render('catalog/information_list', $data));
	}

	private function getForm()
	{
		$this->document->setTitle(_l("Information"));

		$information_id = !empty($_GET['information_id']) ? $_GET['information_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Information Pages"), site_url('catalog/information'));
		$this->breadcrumb->add(_l("Information"), site_url('catalog/information', 'information_id=' . $information_id));

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
			'stores'      => array(option('config_default_store')),
			'layouts'     => array(),
			'sort_order'  => 0,
			'status'      => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($information_info[$key])) {
				$data[$key] = $information_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Data Lists
		$data['data_stores']  = $this->Model_Setting_Store->getStores();
		$data['data_layouts'] = array('' => _l(" --- None --- ")) + $this->Model_Design_Layout->getLayouts();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		if ($information_id) {
			$data['action'] = site_url('catalog/information/update', 'information_id=' . $information_id);
		} else {
			$data['action'] = site_url('catalog/information/insert');
		}

		$data['cancel'] = site_url('catalog/information');

		//Translations
		$translate_fields = array(
			'title',
			'description',
		);

		$data['translations'] = $this->translation->getTranslations('information', $information_id, $translate_fields);

		$this->response->setOutput($this->render('catalog/information_form', $data));
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

		return empty($this->error);
	}

	private function validateCopy()
	{
		if (!$this->user->can('modify', 'catalog/information')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify information!");
		}

		return empty($this->error);
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
			if (option('config_account_terms_info_id') == $information_id) {
				$this->error['warning' . $information_id] = _l("Warning: This information page cannot be deleted as it is currently assigned as the store account terms!");
			}

			if (option('config_checkout_terms_info_id') == $information_id) {
				$this->error['warning' . $information_id] = _l("Warning: This information page cannot be deleted as it is currently assigned as the store checkout terms!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByInformationId($information_id);

			if ($store_total) {
				$this->error['warning' . $information_id] = sprintf(_l("Warning: This information page cannot be deleted as its currently used by %s stores!"), $store_total);
			}
		}

		return empty($this->error);
	}
}
