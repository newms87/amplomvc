<?php
/**
 * Title: Automated Tasks
 * Icon: cron_icon.png
 * Order: 12
 */

class Admin_Controller_Setting_Cron extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Automated Tasks"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->save('cron', 'cron_tasks', $_POST, 0, false);

			//TODO: Implement full cron control from this code:
			/*
			* 		$output = shell_exec('crontab -l');
		echo $output;
		file_put_contents('/tmp/crontab.txt', $output.'* * * * * NEW_CRON'.PHP_EOL);
		echo exec('crontab /tmp/crontab.txt');
		echo exec('rm -fv /tmp/crontab.txt');
			*/

			$this->message->add('success', _l('Successfully updated the Automated Tasks!'));

			$this->url->redirect('setting/cron');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l('System Settings'), $this->url->link('setting/store'));
		$this->breadcrumb->add(_l('Automated Tasks'), $this->url->link('setting/cron'));

		if ($this->request->isPost()) {
			$tasks = $_POST;
		} else {
			$tasks = $this->config->load('cron', 'cron_tasks', 0);
		}

		$data = $tasks;

		//AC Template
		$data['tasks']['__ac_template__'] = array(
			'name'       => "New Task",
			'file'       => '',
			'method'     => '',
			'time'       => array(
				'i' => '0',
				'h' => '*',
				'd' => '*',
				'm' => '*',
				'w' => '*',
			),
			'last_run'   => '',
			'status'     => 1,
			'sort_order' => 0,
		);

		//Template Data
		$data['cron_status'] = $this->config->get('config_cron_status');

		$cron_files = $this->tool->get_files_r(DIR_CRON);

		$cron_methods = array();

		foreach ($cron_files as $key => &$file) {
			$filename = pathinfo($file, PATHINFO_FILENAME);

			if ($filename === 'cron_job') {
				unset($cron_files[$key]);
				continue;
			}
			$contents = file_get_contents($file);

			$matches = null;
			preg_match_all("/public\\s*function\\s*([a-z0-9_]*)/i", $contents, $matches);

			$cron_methods[$filename] = $matches[1];

			$file = $filename;
		}

		$data['data_files']   = $cron_files;
		$data['data_methods'] = $cron_methods;

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']     = $this->url->link('setting/cron');
		$data['cancel']   = $this->url->link('setting/store');
		$data['run_cron'] = $this->url->link('common/home', 'run_cron');
		$data['activate'] = $this->url->link('setting/cron/activate');

		//Render
		$this->response->setOutput($this->render('setting/cron', $data));
	}

	public function activate()
	{
		if (isset($_POST['cron_status'])) {
			$this->config->save('config', 'config_cron_status', $_POST['cron_status'] ? 1 : 0, 0, true);
		}

		$this->url->redirect('setting/cron');
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'setting/cron')) {
			$this->error['warning'] = _l('You do not have permission to modify the Automated Tasks');
		}

		return $this->error ? false : true;
	}
}
