<?php
define("DRAW_AUTO_SIZE", 1);
define("DRAW_WRAP_TEXT", 2);

class Draw extends Library
{
	private $canvas_list = array();
	private $canvas;
	private $image_url;

	//Text
	private $font_path;
	private $font_file;
	private $font_color;
	private $font_size = 12;
	private $text_angle = 0;
	private $antialias = 1;

	function __construct($registry)
	{
		parent::__construct($registry);

		$this->font_path = $this->config->isAdmin() ? DIR_APPLICATION . "view/fonts/" : DIR_THEME . $this->config->get('config_theme') . '/fonts/';

		$this->set_canvas('default');
	}

	public function set_canvas($name, $width = 100, $height = 100)
	{
		if ($width < 1 || $height < 1) {
			trigger_error("Error in Draw library: set_canvas(): width and height must be greater than 0!");
			return;
		}

		if (!isset($this->canvas_list[$name])) {
			$this->canvas_list[$name] = imagecreatetruecolor($width, $height);
			$this->font_color         = imagecolorallocate($this->canvas_list[$name], 0, 0, 0);
		}

		$this->canvas = $this->canvas_list[$name];
	}

	public function set_transparent_color($color)
	{
		return imagecolortransparent($this->canvas, $this->get_hex_color($color));
	}

	public function set_background($color)
	{
		return imagefilledrectangle($this->canvas, 0, 0, imagesx($this->canvas) - 1, imagesy($this->canvas) - 1, $this->get_hex_color($color));
	}

	public function font_format($font, $size = null, $color = null, $angle = 0, $antialias = 1)
	{
		$this->font_file = $this->font_path . $font;

		$this->set_text_size($size);

		$this->set_text_color($color);

		$this->text_angle = $angle;

		$this->antialias = $antialias ? 1 : -1;
	}

	public function set_text_size($size)
	{
		if (!$size) {
			return;
		}

		$this->font_size = $size;
	}

	public function set_text_color($color)
	{
		$this->font_color = $this->get_hex_color($color);
	}

	public function write_text($text, $x = 0, $y = 0, $line_spacing = 10, $flag = null)
	{
		if (!$this->font_file) {
			trigger_error("You must call Draw::text_format() before writing text to the canvas!");
			return;
		}

		$words = explode(' ', $text);
		$lines = array();

		$max_x = imagesx($this->canvas);
		$max_y = imagesy($this->canvas);

		$line = 1;

		//caclulate the line breaks
		do {
			$new_word = array_shift($words);

			if (isset($lines[$line])) {
				$test_line = implode(' ', $lines[$line]) . $new_word;
			} else {
				$test_line = $new_word;
			}

			$position = imagettfbbox($this->font_size, $this->text_angle, $this->font_file, $test_line);

			if ($position[2] > $max_x) {
				$line++;
			}

			$lines[$line][] = $new_word;

			if (empty($words)) {
				break;
			}
		} while (true);


		foreach ($lines as $line => $text) {
			$y_offset = $y + ($line * $this->font_size) + (($line - 1) * $line_spacing);

			$position = imagettftext($this->canvas, $this->font_size, $this->text_angle, $x, $y_offset, $this->antialias * $this->font_color, $this->font_file, implode(' ', $text));
		}

		if (!$position) {
			trigger_error("Error in Draw::write_text(): an error was encountered, please check the settings and system configuration");
			return false;
		}

		return true;
	}

	public function render($file = null)
	{
		if (!$file) {
			$file = DIR_GENERATED_IMAGE . 'image_' . uniqid() . '.png';
		}

		if (!is_dir(dirname($file))) {
			$mode = octdec($this->config->get('config_image_dir_mode'));
			mkdir(dirname($file), $mode, true);
			chmod(dirname($file), $mode);
		}

		if (imagepng($this->canvas, $file)) {
			$this->image_url = str_replace(SITE_DIR, SITE_URL, $file);

			return $file;
		} else {
			trigger_error("Error in Draw library: render(): unable to write image file!");
			return false;
		}
	}

	public function get_image_url()
	{
		if (!$this->image_url) {
			trigger_error("Error in Draw Library: get_image_url(): You must call Draw::render() first!");
			return '';
		}

		return $this->image_url;
	}

	public function get_hex_color($color)
	{
		if (!$color || strpos($color, '#') !== 0 || strlen($color) !== 7 || preg_match("/[^A-F0-9]/i", substr($color, 1)) > 0) {
			trigger_error("ERROR in Draw library: set_text_color(\$color): \$color must be in hex format #FFFFFF");
			return;
		}

		return imagecolorallocate($this->canvas, (int)hexdec($color[1] . $color[2]), (int)hexdec($color[3] . $color[4]), (int)hexdec($color[5] . $color[6]));
	}
}