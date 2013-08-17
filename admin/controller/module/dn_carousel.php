<?php
class Admin_Controller_Module_DnCarousel extends Controller
{


	public function index()
	{
		$this->template->load('module/dn_carousel');

		$this->language->load('module/dn_carousel');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('dn_carousel', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/module'));
		}

		$this->language->set('button_add_module', $this->_('button_add_carousel'));

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('module/dn_carousel'));

		$this->data['action'] = $this->url->link('module/dn_carousel');

		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();

		if (isset($_POST['dn_carousel_module'])) {
			$this->data['modules'] = $_POST['dn_carousel_module'];
		} elseif ($this->config->get('dn_carousel_module')) {
			$this->data['modules'] = $this->config->get('dn_carousel_module');
		}


		foreach ($this->data['modules'] as $mod_key => $mod) {
			foreach ($mod['data'] as $key => $md) {
				$a                                                              = $this->Model_Cms_Article->getArticle($md['article_id']);
				$this->data['modules'][$mod_key]['data'][$key]['article_title'] = isset($a['title']) ? $a['title'] : "Article Not Found";
				$image                                                          = $this->data['modules'][$mod_key]['data'][$key]['image'];
				$image                                                          = isset($image) && !empty($image) && file_exists(DIR_IMAGE . $image) ? $image : "no_image.png";
				$this->data['modules'][$mod_key]['data'][$key]['thumb']         = $this->image->resize($image, 100, 100);
				$this->data['modules'][$mod_key]['data'][$key]['image']         = $image;
			}
		}

		$this->data['no_image'] = $this->image->resize('no_image.png', 100, 100);


		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		$this->data['lang_id']   = $this->config->get('config_language_id');


		$layouts               = $this->Model_Design_Layout->getLayouts();
		$this->data['layouts'] = array();
		foreach ($layouts as $layout) {
			$this->data['layouts'][$layout['layout_id']] = $layout['name'];
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/dn_carousel')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
