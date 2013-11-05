<?php
class Admin_Controller_Tool_Logs extends Controller
{
	public function index()
	{
		//Log File
		$log = isset($_GET['log']) ? $_GET['log'] : 'log';

		$log_name = $log === 'log' ? _l("Default") : ucfirst($log);

		//Page Head
		$this->document->setTitle(_l("%s Log", $log_name));

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('Log Files'), $this->url->link('tool/logs'));
		$this->breadcrumb->add(_l("%s Log", $log_name), $this->url->link('tool/logs', 'log=' . $log));

		//Action Buttons
		$this->data['remove'] = $this->url->link('tool/logs/remove', 'log=' . $log);
		$this->data['clear']  = $this->url->link('tool/logs/clear', 'log=' . $log);

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

		$this->data['entries'] = $entries;

		$next = $start + $limit;
		$prev = $start - $limit > 0 ? $start - $limit : 0;

		if ($current >= ($start + $limit)) {
			$this->data['next'] = $this->url->link('tool/logs', 'log=' . $log . '&start=' . $next . '&limit=' . $limit);
		}

		if ($start > 0) {
			$this->data['prev'] = $this->url->link('tool/logs', 'log=' . $log . '&start=' . $prev . '&limit=' . $limit);
		}

		//Template Data
		$this->data['log_name'] = $log_name;

		$log_files = $this->tool->get_files_r(DIR_LOGS, array('txt'));

		foreach ($log_files as &$file) {
			$base = $file->getBasename('.txt');

			$file = array(
				'name' => $base === 'log' ? _l("Default") : ucfirst($base),
				'href' => $this->url->link('tool/logs', 'log=' . $base),
				'selected' => $base === $log,
			);
		}

		$this->data['data_log_files'] = $log_files;

		$this->data['limit'] = $sort['limit'];

		//Limits
		$this->data['limits'] = $this->sort->renderLimits();

		//The Template
		$this->template->load('tool/logs');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function remove($lines = null)
	{
		if (empty($_GET['log'])) {
			$this->url->redirect('tool/logs');
		}

		if (!isset($_POST['entries']) && $lines === null) {
			$this->message->add('warning', _l("No entries were selected for removal!"));
		} else {
			$entries = ($lines !== null) ? $lines : $_POST['entries'];

			if (preg_match("/[^\\d\\s,-]/", $entries) > 0) {
				$this->message->add('warning', _l("Invalid Entries for removal: %s. Use either ranges or integer values (eg: 3,40-50,90,100)", $entries));
			}

			$file = DIR_LOGS . $this->config->get('config_error_filename');

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
			$this->response->setOutput(json_encode($this->message->fetch()));
		} else {
			$this->url->redirect('tool/logs', 'log=' . $_GET['log']);
		}
	}

	public function clear()
	{
		if (empty($_GET['log'])) {
			$this->url->redirect('tool/logs');
		}

		$file = DIR_LOGS . $_GET['log'] . '.txt';

		file_put_contents($file, '');

		$this->message->add('success', _l("Log Entries have been cleared in <strong>$file</strong>!"));

		$this->url->redirect('tool/logs', 'log=' . $_GET['log']);
	}
}
