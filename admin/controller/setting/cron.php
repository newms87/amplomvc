<?php
class Admin_Controller_Setting_Cron extends Controller
{
	public function index()
	{
		$this->document->setTitle(_("Automated Tasks"));

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

			$this->message->add('success', _('Successfully updated the Automated Tasks!'));

			$this->url->redirect($this->url->link('setting/cron'));
		}

		//Breadcrumbs
		$this->breadcrumb->add(_("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_('System Settings'), $this->url->link('setting/store'));
		$this->breadcrumb->add(_('Automated Tasks'), $this->url->link('setting/cron'));

		if ($this->request->isPost()) {
			$tasks = $_POST;
		} else {
			$tasks = $this->config->load('cron', 'cron_tasks', 0);
		}

		$this->data = $tasks;

		//AC Template
		$this->data['tasks']['__ac_template__'] = array(
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

		$this->data['data_files']   = $cron_files;
		$this->data['data_methods'] = $cron_methods;

		//Action Buttons
		$this->data['save']     = $this->url->link('setting/cron');
		$this->data['cancel']   = $this->url->link('setting/store');
		$this->data['run_cron'] = $this->url->link('common/home', 'run_cron');

		//The Template
		$this->template->load('setting/cron');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/cron')) {
			$this->error['warning'] = _('You do not have permission to modify the Automated Tasks');
		}

		return $this->error ? false : true;
	}
}
