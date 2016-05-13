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

class Mod extends Library
{
	function isApplied($mod_file, $directives = array())
	{
		if (!is_file($mod_file)) {
			$this->error['mod_file'] = _l("The mod file %s does not exist.");
			return;
		}

		$directives = get_comment_directives($mod_file) + $directives;

		$destination_file = !empty($directives['destination']) ? DIR_SITE . preg_replace("#^" . DIR_SITE . "#", '', trim($directives['destination'])) : false;

		if (!$destination_file) {
			$this->error['destination'] = _l("Destination file not set. No way to check if mod file %s has been applied.", $mod_file);
			return;
		}

		if (!is_file($destination_file)) {
			return false;
		} else {
			$destination_filemtime = filemtime($destination_file);
			$mod_file_filemtime    = filemtime($mod_file);

			if ($destination_filemtime === $mod_file_filemtime) {
				return true;
			}
		}

		$ext = pathinfo(preg_replace("/\\.mod$/", '', $destination_file), PATHINFO_EXTENSION);

		$contents = file_get_contents($destination_file);

		$meta = $this->removeMeta($contents, $ext);

		if (!empty($meta['src'])) {
			if (filemtime(DIR_SITE . $meta['src']) > $destination_filemtime) {
				return false;
			}
		}

		$mod_file = str_replace(DIR_SITE, '', $mod_file);

		foreach ($meta['mod'] as $mf) {
			if ($mod_file === $mf) {
				return $destination_filemtime >= $mod_file_filemtime;
			}
		}

		return false;
	}

	public function apply($mod_file, $directives = array())
	{
		if (!is_file($mod_file)) {
			$this->error['file'] = _l("Mod file was not found at %s", $mod_file);
			return false;
		}

		$directives = get_comment_directives($mod_file) + $directives;

		if (isset($directives['skip'])) {
			return true;
		}

		if (empty($directives['source'])) {
			$this->error['source'] = _l("Source file was not set. You can set the Source directive in the PHPDoc Comment for Mod File %s", $mod_file);
			return false;
		}

		$source_file      = DIR_SITE . trim($directives['source']);
		$destination_file = !empty($directives['destination']) ? DIR_SITE . trim($directives['destination']) : $source_file . '.mod';
		$algorithm        = false;

		if (!empty($directives['algorithm'])) {
			$algorithm = trim(strtolower($directives['algorithm']));
		} //Intelligent Guess
		else {
			$contents = file_get_contents($mod_file);

			if (strpos($contents, '=====') !== false) {
				$algorithm = 'merge';
			}
		}

		if (!$algorithm) {
			if (!_is_writable(dirname($destination_file)) || !@symlink($mod_file, $destination_file)) {
				$this->error['destination'] = _l("Unable to write to the destination file (or directory of) %s", $destination_file);
				return false;
			}

			return true;
		}

		if ($this->isApplied($mod_file, array('destination' => $destination_file))) {
			return true;
		}

		if (!is_file($source_file)) {
			$this->error['source'] = _l("Source file %s was not found.", $source_file);
			return false;
		}

		$instance = $this->load('system/mod/' . $algorithm);

		if (!$instance) {
			$this->error['algorithm'] = _l("Unknown Mod algorithm %s", $algorithm);
			return false;
		}

		$ext      = pathinfo(preg_replace("/\\.mod$/", '', $source_file), PATHINFO_EXTENSION);
		$rel_path = str_replace(DIR_SITE, '', $mod_file);

		$meta = array();

		if (is_file($destination_file)) {
			$contents = file_get_contents($destination_file);

			$meta = $this->removeMeta($contents, $ext);

			//If this mod has already been applied, reapply the whole file
			if (array_search($rel_path, $meta['mod']) !== false) {
				return $this->reapply($destination_file);
			} else {
				//Save without Meta data
				file_put_contents($destination_file, $contents);

				$source_file   = $destination_file;
				$meta['mod'][] = $rel_path;
			}
		} else {
			$meta['mod'] = array($rel_path);
		}

		$contents = $instance->apply($source_file, $mod_file, $ext, $meta);

		if (!$contents) {
			$this->error = $instance->fetchError();
			return false;
		}

		if (empty($meta['source'])) {
			$meta['source'] = str_replace(DIR_SITE, '', $source_file);
		}

		krsort($meta);

		$this->addMeta($contents, $meta, $ext);

		if (!file_put_contents($destination_file, $contents)) {
			$this->error['write'] = _l("Failed to write to %s.", $destination_file);
			return false;
		}

		return true;


		/* TODO: Decide if this is necessary....
		$set_file_root = function (&$file) {
			$file = DIR_SITE . trim($file);
		};
		$file_filter   = function ($file) {
			return trim($file);
		};

		if (!empty($directives['require'])) {
			$directives['require'] = array_filter(explode("\n", $directives['require']), $file_filter);
			array_walk_recursive($directives['require'], $set_file_root);
		}

		if (!empty($directives['include'])) {
			$directives['include'] = array_filter(explode("\n", $directives['include']), $file_filter);
			array_walk_recursive($directives['include'], $set_file_root);
		}
		*/
	}

