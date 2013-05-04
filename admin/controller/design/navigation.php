<?php 
class ControllerDesignNavigation extends Controller {
	
	public function display_links($links, $parent = '', $depth = 2){
		$tab1 = str_repeat("\t", $depth);
		$tab = str_repeat("\t", $depth+1);
		$html = '';
		
		foreach($links as $key => $link){
			$link['name'] = $parent . $link['name'];
			
			$html .= $tab1 . "'$link[name]' => array(\r\n";
			$html .= $tab . "'display_name'\t=> '" . $link['display_name'] . "',\r\n";
			$html .= $tab . "'name'\t\t\t=> '" . $link['name'] . "',\r\n";
			$html .= $tab . "'title'\t\t\t=> '',\r\n";
			$html .= $tab . "'href'\t\t\t=> '" . $link['href'] . "',\r\n";
			$html .= $tab . "'query'\t\t\t=> '" . $link['query'] . "',\r\n";
			$html .= $tab . "'is_route'\t\t=> " . $link['is_route'] . ",\r\n";
			$html .= $tab . "'parent_id'\t\t=> '" . $parent . "',\r\n";
			$html .= $tab . "'sort_order'\t=> " . $link['sort_order'] . ",\r\n";
			$html .= $tab . "'status'\t\t\t=> " . $link['status'] . ",\r\n";
			
			$html .= $tab1 . "),\r\n\r\n";
			
			if(!empty($link['children'])){
				$html .= $this->display_links($link['children'], $link['name'], $depth+1);
			}
		}
		
		return $html;
	}
	
