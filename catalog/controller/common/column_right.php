<?php
class Catalog_Controller_Common_ColumnRight extends Controller 
{
	public function index()
	{
		$this->template->load('common/column_right');
		
		//Load Blocks associated with this position
		$blocks = $this->Model_Block_Block->getBlocksForPosition('column_right');
		
		$this->data['blocks'] = array();
		
		foreach ($blocks as $key => $block) {
			$this->data['blocks'][] = $this->getBlock($key);
		}
		
		$this->render();
	}
}