<?php
class Catalog_Controller_Common_Footer extends Controller
{
	public function index()
	{
		$this->template->load('common/footer');
		$this->data['links_footer'] = $this->document->getLinks('footer');

		$this->data['social_networks'] = $this->getBlock('extras/social_media');

		$this->render();
	}
}
