<?php
class Catalog_Controller_Area_Above extends Controller
{
	public function index()
	{
		$this->view->load('area/above');

		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('above');

		$this->data['blocks'] = array();

		foreach ($blocks as $key => $block) {
			$settings               = $block['settings'] + $block['profile'];
			$this->data['blocks'][] = $this->getBlock($key, array(), $settings);
		}

		$this->render();
	}
}
