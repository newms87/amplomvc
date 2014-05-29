<?php
class App_Controller_Admin_Sale_VoucherTheme extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		$this->document->setTitle(_l("Voucher Themes"));

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['voucher_theme_id'])) {
				$this->Model_Sale_VoucherTheme->addVoucherTheme($_POST);
			} //Update
			else {
				$this->Model_Sale_VoucherTheme->editVoucherTheme($_GET['voucher_theme_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified voucher themes!"));
				redirect('admin/sale/voucher_theme');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['voucher_theme_id']) && $this->validateDelete()) {
			$this->Model_Sale_VoucherTheme->deleteVoucherTheme($_GET['voucher_theme_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified voucher themes!"));
				redirect('admin/sale/voucher_theme');
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Voucher Themes"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Voucher Themes"), site_url('admin/sale/voucher_theme'));

		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => _l("Theme Image"),
			'filter'       => false,
			'sortable'     => true,
			'sort_value'   => '__image_sort__image',
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Theme Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$voucher_theme_total = $this->Model_Sale_VoucherTheme->getTotalVoucherThemes($filter);
		$voucher_themes      = $this->Model_Sale_VoucherTheme->getVoucherThemes($sort + $filter);

		$url_query    = $this->url->getQueryExclude('voucher_theme_id');
		$image_width  = option('config_image_admin_list_width');
		$image_height = option('config_image_admin_list_height');

		foreach ($voucher_themes as &$voucher_theme) {
			$voucher_theme['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme['voucher_theme_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/sale/voucher_theme/delete', 'voucher_theme_id=' . $voucher_theme['voucher_theme_id'] . '&' . $url_query)
				)
			);

			$voucher_theme['thumb'] = $this->image->resize($voucher_theme['image'], $image_width, $image_height);
		}
		unset($voucher_theme);

		//Build The Table
		$tt_data = array(
			'row_id' => 'voucher_theme_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($voucher_themes);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$data['list_view'] = $this->table->render();

		//Batch Actions
		$data['batch_actions'] = array(
			'enable'  => array(
				'label' => _l("Enable"),
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

		$data['batch_update'] = 'sale/voucher_theme/batch_update';

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $voucher_theme_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['insert'] = site_url('admin/sale/voucher_theme/insert');

		//Render
		$this->response->setOutput($this->render('sale/voucher_theme_list', $data));
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Voucher Themes"));

		//Insert or Update
		$voucher_theme_id = isset($_GET['voucher_theme_id']) ? (int)$_GET['voucher_theme_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Voucher Themes"), site_url('admin/sale/voucher_theme'));

		if ($voucher_theme_id) {
			$this->breadcrumb->add(_l("Edit"), site_url('admin/sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme_id));
		} else {
			$this->breadcrumb->add(_l("Add"), site_url('admin/sale/voucher_theme/update'));
		}

		//Load Information
		if ($voucher_theme_id && !$this->request->isPost()) {
			$voucher_theme_info = $this->Model_Sale_VoucherTheme->getVoucherTheme($voucher_theme_id);
		}

		//Load Values or Defaults
		$defaults = array(
			'name'  => '',
			'image' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($voucher_theme_info[$key])) {
				$data[$key] = $voucher_theme_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Translations
		$data['translations'] = $this->Model_Sale_VoucherTheme->getVoucherThemeTranslations($voucher_theme_id);

		//Action Buttons
		$data['save']   = site_url('admin/sale/voucher_theme/update', 'voucher_theme_id=' . $voucher_theme_id);
		$data['cancel'] = site_url('admin/sale/voucher_theme');

		//Render
		$this->response->setOutput($this->render('sale/voucher_theme_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify voucher themes!");
		}

		if (!$this->validation->text($_POST['name'], 3, 32)) {
			$this->error['name'] = _l("Voucher Theme Name must be between 3 and 32 characters!");
		}

		if (empty($_POST['image'])) {
			$this->error['image'] = _l("Image required!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify voucher themes!");
		}

		if (!empty($_GET['selected'])) {
			$voucher_theme_ids = $_GET['selected'];
		} else {
			$voucher_theme_ids = array();
		}

		if (!empty($_GET['voucher_theme_id'])) {
			$voucher_theme_ids[] = $_GET['voucher_theme_id'];
		}

		foreach ($voucher_theme_ids as $voucher_theme_id) {
			$in_use = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher WHERE voucher_theme_id = " . (int)$voucher_theme_id);

			if ($in_use) {
				$this->error['warning'] = _l("Warning: This voucher theme cannot be deleted as it is currently assigned to %s vouchers!", $in_use);
				break;
			}
		}

		return empty($this->error);
	}
}
