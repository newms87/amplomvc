<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC Dev Plugin
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Example extends Controller
{
	public function __construct()
	{
		if (!user_can('access', 'admin')) {
			redirect('error/not_found');
		}
	}

	//Accessed via /site_root/example/example/
	public function index()
	{
		//The page
		$page_id = _get('page_id', 0);

		$page = $this->Model_Page->getPage($page_id);

		if (!$page) {
			//If you have an error in the Model call
			if ($this->Model_Page->hasError()) {
				message('error', $this->Model_Page->fetchError());
			}

			//For this example, if no page is found, we dont really care, so continue to load page.

			//You may want to redirect here though..
			//redirect('error/not_found');
		}

		//Alternative messages
//		message('warning', "my error message"); //essentially same as error
//		message('notify', "my notification message");
//		message('success', "my success message");
//		message('myclass', "my class message");

		//Page Head
		set_page_info('title', _l("My Page Title"));
		set_page_meta('description', _l("My Page Meta Description"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Example"), site_url('example/example'));

		//Template Data
		$data = $page;

		//To be more specific, we can set title and content here.
		//For this example title and content are loaded form the model into the $page variable,
		//which is then passed to $data.

		//Alternatively we can set those variables or any other template variable here.

		$data['title'] = _l("Example Title");
		$data['content'] = _l("A whole bunch of content for the page");

		//Render
		output($this->render('example/example', $data));
	}

	//Accessed via /site_root/example/example/my_method
	public function my_method()
	{
		//.. Your customer function here
	}
}
