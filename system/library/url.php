<?php
class Url {
   private $registry;
	private $url;
	private $ssl;
	private $rewrite = array();
   private $pretty_url;
   private $use_pretty_url = false;
	private $ie_version = null;
   private $secure_pages = array();
   private $store_info = array();
   
	public function __construct(&$registry, $url, $ssl) {
		$this->url = $url;
		$this->ssl = $ssl;
      $this->registry = &$registry;
      
      //TODO - finish secure pages
      if($ssl){
         $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "secure_page");
         $this->secure_pages = $query->rows;
      }
	}
   
   public function __get($key){
      return $this->registry->get($key);
   }
   
   public function current_page(){
      $protocol = !empty($_SERVER['HTTPS']) ? 'https://':'http://';
      return $protocol . $_SERVER['SERVER_NAME'] . preg_replace('/&amp;/','%26',$_SERVER['REQUEST_URI']);
   }
   
   public function reload_page(){
      header("Location: http://" . $_SERVER['SERVER_NAME'] . str_replace('&amp;','&',$_SERVER['REQUEST_URI']));
      exit;
   }
   
   public function load($url, $admin = false){
      
      if($admin){
         //we save the session to the DB because we lose sessions when using cURL
         $this->session->save_token_session();
      }
      
       session_write_close();
       
       $ch = curl_init();
       $timeout = 5;
       
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
       
       if($admin){
         curl_setopt($ch, CURLOPT_COOKIE, 'token=' . $this->session->data['token']);
       }
       
       $data = curl_exec($ch);
       
       curl_close($ch);
       
       return $data;
   }
   
   public function get_query(){
      $query = '';
      
      $args = func_get_args();
      
      $filters = array();
      
      foreach($args as $a){
         if(is_array($a)){
            $filters = array_merge($filters, $a);
         }
         elseif(is_string($a)){
            $filters[] = $a;
         }
         else{
            list(,$caller) = debug_backtrace(false);
            trigger_error("Url::get_query(\$arg1, [\$arg2, ...]) - all arguments must be an array or string! called from $caller[file] line $caller[line]");
            return '';
         }
      }

      if($filters){
         foreach($filters as $f){
            if (isset($_GET[$f])){
               if(is_array($_GET[$f])){
                  $query .= ($query ? '&':'') . http_build_query(array($f => $_GET[$f]));
               }
               else{
                  $query .= ($query ? '&':'') . "$f=" . $_GET[$f];
               }
            }
         }
      }
      else{
         return http_build_query($_GET);
      }
      
      $query = $this->decodeURIComponent($query);
      
      return $query;
   }
	
   public function get_pretty_url(){
      return $this->pretty_url;
   }
   
   public function pretty_link($route, $args){
      return $this->link($route, $args, false, true, true);
   }
   
   public function link_admin($route, $args = '', $encode_url = false){
      $link = $this->link($route, $args, 'admin', $encode_url);
      
      if(!$this->user->validate_token()){
         $this->user->login('guest','guest');
      }
      
      if(empty($this->session->data['token'])){
         trigger_error("There was an error while generating the Admin link. The token was not set!");
         return '';
      }
      
      return $link;
   }
   
	public function link($route, $query = '', $store_id=false, $encode_url = true, $force_pretty_url=false) {
      $url = '';
      $query = ltrim($query, '&');
      
      //This will Generate pretty_url's for the links for browsers
      //that do not support the window.history.pushState() javascript function (HTML 5)
      if($this->use_pretty_url){
         if($this->ie_version === null){
            $this->ie_version = $this->is_IE();
         }
         
         //TODO: IMPORTANT: Find a way to optimize this for SEO compatibility! (maybe only do this for web crawlers / search bots?)
         if($force_pretty_url || true || (!defined("IS_ADMIN") && $this->ie_version !== false && $this->ie_version < 10)){
            return $this->find_alias($route, $query, $store_id);
         }
      }
      
      $url = $this->get_base_url($store_id) . '?route=' . $route;
		
		if ($query) {
		   if($encode_url){
			   $url .= str_replace('&', '&amp;',  '&' . $query);
         }
         else{ 
            $url .= '&' . $query;
         }
		}
      
		return $url;
	}
   
   private function get_base_url($store_id){
      //If no pretty url was requested or found
      
      if($store_id === 0){
      	return SITE_URL;
		}
		elseif($store_id > 0){
         $link = $this->ssl ? 'ssl':'url';
         
         $link_query = $this->db->query("SELECT $link as link FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
         
         if($link_query->num_rows){
            return $link_query->row['link'];
         }
         
         trigger_error("Error in Url Library: Store did not exist! store_id = " . $store_id . '.  ' . get_caller(2));
          
         return '';
      }
      else{
         $url = $this->ssl ? $this->ssl : $this->url;
         
         if($store_id === 'admin' && !defined("IS_ADMIN")){
            return $url . 'admin/';
         }
         
         return $url; 
      }
   }
   
   /**
    * This determines if the browser being used is Internet Explorer and returns the version number
    * 
    * @return int or bool - version # of IE, or false if it is not IE 
    */
   public function is_IE(){
      $match = null;
      
      $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER["HTTP_USER_AGENT"] : '';
       
      if(preg_match("/MSIE\s*\d{1,2}.\d{1,2}/i", $user_agent, $match) == 0){
         return false;
      }
      else {
         return (float)str_replace('msie ', '', strtolower($match[0])); 
      }
      
   }
	
   public function site($uri='', $query='', $base_site=false){
      return ($base_site ? SITE_URL : $this->url) . $uri . (!empty($query)?"?$query":'');
   }
	
   public function store($store_id, $uri='', $query=''){
      $link = $this->ssl ? 'ssl' : 'url';
      
      $q = $this->db->query("SELECT $link as link FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
      
		if(!$q->num_rows) return '';
      $url = $q->row['link'];
      
      return $url . $uri . (!empty($query) ? '?' . $query : '');  
   }
   
   public function urlencode_link($uri='',$query=''){
      return preg_replace("/%26amp%3B/i","%26",urlencode($this->link($uri,$query)));
   }
   
   public function decodeURIcomponent($uri){
      $patterns = array('/&gt;/','/&lt;/');
      $replacements = array('>','<');
      return preg_replace($patterns, $replacements, rawurldecode($uri));
   }
	
	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}
   
   public function redirect($url, $status = 302) {
      header('Status: ' . $status);
      header('Location: ' . str_replace('&amp;', '&', $url));
      exit();
   }
   
   public function getSeoUrl(){
      //getSeoUrl() is always called if config is set, immediately after creating a new Url() in the startup
      //if we are using pretty_urls
      $this->use_pretty_url = true;
      
      //Pretty Urls
      if(isset($_GET['route'])){
         $this->pretty_url = $this->find_alias($_GET['route'], http_build_query($_GET), null, true);
         
         return $this->pretty_url;
      }
      
      // Decode URL
      if (isset($_GET['_route_'])) {
         $parts = $_GET['_route_'];
         $parts = trim($parts,'/ ');
         
         $parts = preg_replace("/^admin\/?/", '', $parts);
         
         $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($parts) . "' AND status = '1' LIMIT 1");
         
         if ($query->num_rows) {
            $this->pretty_url = $this->site($parts);
            
            if($query->row['redirect']){
               if(!preg_match("/^https?:\/\//",$query->row['redirect'])){
                  $query->row['redirect'] = $this->url . ($query->row['admin'] ? 'admin/index.php?' : 'index.php?') . $query->row['redirect']; 
               }
               
               $this->redirect($query->row['redirect']);
            }
            
            if($query->row['admin'] && !defined("IS_ADMIN")){
               if(isset($_GET['_route_'])){
                  unset($_GET['_route_']);
               }
               
               $this->redirect($this->url . 'admin/' . $query->row['keyword'] . (!empty($_GET) ? '?'.http_build_query($_GET) : ''));
            }
            
            $url = explode('=',$query->row['query'],2);
            
            if($url[0] == 'redirect'){
               list($route,$query) = explode('?',$url[1],2);
               $this->redirect($this->url->link($route,$query),302);
            }
            
            $_GET['route'] = $query->row['route'];
            if(count($url) > 1){
               $_GET[$url[0]] = $url[1];
            }
         }
         
         if(!isset($_GET['route'])){
            $_GET['route'] = 'error/not_found';
         }
      }
      elseif(!isset($_GET['route'])){
         $_GET['route'] = 'common/home';
      }
      
      return $_GET['route'];
   }

   private function find_alias($route, $query = '', $store_id = false, $redirect = false){
      if($store_id === false && defined("IS_ADMIN")){
         $store_id =  'admin';
      }
      
      $admin_query = $store_id === 'admin' ? "AND admin='1'" : '';
      
      $remove_query_vars = array(
         'route',
         '_route_',
      );
      
      if($query){
         $query_field = "AND '" . $this->db->escape(urldecode($query)) . "' like CONCAT('%', query, '%')";
      }
      else{
         $query_field = "AND query = ''";
      }
      
      $q = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE '" . $this->db->escape($route) . "' like CONCAT(route, '%') AND route != '' $query_field AND status='1' $admin_query ORDER BY query DESC LIMIT 1");
      
      if($q->num_rows){
         if($q->row['redirect']){
            if(!preg_match("/^https?:\/\//",$q->row['redirect'])){
               $q->row['redirect'] = $this->url . ($q->row['admin'] ? 'admin/index.php?' : 'index.php?') . $q->row['redirect']; 
            }
            
            if($redirect){
               $this->redirect($q->row['redirect']);
            }
            else{
               return $q->row['redirect'];
            }
         }
         
         $keyword = $q->row['keyword'];
         
         if($q->row['admin']){
            $store_id = 'admin';
         }
         
         $query_vars = explode('&', $q->row['query']);
         foreach($query_vars as $var){
            list($remove_query_vars[]) = explode('=', $var);
         }
      }
           
      $query = $this->remove_query_vars($query, $remove_query_vars);
      
      $url = $this->get_base_url($store_id);
      
      if(empty($keyword)){
         return $url . 'index.php?route=' . $route . ($query ? '&' . $query : '');
      }
      else{
         return $url . $keyword . ($query ? '?' . $query : '');
      }
   }
   
   public function remove_query_vars($query, $vars){
      if(!is_array($vars)){
         $vars = array($vars);
      }
      
      foreach($vars as $var){
         $patterns = array(
            "/&" . preg_quote($var) . "=[^&]+/",
            "/^" . preg_quote($var) . "=[^&]+/",
            '/&' . preg_quote($var) . '&/',
            '/\^' . preg_quote($var) . '&/',
         );
         $replacements = array(
            '',
            '',
            '&',
            '',
         );
         
         $query = preg_replace($patterns, $replacements, $query);
      }
      
      return trim($query, '&');
   }
}