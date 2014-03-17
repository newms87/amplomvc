<?php
class Catalog_Controller_Area_Top extends Controller
{
	public function index()
	{
		$this->view->load('area/top');

		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('top');

		$this->data['blocks'] = array();

		foreach ($blocks as $key => $block) {
			$settings               = $block['settings'] + $block['profile'];
			$this->data['blocks'][] = $this->getBlock($key, array(), $settings);
		}

		$this->render();
	}
}
