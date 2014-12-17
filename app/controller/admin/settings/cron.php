<?php
/**
 * Title: Automated Tasks
 * Icon: cron.png
 * Order: 12
 */

class App_Controller_Admin_Settings_Cron extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Automated Tasks"));

		if (IS_POST && $this->validate()) {
			$this->config->save('cron', 'cron_tasks', $_POST, false);

			//TODO: Implement full cron control from this code:
			/*
			* 		$output = shell_exec('crontab -l');
		echo $output;
		file_put_contents('/tmp/crontab.txt', $output.'* * * * * NEW_CRON'.PHP_EOL);
		echo exec('crontab /tmp/crontab.txt');
		echo exec('rm -fv /tmp/crontab.txt');
			*/

			message('success', _l('Successfully updated the Automated Tasks!'));

			redirect('admin/settings/cron');
		}

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l('System Settings'), site_url('admin/settings'));
		breadcrumb(_l('Automated Tasks'), site_url('admin/settings/cron'));

		if (IS_POST) {
			$tasks = $_POST;
		} else {
			$tasks = $this->config->load('cron', 'cron_tasks');
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
		$data['cron_status'] = option('config_cron_status');

		$cron_files = get_files(DIR_CRON);

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
		$data['save']     = site_url('admin/settings/cron');
		$data['cancel']   = site_url('admin/settings');
		$data['run_cron'] = site_url('', 'run_cron');
		$data['activate'] = site_url('admin/settings/cron/activate');

		//Render
		output($this->render('settings/cron', $data));
	}

	public function activate()
	{
		if (isset($_POST['cron_status'])) {
			$this->config->save('config', 'config_cron_status', $_POST['cron_status'] ? 1 : 0, true);
		}

		redirect('admin/settings/cron');
	}

	private function validate()
	{
		if (!user_can('w', 'admin/settings/cron')) {
			$this->error['warning'] = _l('You do not have permission to modify the Automated Tasks');
		}

		return empty($this->error);
	}
}
