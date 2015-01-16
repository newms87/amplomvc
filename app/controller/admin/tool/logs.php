<?php

class App_Controller_Admin_Tool_Logs extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("System Logs"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("User"), site_url('admin/tool/logs'));

		//Listing
		$data['listing'] = $this->listing();

		//Batch Actions
		$actions = array(
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/tool/logs/batch-action'),
		);

		//Response
		output($this->render('tool/logs', $data));
	}

	public function listing()
	{
		//The Table Columns
		$requested_cols = $this->request->get('columns');
		$columns = $this->Model_Log->getColumns($requested_cols);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('log_id', 'desc');
		$filter = _get('filter', array());

		list($entries, $total) = $this->Model_Log->getRecords($sort, $filter, null, true);

		$listing = array(
			'row_id'         => 'log_id',
			'extra_cols'     => $this->Model_User->getColumns(false),
			'columns'        => $columns,
			'rows'           => $entries,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
			'listing_path'   => 'admin/tool/logs/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function batch_action()
	{

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
			redirect('admin/tool/logs', 'log=' . $log);
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
			redirect('admin/tool/logs', 'log=' . $log);
		}
	}
}
