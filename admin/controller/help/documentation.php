<?php
class ControllerHelpDocumentation extends Controller {
	
	public function index() {	
		$this->template->load('help/documentation');

		$this->load->language('help/documentation');

		$this->document->setTitle($this->_('heading_title'));
		
		$s = $this->_('sections');
		$this->replace_tokens($s);
		$this->data['sections'] = $s;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('help/documentation'));
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	function replace_tokens(&$section){
		foreach($section as &$s){
			$matches=null;
			if(is_array($s))
				$this->replace_tokens($s);
			else{
				if(preg_match_all("/%@[^%]*%@/",$s,$matches)){
					foreach($matches[0] as $m)
						$s = preg_replace("/%@[^%]*%@/",$this->url->link(preg_replace("/%@/",'',$m)),$s,1);
				}
				if(preg_match_all("/%%[^%]*%%/",$s,$matches)){
					foreach($matches[0] as $m)
						$s = preg_replace("/%%[^%]*%%/","<span class='n'>".preg_replace("/%%/",'',$m)."</span>",$s,1);
				}
				if(preg_match_all("/%![^%]*%!/",$s,$matches)){
					foreach($matches[0] as $m)
						$s = preg_replace("/%![^%]*%!/","<span class='important'>".preg_replace("/%!/",'',$m)."</span>",$s,1);
				}
			} 
		}
	}
}