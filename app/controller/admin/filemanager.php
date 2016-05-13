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

class App_Controller_Admin_Filemanager extends Controller
{
	public function index()
	{
		_is_writable(DIR_IMAGE);

		require_once DIR_RESOURCES . 'js/responsive-filemanager/filemanager/dialog.php';
		//output("<iframe src=\"" . site_url('system/resources/js/responsive-filemanager/filemanager/dialog.php') . "\"></iframe>");
	}

	public function execute()
	{
		require_once DIR_RESOURCES . 'js/responsive-filemanager/filemanager/execute.php';
	}

	public function ajax_calls()
	{
		require_once DIR_RESOURCES . 'js/responsive-filemanager/filemanager/ajax_calls.php';
	}

	public function upload()
	{
		require_once DIR_RESOURCES . 'js/responsive-filemanager/filemanager/upload.php';
	}

	public function force_download()
	{
		require_once DIR_RESOURCES . 'js/responsive-filemanager/filemanager/force_download.php';
	}

	public function get_thumb()
	{
		$width  = _get('width', option('admin_thumb_width'));
		$height = _get('height', option('admin_thumb_height'));

		output(image($_GET['image'], $width, $height));
	}
}
