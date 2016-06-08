<?php

/**
 * @author  Daniel Newman
 * @date    6/5/2016
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class App_Controller_Restful extends App_Controller_Api
{
	protected
		$id,
		$mode,
		$data,
		$model,
		$pk = 'id',
		$instance;

	const
		MODE_CREATE = 1,
		MODE_READ = 2,
		MODE_UPDATE = 3,
		MODE_MODIFY = 4,
		MODE_DELETE = 5;

	public function __construct()
	{
		if (!$this->model) {
			trigger_error(_l("The model for the restful API interface was not set. Please set the protected \$model attribute."));
			exit;
		}

		parent::__construct();

		$this->instance = $this->registry->get($this->model);

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$this->mode = self::MODE_CREATE;
				break;

			case 'PUT':
				$this->mode = self::MODE_UPDATE;
				break;

			case 'PATCH':
				$this->mode = self::MODE_MODIFY;
				break;

			case 'DELETE':
				$this->mode = self::MODE_DELETE;
				break;

			case 'GET':
			default:
				$this->mode = self::MODE_READ;
				break;
		}

		$this->args = $this->router->getParameters();

		if (!empty($this->args[0]) && is_numeric($this->args[0])) {
			$this->id = (int)$this->args[0];
		}

		$this->data = json_decode(file_get_contents('php://input'), true);
	}

	public function index()
	{
		switch ($this->mode) {
			case self::MODE_CREATE:
				return $this->create();

			case self::MODE_UPDATE:
				return $this->update();

			case self::MODE_MODIFY:
				return $this->modify();

			case self::MODE_DELETE:
				return $this->delete();

			case self::MODE_READ:
				return $this->read();
		}
	}

	protected function read()
	{
		if ($this->id) {
			$record = $this->instance->getRecord($this->id);

			if ($record) {
				output_restful(200, 'OK', $record);
			} else {
				output_restful(404, 'Resource Not Found');
			}
		} else {
			$options = (array)_request('options');

			//Limit API requests to 20 results by default
			$options += array(
				'limit' => 20,
			);

			$records = $this->instance->getRecords((array)_request('sort'), (array)_request('filter'), $options, _request('total'));

			output_restful(200, 'OK', $records);
		}
	}

	protected function create()
	{
		$path = $this->router->getPath();

		if ($this->id) {
			if ($this->instance->hasRecord($this->id)) {
				output_restful(409, "Conflict");
			} else {
				$this->data[$this->pk] = $this->id;

				if ($record_id = $this->instance->save(null, (array)$this->data)) {
					$response = array(
						'id'  => $record_id,
						'url' => site_url($path . '/' . $record_id),
					);

					output_restful(201, "Created", $response);
				} else {
					$response = array(
						'error' => $this->instance->fetchError(),
					);

					output_restful(400, "Bad Request", $response);
				}
			}
		} else {
			$response = array();

			foreach ($this->data as $key => $data) {
				if ($record_id = $this->instance->save(null, $data)) {
					$response['data'][$key] = array(
						'id'  => $record_id,
						'url' => site_url($path . '/' . $record_id),
					);
				} else {
					$response['error'][$key] = $this->instance->fetchError();
				}
			}

			if (!empty($response['data'])) {
				output_restful(201, "Created", $response);
			} else {
				output_restful(400, "Bad Request", $response);
			}
		}
	}

	protected function update()
	{

	}

	protected function modify()
	{

	}

	protected function delete()
	{
		if ($this->id) {
			if ($this->instance->hasRecord($this->id)) {
				if ($this->instance->remove($this->id)) {
					output_restful(200, 'OK');
				} else {
					output_restful(400, 'Bad Request', $this->instance->fetchError());
				}
			} else {
				output_restful(404, 'Not Found');
			}
		} else {
			if ($this->instance->clearAllRecords()) {
				output_restful(204, 'No Content');
			}
		}
	}
}
