<?php

class App_Controller_Admin_Logs extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("System Logs"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/logs'));

		//Listing
		$data['listing'] = $this->listing();

		//Batch Actions
		$actions = array(
			'delete' => array(
				'label' => _l("Delete"),
			),
			'clear'  => array(
				'type'       => 'select',
				'label'      => _l("Clear"),
				'build_data' => array('' => _l("All")) + $this->Model_Log->getLogs(),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/logs/batch-action'),
		);

		//Response
		output($this->render('logs', $data));
	}

	public function listing()
	{
		$sort    = (array)_get('sort', array('log_id' => 'DESC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'log_id',
			'page'    => (int)_get('page'),
			'limit'   => (int)_get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_Log->getColumns((array)_request('columns')),
		);

		list($entries, $total) = $this->Model_Log->getRecords($sort, $filter, $options, true);

		$listing = array(
			'extra_cols'     => $this->Model_Log->getColumns(false),
			'records'        => $entries,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
			'listing_path'   => 'admin/logs/listing',
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function batch_action()
	{
		$action = _post('action');

		if ($action === 'clear') {
			$this->Model_Log->clear(_post('value'));
		} else {
			foreach (_post('batch', array()) as $log_id) {
				switch ($action) {
					case 'delete':
						$this->Model_Log->remove($log_id);
						break;
				}
			}
		}

		if ($this->Model_Log->hasError()) {
			message('error', $this->Model_Log->fetchError());
		} else {
			message('success', _l("The log table was updated (Note: Log files are unchanged)."));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/logs');
		}
	}

	public function remove($lines = null)
	{
		$log = _get('log');

		if ($log) {
			$entries = _post('entries', $lines);

			if (!$entries || preg_match("/[^\\d\\s,-]/", $entries)) {
				message('warning', _l("Invalid Entries for removal: %s. Use either ranges or integer values (eg: 3,40-50,90,100)", $entries));
			} else {

				$file = $this->dir . $log . '.txt';

				if (!is_file($file)) {
					message('warning', _l("Invalid log file %s", $file));
				} else {
					$file_lines = explode("\n", file_get_contents($file));

					foreach (explode(',', $entries) as $entry) {
						if (strpos($entry, '-')) {
							list($from, $to) = explode('-', $entry);
							for ($i = (int)$from; $i <= (int)$to; $i++) {
								unset($file_lines[$i]);
							}
						} else {
							unset($file_lines[(int)$entry]);
						}
					}

					file_put_contents($file, implode("\n", $file_lines));

					message('success', _l('Entry Removed from %s!', $file));
				}
			}
		} else {
			message('error', _l("Must specify log file"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/logs', 'log=' . $log);
		}
	}

	public function clear()
	{
		$log = _get('log');

		if ($log) {
			$file = $this->dir . $log . '.txt';

			if (is_file($file)) {
				file_put_contents($file, '');

				message('success', _l("Log Entries have been cleared in <strong>$file</strong>!"));
			} else {
				message('error', _l("Invalid log file %s", $log));
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/logs', 'log=' . $log);
		}
	}
}
