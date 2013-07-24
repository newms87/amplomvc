<?php
class Admin_Model_Design_Template extends Model
{
	public function getTemplatesFrom($path, $admin = false)
	{
		if ($admin) {
			$root = SITE_DIR . 'admin/';
			$themes = $this->theme->getAdminThemes();
		}
		else {
			$root = SITE_DIR . 'catalog/view/theme/';
			$themes = $this->theme->getThemes();
		}
		
		$templates = array();
	
		foreach ($themes as $theme) {
			$dir = $root . $theme . '/template/' . trim($path,'/') . '/';
			
			if (!is_dir($dir)) {
				continue;
			}
			
			$files = scandir($dir);
			
			$template_files = array();
			
			foreach ($files as $file) {
				if (is_file($dir . $file) && preg_match("/\.tpl$/", $file) > 0) {
					$filename = str_replace('.tpl', '', $file);
					$template_files[$filename] = $filename;
				}
			}
			
			$templates[$theme] = $template_files;
		}
		
		return $templates;
	}
}