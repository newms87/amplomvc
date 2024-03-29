<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */
class Image extends Library
{
	public
		$file,
		$image,
		$width,
		$height,
		$mime,
		$bits,
		$filesize,
		$dirname,
		$filename,
		$basename,
		$extension,
		$dir_mode;

	private $bg_color = '';

	static $streams = array();

	public function __construct($file = null, $create = false)
	{
		parent::__construct();

		$this->dir_mode = option('config_image_dir_mode');

		if (!self::$streams) {
			self::$streams = (array)stream_get_wrappers();
		}

		if ($file) {
			$this->set($file, $create);
		}
	}

	public function get($image_path, $return_dir = false)
	{
		if (preg_match("/#file_id=(\\d+)$/", $image_path, $matches)) {
			return $this->Model_File->getField($matches[1], $return_dir ? 'path' : 'url');
		}

		$image_url = is_url($image_path) ? $image_path : false;

		if (strpos($image_url, DIR_IMAGE) === 0 || strpos($image_url, DIR_DOWNLOAD) === 0 || strpos($image_url, DIR_SITE) === 0) {
			$image_url = false;
		}

		if ($image_url && !$return_dir) {
			$image = $image_url;
		} else {
			$scheme = parse_url($image_path, PHP_URL_SCHEME);

			if ($image_url && ($scheme && !in_array($scheme, self::$streams))) {
				return $image_url;
			}

			$replace = array(
				'|\\\\|'                 => '/',
				'|/\\./|'                => '/',
				'|' . URL_IMAGE . '|'    => DIR_IMAGE,
				'|' . URL_DOWNLOAD . '|' => DIR_DOWNLOAD,
				'|' . URL_SITE . '|'     => DIR_SITE,
				"|[#\\?].*$|"            => '',
			);

			$image = preg_replace(array_keys($replace), $replace, $image_path);

			if (!is_file($image)) {
				if (is_file(DIR_IMAGE . $image)) {
					$image = DIR_IMAGE . $image;
				} else {
					if (strpos($image, "/theme/")) {
						if ($image = $this->theme->findFile($image)) {
							return $image;
						}
					}

					if (defined("AMPLO_IMAGE_ERROR_LOG") && AMPLO_IMAGE_ERROR_LOG) {
						write_log('image', _l("Unable to locate image file %s<BR>FROM: %s<BR><BR>%s", $image_path, $this->url->here(), get_caller()));
					}

					$this->error['image'] = _l("Could not locate image file %s", $image_path);

					return false;
				}
			}
		}

		$replace = array(
			DIR_IMAGE    => URL_IMAGE,
			DIR_DOWNLOAD => URL_DOWNLOAD,
			DIR_SITE     => URL_SITE,
		);

		return $return_dir ? $image : str_replace(array_keys($replace), $replace, $image);
	}

	public function set($image, $create_image = false)
	{
		$file = $this->get($image, true);

		$this->file = is_file($file) ? $file : null;

		if (!$this->file) {
			return false;
		}

		$pathinfo        = pathinfo($this->file);
		$this->extension = isset($pathinfo['extension']) ? $pathinfo['extension'] : 'png';
		$this->filename  = $pathinfo['filename'];
		$this->dirname   = $pathinfo['dirname'];
		$this->basename  = $pathinfo['basename'];

		$info           = getimagesize($this->file);
		$this->width    = $info[0];
		$this->height   = $info[1];
		$this->bits     = $info['bits'];
		$this->mime     = $info['mime'];
		$this->filesize = filesize($this->file);

		if ($create_image) {
			$this->create();
		}

		return $this->file;
	}

	public function setBGColor($color)
	{
		$this->bg_color = $color;
	}

	public function clear()
	{
		imagedestroy($this->image);
		$this->image    = null;
		$this->file     = null;
		$this->width    = null;
		$this->height   = null;
		$this->bits     = null;
		$this->mime     = null;
		$this->filesize = null;
	}

	public function create()
	{
		if ($this->image) {
			imagedestroy($this->image);
			$this->image = null;
		}

		$this->image = _create_image($this->file, $this->mime);

		if ($this->image) {
			$this->width  = imagesx($this->image);
			$this->height = imagesy($this->image);

			return true;
		}

		return false;
	}

