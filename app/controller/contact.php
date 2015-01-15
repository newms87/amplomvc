<?php

class App_Controller_Contact extends Controller
{
	public function index()
	{
		set_page_info('title', _l("Contact Us"));

		$contact = $_POST;

		if (is_logged()) {
			$contact += array(
				'email' => customer_info('email'),
				'name'  => trim(customer_info('first_name') . ' ' . customer_info('last_name')),
			);
		}

		$contact += array(
			'email'   => '',
			'name'    => '',
			'message' => '',
		);

		output($this->render('contact', $contact));
	}

	public function submit()
	{
		if ($this->Model_Contact->sendMessage($_POST)) {
			message('success', _l("Your message was sent successfully!"));
		} else {
			message('error', $this->Model_Contact->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('contact');
		}
	}
}