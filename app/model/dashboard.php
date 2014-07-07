<?php
class App_Model_Dashboard extends Model
{
	public function save($dashboard_id, $dashboard = array())
	{
		if ($dashboard_id && empty($dashboard['name'])) {
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
		$this->Model_Block_Widget_Views->removeGroup('dash-' . $dashboard_id);

		return $this->delete('dashboard', $dashboard_id);
	}

	public function getDashboard($dashboard_id)
	{
		$dashboard = $this->queryRow("SELECT * FROM " . $this->prefix . "dashboard WHERE dashboard_id = " . (int)$dashboard_id);

		$dashboard['name'] = html_entity_decode($dashboard['name']);

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
}