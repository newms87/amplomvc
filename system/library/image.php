<?php
class Image {
    private $file;
    private $image;
    private $info;
	 private $dir_mode;
		
	public function __construct($registry, $file = null) {
		if($file) $this->set_image($file);
		
		$this->dir_mode = $registry->get('config')->get('config_image_dir_mode');
	}
	
	public function get($filename){
		if(!is_file(DIR_IMAGE . $filename)) return '';
		
		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			return HTTPS_IMAGE . $filename;
		} else {
			return HTTP_IMAGE . $filename;
		}
	}
	
	public function set_image($file){
		if (is_file($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
            	'width'  => $info[0],
            	'height' => $info[1],
            	'bits'   => $info['bits'],
            	'mime'   => $info['mime']
        	);
			
			//increase the maximum memory limit from the settings
         $max = isset($this->config)?$this->config->get('config_image_max_mem'):'2G';
         ini_set('memory_limit',$max);
         
        	$this->image = $this->create($file);
    	} else {
    		$this->log->write("Error: Could not load image $file!");
    	}
	}
	
	public function unset_image(){
		imagedestroy($this->image);
		$this->image = null;
		$this->file = null;
		$this->info = null;
	}
	
	private function create($image) {
		$mime = $this->info['mime'];
		
		if ($mime == 'image/gif') {
			if(function_exists('imagecreatefromgif'))
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			if(function_exists('imagecreatefrompng'))
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			if(function_exists('imagecreatefromjpeg'))
			return imagecreatefromjpeg($image);
		}
    }	
	
    public function save($file, $quality = 90) {
    	//make the image cache directory if it does not exist
		$file_dir = dirname($file);
       
		if (!is_dir($file_dir)) {
		   $mode = octdec($this->dir_mode);
			@mkdir($file_dir, $mode, true);
         chmod($file_dir, $mode);
		}
		
    	$info = pathinfo($file);
       
		$extension = strtolower($info['extension']);
   		
		$success = false;
		
		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				$success = imagejpeg($this->image, $file, $quality);
			} elseif($extension == 'png') {
				$success = imagepng($this->image, $file, 0);
			} elseif($extension == 'gif') {
				$success = imagegif($this->image, $file);
			}
			
			$this->unset_image();
		}
		
		if(!$success){
			trigger_error("Image::save(): Failed to save image file $file!");
		}
    }	    
	
    public function resize($filename, $width = 0, $height = 0, $background_color = '') {
    	if (!is_file(DIR_IMAGE . $filename)) {
			return '';
		}
		
		$info = pathinfo($filename);
		
		//if the background is transparent and the mime type is not png or gif, change to png
      if(!$background_color && !in_array(strtolower($info['extension']), array('png','gif'))){
         $extension = 'png';
      }
      else{
         $extension = $info['extension'];
      }
		
		$old_image = DIR_IMAGE . $filename;
		$new_image_path = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		$new_image = DIR_IMAGE . $new_image_path;
		
		//if image is already in cache, return cached version
		if (is_file($new_image) && (filemtime($old_image) < filemtime($new_image))) {
			return $this->get($new_image_path);
		}
		
		//this sets the image file as the active image to modify
		$this->set_image($old_image);
		
		//if the image height is not set do not return an image
		if (!$this->info['width'] || !$this->info['height'] || !$width || !$height) {
			return '';
		}
		
		$scale_x = $width / $this->info['width'];
		$scale_y = $height / $this->info['height'];
		
		//if the image is the correct size we do not need to do anything
		if ($scale_x === 1 && $scale_y === 1) {
			return $this->get($filename);
		}
		
		$new_width = (int)($this->info['width'] * $scale_x);
		$new_height = (int)($this->info['height'] * $scale_y);
    	$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);
        		        
    	$image_old = $this->image;
		
		$this->image = imagecreatetruecolor((int)$width, (int)$height);
			
		if (!$background_color || (isset($this->info['mime']) && $this->info['mime'] == 'image/png')) {		
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
		   if($background_color){
		      $background = $this->heximagecolorallocate($background_color);
         }
         else{
			   $background = imagecolorallocate($this->image, 255, 255, 255);
         }
		}
		 
		 imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
	
       imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
       imagedestroy($image_old);
           
       $this->info['width']  = $width;
       $this->info['height'] = $height;
		 
		 $this->save($new_image);
		 
		 return $this->get($new_image_path);
    }
    
    public function watermark($file, $position = 'bottomright') {
        $watermark = $this->create($file);
        
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        
        switch($position) {
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
    
    public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
        $image_old = $this->image;
        $this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);
        
        imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);
        
        $this->info['width'] = $bottom_x - $top_x;
        $this->info['height'] = $bottom_y - $top_y;
    }
    
    public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);
		
        $this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
        
		$this->info['width'] = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
    }
	    
    private function filter($filter) {
        imagefilter($this->image, $filter);
    }
            
    private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);
        
		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
    }
    
    private function merge($file, $x = 0, $y = 0, $opacity = 100) {
        $merge = $this->create($file);

        $merge_width = imagesx($image);
        $merge_height = imagesy($image);
		        
        imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
    }
   
   public function heximagecolorallocate($hex){
      if(!$hex || strpos($hex, '#') !== 0 || strlen($hex) !== 7 || preg_match("/[^A-F0-9]/i",substr($hex,1)) > 0){
         trigger_error("ERROR in Draw library: set_text_color(\$color): \$color must be in hex format #FFFFFF");
         return;
      }
      
      return imagecolorallocate($this->image, (int)hexdec($hex[1].$hex[2]), (int)hexdec($hex[3].$hex[4]), (int)hexdec($hex[5].$hex[6]));
   }
   
	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}
		
		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);   
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);    
		} else {
			return false;
		}
		
		$r = hexdec($r); 
		$g = hexdec($g); 
		$b = hexdec($b);    
		
		return array($r, $g, $b);
	}	
}