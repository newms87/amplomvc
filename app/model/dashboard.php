<?php

class App_Model_Dashboard extends Model
{
	public function save($dashboard_id, $dashboard = array())
	{
		if (!$dashboard_id && empty($dashboard['name'])) {
			$count = 1;
			while (empty($dashboard['name'])) {
				$dashboard['name'] = 'New Dashboard ' . $count++;
				if ($this->queryVar("SELECT COUNT(*) FROM " . $this->prefix . "dashboard WHERE name = '$dashboard[name]'")) {
					$dashboard['name'] = '';
				}
			}
		}

		if ($dashboard_id) {
			$dashboard_id = $this->update('dashboard', $dashboard, $dashboard_id);
		} else {
			$dashboard_id = $this->insert('dashboard', $dashboard);
		}

		return $dashboard_id;
	}

	public function remove($dashboard_id)
	{
		$this->Model_View->removeGroup('dash-' . $dashboard_id);

		return $this->delete('dashboard', $dashboard_id);
	}

	public function getDashboard($dashboard_id)
	{
		$dashboard = $this->queryRow("SELECT * FROM " . $this->prefix . "dashboard WHERE dashboard_id = " . (int)$dashboard_id);

		if ($dashboard) {
			$dashboard['name'] = html_entity_decode($dashboard['name']);
		}

		return $dashboard;
	}

	public function getDashboards()
	{
		$dashboards = $this->queryRows("SELECT * FROM " . $this->prefix . "dashboard");

		foreach ($dashboards as &$dashboard) {
			$dashboard['name'] = html_entity_decode($dashboard['name']);
		}
		unset($dashboard);

		return $dashboards;
	}

	public function emailReports($dashboard_id, $to = null)
	{
		//New Dashboard
		if ($dashboard_id) {
			$dashboard = $this->Model_Dashboard->getDashboard($dashboard_id);
		}

		if (empty($dashboard)) {
			$this->error = _l("Unable to locate dashboard");
		} else {
			$views = $this->Model_View->getViews('dash-' . $dashboard_id);

			foreach ($views as &$view) {
				$view['image'] = $this->Model_View->getViewMeta($view['view_id'], 'chart_image');
			}
			unset($view);

			$data = array(
				'dashboard' => $dashboard,
				'views'     => $views,
				'to'        => $to ? $to : option('config_email'),
				'subject'   => _l("%s", strip_tags($dashboard['name'])),
			);

			call('mail/reports', $data);
		}

		return empty($this->error);
	}
}