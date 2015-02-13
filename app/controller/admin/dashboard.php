<?php

class App_Controller_Admin_Dashboard extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Dashboards"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Dashboard"), site_url('admin/dashboard'));

		//Template Data
		$data['dashboards'] = $this->Model_Dashboard->getUserDashboards();

		//Render
		output($this->render('dashboard/list', $data));
	}

	public function view()
	{
		$dashboard_id = _get('dashboard_id');

		//New Dashboard
		if (!$dashboard_id) {
			if (user_can('w', 'admin/dashboard/save')) {
				$dashboard_id = $this->Model_Dashboard->save(null, array());
				redirect('admin/dashboard/view', 'dashboard_id=' . $dashboard_id);
			} else {
				message('error', _l("You do not have permission to modify Dashboards"));
				redirect('admin/dashboard');
			}
		}

		$dashboard = $this->Model_Dashboard->getDashboard($dashboard_id);

		if (!$dashboard) {
			message('error', _l("Unable to locate dashboard with ID %s", $dashboard_id));
			redirect('admin/dashboard');
		}

		if (!user_can('r', 'admin/dashboards/' . $dashboard['name'])) {
			message('error', _l("You do not have permission to view the %s dashboard", $dashboard['title']));
			redirect('admin/dashboard');
		}

		//Page Head
		set_page_info('title', $dashboard['title']);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Dashboards"), site_url('admin/dashboard'));
		breadcrumb($dashboard['title'], site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard_id));

		$dashboard['group'] = 'dash-' . $dashboard_id;

		$dashboard['can_edit'] = user_can('w', 'admin/dashboards/' . $dashboard['name']);

		//Render
		output($this->render('dashboard/view', $dashboard));
	}

	public function save()
	{
		$dashboard_id = _request('dashboard_id');
		$dashboard    = $this->Model_Dashboard->getDashboard($dashboard_id);

		if ($dashboard) {
			if (user_can('w', 'admin/dashboards/' . $dashboard['name'])) {
				if ($dashboard_id = $this->Model_Dashboard->save($dashboard_id, $_POST)) {
					message('success', _l("The dashboard has been saved"));
					message('data', array('dashboard_id' => $dashboard_id));
				} else {
					message('error', $this->Model_Dashboard->getError());
				}
			} else {
				message('error', _l("You do not have permission to modify the %s dashboard", $dashboard['title']));
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/dashboard');
		}
	}

	public function remove()
	{
		$dashboard_id = _request('dashboard_id');
		$dashboard    = $this->Model_Dashboard->getDashboard($dashboard_id);

		if ($dashboard) {
			if (user_can('w', 'admin/dashboards/' . $dashboard['name'])) {
				if ($this->Model_Dashboard->remove($dashboard_id)) {
					message('success', _l("The dashboard has been removed"));
				} else {
					message('error', $this->Model_Dashboard->getError());
				}
			} else {
				message('error', _l("You do not have permission to modify the %s dashboard", $dashboard['title']));
			}
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/dashboard');
		}
	}

	public function email_reports()
	{
		$dashboard_id = _request('dashboard_id');

		$to = _request('to');

		if ($this->Model_Dashboard->emailReports($dashboard_id, $to)) {
			message('success', _l("Reports were emailed to %s", $to));
		} else {
			message('error', $this->Model_Dashboard->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/dashboard');
		}
	}
}
