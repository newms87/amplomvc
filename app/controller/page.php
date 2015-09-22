<?php

class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = _get('page_id');

		$page = $this->Model_Page->getPage($page_id ? $page_id : $this->route->getSegment(1));

		if (!$page) {
			return call('error/not_found');
		}

		$page_id = $page['page_id'];

		//Page Head
		set_page_info('title', $page['title']);

		//TODO: Put the page style into a cached file. (load in page header!)
		$page['style'] = $this->Model_Page->compileStyle($page_id, $page['style']);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		set_option('config_layout_id', $page['layout_id']);

		$page['content_file'] = _mod($page['content_file']);

		$template = 'page_template/' . (!empty($page['template']) ? $page['template'] : 'default');

		//Render
		output($this->render($template, $page));
	}

	public function preview($page = array())
	{
		//The page
		if (isset($_GET['page_id'])) {
			$page += $this->Model_Page->getPageForPreview($_GET['page_id']);
		} elseif (IS_POST) {
			$page += $_POST;

			$page['content'] = html_entity_decode(_post('content'));
			$page['style']   = html_entity_decode(_post('style'));
		}

		if (!$page) {
			return call('error/not_found');
		}

		$page += array(
			'page_id'   => 0,
			'layout_id' => 0,
		);

		//Page Head
		set_page_info('title', $page['title']);

		if ($page['style']) {
			$page['style'] = $this->document->compileLessContent($page['style']);
		}

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		set_option('config_layout_id', $page['layout_id']);

		$template = 'page_template/' . (!empty($page['template']) ? $page['template'] : 'default');

		//Render
		output($this->render($template, $page));
	}
}
