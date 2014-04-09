<?php

class Admin_Controller_Block_Add extends Controller
{
	public function index()
	{
		//Notify User this is oly for developers
		$this->message->add('notify', _l("Adding a Block will simply setup the files in the system on the front end and back end. If you are not a developer this is worthless!"));

		//Page Title
		$this->document->setTitle(_l("New Block"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Blocks"), $this->url->link('block/block'));
		$this->breadcrumb->add(_l("New Block"), $this->url->link('block/add'));

		$defaults = array(
			'name'                => '',
			'path'                => '',
			'language_file'       => true,
			'settings_file'       => true,
			'profiles_file'       => true,
			'themes'              => array('default'),
			'front_language_file' => true,
		);

		$data = $_POST + $defaults;

		$data['data_themes'] = $this->theme->getThemes();

		//Actions
		$data['save']   = $this->url->link('block/add/add');
		$data['cancel'] = $this->url->link('block/block');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render('block/add', $data));
	}


	public function add()
	{
		if (!$this->user->can('modify', 'block/add')) {
			$this->error['warning'] = _l("You do not have permission to Add Blocks");
			$this->url->redirect('block/block');
		}

		if (!$this->block->add($_POST)) {
			$this->message->add('error', $this->block->getError());
		} else {
			$this->message->add('success', _l("The Block %s was created successfully!", $_POST['name']));

			$this->url->redirect('block/block', 'name=' . $_POST['path']);
		}

		$this->index();
	}
}
