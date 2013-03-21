<?php
class ControllerModuleDnCarousel extends Controller {
	protected function index($setting) {
	   $this->template->load('module/dn_carousel');
      
		$this->language->load('module/dn_carousel'); 

		if (empty($setting['limit']))
			$setting['limit'] = 9;
		
		//Load all the article Information (MAYBE ONLY NEED URL!?)
		//$hp_mods = $this->config->get('dn_carousel_module');
		foreach($hp_mods as &$mod){
			foreach($mod['data'] as &$mod_data){
				//$mod_data['article'] = $this->model_cms_article->getArticle($mod_data['article_id']);
			}
		}
		
		$this->render();
	}
}