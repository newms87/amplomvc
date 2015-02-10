<?php

class App_Model_Dashboard extends App_Model_Table
{
	protected $table = 'dashboard', $primary_key = 'dashboard_id';

	public function save($dashboard_id, $dashboard = array())
	{
		if (!$dashboard_id && empty($dashboard['title'])) {
			$count = 1;
			while (empty($dashboard['title'])) {
				$dashboard['title'] = 'New Dashboard ' . $count++;
				if ($this->queryVar("SELECT COUNT(*) FROM {$this->t['dashboard']} WHERE `title` = '" . $dashboard['title'] . "'")) {
					$dashboard['title'] = '';
				}
			}
		}

		if ($dashboard_id) {
			$dashboard_id = $this->update('dashboard', $dashboard, $dashboard_id);
		} else {
			if (empty($dashboard['name'])) {
				$dashboard['name'] = slug($dashboard['title']);
			}

			$count = 1;
			while ($this->queryVar("SELECT COUNT(*) FROM {$this->t['dashboard']} WHERE `name` = '" . $dashboard['name'] . "'")) {
				$dashboard['name'] = preg_replace("/_[\\d]+/", '', $dashboard['name']) . '_' . $count++;
			}

			$dashboard_id = $this->insert('dashboard', $dashboard);
		}

		clear_cache('dashboard');

		return $dashboard_id;
	}

	public function remove($dashboard_id)
	{
		$this->Model_View->removeGroup('dash-' . $dashboard_id);

		clear_cache('dashboard');

		return $this->delete('dashboard', $dashboard_id);
	}

	public function getDashboard($dashboard_id)
	{
		$dashboard = $this->queryRow("SELECT * FROM {$this->t['dashboard']} WHERE dashboard_id = " . (int)$dashboard_id);

		if ($dashboard) {
			$dashboard['title'] = html_entity_decode($dashboard['title']);
		}

		return $dashboard;
	}

	public function getUserDashboards()
	{
		$dashboards = $this->getRecords(array('cache' => true));

		foreach ($dashboards as $key => $dashboard) {
			if (!user_can('r', 'admin/dashboards/' . $dashboard['name'])) {
				unset($dashboards[$key]);
			} else {
				$dashboards[$key]['title'] = html_entity_decode($dashboard['title']);
			}
		}

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
				if ($view['view_type']) {
					$view['image'] = $this->Model_View->getViewMeta($view['view_id'], 'chart_image');
				} else {
					$listing = $this->Model_View->getViewListing($view['view_listing_id']);

					if (!empty($listing['path'])) {
						$settings = array(
							array(
								'return_data'     => true,
								'view_listing_id' => $listing['view_listing_id'],
								'sort'            => array(
									'limit' => _request('limit', 100),
									'start' => _request('start', 0),
								),
							)
						);

						$action = new Action($listing['path'], $settings);
						$result = $action->execute();

						if ($result) {
							$view['data'] = $action->getOutput();
						}
					}
				}
			}
			unset($view);

			$data = array(
				'dashboard' => $dashboard,
				'views'     => $views,
				'to'        => $to ? $to : option('site_email'),
				'subject'   => _l("%s", strip_tags($dashboard['title'])),
			);

			call('mail/reports', $data);
		}

		return empty($this->error);
	}
}
