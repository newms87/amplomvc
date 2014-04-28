<?php
class Catalog_Controller_Account_Edit extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', site_url('account/edit'));

			redirect('customer/login');
		}

		$this->document->setTitle(_l("My Account Information"));

		if ($this->request->isPost() && $this->validate()) {
			$this->customer->edit($_POST);

			$this->message->add('success', _l("Success: Your account has been successfully updated."));

			redirect('account');
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Account"), site_url('account'));
		$this->breadcrumb->add(_l("Edit Information"), site_url('account/edit'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		$data['action'] = site_url('account/edit');

		if (!$this->request->isPost()) {
			$customer_info = $this->customer->info();
		}

		if (isset($_POST['firstname'])) {
			$data['firstname'] = $_POST['firstname'];
		} elseif (isset($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$data['lastname'] = $_POST['lastname'];
		} elseif (isset($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
			$data['email'] = $_POST['email'];
		} elseif (isset($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
			$data['telephone'] = $_POST['telephone'];
		} elseif (isset($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
			$data['fax'] = $_POST['fax'];
		} elseif (isset($customer_info)) {
			$data['fax'] = $customer_info['fax'];
		} else {
			$data['fax'] = '';
		}

		$data['back'] = site_url('account');

		$this->response->setOutput($this->render('account/edit', $data));
	}

	private function validate()
	{
		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (($this->customer->info('email') !== $_POST['email']) && $this->customer->emailRegistered($_POST['email'])) {
			$this->error['warning'] = _l("Warning: E-Mail address is already registered!");
		}

		if (isset($_POST['telephone']) && !$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		return empty($this->error);
	}
}
