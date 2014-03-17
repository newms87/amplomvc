<?php
class Admin_Controller_Block_Add extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("New Block"));

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Block_Block->addBlock($_POST);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("The Block %s was created successfully!", $_POST['name']));

				$this->url->redirect('block/block', 'name=' . $_POST['route']);
			}

			$this->message->add('warning', _l("Unable to create the new Block. Try again, or build the block manually."));
		}

		$this->message->add('notify', _l("Adding a Block will simply setup the files in the system on the front end and back end. If you are not a developer this is worthless!"));

		$this->getForm();
	}

	private function getForm()
	{
		$this->view->load('block/add');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Blocks"), $this->url->link('block/block'));
		$this->breadcrumb->add(_l("New Block"), $this->url->link('block/add'));

		$this->data['action'] = $this->url->link('block/add');
		$this->data['cancel'] = $this->url->link('block/block');

		$defaults = array(
			'name'                => '',
			'route'               => '',
			'language_file'       => true,
			'settings_file'       => true,
			'profiles_file'       => true,
			'themes'              => array('default'),
			'front_language_file' => true,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($block_info[$key])) {
				$this->data[$key] = $block_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['data_themes'] = $this->theme->getThemes();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'block/add')) {
			$this->error['warning'] = _l("You do not have permission to modify Blocks");
		}

		if (!$this->validation->text($_POST['name'], 3, 128)) {
			$this->error['name'] = _l("Block name must be between 1 and 128 characters!");
		}

		if (empty($_POST['route']) || !preg_match("/^[a-z0-9_]+\\/[a-z0-9_]+\$/i", $_POST['route'])) {
			$this->error['route'] = _l("Route must be in the form mynew/blockroute containing characters a-z, 0-9, or _");
		}

		return $this->error ? false : true;
	}
}
