<?php

class App_Controller_Area extends Controller
{
	public function index($area)
	{
		//Load Blocks associated with this position
		$instances = $this->area->getBlocks($area);

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = block($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/' . $area, $data);
	}

	public function above()
	{
		$this->index('above');
	}

	public function below()
	{
		$this->index('below');
	}

	public function top()
	{
		$this->index('top');
	}

	public function bottom()
	{
		$this->index('bottom');
	}

	public function left()
	{
		$this->index('left');
	}

	public function right()
	{
		$this->index('right');
	}
}
