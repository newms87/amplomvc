<?php
class App_Model_Dashboard extends Model
{
	public function getDashboard($dashboard_id)
	{
		return $this->queryRow("SELECT * FROM " . $this->prefix . "dashboard WHERE dashboard_id = " . (int)$dashboard_id);
	}

	public function getDashboards()
	{
		return $this->queryRows("SELECT * FROM " . $this->prefix . "dashboard");
	}

	public function getListings()
	{
		$paths = array(
			'scopes' => array(
				'path' => 'admin/scopes/listing',
			   'query' => 'scope=roofscope',
			   'name' => "Roofscope",
			),
			'admin/scopes/listing',
		   'admin/page/listing',
		   'admin/client/listing',
		);

		return $paths;
	}
}