	public function reapply($destination_file)
	{
		$contents = file_get_contents($destination_file);
		$meta     = $this->removeMeta($contents);

		if (empty($meta['source'])) {
			$this->error['source'] = _l("There is no source file for the mod destination file %s", $destination_file);
			return false;
		}

		if (empty($meta['mod'])) {
			$this->error['mod'] = _l("There are no mods to apply for mod destination file %s", $destination_file);
			return false;
		}

		@unlink($destination_file);

		foreach ($meta['mod'] as $mod) {
			$mod_directives = array(
				'source' => $meta['source'],
			);

			$this->apply(DIR_SITE . $mod, $mod_directives);
		}

		return true;
	}

	public function unapply($mod_file, $directives = array())
	{
		if (!is_file($mod_file)) {
			$this->error['file'] = _l("Mod file was not found at %s", $mod_file);
			return false;
		}

		$directives = get_comment_directives($mod_file) + $directives;

		if (empty($directives['destination'])) {
			$this->error['destination'] = _l("No destination file was set. Unable to unapply mod file %s", $mod_file);
			return false;
		}

		$destination_file = DIR_SITE . trim($directives['destination']);

		if (!is_file($destination_file)) {
			return true;
		}

		$ext = pathinfo(preg_replace("/\\.mod$/", '', $destination_file), PATHINFO_EXTENSION);

		$contents = file_get_contents($destination_file);

		$meta = $this->removeMeta($contents, $ext);

		@unlink($destination_file);

		if (!empty($meta['source']) && !empty($meta['mod'])) {
			$mod_path = str_replace(DIR_SITE, '', $mod_file);

			foreach ($meta['mod'] as $key => $mod) {
				if ($mod === $mod_path) {
					unset($meta['mod'][$key]);
				}
			}

			if (!empty($meta['mod'])) {
				$directives = array(
					'source' => $meta['source'],
				);

				foreach ($meta['mod'] as $mod) {
					$this->apply(DIR_SITE . $mod, $directives);
				}
			}
		}

		return empty($this->error);
	}

	public function addMeta(&$contents, $meta, $ext = 'php')
	{
		switch ($ext) {
			case 'php':
				if (($s = strpos($contents, '<?php') !== false)) {
					$contents = trim(substr($contents, $s + 5));
				}
				break;
		}

		$meta_string = '';

		foreach ($meta as $key => $value) {
			$key = strtoupper($key);

			if (is_array($value)) {
				foreach ($value as $v) {
					$meta_string .= "#$key: $v\n";
				}
			} else {
				$meta_string .= "#$key: $value\n";
			}
		}

		switch ($ext) {
			case 'php':
				$contents = "<?php\n$meta_string\n\n" . $contents;
				break;

			case 'less':
			case 'css':
				$contents = "/*\n$meta_string\n*/\n\n" . $contents;
				break;

			case 'tpl':
			case 'html':
			default:
				$contents = "<!-- \n$meta_string\n-->\n\n" . $contents;
				break;
		}
	}

	public function removeMeta(&$contents, $ext = 'php')
	{
		switch ($ext) {
			case 'php':
				if (($s = strpos($contents, '<?php') !== false)) {
					$contents = trim(substr($contents, $s + 5));
				}
				break;
		}

		$src = '';

		if (($s = strpos($contents, '#SOURCE:')) !== false) {
			$s += 8;
			$e        = strpos($contents, "\n", $s);
			$src      = trim(substr($contents, $s, $e - $s));
			$contents = substr($contents, $e);
		}

		$mods = array();

		while (($s = strpos($contents, '#MOD:')) !== false) {
			$s += 5;
			$e        = strpos($contents, "\n", $s);
			$mods[]   = trim(substr($contents, $s, $e - $s));
			$contents = substr($contents, $e);
		}

		switch ($ext) {
			case 'php':
				$contents = "<?php\n\n" . trim($contents);
				break;

			case 'less':
			case 'css':
				if (!empty($mods) || !empty($src)) {
					$contents = trim(substr($contents, strpos($contents, '*/') + 2));
				}
				break;

			case 'tpl':
			case 'html':
				if (!empty($mods) || !empty($src)) {
					$contents = trim(substr($contents, strpos($contents, '-->') + 3));
				}
				break;
		}

		return array(
			'source' => $src,
			'mod'    => $mods
		);
	}
}
