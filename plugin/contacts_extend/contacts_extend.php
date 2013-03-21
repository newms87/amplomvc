<?php   
class ModelPluginContactsExtend extends Model {
   
   public function extend_lookbook(&$data){
      $data['lookbook'] = isset($data['lookbook'])?$data['lookbook']:'';
   }
   
   public function add_designer_categories($data){
      if(!empty($data['category'])){
      	if(!is_array($data['category'])){
      		trigger_error("In Contacts Extend Plugin, the category was not an array!");
			}
         foreach($data['category'] as $type=>$cat){
            if(strpos($type,'other') === 0){
               $values = array(
                  'designer_id'   => $data['last_insert_id'],
                  'category_id'   => 0,
                  'category_name' => $cat
                );
            }
            else{
               $values = array(
                  'designer_id'   => $data['last_insert_id'],
                  'category_id'   => $cat,
                  'category_name' => ''
                );
            }
            
            $this->insert('designer_category', $values);
         }
      }
   }
}