<?php
class Catalog_Controller_Page_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;

		$page = $this->Model_Page_Page->getPage($page_id);

		if (!$page) {
			$this->url->redirect("error/not_found");
		}

		//Page Head
		$this->document->setTitle($page['title']);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$this->data = $page;

		//The Template
		$this->view->load('page/page');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header',
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function preview()
	{
		$page_id = !empty($_GET['page_id']) ? (int)$_GET['page_id'] : 0;

		if ($page_id) {
			$page = $this->Model_Page_Page->getPageForPreview($page_id);
		} else {
			$page = array(
				'title'         => "New Page",
				'display_title' => 1,
				'content'       => '',
			);
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Add Styles
		$this->document->addStyle(URL_THEME . 'style/style.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add Scripts
		if ($this->config->get('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);
		$this->document->addScript(URL_THEME_JS . 'common.js', 56);

		//Page Head
		$this->data['direction'] = $this->language->info('direction');
		$this->data['lang']      = $this->language->info('code');

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$page['content'] = html_entity_decode($page['content']);

		$this->data += $page;

		//The Template
		$this->view->load('page/page_preview');

		//Render
		$this->response->setOutput($this->render());
	}
}
