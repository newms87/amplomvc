<?php

abstract class App_Controller_Table extends Controller
{
	/* Example:
	protected $model = array(
		//Required
		'class' => 'App_Model_Client',
		'path'  => 'admin/scope/client',
		'label' => 'username',
		'value' => 'client_id',
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
	}

	public function listing($options = array())
	{
		$sort   = (array)_request('sort', !empty($options['sort_default']) ? $options['sort_default'] : null);
		$filter = (array)_get('filter', !empty($options['filter_default']) ? $options['filter_default'] : null);
		$options += array(
			'index'   => $this->model['value'],
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => array(),
		);

		$options['columns'] += $this->instance->getColumns((array)_request('columns'));

		list($records, $total) = $this->instance->getRecords($sort, $filter, $options, true);

		if (!isset($options['callback'])) {
			foreach ($records as $record_id => &$record) {
				$actions = array(
					'edit'   => array(
						'text' => _l("Edit"),
						'href' => site_url($this->model['path'] . '/form', $this->model['value'] . '=' . $record_id)
					),
					'remove' => array(
						'text' => _l("Delete"),
						'href' => site_url($this->model['path'] . '/remove', $this->model['value'] . '=' . $record_id)
					),
				);

				$record['actions'] = $actions;
			}
			unset($record);
		} elseif (is_callable($options['callback'])) {
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
			$listing['extra_cols'] = $this->Model_Client->getColumns();
		}

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function save()
	{
		if ($record_id = $this->instance->save(_request($this->model['value']), $_POST)) {
			message('success', _l("The record has been updated."));
			message('data', array('record_id' => $record_id));
		} else {
			message('error', $this->instance->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect($this->model['path']);
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
}
