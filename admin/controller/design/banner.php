<?php
class Admin_Controller_Design_Banner extends Controller 
{
	
 
	public function index()
	{
		$this->load->language('design/banner');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('design/banner');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Design_Banner->addBanner($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$url = $this->get_url();
			
			$this->url->redirect($this->url->link('design/banner', $url));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('design/banner');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Design_Banner->editBanner($_GET['banner_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));

			$url = $this->get_url();
					
			$this->url->redirect($this->url->link('design/banner', $url));
		}

		$this->getForm();
	}
 
	public function delete()
	{
		$this->load->language('design/banner');
 
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $banner_id) {
				$this->Model_Design_Banner->deleteBanner($banner_id);
			}
			
			$this->message->add('success', $this->_('text_success'));

			$url = $this->get_url();

			$this->url->redirect($this->url->link('design/banner', $url));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('design/banner_list');

		$default_urls = array('sort'=>'name','order'=>'ASC','page'=>1);
		foreach ($default_urls as $key=>$default) {
			$$key = isset($_GET[$key])?$_GET[$key]:$default;
		}
			
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/banner'));
		
		$this->data['insert'] = $this->url->link('design/banner/insert', $url);
		$this->data['delete'] = $this->url->link('design/banner/delete', $url);
		
		$this->data['banners'] = array();

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
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('design/banner/update', 'banner_id=' . $result['banner_id'] . $url)
			);

			$this->data['banners'][] = array(
				'banner_id' => $result['banner_id'],
				'name'		=> $result['name'],
				'status'	=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'selected'  => isset($_POST['selected']) && in_array($result['banner_id'], $_POST['selected']),
				'action'	=> $action
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
		
		$this->data['sort_name'] = $this->url->link('design/banner', 'sort=name' . $url);
		$this->data['sort_status'] = $this->url->link('design/banner', 'sort=status' . $url);
		
		$url = $this->get_url(array('sort','order'));

		$this->pagination->init();
		$this->pagination->total = $banner_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('design/banner_form');

		$banner_id = isset($_GET['banner_id'])?$_GET['banner_id']:0;
		
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/banner'));
		
		if (!$banner_id) {
			$this->data['action'] = $this->url->link('design/banner/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('design/banner/update', 'banner_id=' . $banner_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('design/banner', $url);
		
		if ($banner_id && (!$this->request->isPost())) {
			$banner_info = $this->Model_Design_Banner->getBanner($banner_id);
		}
		
		$defaults = array('name'=>'',
								'status'=>true
							);
		
		foreach ($defaults as $d=>$value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($banner_info[$d])) {
				$this->data[$d] = $banner_info[$d];
			} elseif (!$banner_id) {
				$this->data[$d] = $value;
			}
		}

		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		if (isset($_POST['banner_image'])) {
			$banner_images = $_POST['banner_image'];
		} elseif (isset($_GET['banner_id'])) {
			$banner_images = $this->Model_Design_Banner->getBannerImages($_GET['banner_id']);
		} else {
			$banner_images = array();
		}
		
		$this->data['banner_images'] = array();
		
		foreach ($banner_images as $banner_image) {
			if ($banner_image['image'] && file_exists(DIR_IMAGE . $banner_image['image'])) {
				$image = $banner_image['image'];
			} else {
				$image = 'no_image.png';
			}
			
			$this->data['banner_images'][] = array(
				'banner_image_description' => $banner_image['banner_image_description'],
				'link'							=> $banner_image['link'],
				'image'						=> $image,
				'thumb'						=> $this->image->resize($image, 100, 100),
				'sort_order'					=> $banner_image['sort_order']
			);
		}
	
		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (isset($_POST['banner_image'])) {
			foreach ($_POST['banner_image'] as $banner_image_id => $banner_image) {
				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					if ((strlen($banner_image_description['title']) < 2) || (strlen($banner_image_description['title']) > 64)) {
						$this->error["banner_image[$banner_image_id][image]"] = $this->_('error_title');
					}
				}
			}
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->_('error_permission');
		}
	
		return $this->error ? false : true;
	}
	
	private function get_url($filters=null)
	{
		$url = '';
		$filters = $filters?$filters:array('sort', 'order', 'page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}
