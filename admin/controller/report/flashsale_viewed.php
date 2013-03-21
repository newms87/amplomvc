<?php
class ControllerReportFlashsaleViewed extends Controller {
	public function index() {
$this->template->load('report/flashsale_viewed');

	   $this->load->language('report/flashsale_viewed');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = $this->get_url();
	   
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('report/flashsale_viewed'));
      					
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
      $flashsale_view_list = $this->model_report_flashsale->getFlashsaleViews();
      
      $flashsale_views = array();
      foreach($flashsale_view_list as $fv){
         if(isset($flashsale_views[$fv['flashsale_id']])){
            $id = &$flashsale_views[$fv['flashsale_id']];
            $unique = false;
            if(($fv['user_id'] == 0 || !in_array($fv['user_id'],$id['users'])) && !in_array($fv['session_id'],$id['sessions'])){
               if($fv['user_id'] != 0)
                  $id['users'][] = $fv['user_id'];
               $id['sessions'][] = $fv['session_id'];
               $id['user_total'] += 1;
               $unique = true;
            }
            if(!in_array($fv['ip_address'],$id['ip_addr'])){
               $id['ip_addr'][] = $fv['ip_address'];
               $id['ip_total'] += 1;
               if($unique)
                  $id['ip_user_total'] += 1;
            }
         }
         else{
            $flashsale_views[$fv['flashsale_id']] = array('user_total'=>1,'users'=>array($fv['user_id']),'sessions'=>array($fv['session_id']),
                                                      'ip_total'=>1,'ip_addr'=>array($fv['ip_address']),
                                                      'ip_user_total'=>1
                                                      );
         } 
      }
      
		$flashsale_viewed_total = $this->model_report_flashsale->getTotalFlashsalesViewed($data); 
		
		$flashsale_views_total = $this->model_report_flashsale->getTotalFlashsaleViews(); 
		
		$this->data['flashsales'] = array();
		
		$results = $this->model_report_flashsale->getFlashsalesViewed($data);
		
		foreach ($results as $result) {
			if ($result['views']) {
				$percent = round($result['views'] / $flashsale_views_total * 100, 2);
			} else {
				$percent = 0;
			}
		   
			$this->data['flashsales'][] = array(
				'name'    => $result['name'],
				'date_start'   => $result['date_start'],
				'date_end'   => $result['date_end'],
				'viewed'  => $result['views'],
				'ip_total'=> $flashsale_views[$result['flashsale_id']]['ip_total'],
				'user_total'=> $flashsale_views[$result['flashsale_id']]['user_total'],
				'ip_user_total'=> $flashsale_views[$result['flashsale_id']]['ip_user_total'],
				'percent' => $percent . '%'			
			);
		}
 		
		$url = $this->get_url();
				
		$this->data['reset'] = $this->url->link('report/flashsale_viewed/reset', $url);
						
		$this->pagination->init();
		$this->pagination->total = $flashsale_viewed_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('report/flashsale_viewed', 'page={page}');
			
		$this->data['pagination'] = $this->pagination->render();
				 
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function reset() {
		$this->load->language('report/flashsale_viewed');
		
		$this->model_report_flashsale->reset();
		
		$this->message->add('success', $this->_('text_success'));
		
		$this->redirect($this->url->link('report/flashsale_viewed'));
	}
   
   private function get_url($filters=null){
      $url = '';
      $filters = $filters?$filters:array('page');
      foreach($filters as $f)
         if (isset($_GET[$f]))
            $url .= "&$f=" . $_GET[$f];
      return $url;
   }
}