<?php 
class ControllerToolErrorLog extends Controller { 
	
	
	public function index() {
$this->template->load('tool/error_log');

	   $this->load->language('tool/error_log');

		$this->document->setTitle($this->_('heading_title'));
		
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('tool/error_log'));
      
      $url_query = $this->url->get_query(array('filter_store'));
      
		$this->data['remove'] = $this->url->link('tool/error_log/remove',$url_query,false,false);
		$this->data['clear'] = $this->url->link('tool/error_log/clear', $url_query);
		
      $defaults = array('limit'=>100,'start'=>0);
      foreach($defaults as $key=>$default){
         $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
      }
		
      $filters = array('filter_store'=>'');
      
      foreach($filters as $key=>$default){
         $this->data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
      }
      
      if($filter_store !== ''){
         if($filter_store == 'a'){
            $store_name = 'Admin';
         }
         else{
            $store_name = $this->model_setting_store->getStoreName((int)$filter_store);
         }
      }else{
         $store_name = '';
      }
      
      
      $current = 0;
      
      $file = DIR_LOGS . $this->config->get('config_error_filename');
      $log = array();
      
		if (file_exists($file)) {
		   $handle = @fopen($file, "r");
	    	if($handle){
	      	while (($buffer = fgets($handle, 4096)) !== false && ($current < ($start+$limit))) {
               if($current >= $start){
               	
                  $b = unserialize(str_replace("\0",'',$buffer));
                  if(!is_array($b)){
                     echo 'invalid log entry<br>' . $current;
							$this->remove($current);
                     html_dump($buffer);
                  }
                  else{
                     if($store_name){
                        if($store_name != $b['s']){
                           $current++;
                           continue;
                        }
                     }
                     $log[] = array_merge(array('line'=>$current),$b);
                  }
               }
               $current++;	
            }
            fclose($handle);
         }
		}
      
      $next = $start+$limit;
      $this->data['next'] = $current >= ($start+$limit)? $this->url->link('tool/error_log', $url_query . '&start='.$next.'&limit='.$limit):'';
      
      $prev = $start-$limit > 0 ? $start - $limit:0;
      $this->data['prev'] = $start>0?$this->url->link('tool/error_log', $url_query . '&start='.$prev.'&limit='.$limit):'';
      
      $this->data['limit'] = $limit; 
      $this->data['log'] = $log;
      
      $url_query = $this->url->get_query(array('limit','start'));
		
      $this->data['filter_url'] = $this->url->link('tool/error_log', '', false, false);
      
		$this->data['loading'] = $this->image->get('data/ajax-loader.gif');
      
      $stores = $this->model_setting_store->getStoreNames();
      
      $default_stores = array(
         array('store_id'=>'','name'=>$this->_('text_select')),
         array('store_id'=>'a','name'=>'Admin'),
        );
      $stores = array_merge($default_stores, $stores);
      
      $this->data['stores'] = $stores;
      
      if($filter_store !== ''){
         $name = '';
         foreach($stores as $store){
            if($store['store_id'] === $filter_store){
               $name = $store['name'];
               break;
            }
         }
         
         $this->language->format('button_clear', $name); 
      }
      else{
         $this->language->format('button_clear', 'Log');
      }
      
      
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function remove($lines=null, $get_page=false){
		$get_page = $lines !== null ? $get_page : !isset($_POST['no_page']);
		
		if(!isset($_POST['entries']) && $lines === null){
			$msg = "No entries were selected for removal!";
			if($get_page){
				$this->message->add('warning', $msg);
			}	
			else{
				echo $msg;
			}
		}
		else{
			$entries = ($lines !== null) ? $lines : $_POST['entries'];
			
			if(preg_match("/[^\d\s,-]/", $entries) > 0){
				$msg = "Invalid Entries for removal: $entries. Use either ranges or integer values (eg: 3,40-50,90,100)";
				if($get_page){
					$this->message->add('warning', $msg);
				}
				else{
					echo $msg;
				}
			}
			
         $this->load->language('tool/error_log');
			
			$file = DIR_LOGS . $this->config->get('config_error_filename');
			
			$file_lines = explode("\n", file_get_contents($file));
			
			foreach(explode(',',$entries) as $entry){
				if(strpos($entry,'-')){
					list($from,$to) = explode('-', $entry);
					for($i=(int)$from; $i<=(int)$to; $i++){
						unset($file_lines[$i]);
					}
				}
				else{
					unset($file_lines[(int)$entry]);
				}
			}
			
			file_put_contents($file, implode("\n",$file_lines));
			$msg = $this->_('text_success_remove');
			if($get_page){
				$this->message->add('success', $msg);
			}
			else{
				echo $msg;
			}
		}
		if($get_page){
			$this->index();
		}
	}
	
	public function clear() {
		$this->load->language('tool/error_log');
		
		$file = DIR_LOGS . $this->config->get('config_error_filename');
		
      
      $filters = array('filter_store'=>'');
      
      foreach($filters as $key=>$default){
         $this->data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
      }
      
      if($filter_store !== ''){
         if($filter_store == 'a'){
            $store_name = 'Admin';
         }
         else{
            $store_name = $this->model_setting_store->getStoreName((int)$filter_store);
         }
      }else{
         $store_name = '';
      }
         
      if($store_name){
         $file_lines = explode("\n", file_get_contents($file));
         
         foreach($file_lines as $key=>$line){
            $data = unserialize(str_replace("\0",'',$line));
            
            if($data['s'] == $store_name){
               unset($file_lines[$key]);
            }
         }
         
         file_put_contents($file, implode("\n",$file_lines));
      }
      else{
		   $handle = fopen($file, 'w+');
         fclose($handle);
      }
				
		$this->message->add('success', $this->_('text_success'));
		
		$this->index();
	}
}
