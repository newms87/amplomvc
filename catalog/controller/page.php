<?php
class Catalog_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;

		$page = $this->Model_Page_Page->getPage($page_id);

		if (!$page) {
			redirect("error/not_found");
		}

		//Page Head
		$this->document->setTitle($page['title']);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$data = $page;

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		$this->response->setOutput($this->render($template, $data));
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
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Add Styles
		$this->document->addStyle(URL_THEME . 'style/style.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(URL_RESOURCES . 'js/jquery/colorbox/colorbox.css');

		//Add Scripts
		if (option('config_jquery_cdn')) {
			$this->document->addScript("http://code.jquery.com/jquery-1.10.2.min.js", 50);
			$this->document->addScript("http://code.jquery.com/ui/1.10.3/jquery-ui.js", 51);
		} else {
			$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
			$this->document->addScript(URL_RESOURCES . 'js/jquery/ui/jquery-ui.js', 51);
		}

		$this->document->addScript(URL_RESOURCES . 'js/common.js', 53);
		$this->document->addScript(theme_url('js/common.js'), 56);

		//Page Head
		$data['direction'] = $this->language->info('direction');
		$data['lang']      = $this->language->info('code');

		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		$page['content'] = html_entity_decode($page['content']);

		$data += $page;

		//Render
		$this->response->setOutput($this->render('page/preview', $data));
	}
}