	public function save($file, $quality = 75)
	{
		if (!_is_writable(dirname($file))) {
			if (defined("AMPLO_IMAGE_ERROR_LOG") && AMPLO_IMAGE_ERROR_LOG) {
				write_log('error', _l("Failed to save image file because directory was not writable: %s!", $file));
			}

			$this->error['file'] = _l("Directory was not writable for %s", $file);

			return false;
		}

		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		$success = false;

		if (is_resource($this->image)) {
			switch ($extension) {
				case 'jpeg':
				case 'jpg':
					$success = imagejpeg($this->image, $file, $quality);
					break;

				case 'gif':
					$success = imagegif($this->image, $file);
					break;

				case 'png':
				default:
					$success = imagepng($this->image, $file, 9);
					break;
			}

			$this->clear();
		}

		if (!$success) {
			write_log('error', _l(__METHOD__ . "(): Failed to save image file %s as %s!", $file, $extension));
		}

		return $success;
	}

	public function convert($file, $type, $output_file = null, $quality = null, $force = false)
	{
		$type = strtolower($type);

		if ($type === 'jpeg') {
			$type = 'jpg';
		}

		//Check if already the correct file type
		if (!$force && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === $type) {
			return $file;
		}

		if (!$output_file) {
			$pathinfo = pathinfo($file);

			$output_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '.' . $type;
		}

		if (!is_file($file)) {
			$this->error['file'] = _l("Unable to locate file %s", $file);

			return false;
		}

		$image = imagecreatefromstring(file_get_contents($file));

		if (!$image) {
			$this->error['image'] = _l("Failed to create image from file %s", $file);

			return false;
		}

		switch ($type) {
			case 'png':
				if (!imagepng($image, $output_file, $quality)) {
					$this->error['imagepng'] = _l("Failed while converting image to PNG");

					return false;
				}
				break;

			case 'jpg':
				if (!imagejpeg($image, $output_file, $quality)) {
					$this->error['imagejpeg'] = _l("Failed while converting image to JPEG");

					return false;
				}
				break;

			default:
				$this->error['type'] = _l("Unknown Conversion Type: %s", $type);

				return false;
		}

		return $output_file;
	}

	public function setMaxSize($width, $height, $keep_ratio = true)
	{
		if (!$this->image) {
			$this->create();
		}

		if ($width && $this->width < $width) {
			$width = null;
		}

		if ($height && $this->height < $height) {
			$height = null;
		}

		if ($width || $height) {
			if ($width && $height && $keep_ratio) {
				if ($width / $this->width > $height / $this->height) {
					$width = null;
				} else {
					$height = null;
				}
			}

			return $this->setSize($width, $height);
		}

		return -1;
	}

	public function setSize($width, $height)
	{
		//if the image is 0 width or 0 height, do not return an image
		if (!$this->width || !$this->height) {
			$this->error['size'] = _l("The image size was 0.");

			return 0;
		}

		//If width and height are 0, we do not scale the image
		if ($width <= 0 && $height <= 0) {
			return -1;
		}

		//Constrain Width
		if ($width <= 0) {
			$scale_y = $height / $this->height;
			$scale_x = $scale_y;
		} //Constrain Height
		elseif ($height <= 0) {
			$scale_x = $width / $this->width;
			$scale_y = $scale_x;
		} //Resize
		else {
			$scale_x = $width / $this->width;
			$scale_y = $height / $this->height;
		}

		//if the image is the correct size we do not need to do anything
		if ($scale_x === 1 && $scale_y === 1) {
			return -1;
		}

		$new_width  = (int)($this->width * $scale_x);
		$new_height = (int)($this->height * $scale_y);


		if (!$this->image) {
			$this->create();
		}

		//Render new image
		$new_image = imagecreatetruecolor((int)$new_width, (int)$new_height);

		if (!$this->bg_color || $this->mime === 'image/png') {
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$background = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagecolortransparent($new_image, $background);
		} else {
			if ($this->bg_color) {
				$background = $this->heximagecolorallocate($this->bg_color);
			} else {
				$background = imagecolorallocate($new_image, 255, 255, 255);
			}
		}

		imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $background);

		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
		imagedestroy($this->image);

		$this->image = $new_image;

