<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC Dev Plugin
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class Cleanup extends Library
{
	function __construct()
	{
		$ignore_list = array(DIR_CACHE);
		$ext         = array('php');


		$files = $this->getFiles(DIR_SITE . "app/controller/admin/", $ext);

		$this->noview($files);

	}

	public function noview($files)
	{
		foreach ($files as $file) {
			$lines = explode("\n", file_get_contents($file));

			$orig_lines = $lines;

			$new_lines = array();

			$view = null;
			echo $file . '<br>';

			foreach ($lines as $num => $line) {
				$matches = null;
				if (preg_match("/\\\$this->view->load\\('([^']+)'\\);/", $line, $matches)) {
					if ($view) {
						$this->error($num, "2 Views found before render $view and $matches[1]");
						continue 2;
					}

					$view = $matches[1];
				} elseif (preg_match("/(\\s*)\\\$this->render\\(([^)]*)\\)/", $line, $matches)) {
					if (!$view) {
						if (!$matches[2]) {
							$this->error($num, "Render found without a view");
						}
						continue 2;
					}

					if (!empty($matches[2])) {
						//$this->error($num, "Render was not empty");
						continue 2;
					}

					$new_lines[] = str_replace("\$this->render()", "\$this->render('$view', \$data)", $line);
					$view        = null;
					continue;
				}

				$new_lines[] = $line;
			}

			$this->print_lines($orig_lines, $new_lines, true);
			file_put_contents($file, implode("\n", $new_lines));
		}
	}

	public function getFiles($dir, $ext = array('php'), $ignore = array(), $depth = 0)
	{
		if ($depth > 20) {
			echo "we have too many recursions!";
			exit;
		}

		if (!is_dir($dir) || in_array($dir . '/', $ignore)) {
			return array();
		}

		$handle = @opendir($dir);

		$files = array();
		while (($file = readdir($handle)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			$file_path = rtrim($dir, '/') . '/' . $file;

			if (is_dir($file_path)) {
				$files = array_merge($files, $this->getFiles($file_path, $ext, $ignore, $depth + 1));
			} else {
				if (!empty($ext)) {
					$match = null;
					preg_match("/[^\\.]*$/", $file, $match);

					if (!in_array($match[0], $ext)) {
						continue;
					}
				}
				$files[] = $file_path;
			}
		}

		return $files;
	}

	public function print_lines($orig, $lines, $changes_only = true, $special_chars = false)
	{
		$orig_i     = 0;
		$total_orig = count($orig);

		for ($new_i = 0; $new_i < count($lines); $new_i++) {
			$l = $lines[$new_i];

			if ($orig_i >= $total_orig) {
				$color = '#C2E782';
			} elseif ($lines[$new_i] != $orig[$orig_i]) {
				$in_orig = false;
				for ($i = $orig_i; $i < count($orig); $i++) {
					if ($lines[$new_i] == $orig[$i]) {
						$in_orig = $i;
						break;
					}
				}

				$in_new = false;
				for ($i = $new_i; $i < count($lines); $i++) {
					if ($orig[$orig_i] == $lines[$i]) {
						$in_new = $i;
						break;
					}
				}

				if ($in_new && $in_orig) {
					if ($in_new < $in_orig) {
						$in_orig = false;
					} else {
						$in_new = false;
					}
				}

				if ($in_new) {
					for ($i = $new_i; $i < $in_new; $i++) {
						$this->pl($i + 1, $lines[$i], '#C2E782', $special_chars);
					}

					$new_i = $in_new - 1;

					continue;
				} elseif ($in_orig) {
					for ($i = $orig_i; $i < $in_orig; $i++) {
						$this->pl($i + 1, $orig[$i], '#F98888', $special_chars);
					}

					$orig_i = $in_orig;
					$new_i++;

					continue;
				} else {
					$orig_i++;
					$color = '#C282E7';
					/*
					$changes = false;
					for ($i = 0; $i < count($lines) - $new_i; $i++) {
						if (!isset($orig[$orig_i+$i])) break;

						if ($orig[$orig_i+$i] == $lines[$new_i+$i]) {
							echo "FOUND Changes!<br>";
							$changes = $i;
							break;
						}
					}

					if ($changes) {
						for ($i = 0; $i < $changes; $i++) {
							$this->pl($orig_i + $i + 1, $orig[$orig_i + $i], '#A232A7', $special_chars);
							$this->pl($new_i + $i + 1, $lines[$new_i + $i], '#C282E7', $special_chars);
						}

						$new_i += $i-1;
						$orig_i += $i;

						continue;
					} else {
						$color = '#C2E782';
					}*/
				}
			} else {
				$orig_i++;
				if ($changes_only) {
					continue;
				}
				$color = '#CBCBCB';
			}

			$this->pl($new_i + 1, $l, $color, $special_chars);
		}
	}

	public function pl($num, $line, $color = '#CBCBCB', $special_chars = true)
	{
		if ($special_chars) {
			$line = htmlspecialchars($line);
		}

		$line = preg_replace(array(
			'/ /',
			'/\t/'
		), array(
			'&nbsp;',
			'&nbsp;&nbsp;&nbsp;'
		), $line);

		echo "<div style='background: $color'>$num. $line</div>";
	}

	public function error($num, $msg)
	{
		echo "<div style=\"background: red; color:white\">$num.&nbsp;&nbsp;&nbsp; $msg</div>";
	}
}
