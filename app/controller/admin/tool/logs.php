<?php

class App_Controller_Admin_Tool_Logs extends Controller
{
	private $dir;

	private $fields = array(
		'date'    => 'Date',
		'ip'      => 'IP',
		'message' => 'Message',
		'uri'     => 'URL',
		'query'   => 'Query',
		'agent'   => 'User Agent',
	);

	public function __construct()
	{
		parent::__construct();

		$this->dir = DIR_LOGS . SITE_PREFIX . '/';
	}

	public function index()
	{
		//Log File
		$log = _get('log', 'default');

		$log_name = ucfirst($log);

		//Page Head
		set_page_info('title', _l("%s Log", $log_name));

		//Breadcrumbs
		breadcrumb(_l('Home'), site_url('admin'));
		breadcrumb(_l('Log Files'), site_url('admin/tool/logs'));
		breadcrumb(_l("%s Log", $log_name), site_url('admin/tool/logs', 'log=' . $log));

		//Sort and Filter
		$sort   = $this->sort->getQueryDefaults('date', 'ASC');
		$filter = _get('filter', null);

		$start = $sort['start'];
		$limit = $sort['limit'];

		$current  = -1;
		$num_cols = count(Log::$cols);

		$file    = $this->dir . $log . '.txt';
		$entries = array();

		if (file_exists($file)) {
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false && ($current < ($start + $limit))) {
					$current++;

					if ($current >= $start) {

						$data = explode("\t", $buffer, $num_cols);

						//Invalid entry
						if (count($data) < $num_cols - 1) {
							continue;
						}

						$entry = array(
							'line' => $current,
						);

						$entry += array_combine(Log::$cols, $data);

						$entry['message'] = str_replace("__nl__", "<br />", $entry['message']);

						$entries[] = $entry;
					}
				}
				fclose($handle);
			}
		}

		$data['entries'] = $entries;

		$next = $start + $limit;
		$prev = $start - $limit > 0 ? $start - $limit : 0;

		if ($current >= ($start + $limit)) {
			$data['next'] = site_url('admin/tool/logs', 'log=' . $log . '&start=' . $next . '&limit=' . $limit);
		}

		if ($start > 0) {
			$data['prev'] = site_url('admin/tool/logs', 'log=' . $log . '&start=' . $prev . '&limit=' . $limit);
		}

		//Template Data
		$data['log_name'] = $log_name;

		$log_files = get_files($this->dir, array('txt'));

		foreach ($log_files as &$file) {
			$base = $file->getBasename('.txt');

			$file = array(
				'name'     => $base === 'log' ? _l("Default") : ucfirst($base),
				'href'     => site_url('admin/tool/logs', 'log=' . $base),
				'selected' => $base === $log,
			);
		}
		unset($file);

		$data['data_log_files'] = $log_files;

		$data['limit'] = $sort['limit'];

		//Limits
		$data['limits'] = $this->sort->renderLimits();

		$data['log'] = $log;

		$data['fields'] = $this->fields;

		//Render
		output($this->render('tool/logs', $data));
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
