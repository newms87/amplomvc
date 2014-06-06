<?php
class App_Controller_Block_Information_Contact extends App_Controller_Block_Block
{
	public function build($settings)
	{
		//Load Value or Defaults
		$defaults = array(
			'name'    => $this->customer->info('firstname'),
			'email'   => $this->customer->info('email'),
			'enquiry' => '',
			'captcha' => '',
		);

		$settings += $defaults;

		//Captcha Image
		$settings['captcha_url'] = site_url("block/information/contact/captcha");

		//Action
		$settings['action'] = site_url('block/information/contact/submit');

		//Set Error Redirect
		$this->request->setRedirect($this->url->here(), null, 'contact-form');

		//Render
		$this->render('block/information/contact', $settings);
	}

	public function submit()
	{
		if (!validate('text', $_POST['name'], 3, 64)) {
			$this->message->add('error', _l("Name must be between 3 and 32 characters!"));
		}

		if (!validate('email', $_POST['email'])) {
			$this->message->add('error', _l("E-Mail Address does not appear to be valid!"));
		}

		if (!validate('text', $_POST['enquiry'], 10, 3000)) {
			$this->message->add('error', _l("Enquiry must be between 10 and 3000 characters!"));
		}

		if (!$this->captcha->validate($_POST['captcha'])) {
			$this->message->add('error', _l("Verification code does not match the image!"));
		}

		if (!$this->message->has('error')) {
			call('mail/contact', $_POST);
			$this->message->add('success', _l("We have received your message! We will be in contact with you shortly."));
		}

		//Response
		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			if ($this->message->has('error')) {
				redirect($this->request->getRedirect('contact-form'));
			} else {
				redirect();
			}
		}
	}

	public function captcha()
	{
		$this->captcha->generate();
	}

	public function settings(&$settings)
	{
		$defaults = array(
			'contact_info' => _l("Please feel free to contact us with any questions!"),
		);

		$settings += $defaults;

		//Send data to template
		$data['settings'] = $settings;

		//Render
		$this->render('block/information/contact_settings', $data);
	}
}
