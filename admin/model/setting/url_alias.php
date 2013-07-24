<?php
class Admin_Model_Setting_UrlAlias extends Model
{
	public function addUrlAlias($data)
	{
		$data['status'] = isset($data['status'])?$data['status']:1;
		$data['keyword'] = $this->format_url($data['keyword']);
		
		if (!isset($data['store_id']) || (empty($data['store_id']) && $data['store_id'] !== 0)) {
			$data['store_id'] = -1;
		}
		
		$this->insert('url_alias', $data);
	}
	
	public function editUrlAlias($url_alias_id, $data)
	{
		if (isset($data['keyword'])) {
			$data['keyword'] = $this->format_url($data['keyword']);
		}
		
		if (!isset($data['store_id']) || (!$data['store_id'] && $data['store_id'] !== 0)) {
			$data['store_id'] = -1;
		}
		
		$this->update('url_alias', $data, $url_alias_id);
	}
	
	public function format_url($url)
	{
		$l = preg_replace("/[^A-Za-z0-9\/\\\\]+/","-",strtolower($url));
		$l = preg_replace("/(^-)|(-$)/",'',$l);
		$l = preg_replace("/[\/\\\\]-/","/",$l);
		return $l;
	}
	
	public function deleteUrlAlias($url_alias_id)
	{
		$this->delete('url_alias', $url_alias_id);
	}
	
	public function deleteUrlAliasByRouteQuery($route, $query)
	{
		$this->delete('url_alias', array('route'=>$route, 'query'=>$query));
	}
	
	public function getUrlAlias($url_alias_id)
	{
		$query = $this->get("url_alias", '*', $url_alias_id);
		
		return $query->row;
	}
	
	public function getUrlAliasByKeyword($keyword)
	{
		$query = $this->get("url_alias", '*', array('keyword' => $keyword));
		
		return $query->row;
	}
	
	public function getUrlAliasByRouteQuery($route, $query = '')
	{
		$query = $this->get("url_alias", '*', array('route' => $route, 'query' => $query));
		
		return $query->row;
	}
	
	public function getUrlAliases()
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "url_alias ORDER BY keyword");
		
		return $query->rows;
	}
}
