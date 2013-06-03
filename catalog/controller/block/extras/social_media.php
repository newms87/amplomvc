<?php
class Catalog_Controller_Block_Extras_SocialMedia extends Controller 
{
	public function index($settings, $type = 'standard')
	{
		switch($type){
			case 'standard':
				$this->template->load('block/extras/my_networks');
				break;
			default:
				$this->template->load('block/extras/my_networks');
				break;
		}
		
		if (isset($settings['networks'])) {
			//TODO: MOVE the size settings to the block profile
			if (!isset($settings['width'])) {
				$settings['width'] = $settings['height'] = 25;
			}
			
			foreach ($settings['networks'] as &$network) {
				$network['thumb'] = $this->image->resize($network['icon'], $settings['width'], $settings['height']);
			}
			
			$this->data['networks'] = $settings['networks'];
		}
		else {
			return ; // we return without rendering because there is nothing to output.
		}
		
		$this->render();
	}
}