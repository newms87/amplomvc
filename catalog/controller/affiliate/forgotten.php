<?php
class Catalog_Controller_Affiliate_Forgotten extends Controller 
{
	

	public function index()
	{
		$this->template->load('affiliate/forgotten');

		if ($this->affiliate->isLogged()) {
			$this->url->redirect($this->url->link('affiliate/account'));
		}

		$this->language->load('affiliate/forgotten');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->language->load('mail/forgotten');
			
			$password = substr(md5(rand()), 0, 7);
			
			$this->Model_Affiliate_Affiliate->editPassword($_POST['email'], $password);
			
			$subject = sprintf($this->_('text_subject'), $this->config->get('config_name'));
			
			$message  = sprintf($this->_('text_greeting'), $this->config->get('config_name')) . "\n\n";
			$message .= $this->_('text_password') . "\n\n";
			$message .= $password;

			$this->mail->init();

			$this->mail->setTo($_POST['email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($this->config->get('config_name'));
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$this->mail->send();
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('affiliate/login'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_forgotten'), $this->url->link('affiliate/forgotten'));

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['action'] = $this->url->link('affiliate/forgotten');
 
		$this->data['back'] = $this->url->link('affiliate/login');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
						
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!isset($_POST['email'])) {
			$this->error['warning'] = $this->_('error_email');
		} elseif (!$this->Model_Affiliate_Affiliate->getTotalAffiliatesByEmail($_POST['email'])) {
			$this->error['warning'] = $this->_('error_email');
		}

		return $this->error ? false : true;
	}
}