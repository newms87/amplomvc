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

abstract class App_Controller_Table extends Controller
{
	/* Example:
	protected $model = array(
		//Required
		'class' => 'App_Model_Client',
		'path'  => 'admin/client',
		'label' => 'username',
		'value' => 'client_id',

		//Optional
		'title' => 'Client',
	);

	*/

	protected $model, $instance;

	public function __construct()
	{
		if (!$this->model) {
			return trigger_error(_l("%s: \$this->model must be set. See example in %s", __CLASS__, __FILE__));
		} elseif (empty($this->model['class']) || empty($this->model['path']) || empty($this->model['label']) || empty($this->model['value'])) {
			return trigger_error(_l("%s: \$this->model must have class, path, label and value set. See example in %s", __CLASS__, __FILE__));
		}

		parent::__construct();

		$this->instance = new $this->model['class']();

		$this->model += array(
			'title' => '',
		);
	}

	public function index($options = array())
	{
		//Page Head
		set_page_info('title', _l("%s Listings", $this->model['title']));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("%s List", $this->model['title']), site_url($this->model['path']));

		$options += array(
			'model'        => $this->model,
			'template'     => 'table/list',
			'batch_action' => array(
				'actions' => array(
					'delete' => array(
						'label' => _l("Delete"),
					),
				),
			),
		);

		if (!empty($options['batch_action']) && empty($options['batch_action']['url'])) {
			$options['batch_action']['url'] = site_url($this->model['path'] . '/batch-action');
		}

		//Response
		output($this->render($options['template'], $options));
	}

	public function listing($options = array())
	{
		$sort   = (array)_request('sort', !empty($options['sort_default']) ? $options['sort_default'] : null);
		$filter = (array)_get('filter', !empty($options['filter_default']) ? $options['filter_default'] : null);
		$options += array(
			'index'    => $this->model['value'],
			'page'     => _get('page'),
			'limit'    => _get('limit', option('admin_list_limit', 20)),
			'columns'  => array(),
			'actions'  => array(),
			'callback' => null,
		);

		$options['actions'] += array(
			'edit'   => array(
				'text' => _l("Edit"),
				'path' => $this->model['path'] . '/form',
			),
			'delete' => array(
				'text' => _l("Delete"),
				'path' => $this->model['path'] . '/remove',
			),
		);

		$options['columns'] += $this->instance->getColumns((array)_request('columns'));

		if (!empty($options['sort'])) {
			$sort = $options['sort'] + $sort;
		}

		if (!empty($options['filter'])) {
			$filter = $options['filter'] + $filter;
		}

		list($records, $total) = $this->instance->getRecords($sort, $filter, $options, true);

		foreach ($records as &$record) {
			if (isset($record[$this->model['value']])) {
				foreach ($options['actions'] as $name => $action) {
					if ($action && (isset($action['user_can']) ? $action['user_can'] : user_can('w', $action['path']))) {
						$action['href'] = site_url($action['path'], $this->model['value'] . '=' . $record[$this->model['value']]);

						$record['actions'][$name] = $action;
					}
				}
			}
		}
		unset($record);

		if (is_callable($options['callback'])) {
			$options['callback']($records, $total);
		}

		//Listing Widget Params
		$listing = array(
			'records'      => $records,
			'sort'         => $sort,
			'filter_value' => $filter,
			'total'        => $total,
		);

		//Default Values
		$listing += $options + array(
				'pagination'   => true,
				'listing_path' => $this->model['path'] . '/listing',
				'save_path'    => $this->model['path'] . '/save',
			);

		if (!isset($listing['extra_cols']) && empty($_REQUEST['columns'])) {
			$listing['extra_cols'] = $this->instance->getColumns();
		}

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form($options = array())
	{
		$options += array(
			'defaults' => array(),
			'template' => 'table/form',
			'columns'  => $this->instance->getColumns(),
			'model'    => $this->model,
		);

		//Page Head
		set_page_info('title', _l("%s Form", $this->model['title']));

		//Insert or Update
		$record_id = _get($this->model['value'], null);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("%s Listing", $this->model['title']), site_url($this->model['path']));
		breadcrumb($record_id ? _l("Update") : _l("New"), site_url($this->model['path'] . '/form', $this->model['value'] . '=' . $record_id));

		//The Data
		$record = $_POST;

		if ($record_id && !IS_POST) {
			$record = $this->instance->getRecord($record_id);
		}

		$record += $options['defaults'];

		$options['record_id'] = $record_id;
		$options['record']    = $record;

		//Response
		output($this->render($options['template'], $options));
	}

	public function save()
	{
		if ($record_id = $this->instance->save(_request($this->model['value']), $_POST)) {
			message('success', _l("The record has been updated."));
			message('data', array($this->model['value'] => $record_id));
			$_GET[$this->model['value']] = $record_id;
		} else {
			message('error', $this->instance->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error') && method_exists($this, 'form')) {
			post_redirect($this->model['path'] . '/form', $_GET);
		} else {
			redirect($this->model['path'] . '/form', $_GET);
		}
	}

	public function remove()
	{
		if ($this->instance->remove(_get($this->model['value']))) {
			message('success', _l("The record was removed."));
		} else {
			message('error', $this->instance->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect($this->model['path']);
		}
	}

	public function autocomplete($options = array())
	{
		$sort   = (array)_request('sort', isset($options['sort_default']) ? $options['sort_default'] : array($this->model['label'] => 'ASC'));
		$filter = (array)_request('filter', isset($options['filter_default']) ? $options['filter_default'] : null);
		$options += array(
			'page'  => _get('page'),
			'limit' => _get('limit', option('config_autocomplete_limit', 10)),
		);

		//Label and Value
		$value = _get('value', $this->model['value']);
		$label = _get('label', $this->model['label']);

		//Load Sorted / Filtered Data
		$records = $this->instance->getRecords($sort, $filter, $options);

		foreach ($records as &$record) {
			$record['label'] = $record[$label];
			$record['value'] = $record[$value];
		}
		unset($record);

		output_json($records);
	}

	public function batch_action($options = array())
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		if ($options['callback']) {
			$options['callback']($batch, $action, $value);
		} else {
			foreach ($batch as $record_id) {
				switch ($action) {
					case 'delete':
						$this->instance->remove($record_id);
						break;
				}
			}
		}

		if ($this->instance->hasError()) {
			message('error', $this->instance->fetchError());
		} else {
			message('success', _l("%s records updated successfully.", $this->model['title'] ?: 'All'));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect($this->model['path']);
		}
	}
}
