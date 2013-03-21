<?php
class Tool {
   private $registry;
   
   public function __construct(&$registry) {
      $this->registry = &$registry;
   }
	
	public function __get($key){
		return $this->registry->get($key);
	}
   
   public function error_set(){
      if(isset($this->session->data['warning']) && $this->session->data['warning']){
         return true;
      }
      
      return false;
   }
   
   public function error_info(){
      list(,,$caller) = debug_backtrace(false);
      return "<span style='font-weight:bold; color:#E72727'>$caller[file] on line $caller[line]: </span>";
   }
   
   public function insertables($insertables, $text, $start = '%', $end = '%'){
      $patterns = array();
      $replacements = array();
      foreach($insertables as $key=>$value){
         $patterns[] = "/$start" . $key . "$end/";
         $replacements[] = $value?$value:'';
      }
      return preg_replace($patterns, $replacements, $text);
   }
   
   public function format_invoice($d){   
      $date_format = array();
      return preg_match("/%.*%/",$d,$date_format)?preg_replace("/%.*%/",date(preg_replace("/%/",'',$date_format[0])), $d):$d;
   }
   
   public function format_datetime($date=null, $format = NULL){
      if(!$format){
         $format = $this->language->getInfo('datetime_format');
      }
      
      if($date){
         return is_object($date) ? $date->format($format) : date_format(date_create($date),$format);
      }
      else{
         return date_format(date_create(), $format);
      }
   }
   
   public function format_date($date = null, $format = ''){
      if(!$format){
         $format = $this->language->getInfo('date_format_short');
      }
      
      if($date){
         return is_object($date) ? $date->format($format) : date_format(date_create($date),$format);
      }
      else{
         return date_format(date_create(), $format);
      }
   }
   
   function sort_by_array($array,$order, $sort_key){
      $new_array = array();
      foreach($order as $o)
         foreach($array as $a)
            if($a[$sort_key] == $o)
               $new_array[] = $a;
      return $new_array;
   }
   
   /**
    * limits the number of characters in a string to the nearest word or character
    */
   public function limit_characters($string, $num, $append = '...', $keep_word = true){
      if($keep_word){
         $words = explode(' ', $string);
         $short = '';
         foreach($words as $word){
            if((strlen($short) + strlen($word)+1) > $num){
               $short .= $append;
               break;
            }
            $short .= empty($short)?$word:' '.$word;
         }
      }
      else{
         if(strlen($string) > $num){
            $short = substr($string, 0, $num) . $append;
         }
         else{
            $short = $string;
         }
      }
      
      return $short;
   }
    
   public function parse_xml_to_array($xml){
      $return = array();
      foreach ($xml->children() as $parent => $child){
         $the_link = false;
         foreach($child->attributes() as $attr=>$value)
            if($attr == 'href')
               $the_link = $value;
            $return["$parent"][] = $this->parse_xml_to_array($child)?$this->parse_xml_to_array($child):($the_link?"$the_link":"$child"); 
      } 
      return $return; 
   }
}