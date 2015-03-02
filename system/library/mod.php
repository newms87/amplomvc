<?php

class Mod extends Library
{
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
			$algorithm = trim($directives['algorithm']);
		} //Intelligent Guess
		else {
			$contents = file_get_contents($mod_file);

			if (strpos($contents, '=====')) {
				$algorithm = 'merge';
			}
		}

		if (!$algorithm) {
			if (!_is_writable(dirname($destination_file)) || !symlink($mod_file, $destination_file)) {
				$this->error['destination'] = _l("Unable to write to the destination file / directory %s", $destination_file);
				return false;
			}

			return true;
		}

		if (!is_file($source_file)) {
			$this->error['source'] = _l("Source file %s was not found.", $source_file);
			return false;
		}

		if (is_file($destination_file)) {
			$source_file = $destination_file;
		}

		$instance = $this->load('system/mod/' . $algorithm);

		if (!$instance) {
			$this->error['algorithm'] = _l("Unknown Mod algorithm %s", $algorithm);
			return false;
		}

		$contents = $instance->apply($source_file, $mod_file);

		if (!$contents) {
			$this->error = $instance->getError();
			return false;
		}

		$php = preg_match("/\\.php(\\.mod)?$/", $mod_file);

		$meta = $this->removeMeta($contents, $php);

		if (empty($meta['source'])) {
			$meta['source'] = str_replace(DIR_SITE, '', $source_file);
		}

		$meta['mod'][] = str_replace(DIR_SITE, '', $mod_file);

		$this->addMeta($contents, $meta, $php);

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

		$php = preg_match("/\\.php(\\.mod)?$/", $destination_file);

		$contents = file_get_contents($destination_file);

		$meta = $this->removeMeta($contents, $php);

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

	public function addMeta(&$contents, $meta, $php = true)
	{
		if ($php) {
			if (($s = strpos($contents, '<?php') !== false)) {
				$contents = trim(substr($contents, $s + 5));
			}
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

		if ($php) {
			$contents = "<?php\n$meta_string\n\n" . $contents;
		} else {
			$contents = "<!-- \n$meta_string\n-->\n\n" . $contents;
		}
	}

	public function removeMeta(&$contents, $php = true)
	{
		if ($php) {
			if (($s = strpos($contents, '<?php') !== false)) {
				$contents = trim(substr($contents, $s + 5));
			}
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

		if ($php) {
			$contents = "<?php\n\n" . trim($contents);
		} elseif (!empty($mods) || !empty($src)) {
			$contents = trim(substr($contents, strpos($contents, '-->') + 3));
		}

		return array(
			'source' => $src,
			'mod'    => $mods
		);
	}
}
