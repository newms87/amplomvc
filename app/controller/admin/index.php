<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Index extends Controller
{
	public function index()
	{
		set_page_info('title', option('admin_title', _l("Amplo MVC Admin")));

		breadcrumb(_l("Home"), site_url('admin'));

		$data = array();

		output($this->render('index', $data));
	}
}
