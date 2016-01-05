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

class App_Controller_Manager_File extends Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!is_logged()) {
			redirect('customer/login');
		}
	}

	public function index($data = array())
	{
		if (!$this->is_ajax) {
			page_info('title', _l("Amplo File Manager"));
		}

		$data += array(
			'accept'       => '.png',
			'thumb_width'  => option('fm_thumb_width', 100),
			'thumb_height' => option('fm_thumb_height', 100),
		);

		//Render
		output($this->render('manager/file', $data));
	}

	public function listing($data = array())
	{
		$sort    = (array)_request('sort', array('title' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index' => 'file_id',
			'limit' => (int)_request('limit', 20),
			'start' => (int)_request('start'),
		);

		$filter['customer_id'] = customer_info('customer_id');

		list($files, $total) = $this->Model_File->getRecords($sort, $filter, $options, true);

		$data['files'] = $files;
		$data['total'] = $total;

		output_json($data);
	}

	public function upload()
	{
		$files = _files();

		$file_records = array();

		foreach ($files as $file) {
			if ($file_id = $this->Model_File->upload($file, $_POST)) {
				message('success', _l("File saved!"));
				$file_records[$file_id] = $this->Model_File->getRecord($file_id);
			}

			//Retrieve and clear all errors
			message('error', $this->Model_File->fetchError());
		}

		message('data', $file_records);

		output_message();
	}

	public function upload_iframe()
	{
		$files = _files();

		$path      = _post('path', '');
		$file_name = count($files) > 1 ? false : _post('name');

		$saved = array();

		foreach ($files as $file) {
			$name = $file_name ? $file_name : $file['name'];

			$path = ltrim(rtrim($path, '/') . '/' . $name, '/');

			if (empty($file['error'])) {
				if (_is_writable(dirname(DIR_DOWNLOAD . $path)) && move_uploaded_file($file['tmp_name'], DIR_DOWNLOAD . $path)) {
					$saved[$file['name']] = URL_DOWNLOAD . $path;
				} else {
					message('error', _l("There was a problem saving your file %s to the server. Please upload again.", $file['name']));
				}
			} else {
				message('error', _l("File upload failed for %s. Please try again.", $file['name']));
			}
		}

		message('data', $saved);

		output(json_encode($this->message->fetch()));
	}

	public function remove()
	{
		if ($this->Model_File->remove(_request('file_id'))) {
			message('success', _l("File removed successfully"));
		} else {
			message('error', $this->Model_File->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('file');
		}
	}
}
