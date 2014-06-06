<?php
class App_Controller_Admin_Tool_Logs extends Controller
{
	public function index()
	{
		//Log File
		$log = isset($_GET['log']) ? $_GET['log'] : 'log';

		$log_name = $log === 'log' ? _l("Default") : ucfirst($log);

		//Page Head
		$this->document->setTitle(_l("%s Log", $log_name));

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), site_url());
		$this->breadcrumb->add(_l('Log Files'), site_url('admin/tool/logs'));
		$this->breadcrumb->add(_l("%s Log", $log_name), site_url('admin/tool/logs', 'log=' . $log));

		//Sort and Filter
		$sort   = $this->sort->getQueryDefaults('store_id', 'ASC');
		$filter = isset($_GET['filter']) ? $_GET['filter'] : null;

		$start = $sort['start'];
		$limit = $sort['limit'];

		$current = -1;

		$file    = DIR_LOGS . $log . '.txt';
		$entries = array();

		if (file_exists($file)) {
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false && ($current < ($start + $limit))) {
					$current++;

					if ($current >= $start) {

						$data = explode("\t", $buffer, 7);

						//Invalid entry
						if (count($data) < 6) {
							continue;
						}

						$entries[] = array(
							'line'    => $current,
							'date'    => $data[0],
							'ip'      => $data[1],
							'uri'     => $data[2],
							'query'   => $data[3],
							'store'   => $data[4],
							'agent'   => $data[5],
							'message' => str_replace("__nl__", "<br />", $data[6]),
						);
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

		$log_files = $this->tool->getFiles(DIR_LOGS, array('txt'));

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

		//Action Buttons
		$data['remove'] = site_url('admin/tool/logs/remove', 'log=' . $log);
		$data['clear']  = site_url('admin/tool/logs/clear', 'log=' . $log);

		//Render
		$this->response->setOutput($this->render('tool/logs', $data));
	}

	public function remove($lines = null)
	{
		if (empty($_GET['log'])) {
			redirect('admin/tool/logs');
		}

		if (!isset($_POST['entries']) && $lines === null) {
			$this->message->add('warning', _l("No entries were selected for removal!"));
		} else {
			$entries = ($lines !== null) ? $lines : $_POST['entries'];

			if (preg_match("/[^\\d\\s,-]/", $entries) > 0) {
				$this->message->add('warning', _l("Invalid Entries for removal: %s. Use either ranges or integer values (eg: 3,40-50,90,100)", $entries));
			}

			$file = DIR_LOGS . option('config_error_filename');

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

			$this->message->add('success', _l('Entry Removed!'));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('admin/tool/logs', 'log=' . $_GET['log']);
		}
	}

	public function clear()
	{
		if (empty($_GET['log'])) {
			redirect('admin/tool/logs');
		}

		$file = DIR_LOGS . $_GET['log'] . '.txt';

		file_put_contents($file, '');

		$this->message->add('success', _l("Log Entries have been cleared in <strong>$file</strong>!"));

		redirect('admin/tool/logs', 'log=' . $_GET['log']);
	}
}