	public function index() {
		$links = $this->model_design_navigation->getNavigationLinks();
		html_dump($links['admin'],'links');
		$html = $this->display_links($links['admin']);
		
		file_put_contents(SITE_DIR . 'admin_text.txt', $html);
		 
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_navigation->addNavigationGroup($_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('design/navigation'));
		}
		else{
			$this->getForm();
		}
	}

	public function update() {
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_navigation->editNavigationGroup($_GET['navigation_group_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('design/navigation'));
		}
		else{
			$this->getForm();
		}
	}
 
	public function delete() {
		$this->load->language('design/navigation');
 
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $navigation_group_id) {
				$this->model_design_navigation->deleteNavigationGroup($navigation_group_id);
			}
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('design/navigation'));
		}

		$this->getList();
	}
	
	public function reset_admin_navigation(){
		$this->model_design_navigation->reset_admin_navigation_group();
		
		$this->message->add("notify", "Admin Navigation Group has been reset!");
		
		$this->redirect($this->url->link("design/navigation"));
	}
	
	public function batch_update() {
      $this->load->language('design/navigation');

      $this->document->setTitle($this->_('heading_title'));
      
      if (isset($_POST['selected']) && isset($_GET['action'])) {
         foreach ($_POST['selected'] as $navigation_group_id) {
            switch($_GET['action']){
               case 'enable':
                  $this->model_design_navigation->editNavigationGroup($navigation_group_id, array('status' => 1));
                  break;
               case 'disable':
                  $this->model_design_navigation->editNavigationGroup($navigation_group_id, array('status' => 0));
                  break;
            }
            if($this->error)
               break;
         }
			
         if(!$this->error){
            if(!$this->message->error_set()){
               $this->message->add('success',$this->_('text_success'));
					
					$this->redirect($this->url->link('design/navigation'));
            }
         }
      }

      $this->getList();
   }

	private function getList() {
      $this->language->load('design/navigation');
      
      $this->template->load('design/navigation_list');

      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/navigation'));
      
		//This table column data is in admin/view/template_option/[template]/design/navigation_list.to
      $table = $this->template->get_table('listview');
      
		$this->data['data_stores'] = array('admin' => array('store_id' => '-', 'name' => $this->_('text_admin_panel'))) + $this->model_setting_store->getStores();
		
		//Add table filter data		
		$table->set_column_filter('store_ids', 'select', array(''=>'') + $this->data['data_stores'], array('store_id' => 'name'));
		$table->set_column_filter('status', 'select', $this->_('data_statuses_blank'));
		
		
		//Add Table Cell Data array
		$table->set_column_cell_data('store_ids', 'assoc_array', $this->data['data_stores'], array('store_id' => 'name','store_id'));
		$table->set_column_cell_data('status', 'map', $this->_('data_statuses'));
		
      $table->set_template('table/sort_filter_list');
      
		//Add Sorting / Paging information to the table and the $data query
      $data = array(
         'sort'  =>'name',
         'order' =>'ASC',
         'page'  =>1
      );
      
      foreach($data as $key => $default){
         if(isset($_GET[$key])){
            $data[$key] = $_GET[$key];
         }
      }
      
      $table->add_extra_data($data);
      
		//Set the filter value if set by user
      if(isset($_GET['filter'])){
      	$data += $_GET['filter'];
			
         foreach($_GET['filter'] as $filter => $value){
            $table->set_column_filter_value($filter, $value);
         }
      }
      
		//Filter Query is for saving the previous filter and adding to the URL query string
      $queries = array(
         'filter_query' => $this->url->get_query('filter'),
      ); 
      
      $table->add_extra_data($queries);
      
		//finish adding $data query information
      $data['limit'] = $this->config->get('config_admin_limit');
      $data['start'] = ($data['page'] - 1) * $data['limit'];
      
		
		//Retrieve the Filtered Table row data
      $nav_group_total = $this->model_design_navigation->getTotalNavigationGroups($data);
      
      $results = $this->model_design_navigation->getNavigationGroups($data);
      
      $navigation_groups = array();
      
      foreach ($results as $result) {
         $action = array(
            'text' => $this->_('text_edit'),
            'href' => $this->url->link('design/navigation/update', 'navigation_group_id=' . $result['navigation_group_id'])
         );
         
         $result['action'] = $action;
         
         $navigation_groups[] = $result;
      }
      
		//This sets the table row data
      $table->set_table_data($navigation_groups);
      
      $this->data['navigation_group_view'] = $table->build();
      
      
      $url = $this->url->get_query('filter', 'sort', 'order', 'page');
      
		//Batch Actions
      $this->data['batch_actions'] = array('enable'=>'Enable','disable'=>'Disable');
		$this->data['batch_action_values'] = array();
      $this->data['batch_action_go'] = $this->url->link('design/navigation/batch_update', $url);
      
      $this->data['insert'] = $this->url->link('design/navigation/insert', $url);
      $this->data['copy'] = $this->url->link('design/navigation/copy', $url);
      $this->data['delete'] = $this->url->link('design/navigation/delete', $url);
      
      $url = $this->url->get_query('filter', 'sort', 'order');
      
      $this->pagination->init();
      $this->pagination->total = $nav_group_total;
      $this->pagination->page = $data['page'];
      $this->pagination->limit = $this->config->get('config_admin_limit');
      $this->pagination->text = $this->_('text_pagination');
      $this->pagination->url = $this->url->link('design/navigation', $url . '&page={page}');
         
      $this->data['pagination'] = $this->pagination->render();
      
      $this->children = array(
         'common/header',
         'common/footer'
      );
      
      $this->response->setOutput($this->render());
   }

	private function getForm() {
		$this->language->load('design/navigation');
		
		$this->template->load('design/navigation_form');

	   $navigation_group_id = isset($_GET['navigation_group_id']) ? $_GET['navigation_group_id'] : null;
      
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/navigation'));
      
		if (!$navigation_group_id) {
			$this->data['action'] = $this->url->link('design/navigation/insert');
		} else {
			$this->data['action'] = $this->url->link('design/navigation/update', 'navigation_group_id=' . $navigation_group_id);
		}
		
		$this->data['cancel'] = $this->url->link('design/navigation');

		if ($navigation_group_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
      	$navigation_group_info = $this->model_design_navigation->getNavigationGroup($navigation_group_id);
    	}
		
      //initialize the values in order of Post, Database, Default
      $defaults = array(
         'name' => '',
         'links' => array(),
         'store_ids' => array(0),
         'status' => 1,
      );

      foreach($defaults as $key => $default){
         if (isset($_POST[$key])) {
            $this->data[$key] = $_POST[$key];
         } elseif (isset($navigation_group_info[$key])) {
            $this->data[$key] = $navigation_group_info[$key];
         } elseif(!$navigation_group_id) {
            $this->data[$key] = $default;
         }
      }
		
		$admin_store = array('admin' => array('store_id' => -1, 'name' => $this->_('text_admin_panel')));
		
      $this->data['data_stores'] = $admin_store + $this->model_setting_store->getStores();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/navigation')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$navigation_group_id = isset($_GET['navigation_group_id']) ? (int)$_GET['navigation_group_id'] : 0;
		
		if(!isset($_POST['store_ids'])){
			$_POST['store_ids'] = array('');
		}
		
		if(!$this->validation->text($_POST['name'], 3, 64)){
			$this->error['name'] = $this->_('error_name');
		}
		
		//unset the fake link
		unset($_POST['links']['%link_num%']);
		
		foreach($_POST['links'] as $key => $link){
			if(!$this->validation->text($link['display_name'], 1, 255)){
				$link_name = !empty($link['name']) ? $link['name'] : ( !empty($link['display_name']) ? $link['display_name'] : $key ); 
				$this->error["links[$key][display_name]"] = $this->language->format('error_display_name', $link_name);
			}
		
			//If name already exists in database, append _n to the name
			if(empty($link['name'])){
				$name = $this->tool->get_slug($link['display_name']);
			}
			else{
				$name = $this->db->escape($this->tool->get_slug($link['name']));
			}
			
			$count = 0;
			do{
				$check_name = $count ? $name . '_' . $count : $name;
				
				$result = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "navigation_group WHERE name = '$check_name' AND navigation_group_id != $navigation_group_id");
				
				$count++;
			}while($result->row['total']);
			
			$_POST['links'][$key]['name'] = $check_name;
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/navigation')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}
