<?php
class Catalog_Controller_Common_Footer extends Controller 
{
	protected function index()
	{
		$this->template->load('common/footer');

		$this->language->load('common/footer');
		
		$this->data['links_footer'] = $this->document->getLinks('footer');
		
		if (!$this->data['links_footer']) {
			//All the informational links
			foreach ($this->Model_Catalog_Information->getInformations() as $result) {
				$link_info = array(
					'name' => 'information_' . $result['information_id'],
					'display' => $result['title'],
					'href' => $this->url->link('information/information', 'information_id=' . $result['information_id']),
				);
				
				$this->document->addLink('footer', $link_info);
			}
			
			//Contact Form Link
			$link_contact = array(
				'name' => 'contact',
				'display' => $this->_('text_contact'),
				'href' => $this->url->link('information/contact'),
			);
			
			$this->document->addLink('footer', $link_contact);
			
			//Newsletter Link
			$link_newsletter = array(
				'name' => 'newsletter',
				'display' => $this->_('text_newsletter'),
				'href' => $this->url->link('account/newsletter'),
			);
			
			$this->document->addLink('footer', $link_newsletter);
			
			//Admin Portal link
			$link_admin = array(
				'name' => 'admin_portal',
				'display' => $this->_('text_portal'),
				'href' => $this->url->admin(),
			);
			
			$this->document->addLink('footer', $link_admin);
			
			$this->data['links_footer'] = $this->document->getLinks('footer');
		}
		
		$this->data['social_networks'] = $this->getBlock('extras', 'social_media');
		
		$this->render();
	}
}
