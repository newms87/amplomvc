<?php
class Admin_Controller_Tool_ErrorLog extends Controller
{
	public function index()
	{
		$this->template->load('tool/error_log');

		$this->language->load('tool/error_log');

		$this->document->setTitle(_l("Error Log"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Error Log"), $this->url->link('tool/error_log'));

		$url_query = $this->url->getQuery('filter_store');

		$this->data['remove'] = $this->url->link('tool/error_log/remove', $url_query);
		$this->data['clear']  = $this->url->link('tool/error_log/clear', $url_query);

		$defaults = array(
			'limit' => 100,
			'start' => 0
		);
		foreach ($defaults as $key => $default) {
			$$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		$filters = array('filter_store' => '');

		foreach ($filters as $key => $default) {
			$this->data[$key] = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		$filter_store = $this->data['filter_store'];
		$start = $this->data['start'];
		$limit = $this->data['limit'];

		if ($filter_store !== '') {
			if ($filter_store == 'a') {
				$store_name = 'Admin';
			} else {
				$store_name = $this->Model_Setting_Store->getStoreName((int)$filter_store);
			}
		} else {
			$store_name = '';
		}


		$current = -1;

		$file = DIR_LOGS . $this->config->get('config_error_filename');
		$log  = array();

		if (file_exists($file)) {
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false && ($current < ($start + $limit))) {
					$current++;

					if ($current >= $start) {

						$data = explode("\t", $buffer, 7);

						if (count($data) < 6 || ($store_name && $store_name != $data[4])) {
							continue;
						}

						$log[] = array(
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

		$next               = $start + $limit;
		$this->data['next'] = $current >= ($start + $limit) ? $this->url->link('tool/error_log', $url_query . '&start=' . $next . '&limit=' . $limit) : '';

		$prev               = $start - $limit > 0 ? $start - $limit : 0;
		$this->data['prev'] = $start > 0 ? $this->url->link('tool/error_log', $url_query . '&start=' . $prev . '&limit=' . $limit) : '';

		$this->data['limit'] = $limit;
		$this->data['log']   = $log;

		$this->data['filter_url'] = $this->url->link('tool/error_log');

		$this->data['loading'] = $this->image->get('data/ajax-loader.gif');

		$stores = $this->Model_Setting_Store->getStoreNames();

		$default_stores = array(
			array(
				'store_id' => '',
				'name'     => _l(" --- Please Select --- ")
			),
			array(
				'store_id' => 'a',
				'name'     => 'Admin'
			),
		);
		$stores         = array_merge($default_stores, $stores);

		$this->data['stores'] = $stores;

		if ($filter_store !== '') {
			$name = '';
			foreach ($stores as $store) {
				if ($store['store_id'] === $filter_store) {
					$name = $store['name'];
					break;
				}
			}

			$this->data['button_clear'] = _l("Clear All %s Entries", $name);
		} else {
			$this->data['button_clear'] = _l("Clear All %s Entries", _l("Log"));
		}


		$this->children = array(
			'common/header',
			'common/footer'
		);

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

			$this->language->load('tool/error_log');

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
			$msg = _l("Success: You have successfully removed entries from your error log!");
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
		$this->language->load('tool/error_log');

		$file = DIR_LOGS . $this->config->get('config_error_filename');


		$filters = array('filter_store' => '');

		foreach ($filters as $key => $default) {
			$this->data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		if ($filter_store !== '') {
			if ($filter_store == 'a') {
				$store_name = 'Admin';
			} else {
				$store_name = $this->Model_Setting_Store->getStoreName((int)$filter_store);
			}
		} else {
			$store_name = '';
		}

		if ($store_name) {
			$file_lines = explode("\n", file_get_contents($file));

			foreach ($file_lines as $key => $line) {
				$data = explode("\t", $line);

				if ($data[4] == $store_name) {
					unset($file_lines[$key]);
				}
			}

			file_put_contents($file, implode("\n", $file_lines));
		} else {
			$handle = fopen($file, 'w+');
			fclose($handle);
		}

		$this->message->add('success', _l("Success: You have successfully cleared your error log!"));

		$this->index();
	}
}
