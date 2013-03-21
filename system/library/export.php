<?php
class Export {
   private $registry;
   private $contents = '';
   
   function __construct(&$registry){
      $this->registry = &$registry;
   }
   
   public function __get($key){
      return $this->registry->get($key);
   }
   
   public function get_contents(){
      return $this->contents;
   }
   
   public function save_contents($file){
      if(!is_dir(dirname($file))){
         $mode = octdec($this->config->get('config_default_dir_mode'));
         mkdir($path, dirname($file),true);
         chmod($path, dirname($file));
      }
      
      file_put_contents($file, $this->contents);
   }
   
   public function download_contents_as($type = 'csv', $file){
      switch ($type) {
        case 'csv':
           $headers = array(
              "Content-type: text/csv",
              "Content-Disposition: attachment; filename=$file",
              "Pragma: no-cache",
              "Expires: 0",
           );
           break;
        case 'xls':
           break;
        case 'xlsx':
           break;
        
        default:
           trigger_error("Error in Export::download_contents_as(): Unknown content type: $type!");
           break;
      }
      
      $this->response->setHeader($headers);
      
      $this->response->setOutput($this->contents);
   }
   
   public function generate_csv($columns, $data, $row_headings = true){
      $num_cols = count($columns);
      
      if($row_headings){
         $index = 0;
         foreach($columns as $col){
            $this->contents .= '"' . $col . '"' . ($index++ < $num_cols ? ',':'');
         }
         
         $this->contents .= "\r\n";
      }
      
      foreach($data as $d){
         $index = 0;
         foreach(array_keys($columns) as $key){
            $value = isset($d[$key]) ? $d[$key] : '';
            
            $this->contents .= '"' . $value . '"' . ($index++ < $num_cols ? ',':'');
         }
         
         $this->contents .= "\r\n";
      }
   }
}