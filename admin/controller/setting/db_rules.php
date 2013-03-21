<?php
class ControllerSettingDbRules extends Controller {
	 

	public function index() {
		$this->load->language('setting/db_rules');

		$this->document->setTitle($this->_('heading_title'));
		 
		$this->getList();
	}
	      
  	public function insert() {
    	$this->load->language('setting/db_rules');

    	$this->document->setTitle($this->_('heading_title')); 
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$store_id = $this->model_setting_db_rules->addDbRule($_POST);
	  		
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/db_rules'));
    	}
	
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('setting/db_rules');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_db_rules->editDbRule($_GET['db_rule_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/db_rules', 'store_id=' . $_GET['store_id']));
		}

    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('setting/db_rules');

    	$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $db_rule_id) {
				$this->model_setting_db_rules->deleteDbRule($db_rule_id);
			}

			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('setting/db_rules'));
		}

    	$this->getList();
  	}
	
	private function getList() {
$this->template->load('setting/db_rules_list');

	   $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/db_rules'));
      
		$this->data['insert'] = $this->url->link('setting/db_rules/insert');
		$this->data['delete'] = $this->url->link('setting/db_rules/delete');	
      
      $url = $this->get_url(array('page'));
      
		$db_rules = $this->model_setting_db_rules->getDbRules();
 
    	foreach ($db_rules as &$db_rule) {
			$action = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('setting/db_rules/update', 'db_rule_id=' . $db_rule['db_rule_id'])
			);
         
         $db_rule['selected'] = isset($_POST['selected']) && in_array($result['db_rule_id'], $_POST['selected']);
			$db_rule['action']   = $action;
		}
      
      $this->data['db_rules'] = $db_rules;
	
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	 
	public function getForm() {
$this->template->load('setting/db_rules_form');

	   $db_rule_id = isset($_GET['db_rule_id']) ? $_GET['db_rule_id']:null;
      
	   $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/db_rules'));
      
		if (!$db_rule_id) {
			$this->data['action'] = $this->url->link('setting/db_rules/insert');
		} else {
			$this->data['action'] = $this->url->link('setting/db_rules/update', 'db_rule_id=' . $db_rule_id);
		}
				
		$this->data['cancel'] = $this->url->link('setting/db_rules');
      
      $db_rule_info = $db_rule_id ? $this->model_setting_db_rules->getDbRule($db_rule_id) : null;
      
      $defaults = array(
         'table'       =>'',
         'column'      =>'',
         'escape_type' =>'',
         'truncate'    =>''
        );
           
      foreach($defaults as $d=>$value){
         if (isset($_POST[$d])) {
            $this->data[$d] = $_POST[$d];
         } elseif (isset($db_rule_info[$d])) {
            $this->data[$d] = $db_rule_info[$d];
         } elseif(!$db_rule_id) {
            $this->data[$d] = $value;
         }
      }

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/db_rules')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
      $required = array( 
         'table',
         'column',
        );
      foreach($required as $r){
         if(!$_POST[$r]){
            $this->error[$r] = $this->_('error_' . $r);
         }
      }
            
		return empty($this->error);
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/db_rules')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
      return empty($this->error);
	}
	
   private function get_url($filters=null){
      $url = '';
      $filters = $filters?$filters:array('sort', 'order', 'page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
}