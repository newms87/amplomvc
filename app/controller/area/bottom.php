<?php

class App_Controller_Area_Bottom extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$instances = $this->area->getBlocks('bottom');

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = block($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/bottom', $data);
	}
}
