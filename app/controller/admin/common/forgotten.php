<?php
class App_Controller_Admin_Common_Forgotten extends Controller
{
	public function index()
	{
		//Verify User is not already logged in
		if ($this->user->isLogged()) {
			redirect();
		}

		//Page Title
		$this->document->setTitle(_l("Forgot Your Password?"));

		//Handle POST
		if (is_post() && $this->validate()) {
			$code = $this->user->generateCode();

			$this->user->setResetCode($_POST['email'], $code);

			$email_data = array(
				'reset' => site_url('admin/common/forgotten/reset', 'code=' . $code),
				'email' => $_POST['email'],
			);

			call('admin/mail/forgotten_admin', $email_data);

			message('success', _l("Please follow the link that was sent to your email to reset your password."));

			redirect('admin/common/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Forgotten Password"), site_url('admin/common/forgotten'));

		//Entry Data
		$data['email'] = isset($_POST['email']) ? $_POST['email'] : '';

		//Action Buttons
		$data['action'] = site_url('admin/common/forgotten');
		$data['cancel'] = site_url('admin/common/login');

		//Render
		output($this->render('common/forgotten', $data));
	}

	public function reset()
	{
		if ($this->user->isLogged() || empty($_GET['code'])) {
			redirect();
		}

		$code = $_GET['code'];

		$user_id = $this->user->lookupResetCode($code);

		//User not found
		if (!$user_id) {
			message('warning', _l("Unable to locate password reset code. Please try again."));
			redirect('admin/common/login');
		}

		//Handle POST
		if (is_post()) {
			//Validate Password
			if (!validate('password', $_POST['password'])) {
				if ($this->validation->isErrorCode(Validation::PASSWORD_CONFIRM)) {
					$this->error['confirm'] = $this->validation->getError();
				} else {
					$this->error['password'] = $this->validation->getError();
				}
			} else {
				$this->user->updatePassword($user_id, $_POST['password']);
				$this->user->clearResetCode($user_id);

				message('success', _l('You have successfully updated your password!'));
			}

			redirect('admin/common/login');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l('Home'), site_url('admin'));
		$this->breadcrumb->add(_l('Password Reset'), site_url('admin/common/forgotten/reset', 'code=' . $code));

		//Action Buttons
		$data['save']   = site_url('admin/common/forgotten/reset', 'code=' . $code);
		$data['cancel'] = site_url('admin/common/login');

		//Render
		output($this->render('common/reset', $data));
	}

	private function validate()
	{
		if (empty($_POST['email']) || !$this->Model_User_User->getTotalUsersByEmail($_POST['email'])) {
			$this->error['email'] = _l("Warning: The E-Mail Address was not found in our records, please try again!");
		}

		return empty($this->error);
	}
}
