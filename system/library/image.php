<?php

class Image extends Library
{
	private $file;
	private $image;
	private $info;
	private $dir_mode;
	//This is a hack to unregister the shutdown function
	private $safe_shutdown;
	private $shutdown_file = '';

	public function __construct($file = null)
	{
		parent::__construct();

		if ($file) {
			$this->set_image($file);
		}

		$this->dir_mode = option('config_image_dir_mode');
	}

	public function get($filename)
	{
		$filename = str_replace('\\', '/', $filename);

		if (is_file(DIR_IMAGE . $filename)) {
			return URL_IMAGE . $filename;
		} elseif (is_file($filename)) {
			$url = URL_SITE;
			return str_replace(DIR_SITE, $url, $filename);
		}

		return $filename;
	}

	public function info($key = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}

		return $this->info;
	}

	public function set_image($image)
	{
		$file = is_file($image) ? $image : DIR_IMAGE . $image;

		if (is_file($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
				'width'  => $info[0],
				'height' => $info[1],
				'bits'   => $info['bits'],
				'mime'   => $info['mime']
			);

			$this->image = $this->create($file);
		} else {
			$this->error_log->write("Error: Could not load image $file!");
		}
	}

	public function unset_image()
	{
		imagedestroy($this->image);
		$this->image = null;
		$this->file  = null;
		$this->info  = null;
	}

	private function create($image)
	{
		$mime = $this->info['mime'];

		if (!$this->register_safe_shutdown($image)) {
			$this->message->add('warning', "Image Create failed on $image. The file size (" . $this->tool->bytes2str(filesize($image)) . ") is too large for your server.");
			redirect($this->url->here());
		}

		//increase the maximum memory limit from the settings
		$max = isset($this->config) ? option('config_image_max_mem') : '2G';
		ini_set('memory_limit', $max);

		if ($mime == 'image/gif') {
			if (function_exists('imagecreatefromgif')) {
				$image = @imagecreatefromgif($image);
			}
		} elseif ($mime == 'image/png') {
			if (function_exists('imagecreatefrompng')) {
				$image = @imagecreatefrompng($image);
			}
		} elseif ($mime == 'image/jpeg') {
			if (function_exists('imagecreatefromjpeg')) {
				$image = @imagecreatefromjpeg($image);
			}
		}

		$this->unregister_safe_shutdown();

		return $image;
	}

	public function save($file, $quality = 90)
	{
		if (!_is_writable(dirname($file))) {
			$this->error_log->write(__METHOD__ . "(): Failed to save image file because directory was not writable: %s!", $file);
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
					$success = imagepng($this->image, $file, 0);
					break;
			}

			$this->unset_image();
		}

		if (!$success) {
			$this->error_log->write(__METHOD__ . "(): Failed to save image file %s as %s!", $file, $extension);
		}

		return $success;
	}

	public function resize($filename, $width = 0, $height = 0, $background_color = '')
	{
		if (!is_file(DIR_IMAGE . $filename)) {
			//If the file exists but not in the image directory, move it to the image directory and continue
			if (is_string($filename) && is_file($filename)) {
				$copy_file = 'import/' . str_replace(DIR_SITE, '', $filename);

				if (!is_file(DIR_IMAGE . $copy_file)) {
					if (!_is_writable(DIR_IMAGE . dirname($copy_file))) {
						$this->error = _l("Directory was not writable: %s", $copy_file);
						return '';
					}

					copy($filename, DIR_IMAGE . $copy_file);
				}

				$filename = $copy_file;
			} else {
				return '';
			}
		}

		$old_image = DIR_IMAGE . $filename;

		//this sets the image file as the active image to modify
		$this->set_image($old_image);

		//if the image is 0 width or 0 height, do not return an image
		if (!$this->info['width'] || !$this->info['height']) {
			return '';
		}

		//If width and height are 0, we do not scale the image
		if ($width <= 0 && $height <= 0) {
			return $this->get($filename);
		}

		//Constrain Width
		if ($width <= 0) {
			$scale_y = $height / $this->info['height'];
			$scale_x = $scale_y;
		} //Constrain Height
		elseif ($height <= 0) {
			$scale_x = $width / $this->info['width'];
			$scale_y = $scale_x;
		} //Resize
		else {
			$scale_x = $width / $this->info['width'];
			$scale_y = $height / $this->info['height'];
		}

		//if the image is the correct size we do not need to do anything
		if ($scale_x === 1 && $scale_y === 1) {
			return $this->get($filename);
		}

		$new_width  = (int)($this->info['width'] * $scale_x);
		$new_height = (int)($this->info['height'] * $scale_y);

		//Resolve image type and new image name
		$info = pathinfo($filename);

		//if the background is transparent and the mime type is not png or gif, change to png
		$allowed_exts = array(
			'png',
			'gif',
			'jpg'
		);

		if (!$background_color && !in_array(strtolower($info['extension']), $allowed_exts)) {
			$extension = 'png';
		} else {
			$extension = $info['extension'];
		}

		$new_image_path = 'cache/' . $info['filename'] . '-' . $new_width . 'x' . $new_height . '.' . $extension;
		$new_image_file = DIR_IMAGE . $new_image_path;

		//if image is already in cache, return cached version
		if (!is_file($new_image_file) || (_filemtime($old_image) > _filemtime($new_image_file))) {
			//Render new image
			$new_image = imagecreatetruecolor((int)$new_width, (int)$new_height);

			if (!$background_color || (isset($this->info['mime']) && $this->info['mime'] == 'image/png')) {
				imagealphablending($new_image, false);
				imagesavealpha($new_image, true);
				$background = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
				imagecolortransparent($new_image, $background);
			} else {
				if ($background_color) {
					$background = $this->heximagecolorallocate($background_color);
				} else {
					$background = imagecolorallocate($new_image, 255, 255, 255);
				}
			}

			imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $background);

			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
			imagedestroy($this->image);

			$this->image = $new_image;

			$this->save($new_image_file);
		}

		return $this->get($new_image_path);
	}

	public function ico($source, $destination = null, $sizes = null)
	{
		require( DIR_RESOURCES . 'phpico/class-php-ico.php' );

		if (is_file(DIR_IMAGE . $source)) {
			$source = DIR_IMAGE . $source;
		} elseif (!is_file($source)) {
			$this->error = _l("Invalid Source File %s", $source);
			return false;
		}

		if (!$destination) {
			$destination = DIR_IMAGE . 'icon/' . pathinfo($source, PATHINFO_FILENAME) . '.ico';
		}

		if (!_is_writable(dirname($destination))) {
			$this->error = _l("The Destination directory was not writable: %s", $destination);
			return false;
		}

		if (!$sizes) {
			$sizes = array(
				array(16,16),
			   array(32,32),
			   array(48,48),
			   array(64,64),
			);
		}

		$ico_lib = new PHP_ICO( $source, $sizes);
		$ico_lib->save_ico( $destination );

		return str_replace(DIR_IMAGE, URL_IMAGE, $destination);
	}

	public function watermark($file, $position = 'bottomright')
	{
		$watermark = $this->create($file);

		$watermark_width  = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		switch ($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
			case 'bottomright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
		}

		imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, 120, 40);

		imagedestroy($watermark);
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y)
	{
		$image_old   = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width']  = $bottom_x - $top_x;
		$this->info['height'] = $bottom_y - $top_y;
	}

	public function rotate($degree, $color = 'FFFFFF')
	{
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->info['width']  = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
	}

	private function filter($filter)
	{
		imagefilter($this->image, $filter);
	}

	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000')
	{
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	private function merge($file, $x = 0, $y = 0, $opacity = 100)
	{
		$merge = $this->create($file);

		$merge_width  = imagesx($image);
		$merge_height = imagesy($image);

		imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
	}

	public function heximagecolorallocate($hex)
	{
		if (!$hex || strpos($hex, '#') !== 0 || strlen($hex) !== 7 || preg_match("/[^A-F0-9]/i", substr($hex, 1)) > 0) {
			trigger_error("ERROR in Draw library: set_text_color(\$color): \$color must be in hex format #FFFFFF");
			return;
		}

		return imagecolorallocate($this->image, (int)hexdec($hex[1] . $hex[2]), (int)hexdec($hex[3] . $hex[4]), (int)hexdec($hex[5] . $hex[6]));
	}

	private function html2rgb($color)
	{
		if ($color[0] == '#') {
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
	 * @param &$data - an array with a filepath to an image file under a key
	 * @param $img_key - the key in the $data array that points to the image file path
	 * @param $type - the method to sort by. 'HSV' is default.
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

		if (!$this->register_safe_shutdown($image)) {
			list($width, $height) = getimagesize($image);
			$image = $this->resize($image, $width / 2, $height / 2);
			trigger_error("Safe shutdown limit exceeded! Cannot process image");
			$this->unregister_safe_shutdown();
			return array(
				'r' => 0,
				'g' => 0,
				'b' => 0
			);
		}

		$img = null;
		switch (strtolower($ext)) {
			case "png":
				$img = @imagecreatefrompng($image);
				break;
			default:
				$img = @imagecreatefromjpeg($image);
				break;
		}

		$this->unregister_safe_shutdown();

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

	public function register_safe_shutdown($file = '')
	{
		//A hack to unregister the shutdown function
		$this->safe_shutdown = memory_get_peak_usage(true);
		$this->shutdown_file = $file;

		if ($this->safe_shutdown === null) {
			$shutdown = function ($image_class) {
				if ($this->safe_shutdown) {
					if ($this->shutdown_file) {
						$_SESSION['image_safe_max_file_size'] = filesize($this->shutdown_file);
						$size                                 = $this->tool->bytes2str(filesize($this->shutdown_file));
					} else {
						$size = 0;
					}
					$max      = ini_get('memory_limit');
					$max_mem  = $this->tool->bytes2str(memory_get_peak_usage(true));
					$safe_mem = $this->tool->bytes2str($this->safe_shutdown);

					trigger_error("Server Max Memory limit reached: $max_mem / $max. Safe shutdown enabled at $safe_mem. Image File Size: $size");
				}
			};

			register_shutdown_function($shutdown, $this);
		}

		if ($file && !empty($_SESSION['image_safe_max_file_size']) && $_SESSION['image_safe_max_file_size'] <= filesize($file)) {
			return false;
		}

		return true;
	}

	public function unregister_safe_shutdown()
	{
		$this->safe_shutdown = false;
		$this->shutdown_file = '';
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

	public function getBoxColor($image, $start_x, $start_y, $width, $height, $format = 'hex')
	{
		$end_x = $start_x + $width;
		$end_y = $start_y + $height;

		$r = $g = $b = 0;

		for ($x = $start_x; $x < $end_x; $x++) {
			for ($y = $start_y; $y < $end_y; $y++) {
				$rgb = imagecolorat($image, $x, $y);
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

	public function mosaic($image, $rows = 60, $cols = null, $colors = '32bit', $inline_size = false)
	{
		$this->set_image($image);

		$width  = $this->info['width'];
		$height = $this->info['height'];

		$y_offset = floor($height / $rows);

		if (!$cols) {
			$cols = floor(($width / $height) * $rows);
		}

		$x_offset = floor($width / $cols);

		$mosaic = '';

		for ($y = 0; $y < $height - $y_offset; $y += $y_offset) {
			$line = '';

			for ($x = 0; $x < $width - $x_offset; $x += $x_offset) {
				$hex = $this->getBoxColor($this->image, $x, $y, $x_offset, $y_offset);

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
}
