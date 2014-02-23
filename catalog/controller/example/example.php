<?php
class Catalog_Controller_Example_Example extends Controller
{
	//Accessed via /site_root/example/example/
	public function index()
	{
		//The page
		$page_id = !empty($_GET['page_id']) ? $_GET['page_id'] : 0;

		$page = $this->Model_Page_Page->getPage($page_id);

		if (!$page) {
			//If you have an error in the Model call
			if ($this->Model_Page_Page->hasError()) {
				$this->message->add('error', $this->Model_Page_Page->getError());
			}

			//For this example, if no page is found, we dont really care, so continue to load page.

			//You may want to redirect here though..
			//$this->url->redirect('error/not_found');
		}

		//Alternative messages
		$this->message->add('warning', "my error message"); //essentially same as error
		$this->message->add('notify', "my notification message");
		$this->message->add('success', "my success message");
		$this->message->add('myclass', "my class message");

		//Page Head
		$this->document->setTitle(_l("My Page Title"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Example"), $this->url->link('example/example'));

		//Template Data
		$this->data = $page;

		//To be more specific, we can set title and content here.
		//For this example title and content are loaded form the model into the $page variable,
		//which is then passed to $this->data.

		//Alternatively we can set those variables or any other template variable here.

		$this->data['title'] = _l("Example Title");
		$this->data['content'] = _l("A whole bunch of content for the page");

		//The Template
		$this->template->load('example/example');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header',
		);

		//Render
		$this->response->setOutput($this->render());
	}

	//Accessed via /site_root/example/example/my_method
	public function my_method()
	{
		//.. Your customer function here
	}
}