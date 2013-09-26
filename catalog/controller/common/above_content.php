<?php
class Catalog_Controller_Common_AboveContent extends Controller
{
	public function index()
	{
		$this->template->load('common/above_content');

		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('above_content');

		$this->data['blocks'] = array();

		foreach ($blocks as $key => $block) {
			$settings               = $block['settings'] + $block['profile'];
			$this->data['blocks'][] = $this->getBlock($key, array(), $settings);
		}

		$this->render();
	}
}
