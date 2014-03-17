<?php

class View
{
	private $registry;

	private $template;

	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function __get($key)
	{
		return $this->registry->get($key);
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function load($path)
	{
		$this->template = $this->theme->findFile($path);

		if (!$this->template) {
			trigger_error(_l("%s(): Could not resolve template path %s", __METHOD__, $path));
			exit();
		}
	}

	public function render($path = null, $data = array())
	{
		if ($path) {
			$this->load($path);
		}

		if (!$this->template) {
			trigger_error(_l("No template was set!"));
			exit();
		}

		if (is_file($this->template)) {

			extract($data);

			ob_start();

			include(_ac_mod_file($this->template));

			return ob_get_clean();
		} else {
			trigger_error(__METHOD__ . _l("(): Could not load template file %s!", $this->template));
			exit();
		}
	}
}
