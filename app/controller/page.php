<?php

class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;

		if ($page_id) {
			$page = $this->Model_Page->getActivePage($page_id);
		} else {
			$page = $this->Model_Page->getPageByName($this->route->getSegment(1));
		}

		if (!$page) {
			redirect("error/not_found");
		}

		//Page Head
		$this->document->setTitle($page['title']);

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$style = $this->document->compileLess($page['style'], 'page-' . $page['name']);
			} else {
				$style = $page['style'];
			}

			$this->document->addStyle($style);
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		$this->response->setOutput($this->render($template, $page));
	}

	public function content()
	{
		//The page
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;

		$page = $this->Model_Page->getActivePage($page_id);

		if (!$page) {
			redirect("error/not_found");
		}

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = $this->document->compileLess($page['style'], 'page-' . $page['name']);
			}
		}

		//Render
		$this->response->setOutput($this->render('page/content', $page));
	}

	public function preview()
	{
		$page_id = !empty($_GET['page_id']) ? (int)$_GET['page_id'] : 0;

		if ($page_id) {
			$page = $this->Model_Page->getPageForPreview($page_id);
		} else {
			$page = array(
				'title'         => "New Page",
				'display_title' => 1,
				'content'       => '',
				'style'         => '',
			);
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Add Styles
		//Add Styles
		if (is_file(DIR_THEME . 'css/style.less')) {
			$style = $this->document->compileLess(DIR_THEME . 'css/style.less', 'fluid.style.less');
		} else {
			$style = theme_url('css/style.css');
		}

		$this->document->addStyle($style);

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
		$page['direction'] = $this->language->info('direction');
		$page['lang']      = $this->language->info('code');

		$page['styles']  = $this->document->renderStyles();
		$page['scripts'] = $this->document->renderScripts();

		//Render
		$this->response->setOutput($this->render('page/preview', $page));
	}
}
