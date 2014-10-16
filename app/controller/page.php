<?php

class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = _get('page_id', 0);

		if ($page_id) {
			$page = $this->Model_Page->getActivePage($page_id);
		} else {
			$page = $this->Model_Page->getPageByName($this->route->getSegment(1));
		}

		if (IS_AJAX || isset($_GET['content'])) {
			return $this->content($page);
		}

		if (!$page) {
			return call('error/not_found');
		}

		//Page Head
		$this->document->setTitle($page['title']);

		//TODO: Put the page style into a cached file. (load in page header!)
		$page['style'] = $this->Model_Page->compileStyle($page['style']);

		$page['page_id'] = $page_id;

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		output($this->render($template, $page));
	}

	public function content($page = array())
	{
		//The page
		$page_id = _get('page_id', 0);

		if ($page_id) {
			$page += $this->Model_Page->getActivePage($page_id);
		}

		if (!$page) {
			return '';
		}

		$page['style'] = $this->Model_Page->compileStyle($page['style']);

		//Render
		output($this->render('page/content', $page));
	}

	public function preview($page = array())
	{
		//The page
		$page_id = _get('page_id');

		if ($page_id) {
			$page += $this->Model_Page->getPageForPreview($page_id);
		} else {
			$page += array(
				'page_id'       => 0,
				'name'          => 'new-page',
				'title'         => "New Page",
				'display_title' => 1,
				'content'       => '',
				'style'         => '',
				'layout_id'     => option('config_default_layout'),
			);
		}

		if (!$page) {
			return call('error/not_found');
		}

		//Page Head
		$this->document->setTitle($page['title']);

		if ($page['style']) {
			$page['style'] = $this->document->compileLessContent($page['style']);
		}

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		output($this->render($template, $page));
	}

	public function preview_content($page = array())
	{
		//The page
		$page_id = _get('page_id', 0);

		$page += $this->Model_Page->getPageForPreview($page_id);

		if (IS_POST) {
			$page['content'] = html_entity_decode(_post('content'));
			$page['style']   = html_entity_decode(_post('style'));
		}

		if (!$page) {
			return '';
		}

		if ($page['style']) {
			$page['style'] = $this->document->compileLessContent($page['style']);
		}

		//Render
		if (IS_POST) {
			output($this->render('page/content', $page));
		} else {
			output($this->render('page/default', $page));
		}
	}
}
