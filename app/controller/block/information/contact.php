<?php
class App_Controller_Block_Information_Contact extends App_Controller_Block_Block
{
	public function build($settings)
	{
		if ($this->request->isPost() && $this->validate()) {
			call('mail/contact', $_POST);

			$this->success();

			return;
		}

		$data = $settings;

		//The Contact Form
		$contact_form = $this->getForm();

		$contact_info = html_entity_decode($settings['contact_info'], ENT_QUOTES, 'UTF-8');

		$insertables = array(
			'contact_form' => $contact_form,
		);

		$data['contact_info'] = $this->tool->insertables($insertables, $contact_info);

		$this->render('block/information/contact', $data);
	}

	private function getForm()
	{
		//Captcha Image
		$data['captcha_url'] = site_url("block/information/contact/captcha");

		//Action
		$data['action'] = $this->url->here();

		//Load Value or Defaults
		$defaults = array(
			'name'    => $this->customer->info('firstname'),
			'email'   => $this->customer->info('email'),
			'enquiry' => '',
			'captcha' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Render
		return $this->render('block/information/contact_form', $data);
	}

	public function success()
	{
		$data['continue'] = site_url('common/home');

		$this->render('block/information/contact_success', $data);
	}

	private function validate()
	{
		if (!validate('text', $_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Name must be between 3 and 32 characters!");
		}

		if (!validate('email', $_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!validate('text', $_POST['enquiry'], 10, 3000)) {
			$this->error['enquiry'] = _l("Enquiry must be between 10 and 3000 characters!");
		}

		if (!$this->captcha->validate($_POST['captcha'])) {
			$this->error['captcha'] = _l("Verification code does not match the image!");
		}

		return empty($this->error);
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

		foreach ($defaults as $key => $default) {
			if (!isset($settings[$key])) {
				$settings[$key] = $default;
			}
		}

		//Send data to template
		$data['settings'] = $settings;

		//Render
		$this->render('block/information/contact_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
