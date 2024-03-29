<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

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
		if ($this->Model_ContactForm->sendMessage($_POST)) {
			message('success', _l("Thank you %s! Your message has been received.", _post('name')));
		} else {
			message('error', $this->Model_ContactForm->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('contact');
		}
	}
}
