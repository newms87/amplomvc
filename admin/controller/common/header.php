<?php 
class ControllerCommonHeader extends Controller {
	protected function index() {
		if($this->config->get('config_debug') && !empty($_SESSION['debug'])){
        $this->message->add('warning', html_dump($_SESSION['debug'], 'Session Debug', 0, -1, false));
        unset($_SESSION['debug']);  
      }
		
      if($this->user->isDesigner()){
         $this->template->load('common/header_restricted');
      }

	   $this->data['title'] = $this->document->getTitle();
      
		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}
		
      $this->data['messages'] = $this->message->fetch();
      
      $this->data['direction'] = $this->language->getInfo('direction');
      
  		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
      
		$this->data['canonical_link'] = $this->document->getCanonicalLink();	
      
		$this->language->set('lang', $this->language->getInfo('code'));
      if($this->config->get('config_seo_url')){
         $this->data['pretty_url'] = $this->url->get_pretty_url();
      } 
		
		$this->load->language('common/header');
           
      $this->data['admin_logo'] = $this->image->get($this->config->get('config_admin_logo'));
         
      
		if (!$this->user->isLogged()) {
			$this->data['logged'] = '';
			
			$this->data['home'] = $this->url->link('common/login');
		} else {
			$this->data['home'] = $this->url->link('common/home');
			
			$this->data['logged'] = $this->language->format('text_logged', $this->user->getUserName());
	      
         $menu_items = array();
         if($this->user->isDesigner()){
            $this->language->format('support',"mailto:" . $this->config->get('config_email'));
            $menu_items = array('product'=>'catalog/product','product_insert'=>'catalog/product/insert',
                                'home'=>'common/home',
                                'designers'=>'catalog/manufacturer',
                                'logout'=>'common/logout');
            $this->data['user_info'] = $this->url->link('user/user/update','user_id='.$this->user->getId());
         }
         else{
            $this->language->format('support', $this->config->get('config_email_support'));
            
            $this->data['store'] = HTTP_CATALOG;
				
				//Add the Image Manager to the Main Menu if user has permissions
				if($this->user->hasPermission('access','common/elmanager')){
					$link_image_manager = array(
						'name' => $this->_('text_image_manager'),
						'sort_order' => 3,
						'attrs' => array('onclick' => 'dqis_image_manager();'),
					);
					
					$this->document->addLink('admin', $link_image_manager);
					
					$this->document->addScript('image_manager.js');
				}
			}
			
			$this->data['links_admin'] = $this->document->getLinks('admin');
			
			//The Right Side Navigation Menu
			
			//Stores title
			$link_stores = array(
				'name' => 'stores',
				'display' => $this->_('text_stores'),
			);
			
			$this->document->addLink('right', $link_stores);
			
			//Link to all of the stores under the stores top level navigation
			$stores = $this->model_setting_store->getStores();
			foreach($stores as $store){
				$link_store = array(
					'name' => 'store_' . $store['store_id'],
					'display' => $store['name'],
					'href' => $this->url->link('common/home', '', $store['store_id']),
					'parent' => 'stores',
					'attrs' => array('target'=>'_blank')
				);
				
				$this->document->addLink('right', $link_store);
			}
			
			//Logout link
			$link_logout = array(
				'name' => 'logout',
				'display' => $this->_('text_logout'),
				'href' => $this->url->link('common/logout'),
			);
			
			$this->document->addLink('right', $link_logout);
			
			$this->data['links_right'] = $this->document->getLinks('right');
		}
		
		
		$this->data['css_styles'] = $this->document->getStyles();
		$this->data['js_scripts'] = $this->document->getScripts();
		
		$this->render();
	}
}
