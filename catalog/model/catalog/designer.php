<?php
class ModelCatalogDesigner extends Model 
{
	
	public function addDesigner($data)
	{
		$data['name'] = $data['brand'];
		$data['keyword'] = isset($data['keyword'])?$data['keyword']:'';
		$data['image'] = isset($data['image'])?$data['image']:'';
		$data['vendor_id'] = '';
		$data['sort_order'] = isset($data['sort_order'])?$data['sort_order']:0;
		$data['date_expires'] = DATETIME_ZERO;
		$data['editable'] = 0;
		$data['section_attr'] =0;
		
		$designer_id = $this->insert('manufacturer', $data);
		
		$data['manufacturer_id'] = $designer_id;
		$data['language_id'] = $this->config->get('config_language_id');
		$data['description'] = $data['description'];
		$data['shipping_return'] = $this->_('shipping_return_policy');
		$data['teaser'] = '';
		
		$this->insert('manufacturer_description', $data);
		
		$this->model_includes_contact->addContact('manufacturer', $designer_id, $data);
	}
	
	public function getDesigner($designer_id, $description = false)
	{
		$desc_q = '';
		if ($description) {
		$description = "LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id=md.manufacturer_id AND md.language_id='" .$this->config->get('config_language_id') ."')";
		$desc_q = ", md.description";
		}
				
		$query = $this->query("SELECT m.manufacturer_id as designer_id, m.* $desc_q FROM " . DB_PREFIX . "manufacturer m $description LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$designer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		return $query->row;
	}
	
	public function getDesigners($data=null)
	{
		if ($data) {
			$designer_data = array();
			
			$sort = $order = '';
			if (isset($data['sort'])) {
				$sort ='ORDER BY ' . $data['sort'];
				
				if (isset($data['order'])) {
					$order = $data['order'] == 'DESC'?'DESC':'ASC';
				}
				else {
					$order = 'ASC';
				}
			}
			
			$start_limit = '';
			if (isset($data['limit'])) {
				$limit = (int)$data['limit'];
				if($limit < 1)
					$limit = 20;
				
				$start = isset($data['start'])?$data['start']:0;
				if($start < 0)
					$start = 0;
				
				$start_limit = "LIMIT $start, $limit";
			}
			
			$filter_store = "LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id=m2s.manufacturer_id)";
			
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m $filter_store WHERE m.status='1' AND m2s.store_id='" . (int)$this->config->get('config_store_id') . "' $sort $order $start_limit";
			$query = $this->query($sql);
			
			$designer_data = $query->rows;
		}
		else {
			$designer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));
		
			if (!$designer_data) {
				$query = $this->query("SELECT m.manufacturer_id as designer_id, m.* FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.status='1' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY name");
	
				$designer_data = $query->rows;
				
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $designer_data);
			}
		}
	
		return $designer_data;
	}
	
	public function hasProducts($designer_id)
	{
		$query = $this->query("SELECT count(product_id) as num_products FROM " . DB_PREFIX . "product WHERE manufacturer_id='$designer_id'");
		return $query->row['num_products'];
	}
	
	public function getDesignerProducts($designer, $sort_by=array()){
		if(is_integer($designer))
			$designer = $this->getDesigner($designer);
		$section = isset($designer['section_attr'])?$designer['section_attr']:0;
		if (empty($sort_by)) {
			$sort_by = array();
			if($section)
				$sort_by[] ='ad.name ASC';
			$sort_by[] = 'p.sort_order ASC';
		} else if (is_string($sort_by)) {
			$sort_by = array($sort_by);
		}
		
		$lang_id = (int)$this->config->get('config_language_id');
		$customer_group_id = (int)$this->config->get('config_customer_group_id');


		$product_info = "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id=pd.product_id AND pd.language_id='$lang_id')";
		
		$special = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '$customer_group_id' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
		
		$fs = "(SELECT fp.product_id, fp.flashsale_id, f.date_start, f.date_end FROM " . DB_PREFIX . "flashsale_product fp LEFT JOIN " . DB_PREFIX . "flashsale f ON(f.flashsale_id=fp.flashsale_id) WHERE f.date_start < NOW() AND f.date_end > NOW() AND f.status='1' ORDER BY fp.price ASC LIMIT 1) as fs ON (fs.product_id=p.product_id)";
		
		
		$attr_section = $section?"LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pa.product_id=p.product_id AND pa.language_id='$lang_id') " .
							"JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id=pa.attribute_id AND a.attribute_group_id='$section') " .
							"LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id=ad.attribute_id AND ad.language_id='$lang_id')":'';
		
		$select = "p.*, $special, fs.flashsale_id, fs.date_start, fs.date_end, pd.name";
		if($section)
			$select .= ", a.attribute_group_id as section_attr, a.attribute_id as section_id, ad.name as section_name";
		
		//replace the price to incorporate special and regular price
		foreach($sort_by as &$s)
			$s = preg_replace("/^price/","if(special IS NULL,p.price,special)",$s);
		
		$sort_by = "ORDER BY " . implode(', ',$sort_by);
		
		$results = $this->query("SELECT $select FROM " . DB_PREFIX . "product p LEFT JOIN $fs $product_info $attr_section WHERE p.manufacturer_id='$designer[designer_id]' AND p.status='1' $sort_by");
		
		foreach($results->rows as &$product)
			if (!isset($product['section_id'])) {
				$product['section_id'] = 0;
				$product['section_name'] = '';
				$product['section_attr'] = $section;
			}
		
		if($results->num_rows)
			return $results->rows;
		return false;
	}
	
	public function getDesignerArticles($designer_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer_article WHERE manufacturer_id='$designer_id'");
		return $query->num_rows?$query->rows:array();
	}

	public function setDesignerStatus($designer_id, $status)
	{
		$status = $status ? 1 : 0;
		
		$this->query("UPDATE " . DB_PREFIX . "manufacturer SET status='$status' WHERE manufacturer_id= '" . (int)$designer_id . "'");
		$this->model_setting_url_alias->setUrlAliasStatus('product/manufacturer/product', "manufacturer_id=$designer_id", $status);
		$this->model_setting_url_alias->setUrlAliasStatus('designers/designers', "designer_id=$designer_id", $status);
	}
	
	public function activateDesigners()
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE status='0' AND date_active <= NOW() AND date_active != '"  . DATETIME_ZERO . "' AND (date_expires > NOW() OR date_expires = '" . DATETIME_ZERO . "')");
		
		foreach ($query->rows as $row) {
			$this->setDesignerStatus($row['manufacturer_id'], 1);
		}
		
		return $query->rows;
	}
	
	public function expireDesigners()
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE status='1' AND date_expires < NOW() AND date_expires != '" . DATETIME_ZERO . "'");
		
		foreach ($query->rows as $row) {
			$this->setDesignerStatus($row['manufacturer_id'], 0);
		}
		
		return $query->rows;
	}
	
	public function getExpiringSoon($days=5)
	{
		$min_days = $days-1;
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE DATE_ADD(CURDATE(),INTERVAL $days DAY) >= date_expires AND date_expires > DATE_ADD(CURDATE(),INTERVAL $min_days DAY)");
		return $query->rows;
	}
	
	public function is_flashsale_page($designer_id, $with_info=false)
	{
		$select = $with_info?"f.*":"fd.flashsale_id";
		$query = $this->query("SELECT $select FROM " . DB_PREFIX . "flashsale_designer fd LEFT JOIN " . DB_PREFIX . "flashsale f ON(fd.flashsale_id=f.flashsale_id) WHERE fd.designer_id='$designer_id' AND f.status='1' AND f.date_start < NOW() AND f.date_end > NOW()");
		return $query->num_rows>0?($with_info?$query->row:$query->row['flashsale_id']):false;
	}
}
