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

class App_Controller_Index extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', option('site_title'));

		set_page_meta('description', option('site_meta_description'));

		//Render
		output($this->render('index'));
	}
}
