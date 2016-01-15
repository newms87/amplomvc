<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = _get('page_id');

		$page = $this->Model_Page->getPage($page_id ? $page_id : $this->router->getSegment(1));

		if (!$page) {
			return call('error/not_found');
		}

		$page_id = $page['page_id'];

		//Page Head
		set_page_info('title', $page['title']);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		set_option('config_layout_id', $page['layout_id']);

		$page['content_file'] = _mod($page['content_file']);
		$page['style']        = $this->Model_Page->compileStyle($page_id, $page['style']);

		$template = 'page_template/' . (!empty($page['template']) ? $page['template'] : 'default');

		//Render
		output($this->render($template, $page));
	}

	public function preview($page = array())
	{
		if (!user_can('w', 'admin/page/form')) {
			redirect('error/not-found');
		}

		//The page
		if (isset($_GET['page_id'])) {
			$page += $this->Model_Page->getPageForPreview($_GET['page_id']);
		} elseif (IS_POST) {
			$page += $_POST;

			$page['title']   = urldecode(_post('title'));
			$page['content'] = urldecode(_post('content'));
			$page['style']   = urldecode(_post('style'));
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

		$data = $page + array(
				'page' => $page,
			);

		//Render
		output($this->render($template, $data));
	}
}
