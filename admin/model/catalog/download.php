<?php
class Admin_Model_Catalog_Download extends Model 
{
	public function addDownload($data)
	{
		$data['date_added'] = $this->date->now();
		
		if (!empty($data['download'])) {
			$data['filename'] = $data['download'];
		}
		
		$download_id = $this->insert('download', $data);
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('download', $download_id, $data['translations']);
		}
	}
	
	public function editDownload($download_id, $data)
	{
		$old_download = $this->query("SELECT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		
		if (!empty($data['download'])) {
			$data['filename'] = $data['download'];
		}
		
		$download_id = $this->update('download', $data, $download_id);
		
		if (!empty($data['translations'])) {
			$this->translation->setTranslations('download', $download_id, $data['translations']);
		}
		
		//Update Download file for already purchased downloads
		if (isset($data['update'])) {
			$old_filename = $this->escape($old_download['filename']);
			$old_remaining = (int)$old_download['remaining'];
			$new_remaining = (int)$data['remaining'];
			
			$this->query("UPDATE " . DB_PREFIX . "order_download SET remaining = $new_remaining - ($old_remaining - remaining), `filename` = '" . $this->escape($data['filename']) . "', mask = '" . $this->escape($data['mask']) . "' WHERE `filename` = '" . $this->escape($old_filename) . "'");
		}
	}
	
	public function deleteDownload($download_id)
	{
		$this->delete('download', $download_id);
		
		$this->translation->delete('download', $download_id);
	}

	public function getDownload($download_id)
	{ 
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
	}

	public function getDownloads($data = array(), $select = '', $total = false) {
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} else {
			$select = "*";
		}
		
		//From
		$from = DB_PREFIX . "download d";
		
		//Where
		$where = '1';
		
		//Order and limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function getTotalDownloads($data = array())
	{
		return $this->getDownloads($data, '', true);
	}
}