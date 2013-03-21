<?php
class Breadcrumb {
   private $crumbs = array();
   private $default_separator;
   
   function __construct($registry = ''){
      if(is_string($registry)){
         $this->default_separator = $separator;
      }
      else{
         $this->default_separator = $registry->get('config')->get('config_breadcrumb_separator');
      }
   }
   
   public function add($text, $href, $separator = ''){
      if(!empty($this->crumbs)){
         if(!$separator){
            $separator = $this->default_separator;
         }
      }
      else{
         $separator = '';
      }
      
      $this->crumbs[] = array(
         'text' => $text,
         'href' => $href,
         'separator' => $separator
        );
   }
   
   public function get(){
      return $this->crumbs;
   }
   
   public function get_prev_url(){
      if(count($this->crumbs) > 1){
         return $this->crumbs[count($this->crumbs)-2]['href'];
      }
      
      return false;
   }
   
   public function set_separator($separator){
      $this->default_separator = $separator;
   }
}