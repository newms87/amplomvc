<?php
class Catalog_Controller_Area_Bottom extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$instances = $this->block->getAreaInstances('bottom');

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = $this->block->render($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/bottom', $data);
	}
}
