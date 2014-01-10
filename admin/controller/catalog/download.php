<?php
class Admin_Controller_Catalog_Download extends Controller
{
	public function index()
	{
		$this->language->load('catalog/download');

		$this->document->setTitle(_l("Downloads"));

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('catalog/download');

		$this->document->setTitle(_l("Downloads"));

		if ($this->request->isPost() && $this->validateForm()) {
			$data = array();

			if (is_uploaded_file($_FILES['download']['tmp_name'])) {
				$filename = $_FILES['download']['name'] . '.' . md5(rand());

				move_uploaded_file($_FILES['download']['tmp_name'], DIR_DOWNLOAD . $filename);

				if (file_exists(DIR_DOWNLOAD . $filename)) {
					$data['download'] = $filename;
					$data['mask']     = $_FILES['download']['name'];
				}
			}

			$this->Model_Catalog_Download->addDownload(array_merge($_POST, $data));

			$this->message->add('success', _l("Success: You have modified downloads!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('catalog/download', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->language->load('catalog/download');

		$this->document->setTitle(_l("Downloads"));

		if ($this->request->isPost() && $this->validateForm()) {
			$data = array();

			if (is_uploaded_file($_FILES['download']['tmp_name'])) {
				$filename = $_FILES['download']['name'] . '.' . md5(rand());

				move_uploaded_file($_FILES['download']['tmp_name'], DIR_DOWNLOAD . $filename);

				if (file_exists(DIR_DOWNLOAD . $filename)) {
					$data['download'] = $filename;
					$data['mask']     = $_FILES['download']['name'];
				}
			}

			$this->Model_Catalog_Download->editDownload($_GET['download_id'], array_merge($_POST, $data));

			$this->message->add('success', _l("Success: You have modified downloads!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('catalog/download', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('catalog/download');

		$this->document->setTitle(_l("Downloads"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $download_id) {

				$results = $this->Model_Catalog_Download->getDownload($download_id);

				$filename = $results['filename'];

				if (file_exists(DIR_DOWNLOAD . $filename)) {
					@unlink(DIR_DOWNLOAD . $filename);
				}

				$this->Model_Catalog_Download->deleteDownload($download_id);
			}

			$this->message->add('success', _l("Success: You have modified downloads!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('catalog/download', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('catalog/download_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'dd.name';
		}

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Downloads"), $this->url->link('catalog/download', $url));

		$this->data['insert'] = $this->url->link('catalog/download/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/download/delete', $url);

		$this->data['downloads'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$download_total = $this->Model_Catalog_Download->getTotalDownloads();

		$results = $this->Model_Catalog_Download->getDownloads($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('catalog/download/update', 'download_id=' . $result['download_id'] . $url)
			);

			$this->data['downloads'][] = array(
				'download_id' => $result['download_id'],
				'name'        => $result['name'],
				'remaining'   => $result['remaining'],
				'selected'    => isset($_GET['selected']) && in_array($result['download_id'], $_GET['selected']),
				'action'      => $action
			);
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->data['sort_name']      = $this->url->link('catalog/download', 'sort=dd.name' . $url);
		$this->data['sort_remaining'] = $this->url->link('catalog/download', 'sort=d.remaining' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $download_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('catalog/download_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

		if (isset($this->error['download'])) {
			$this->data['error_download'] = $this->error['download'];
		} else {
			$this->data['error_download'] = '';
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Downloads"), $this->url->link('catalog/download', $url));

		if (!isset($_GET['download_id'])) {
			$this->data['action'] = $this->url->link('catalog/download/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/download/update', 'download_id=' . $_GET['download_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('catalog/download', $url);

		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_GET['download_id']) && !$this->request->isPost()) {
			$download_info = $this->Model_Catalog_Download->getDownload($_GET['download_id']);
		}

		if (isset($download_info['filename'])) {
			$this->data['filename'] = $download_info['filename'];
		} else {
			$this->data['filename'] = '';
		}

		if (isset($_GET['download_id'])) {
			$this->data['show_update'] = true;
		} else {
			$this->data['show_update'] = false;
		}

		if (isset($_POST['download_description'])) {
			$this->data['download_description'] = $_POST['download_description'];
		} elseif (isset($_GET['download_id'])) {
			$this->data['download_description'] = $this->Model_Catalog_Download->getDownloadDescriptions($_GET['download_id']);
		} else {
			$this->data['download_description'] = array();
		}

		if (isset($_POST['remaining'])) {
			$this->data['remaining'] = $_POST['remaining'];
		} elseif (!empty($download_info['remaining'])) {
			$this->data['remaining'] = $download_info['remaining'];
		} else {
			$this->data['remaining'] = 1;
		}

		if (isset($_POST['update'])) {
			$this->data['update'] = $_POST['update'];
		} else {
			$this->data['update'] = false;
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/download')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify downloads!");
		}

		foreach ($_POST['download_description'] as $language_id => $value) {
			if ((strlen($value['name']) < 3) || (strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = _l("Name must be between 3 and 64 characters!");
			}
		}

		if ($_FILES['download']['name']) {
			if ((strlen($_FILES['download']['name']) < 3) || (strlen($_FILES['download']['name']) > 128)) {
				$this->error['download'] = _l("Filename must be between 3 and 128 characters!");
			}

			if (substr(strrchr($_FILES['download']['name'], '.'), 1) == 'php') {
				$this->error['download'] = _l("Invalid file type!");
			}

			if (!$this->validation->fileUpload($_FILES['download'])) {
				$this->error['warning'] = $this->validation->getError();
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/download')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify downloads!");
		}

		foreach ($_GET['selected'] as $download_id) {
			$data = array(
				'downloads' => array($download_id),
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf(_l("Warning: This download cannot be deleted as it is currently assigned to %s products!"), $product_total);
			}
		}

		return $this->error ? false : true;
	}
}
