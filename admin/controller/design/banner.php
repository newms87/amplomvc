<?php
class Admin_Controller_Design_Banner extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Banners"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Banners"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Banner->addBanner($_POST);

			$this->message->add('success', _l("Success: You have modified banners!"));

			$url = $this->get_url();

			$this->url->redirect('design/banner', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Banners"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Banner->editBanner($_GET['banner_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified banners!"));

			$url = $this->get_url();

			$this->url->redirect('design/banner', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Banners"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $banner_id) {
				$this->Model_Design_Banner->deleteBanner($banner_id);
			}

			$this->message->add('success', _l("Success: You have modified banners!"));

			$url = $this->get_url();

			$this->url->redirect('design/banner', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$default_urls = array(
			'sort'  => 'name',
			'order' => 'ASC',
			'page'  => 1
		);
		foreach ($default_urls as $key => $default) {
			$$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Banners"), $this->url->link('design/banner'));

		$data['insert'] = $this->url->link('design/banner/insert', $url);
		$data['delete'] = $this->url->link('design/banner/delete', $url);

		$data['banners'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$banner_total = $this->Model_Design_Banner->getTotalBanners();

		$results = $this->Model_Design_Banner->getBanners($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('design/banner/update', 'banner_id=' . $result['banner_id'] . $url)
			);

			$data['banners'][] = array(
				'banner_id' => $result['banner_id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? _l("Enabled") : _l("Disabled")),
				'selected'  => isset($_GET['selected']) && in_array($result['banner_id'], $_GET['selected']),
				'action'    => $action
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

		$data['sort_name']   = $this->url->link('design/banner', 'sort=name' . $url);
		$data['sort_status'] = $this->url->link('design/banner', 'sort=status' . $url);

		$url = $this->get_url(array(
			'sort',
			'order'
		));

		$this->pagination->init();
		$this->pagination->total  = $banner_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('design/banner_list', $data));
	}

	private function getForm()
	{
		$banner_id = isset($_GET['banner_id']) ? $_GET['banner_id'] : 0;

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Banners"), $this->url->link('design/banner'));

		if (!$banner_id) {
			$data['action'] = $this->url->link('design/banner/insert', $url);
		} else {
			$data['action'] = $this->url->link('design/banner/update', 'banner_id=' . $banner_id . $url);
		}

		$data['cancel'] = $this->url->link('design/banner', $url);

		if ($banner_id && !$this->request->isPost()) {
			$banner_info = $this->Model_Design_Banner->getBanner($banner_id);
		}

		$defaults = array(
			'name'   => '',
			'status' => true
		);

		foreach ($defaults as $d => $value) {
			if (isset($_POST[$d])) {
				$data[$d] = $_POST[$d];
			} elseif (isset($banner_info[$d])) {
				$data[$d] = $banner_info[$d];
			} elseif (!$banner_id) {
				$data[$d] = $value;
			}
		}

		$data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['banner_image'])) {
			$banner_images = $_POST['banner_image'];
		} elseif (isset($_GET['banner_id'])) {
			$banner_images = $this->Model_Design_Banner->getBannerImages($_GET['banner_id']);
		} else {
			$banner_images = array();
		}

		$data['banner_images'] = array();

		foreach ($banner_images as $banner_image) {
			if ($banner_image['image'] && file_exists(DIR_IMAGE . $banner_image['image'])) {
				$image = $banner_image['image'];
			} else {
				$image = 'no_image.png';
			}

			$data['banner_images'][] = array(
				'banner_image_description' => $banner_image['banner_image_description'],
				'link'                     => $banner_image['link'],
				'image'                    => $image,
				'thumb'                    => $this->image->resize($image, 100, 100),
				'sort_order'               => $banner_image['sort_order']
			);
		}

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$data['no_image'] = $this->image->resize('no_image.png', 100, 100);

		$this->response->setOutput($this->render('design/banner_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'design/banner')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify banners!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = _l("Banner Name must be between 3 and 64 characters!");
		}

		if (isset($_POST['banner_image'])) {
			foreach ($_POST['banner_image'] as $banner_image_id => $banner_image) {
				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					if ((strlen($banner_image_description['title']) < 2) || (strlen($banner_image_description['title']) > 64)) {
						$this->error["banner_image[$banner_image_id][image]"] = _l("Banner Title must be between 2 and 64 characters!");
					}
				}
			}
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'design/banner')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify banners!");
		}

		return empty($this->error);
	}

	private function get_url($filters = null)
	{
		$url     = '';
		$filters = $filters ? $filters : array(
			'sort',
			'order',
			'page'
		);
		foreach ($filters as $f) {
			if (isset($_GET[$f])) {
				$url .= "&$f=" . $_GET[$f];
			}
		}
		return $url;
	}
}
