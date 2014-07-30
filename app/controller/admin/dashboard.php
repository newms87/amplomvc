<?php

class App_Controller_Admin_Dashboard extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Dashboards"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Dashboard"), site_url('admin/dashboard'));

		//Template Data
		$data['dashboards'] = $this->Model_Dashboard->getDashboards();

		//Render
		output($this->render('dashboard/list', $data));
	}

	public function view()
	{
		$dashboard_id = _get('dashboard_id');

		//New Dashboard
		if (!$dashboard_id) {
			$dashboard_id = $this->Model_Dashboard->save(null, array());
			redirect('admin/dashboard/view', 'dashboard_id=' . $dashboard_id);
		}

		$dashboard = $this->Model_Dashboard->getDashboard($dashboard_id);

		if (!$dashboard) {
			message('error', _l("Unable to locate dashboard with ID %s", $dashboard_id));
			redirect('admin/dashboard');
		}

		//Page Head
		$this->document->setTitle($dashboard['name']);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Dashboards"), site_url('admin/dashboard'));
		breadcrumb($dashboard['name'], site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard_id));

		$dashboard['group'] = 'dash-' . $dashboard_id;

		//Render
		output($this->render('dashboard/view', $dashboard));
	}

	public function save()
	{
		if ($this->Model_Dashboard->save(_request('dashboard_id'), $_POST)) {
			message('success', _l("The dashboard has been saved"));
		} else {
			message('error', $this->Model_Dashboard->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/dashboard');
		}
	}

	public function remove()
	{
		if ($this->Model_Dashboard->remove(_get('dashboard_id'))) {
			message('success', _l("The dashboard has been removed"));
		} else {
			message('error', $this->Model_Dashboard->getError());
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/dashboard');
		}
	}

	public function email_reports()
	{
		$dashboard_id = _request('dashboard_id');

		//New Dashboard
		if ($dashboard_id) {
			$dashboard = $this->Model_Dashboard->getDashboard($dashboard_id);
		}

		if (empty($dashboard)) {
			message('error', _l("Unable to locate dashboard"));
		} else {
			$views = $this->Model_View->getViews('dash-' . $dashboard_id);

			$data = array(
				'dashboard' => $dashboard,
				'views'     => $views,
			);

			call('mail/reports', $data);
		}

		if (IS_AJAX) {
			output($this->message->toJSON());
		} else {
			redirect('admin/dashboard');
		}
	}
}
