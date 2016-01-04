<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Error extends Controller
{
	public function not_found($data = array())
	{
		set_page_info('title', _l("Page Not Found!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Page Not Found!"), site_url('admin/error/not-found'));

		output($this->render('error/not_found', $data), array('HTTP/1.1 404 Not Found'));
	}

	public function permission()
	{
		set_page_info('title', _l("Permission Denied!"));

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Permission Denied!"), site_url('admin/error/permission'));

		output($this->render('error/permission'), array('HTTP/1.1 403 Permission Denied'));
	}
}
