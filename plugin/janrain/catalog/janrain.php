<?php
class ControllerPlugin_JanrainCatalogJanrain extends Controller 
{
	
	public function janrain_header()
	{
		$janrain_settings = array('display_type'=>'popup','icon_size'=>'tiny');
		$this->data['janrain_sign_in'] = $this->getChild('module/janrain',$janrain_settings);
	}
}