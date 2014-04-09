<?php
class Catalog_Controller_Common_Footer extends Controller
{
	public function index()
	{
		$data = array(
			'links_footer' => $this->document->getLinks('footer'),
		);

		$data['social_networks'] = $this->block->render('extras/social_media');

		//Dependencies
		$this->children = array(
			'area/below',
		);

		//Render
		$this->render('common/footer', $data);
	}
}
