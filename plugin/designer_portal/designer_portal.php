<?php
class _DesignerPortal extends Controller 
{
	
	public function are_you_designer_link()
	{
		$this->language->plugin('designer_portal', 'common/header');
		
		$this->document->addLink('secondary', 'are_you_designer', $this->_('text_are_you_designer'), $this->url->site('are-you-a-designer'), 5);
		
		$this->data['links_secondary'] = $this->document->getLinks('secondary');
	}
}
			