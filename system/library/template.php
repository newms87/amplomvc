<?php
class Template extends Library
{
	public $data = array();

	private $name;
	private $file;

	private $template;

	private $root_dir = null;
	private $theme_override = null;

	public function getTemplate()
	{
		return $this->template;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function setTheme($theme)
	{
		$this->theme_override = $theme;
	}

	public function setRootDirectory($dir)
	{
		$this->root_dir = $dir;
	}

	public function set_file($file_name)
	{
		$file = $this->theme->find_file($file_name . '.tpl', $this->theme_override, $this->root_dir);

		if ($file) {
			$this->file = $file;
		} else {
			if ($this->name) {
				$this->cache->delete('template' . $this->name);
			}

			trigger_error('Template::set_file(): Could not find file ' . $file_name . '.tpl! ' . get_caller(0, 2));
			return false;
		}

		return true;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function load($name, $theme = null, $admin = null)
	{
		$this->name = $name;

		if (!$this->set_file($this->name, $theme, $admin)) {
			trigger_error("Unable to load template! " . get_caller());
			exit();
		}
	}

	public function getTemplatesFrom($path, $admin = false, $blank_row = false)
	{
		if ($admin) {
			$root = SITE_DIR . 'admin/view/theme/';
		} else {
			$root = SITE_DIR . 'catalog/view/theme/';
		}

		$themes = $this->theme->getThemes($admin);

		$templates = array();

		foreach ($themes as $theme_dir => $theme) {
			$dir = $root . $theme_dir . '/template/' . trim($path, '/') . '/';

			if (!is_dir($dir)) {
				continue;
			}

			$files = scandir($dir);

			$template_files = array();

			if ($blank_row !== false) {
				$template_files[''] = $blank_row;
			}

			foreach ($files as $file) {
				if (is_file($dir . $file) && preg_match("/\.tpl$/", $file) > 0) {
					$filename                  = str_replace('.tpl', '', $file);
					$template_files[$filename] = $filename;
				}
			}

			$templates[$theme_dir] = $template_files;
		}

		return $templates;
	}

	public function render()
	{
		if (!$this->file) {
			trigger_error("No template was set!" . get_caller(0, 2));
			exit();
		}

		if (is_file($this->file)) {

			extract($this->data);

			ob_start();

			include(_ac_mod_file($this->file));

			return ob_get_clean();
		} else {
			trigger_error('Error: Could not load template file ' . $this->file . '! ' . get_caller(0, 2));
			exit();
		}
	}

	public function find_file($file)
	{
		if (!preg_match("/\\.tpl\$/", $file)) {
			$file .= '.tpl';
		}

		return $this->theme->find_file($file, $this->theme_override, $this->root_dir);
	}
}
