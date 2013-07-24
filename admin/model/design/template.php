<?php
class Admin_Model_Design_Template extends Model
{
	public function getTemplatesFor($view, $with_blanks = false)
	{
		$themes = scandir(SITE_DIR . 'catalog/view/theme/');
		
		if (!$themes) {
			trigger_error("There was a problem reading from the catalog theme directory!");
			exit;
		}
		
		$templates = array();
	
		foreach ($themes as $theme) {
			$dir = SITE_DIR . 'catalog/view/theme/' . $theme . '/template/' . $view . '/';
			if (!is_dir($dir)) {
				continue;
			}
			
			$files = scandir($dir);
			
			$template_files = array();
			
			foreach ($files as $key => $file) {
				if (!in_array($file, array('.','..')) && is_file($dir. $file) && preg_match("/\.tpl$/",$file) > 0) {
					$file = str_replace('.tpl', '', $file);
					$template_files[$file] = $file;
				}
			}
			
			if ($with_blanks) {
				$template_files = array('' => '') + $template_files;
			}
			
			$templates[$theme] = $template_files;
		}
		
		return $templates;
	}
}