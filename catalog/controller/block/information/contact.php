<?php
class Catalog_Controller_Block_Information_Contact extends Controller
{
	public function index($settings)
	{
		if ($this->request->isPost() && $this->validate()) {
			$this->mail->sendTemplate('contact', $_POST);

			$this->success();

			return;
		}

		$this->data = $settings;

		//The Contact Form
		$contact_form = $this->getForm();

		//The Block template
		$this->view->load('block/information/contact');

		$contact_info = html_entity_decode($settings['contact_info'], ENT_QUOTES, 'UTF-8');

		$insertables = array(
			'contact_form' => $contact_form,
		);

		$this->data['contact_info'] = $this->tool->insertables($insertables, $contact_info);

		$this->render();
	}

	private function getForm()
	{
		//Template and Language
		$this->view->load('block/information/contact_form');

		//Captcha Image
		$this->data['captcha_url'] = $this->url->link("block/information/contact/captcha");

		//Action
		$this->data['action'] = $this->url->here();

		//Load Value or Defaults
		$defaults = array(
			'name'    => $this->customer->info('firstname'),
			'email'   => $this->customer->info('email'),
			'enquiry' => '',
			'captcha' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Render
		return $this->render();
	}

	public function success()
	{
		$this->view->load('block/information/contact_success');
		$this->data['continue'] = $this->url->link('common/home');

		$this->render();
	}

	private function validate()
	{
		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = _l("Name must be between 3 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->text($_POST['enquiry'], 10, 3000)) {
			$this->error['enquiry'] = _l("Enquiry must be between 10 and 3000 characters!");
		}

		if (!$this->captcha->validate($_POST['captcha'])) {
			$this->error['captcha'] = _l("Verification code does not match the image!");
		}

		return $this->error ? false : true;
	}

	public function captcha()
	{
		$this->captcha->generate();
	}
}
