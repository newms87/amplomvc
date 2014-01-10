<?php
class Catalog_Controller_Page_Page extends Controller
{
	public function index()
	{
		$this->template->load('page/page');

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

		$page['content'] = html_entity_decode($page['content']);

		$this->data = $page;

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

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
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(HTTP_JS . 'jquery/colorbox/colorbox.css');

		//Add Scripts
		if ($this->config->get('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(HTTP_JS . 'jquery/jquery.js', 50);
			$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(HTTP_JS . 'common.js', 53);
		$this->document->addScript(HTTP_THEME_JS . 'common.js', 56);

		//Page Head
		$this->data['direction'] = $this->language->getInfo('direction');
		$this->language->set('lang', $this->language->getInfo('code'));

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$page['content'] = html_entity_decode($page['content']);

		$this->data += $page;

		//The Template
		$this->template->load('page/page_preview');

		//Render
		$this->response->setOutput($this->render());
	}
}
