<?php
class Catalog_Controller_Common_ContentTop extends Controller
{
	public function index()
	{
		$this->template->load('common/content_top');

		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('content_top');

		$this->data['blocks'] = array();

		foreach ($blocks as $key => $block) {
			$settings = $block['settings'] + $block['profile'];
			$this->data['blocks'][] = $this->getBlock($key, array(), $settings);
		}

		$this->render();
	}
}
