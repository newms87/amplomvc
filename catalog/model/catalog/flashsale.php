<?php
class ModelCatalogFlashsale extends Model {
   public function updateViewed($flashsale_id) {
      $user_id = $this->customer->getId();
      $this->query("INSERT INTO " . DB_PREFIX . "flashsale_views SET flashsale_id = '" . (int)$flashsale_id . "', user_id = '" . (int)$user_id . "', session_id = '" . session_id() . "', ip_address = '" . $_SERVER['REMOTE_ADDR'] . "', date = NOW()");
   }
      
   public function getFlashsale($flashsale_id) {
      if(isset($_GET['preview_flashsale']) && $this->user->canPreview('flashsale')){
         $query = $this->query("SELECT fs.* FROM " . DB_PREFIX . "flashsale fs WHERE fs.flashsale_id='$flashsale_id'");
      }
      else{
         $query = $this->query("SELECT fs.* FROM " . DB_PREFIX . "flashsale fs WHERE fs.flashsale_id='$flashsale_id' AND fs.status='1'");
      }
      return $query->num_rows?$query->row:false;
   }
   
   public function getFlashsales($filter = '', $sort ='fs.date_end', $limit = '') {
      $filter = empty($filter)?'':"AND $filter";
      $sort = empty($sort)?'':"ORDER BY $sort";
      $limit = empty($limit)?'':"LIMIT $limit";
      $query = $this->query("SELECT DISTINCT fs.* FROM " . DB_PREFIX . "flashsale fs LEFT JOIN " . DB_PREFIX . "url_alias ua ON (fs.keyword=ua.keyword) WHERE ua.status='1' AND fs.date_start < NOW() AND fs.date_end > NOW() AND fs.status='1' $filter $sort $limit");
      
      return $query->num_rows?$query->rows:false;
   }
   
   public function getFlashsaleProducts($flashsale, $sort_by=array()){
      if(is_integer($flashsale))
         $flashsale = $this->getFlashsale($flashsale);
      
      $section = isset($flashsale['section_attr'])?$flashsale['section_attr']:0;
      
      if(empty($sort_by)){
         $sort_by = array();
         if($section)
            $sort_by[] ='ad.name ASC';
         $sort_by[] = 'p.sort_order ASC';
      }else if(is_string($sort_by))
         $sort_by = array($sort_by);
      
      $lang_id = (int)$this->config->get('config_language_id');
      $customer_group_id = (int)$this->config->get('config_customer_group_id');


      $product_info = "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id=pd.product_id AND pd.language_id='$lang_id')";
      
      $special = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '$customer_group_id' AND ((ps.date_start = '" . DATETIME_ZERO . "' OR ps.date_start < NOW()) AND (ps.date_end = '". DATETIME_ZERO . "' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
      
      $flashsale_product = "LEFT JOIN " . DB_PREFIX . "flashsale_product fp ON(p.product_id=fp.product_id)";
      
      $attr_section = $section?"LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pa.product_id=p.product_id AND pa.language_id='$lang_id') " .
                      "JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id=pa.attribute_id AND a.attribute_group_id='$section') " .
                      "LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id=ad.attribute_id AND ad.language_id='$lang_id')":'';
      
      $select = "p.*, $special, pd.name";
      if($section)
         $select .= ", a.attribute_group_id as section_attr, a.attribute_id as section_id, ad.name as section_name";
      
      //replace the price to incorporate special and regular price
      foreach($sort_by as &$s)
         $s = preg_replace("/^price/","if(special IS NULL,p.price,special)",$s);
      
      $sort_by = "ORDER BY " . implode(', ',$sort_by);
      
      if(isset($_GET['preview_flashsale']) && $this->user->canPreview('flashsale')){
         $p_status = '';
      }
      else{
         $p_status = "AND p.status='1'";
      }
      
      $results = $this->query("SELECT $select FROM " . DB_PREFIX . "product p $flashsale_product $product_info $attr_section WHERE fp.flashsale_id='$flashsale[flashsale_id]' $p_status $sort_by");
      
      foreach($results->rows as &$product)
         if(!isset($product['section_id'])){
            $product['section_id'] = 0;
            $product['section_name'] = '';
            $product['section_attr'] = $section;
         }
      
      if($results->num_rows)
         return $results->rows;
      return array();
   }
   
   public function getFlashsaleArticles($flashsale_id){
      $query = $this->query("SELECT * FROM " . DB_PREFIX . "flashsale_article WHERE flashsale_id='$flashsale_id'");
      return $query->num_rows?$query->rows:array();
   }
   
   public function activateFlashsaleDesigners($flashsale_id){
      if(isset($_GET['preview_flashsale']) && $this->user->canPreview('flashsale')){
         return;
      }
      
      $query = $this->query("SELECT m.manufacturer_id, m.status FROM " . DB_PREFIX . "flashsale_designer fd LEFT JOIN " . DB_PREFIX . "manufacturer m ON(m.manufacturer_id=fd.designer_id) WHERE fd.flashsale_id='" . (int)$flashsale_id . "'");
      foreach($query->rows as $row){
         if(!(int)$row['status']){
            $this->model_catalog_designer->setDesignerStatus($row['manufacturer_id'], 1);
         }
      }
   }
   
   /**
    * The status of the flashsale
    * @param $flashsale - Can be a flashsale associative array or the flashsale ID
    * 
    * @return string in the set: 'disabled', 'active', 'not started', 'ended'
    */
   public function getStatus($flashsale){
      if(isset($_GET['preview_flashsale']) && $this->user->canPreview('flashsale')){
         return 'active';
      }
      
      if(is_integer($flashsale)){
         $query = $this->get('flashsale', 'date_start, date_end', array("flashsale_id"=>$flashsale));
         $flashsale = $query->row;
      }
      
      if(!$flashsale)return 'disabled';
      $diff_start =  date_diff(new DateTime(), new DateTime($flashsale['date_start']));
      $diff_end =  date_diff(new DateTime(), new DateTime($flashsale['date_end']));
      if(!$diff_start->invert)
         return 'not started';
      else if($diff_end->invert)
         return 'ended';
      else
         return 'active';
   }
   
   public function getFlashsaleDesigners($flashsale_id){
      $query = $this->query("SELECT fp.product_id, p.manufacturer_id, m.name, m.keyword FROM " . DB_PREFIX . "flashsale_product fp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id=fp.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id=m.manufacturer_id) WHERE fp.flashsale_id='$flashsale_id' AND m.status='1' GROUP BY m.manufacturer_id");
      return $query->rows;
   }
}
