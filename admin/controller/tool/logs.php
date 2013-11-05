<?php
class Admin_Controller_Tool_Logs extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('tool/logs'));

		//Log File
		$log = isset($_GET['log']) ? $_GET['log'] : 'log';

		//Action Buttons
		$this->data['remove'] = $this->url->link('tool/logs/remove', 'log=' . $log);
		$this->data['clear']  = $this->url->link('tool/logs/clear', 'log=' . $log);

		//Sort and Filter
		$sort   = $this->sort->getQueryDefaults('store_id', 'ASC');
		$filter = isset($_GET['filter']) ? $_GET['filter'] : null;

		$start = $sort['start'];
		$limit = $sort['limit'];

		$current = -1;

		$file    = DIR_LOGS . $this->config->get('config_error_filename');
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
							'message' => $data[6],
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
		$this->data['log_name'] = $log;


		$this->data['filter_url'] = $this->url->link('tool/logs');

		$stores               = $this->Model_Setting_Store->getStoreNames();
		$this->data['stores'] = $stores;

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

	public function remove($lines = null, $get_page = false)
	{
		$get_page = $lines !== null ? $get_page : !isset($_POST['no_page']);

		if (!isset($_POST['entries']) && $lines === null) {
			$msg = "No entries were selected for removal!";
			if ($get_page) {
				$this->message->add('warning', $msg);
			} else {
				echo $msg;
			}
		} else {
			$entries = ($lines !== null) ? $lines : $_POST['entries'];

			if (preg_match("/[^\d\s,-]/", $entries) > 0) {
				$msg = "Invalid Entries for removal: $entries. Use either ranges or integer values (eg: 3,40-50,90,100)";
				if ($get_page) {
					$this->message->add('warning', $msg);
				} else {
					echo $msg;
				}
			}

			$this->language->load('tool/logs');

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
			$msg = $this->_('text_success_remove');
			if ($get_page) {
				$this->message->add('success', $msg);
			} else {
				echo $msg;
			}
		}
		if ($get_page) {
			$this->index();
		}
	}

	public function clear()
	{
		if (empty($_GET['log'])) {
			$this->url->redirect('tool/logs');
		}

		$file = DIR_LOGS . $_GET['log'] . '.txt';

		$handle = fopen($file, 'w+');
		fclose($handle);

		$this->message->add('success', _l('Log Entries have been cleared!'));

		$this->url->redirect('tool/logs', 'log=' . $_GET['log']);
	}
}
