<?php  
class ControllerModuleSearchBar extends Controller {
	protected function index($setting) {
		$this->template->load('module/search_bar');
		
		$this->language->load('module/search_bar');
		
		$this->data['search_category'] = "";
		$this->data['search_country'] = "";
		$this->data['search_color'] = '';
		$this->data['search_style'] = '';
		$post = $_POST;
		if(isset($post['action']) && $post['action'] == 'betty_search'){
			if(isset($post['search_general'])){
				$this->data['search_general'] = $post['search_general'] == 'SEARCH HERE' ? '':$post['search_general'];
			}
			else{
				$this->data['search_category'] = $post['search_category'];
				$this->data['search_country'] = $post['search_country'];
				$this->data['search_color'] = $post['search_color'];
				$this->data['search_style'] = $post['search_style'];
			}
		}
		$this->data['categories'] = array("0"=>array("display_name"=>"CATEGORY"));
		$categories = $this->model_catalog_category->getAllCategories(false);
		foreach($categories as $cat){
			$this->data['categories'][$cat['category_id']] = array("display_name"=>$cat['name'], "item_class"=> "parent");
			foreach($cat['children'] as $child)
				$$this->data['categories'][$child['category_id']] = array("display_name"=>$child['name'], "item_class"=> "child");
		}
		
		//Color Attribute list
		$this->data['colors'] = array(0=>"COLOR") + $this->model_catalog_product->getOptionList(13);
		
		//Country Attribute list
		$this->data['countries'] = array(0=>"COUNTRY") + $this->model_catalog_product->getAttributeList(8);
		
		//Style Attribute list
		$this->data['styles'] = array(0=>"STYLE") + $this->model_catalog_product->getAttributeList(11);
		
		$this->data['results_url'] = $this->url->link('product/search_results');
		
		$this->render();
  	}
}