		return 1;
	}

	public function resize($width = 0, $height = 0, $max_size = false, $return_dir = false)
	{
		$width  = round($width);
		$height = round($height);

		$new_image_path = 'cache/' . md5($this->dirname) . '-' . $this->filename . '-' . ($max_size ? 'max-' : '') . $width . 'x' . $height . '.' . $this->extension;
		$new_image_file = DIR_IMAGE . $new_image_path;

		//if image is already in cache, return cached version
		if (!is_file($new_image_file) || (_filemtime($this->file) > _filemtime($new_image_file))) {
			if ($max_size) {
				$response = $this->setMaxSize($width, $height, $max_size !== 'no-ratio');
			} else {
				$response = $this->setSize($width, $height);
			}

			if (!$response) {
				return false;
			}

			if ($response > 0) {
				$this->save($new_image_file);
			} elseif ($response < 0) {
				return $return_dir ? $this->file : $this->get($this->file);
			}
		}

		return $return_dir ? $new_image_file : URL_IMAGE . $new_image_path;
	}

	public function ico($source, $destination = null, $sizes = null)
	{
		require_once(DIR_RESOURCES . 'phpico/class-php-ico.php');

		if (is_file(DIR_IMAGE . $source)) {
			$source = DIR_IMAGE . $source;
		} elseif (!is_file($source)) {
			$this->error = _l("Invalid Source File %s", $source);

			return false;
		}

		if (!$destination) {
			$destination = DIR_IMAGE . 'icon/' . uniqid() . '/' . pathinfo($source, PATHINFO_FILENAME) . '.ico';
		}

		if (!_is_writable(dirname($destination))) {
			$this->error = _l("The Destination directory was not writable: %s", $destination);

			return false;
		}

		if (!$sizes) {
			$sizes = array(
				array(
					16,
					16
				),
				array(
					32,
					32
				),
				array(
					48,
					48
				),
				array(
					64,
					64
				),
			);
		}

		$ico_lib = new PHP_ICO($source, $sizes);
		$ico_lib->save_ico($destination);

		return str_replace(DIR_IMAGE, URL_IMAGE, $destination);
	}

	public function watermark($file, $position = 'bottomright')
	{
		$watermark = new Image($file, true);

		$watermark_width  = $watermark->width;
		$watermark_height = $watermark->height;

		switch ($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->width - $watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->height - $watermark_height;
				break;
			case 'bottomright':
				$watermark_pos_x = $this->width - $watermark_width;
				$watermark_pos_y = $this->height - $watermark_height;
				break;
		}

		imagecopy($this->image, $watermark->image, $watermark_pos_x, $watermark_pos_y, 0, 0, 120, 40);

		imagedestroy($watermark);
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y)
	{
		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width  = $bottom_x - $top_x;
		$this->height = $bottom_y - $top_y;
	}

	public function rotate($degree, $color = 'FFFFFF')
	{
		$rgb = $this->hex2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->width  = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	public function filter($filter)
	{
		imagefilter($this->image, $filter);
	}

	public function text($text, $x = 0, $y = 0, $size = 5, $color = '000000')
	{
		$rgb = $this->hex2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	public function merge($file, $x = 0, $y = 0, $opacity = 100, $convert = 'truecolor', $colors = 16000000)
	{
		$merge = new Image($file, true);

		if (!$merge->image) {
			$this->error['image'] = _l("Unable to create image for %s. Merge failed.", $file);

			return false;
		}

		if ($convert) {
			if ($convert === 'truecolor') {
				imagepalettetotruecolor($merge->image);
				imagepalettetotruecolor($this->image);

				$transparent = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
				imagecolortransparent($this->image, $transparent);
				imagealphablending($this->image, true);
			} else {
				imagetruecolortopalette($merge->image, false, $colors);
				imagetruecolortopalette($this->image, false, $colors);
			}
		}

		if ($opacity === 100 && !imageistruecolor($this->image)) {
			imagecopy($this->image, $merge->image, $x, $y, 0, 0, $merge->width, $merge->height);
		} else {
			imagecopymerge($this->image, $merge->image, $x, $y, 0, 0, $merge->width, $merge->height, $opacity);
		}

		return true;
	}

	public function heximagecolorallocate($hex)
	{
		if (!$hex || strpos($hex, '#') !== 0 || strlen($hex) !== 7 || preg_match("/[^A-F0-9]/i", substr($hex, 1)) > 0) {
			trigger_error("ERROR in Draw library: set_text_color(\$color): \$color must be in hex format #FFFFFF");

			return;
		}

		return imagecolorallocate($this->image, (int)hexdec($hex[1] . $hex[2]), (int)hexdec($hex[3] . $hex[4]), (int)hexdec($hex[5] . $hex[6]));
	}

	public function hex2rgb($color)
	{
		if (!is_string($color)) {
			return array(
				0,
				0,
				0,
			);
		}

		if ($color[0] === '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array(
				$color[0] . $color[1],
				$color[2] . $color[3],
				$color[4] . $color[5]
			);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array(
				$color[0] . $color[0],
				$color[1] . $color[1],
				$color[2] . $color[2]
			);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array(
			$r,
			$g,
			$b
		);
	}

	/**
	 * Sorts images by color in place
	 *
	 * @param &$data   - an array with a filepath to an image file under a key
	 * @param $img_key - the key in the $data array that points to the image file path
	 * @param $type    - the method to sort by. 'HSV' is default.
	 */

	public function sort(&$data, $img_key, $type = 'HSV')
	{
		$img_hsv = array();

		foreach ($data as $key => &$d) {
			$colors         = $this->get_dominant_color($d[$img_key]);
			$d['hsv']       = $this->RGB_to_HSV($colors['r'], $colors['g'], $colors['b']);
			$hsv_sort[$key] = $d['hsv'];
		}

		array_multisort($hsv_sort, SORT_ASC, $data);
	}

	public function get_dominant_color($image)
	{
		if (!is_file($image)) {
			$image = DIR_IMAGE . $image;
		}

		if (!is_file($image)) {
			return array(
				'r' => 0,
				'g' => 0,
				'b' => 0
			);
		}

		$ext = pathinfo($image, PATHINFO_EXTENSION);

		$img = null;
		switch (strtolower($ext)) {
			case "png":
				$img = @imagecreatefrompng($image);
				break;
			default:
				$img = @imagecreatefromjpeg($image);
				break;
		}

		$img_width  = imagesx($img);
		$img_height = imagesy($img);

		$num_pixels = $img_width * $img_height;

		//Build Histogram
		$colors = array(
			'r' => 0,
			'g' => 0,
			'b' => 0
		);

		for ($x = 0; $x < $img_width; $x++) {
			for ($y = 0; $y < $img_height; $y++) {
				$rgb = imagecolorat($img, $x, $y);

				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				//ignore boring gray / white / black colors
				if ($r === $g && $g === $b) {
					continue;
				}

				$colors['r'] += $r;
				$colors['g'] += $g;
				$colors['b'] += $b;
			}
		}

		$total = $colors['r'] + $colors['g'] + $colors['b'];

		if (!$total) {
			$total = 1;
		}

		foreach ($colors as $key => $c) {
			$colors[$key] = 100 * ($c / $total);
		}

		return $colors;
	}

	public function RGB_to_HSV($R, $G, $B) // RGB values:	0-255, 0-255, 0-255
	{ // HSV values:	0-360, 0-100, 0-100
		// Convert the RGB byte-values to percentages
		$R = ($R / 255);
		$G = ($G / 255);
		$B = ($B / 255);

		// Calculate a few basic values, the maximum value of R,G,B, the
		//	minimum value, and the difference of the two (chroma).
		$maxRGB = max($R, $G, $B);
		$minRGB = min($R, $G, $B);
		$chroma = $maxRGB - $minRGB;

		// Value (also called Brightness) is the easiest component to calculate,
		//	and is simply the highest value among the R,G,B components.
		// We multiply by 100 to turn the decimal into a readable percent value.
		$computedV = 100 * $maxRGB;

		// Special case if hueless (equal parts RGB make black, white, or grays)
		// Note that Hue is technically undefined when chroma is zero, as
		//	attempting to calculate it would cause division by zero (see
		//	below), so most applications simply substitute a Hue of zero.
		// Saturation will always be zero in this case, see below for details.
		if ($chroma == 0) {
			return array(
				'H' => 0,
				'S' => 0,
				'V' => $computedV
			);
		}

		// Saturation is also simple to compute, and is simply the chroma
		//	over the Value (or Brightness)
		// Again, multiplied by 100 to get a percentage.
		$computedS = 100 * ($chroma / $maxRGB);

		// Calculate Hue component
		// Hue is calculated on the "chromacity plane", which is represented
		//	as a 2D hexagon, divided into six 60 degree sectors. We calculate
		//	the bisecting angle as a value 0 <= x < 6, that represents which
		//	portion of which sector the line falls on.
		if ($R == $minRGB) {
			$h = 3 - (($G - $B) / $chroma);
		} elseif ($B == $minRGB) {
			$h = 1 - (($R - $G) / $chroma);
		} else {
			$h = 5 - (($B - $R) / $chroma);
		}

		// After we have the sector position, we multiply it by the size of
		//	each sector's arc (60 degrees) to obtain the angle in degrees.
		$computedH = 60 * $h;

		return array(
			'H' => $computedH,
			'S' => $computedS,
			'V' => $computedV
		);
	}

	public function colorReplace($color, $replace, $exact = true, $convert = true, $colors = 32000000000)
	{
		if (imageistruecolor($this->image)) {
			if ($convert) {
				imagetruecolortopalette($this->image, false, $colors);
				$exact = false;
			} else {
				trigger_error($this->error['convert'] = "Color replace for truecolor images not yet implemented.");

				return false;
			}
		}

		if (is_string($color)) {
			$color = $this->hex2rgb($color);
		}

		if ($exact) {
			$color_index = imagecolorexact($this->image, $color[0], $color[1], $color[2]);
		} else {
			$color_index = imagecolorresolve($this->image, $color[0], $color[1], $color[2]);
		}

		if ($color_index < 0) {
			$this->error['color'] = _l("Unable to find the correct color in the image.");

			return false;
		}

		if (is_string($replace)) {
			$replace = $this->hex2rgb($replace);
		}

		imagecolorset($this->image, $color_index, $replace[0], $replace[1], $replace[2]);

		return true;
	}

	public function getPixelColor($image, $x, $y, $format = 'hex')
	{
		$rgb = imagecolorat($image, $x, $y);
		$r   = ($rgb >> 16) & 0xFF;
		$g   = ($rgb >> 8) & 0xFF;
		$b   = $rgb & 0xFF;

		if ($format === 'hex') {
			return '#' . dechex($r) . dechex($g) . dechex($b);
		}

		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b
		);
	}

	public function getBoxColor($start_x, $start_y, $width, $height, $format = 'hex')
	{
		$end_x = $start_x + $width;
		$end_y = $start_y + $height;

		$r = $g = $b = 0;

		for ($x = $start_x; $x < $end_x; $x++) {
			for ($y = $start_y; $y < $end_y; $y++) {
				$rgb = imagecolorat($this->image, $x, $y);
				$r += ($rgb >> 16) & 0xFF;
				$g += ($rgb >> 8) & 0xFF;
				$b += $rgb & 0xFF;
			}
		}

		$total = $width * $height;

		$r /= $total;
		$g /= $total;
		$b /= $total;

		if ($format === 'hex') {
			return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
		}

		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b
		);
	}

	public function hex6to3($hex)
	{
		preg_match("|#([\\da-f]{2})([\\da-f]{2})([\\da-f]{2})|", $hex, $match);

		array_shift($match);

		//Not a 6-digit hex number
		if (count($match) !== 3) {
			return $hex;
		}

		foreach ($match as &$m) {
			$n = hexdec($m);
			$m = dechex(intval($n / 17) + (($n % 17 < 8) ? 0 : 1));
		}
		unset($m);

		return "#" . implode("", $match);
	}

	public function mosaic($rows = 60, $cols = null, $colors = '32bit', $inline_size = false)
	{
		$width  = $this->width;
		$height = $this->height;

		$y_offset = floor($height / $rows);

		if (!$cols) {
			$cols = floor(($width / $height) * $rows);
		}

		$x_offset = floor($width / $cols);

		$mosaic = '';

		for ($y = 0; $y < $height - $y_offset; $y += $y_offset) {
			$line = '';

			for ($x = 0; $x < $width - $x_offset; $x += $x_offset) {
				$hex = $this->getBoxColor($x, $y, $x_offset, $y_offset);

				//Not recommended for emails!
				if ($colors === '16bit') {
					$hex = $this->hex6to3($hex);
				}

				if ($inline_size) {
					$line .= "<td width=\"$x_offset\" height=\"$y_offset\" bgcolor=\"$hex\">&nbsp;</td>";
				} else {
					$line .= "<td bgcolor=\"$hex\">&nbsp;</td>\n";
				}
			}

			$mosaic .= "<tr>$line</tr>";
		}

		$bytes = strlen($mosaic);

		$mosaic = "<table class=\"mosaic\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">$mosaic</table>";

		if (!$inline_size) {
			$mosaic = "<style type=\"text/css\">.mosaic td{font-size: 1px; line-height:1px; width: " . $x_offset . "px; height: " . $y_offset . "px}</style>" . $mosaic;
		}

		return $mosaic;
	}

	public function createSprite($css_file, $images, $nx = 3, $prefix = 'si-')
	{
		set_time_limit(300);

		$sheets = array();

		if ($nx <= 1) {
			$nx = 1;
		}

		$file_name = 'sprite/amplo-' . count($images) . '-' . md5(serialize($images));

		for ($n = $nx; $n > 0; $n--) {
			$sheets[$n] = array(
				'file'    => DIR_IMAGE . $file_name . '@' . $n . 'x.png',
				'url'     => URL_IMAGE . $file_name . '@' . $n . 'x.png',
				'width'   => 0,
				'height'  => 0,
				'y_index' => 0,
			);
		}

		if (!_is_writable(dirname($sheets[1]['file']))) {
			trigger_error(_l("Sprite Image file was not writable: %s", $sheets[1]['file']));

			return false;
		}

		$sprites = array();

		foreach ($images as &$img) {
			list($width, $height) = getimagesize($img);

			//Give this sprite a unique name
			$sprite_name = $prefix . slug(pathinfo($img, PATHINFO_FILENAME), '-');

			$count     = 0;
			$orig_name = $sprite_name;

			while (isset($sprites[$sprite_name])) {
				$sprite_name = $orig_name . '-' . $count++;
			}

			$sprite = array(
				'name'   => $sprite_name,
				'width'  => $width,
				'height' => $height,
				'image'  => $img,
			);

			for ($n = $nx; $n > 0; $n--) {
				$sprite['width_' . $n]  = round($width * $n / $nx);
				$sprite['height_' . $n] = round($height * $n / $nx);

				$sheets[$n]['width'] = max($sheets[$n]['width'], $sprite['width_' . $n]);
				$sheets[$n]['height'] += $sprite['height_' . $n] + (3 * $n);
			}

			$sprites[$sprite_name] = $sprite;
		}


		//Build Sprite PNG images
		$style   = '';
		$url_set = '';

		for ($n = $nx; $n > 0; $n--) {
			$sheet = imagecreatetruecolor($sheets[$n]['width'], $sheets[$n]['height']);

			$transparency = imagecolorallocatealpha($sheet, 0, 0, 0, 127);
			imagealphablending($sheet, false);
			imagefilledrectangle($sheet, 0, 0, $sheets[$n]['width'], $sheets[$n]['height'], $transparency);
			imagealphablending($sheet, true);
			imagesavealpha($sheet, true);

			$sheets[$n]['image'] = $sheet;

			foreach ($sprites as $sprite) {
				$image = @imagecreatefrompng($sprite['image']);

				if ($image) {
					$name = $sprite['name'];
					$y    = '-' . $sheets[1]['y_index'] . 'px';
					$w    = $sprite['width_1'] . 'px';
					$h    = $sprite['height_1'] . 'px';

					$style .= <<<CSS
.$name {
	background-position: 0 $y;
	width: $w;
	height: $h;
}\n\n
CSS;

					imagecopyresampled($sheets[$n]['image'], $image, 0, $sheets[$n]['y_index'], 0, 0, $sprite['width_' . $n], $sprite['height_' . $n], $sprite['width'], $sprite['height']);
					$sheets[$n]['y_index'] += $sprite['height_' . $n] + (3 * $n);

					imagedestroy($image);
				}
			}

			//Save to file and destroy image
			imagepng($sheets[$n]['image'], $sheets[$n]['file'], 9);
			imagedestroy($sheets[$n]['image']);

			$url_set .= ($url_set ? ', ' : '') . "url({$sheets[$n]['url']}) {$n}x";
		}

		$url = $sheets[1]['url'];

		$style = <<<CSS
.amp-sprite {
  background-image: url($url);
  background-image: -webkit-image-set($url_set);
  background-image: -moz-image-set($url_set);
  background-image: -ms-image-set($url_set);
  background-image: -o-image-set($url_set);
}
$style
CSS;

		file_put_contents($css_file, $style);
		$this->plugin->gitIgnore($css_file);

		return $sheets;
	}
}
