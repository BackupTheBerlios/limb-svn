<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 

abstract class image_library
{
  const FLIP_HORIZONTAL = 1;
  const FLIP_VERTICAL = 2;

	protected $input_file = null;
	protected $input_file_type = null;
	protected $output_file = null;
	protected $output_file_type = null;
	protected $read_types = array();
	protected $create_types = array();
	protected $library_installed = false;
	
	function is_library_installed()
	{
		return $this->library_installed;
	}

	public function set_input_file($file_name, $type = '')
	{
		if (!$this->library_installed)
			return false;
		
		if (!file_exists($file_name))
			return false;
		
		if (!$this->is_type_read_supported($type))
			return false;
		
		$this->input_file = $file_name;
		$this->input_file_type = $type;

		return true;
	}
	
	public function set_output_file($file_name, &$type)
	{
		if (!$this->library_installed)
			return false;
		
		if (!$this->is_type_create_supported($type))
			if (!$this->is_type_create_supported('PNG'))
				if (!$this->is_type_create_supported('JPEG'))
					return false;
				else
					$type = 'JPEG';
			else
				$type = 'PNG';
		
		$this->output_file = $file_name;
		$this->output_file_type = $type;
		
		return true;
	}
	
	public function get_image_type($str)
	{
		if (preg_match("/bmp/i", $str))
			return 'BMP';
		
		if (preg_match("/gif/i", $str))
			return 'GIF';
		
		if (preg_match("/png/i", $str))
			return 'PNG';
		
		if (preg_match("/(jpeg|jpg)/i", $str))
			return 'JPEG';
	}
	
	public function get_mime_type($str)
	{
		if (preg_match("/bmp/i", $str))
			return 'image/bmp';
		
		if (preg_match("/gif/i", $str))
			return 'image/gif';
		
		if (preg_match("/png/i", $str))
			return 'image/png';
		
		if (preg_match("/(jpeg|jpg)/i", $str))
			return 'image/jpeg';
	}
	
	public function is_type_read_supported($type)
	{
		return in_array(strtoupper($type), $this->read_types);
	}
	
	public function is_type_create_supported($type)
	{
		return in_array(strtoupper($type), $this->create_types);
	}
	
	public function get_read_supported_types()
	{
		return $this->read_types;
  }

	public function get_create_supported_types()
	{
		return $this->create_types;
  }
  
  public function get_dst_dimensions($src_width, $src_height, $params)
  {
		if (isset($params['max_dimension']))
		{
			$params['preserve_aspect_ratio'] = true;
			if ($src_width > $src_height)
				$params['width'] = $params['max_dimension'];
			else
				$params['height'] = $params['max_dimension'];
		}

		if (isset($params['scale_factor']))
		{
			$dst_width = floor($src_width * $params['scale_factor']);
			$dst_height = floor($src_height * $params['scale_factor']);
		}
		elseif(isset($params['xscale']) || isset($params['yscale']))
		{
			if (isset($params['xscale']))
				$dst_width = floor($src_width * $params['xscale']);
			else
				if(isset($params['preserve_aspect_ratio']))
					$dst_width = floor($src_width * $params['yscale']);
				else
					$dst_width = $src_width;
			
			if (isset($params['yscale']))
				$dst_height = floor($src_height * $params['yscale']);
			else
				if(isset($params['preserve_aspect_ratio']))
					$dst_height = floor($src_height * $params['xscale']);
				else
					$dst_height = $src_height;
		}
		elseif(isset($params['width']) || isset($params['height']))
		{
			if (isset($params['width']))
				$dst_width = $params['width'];
			else
				if(isset($params['preserve_aspect_ratio']))
				{
					$factor = $params['height'] / $src_height;
					$dst_width = floor($src_width * $factor);
				}
				else
					$dst_width = $src_width;
			
			if (isset($params['height']))
				$dst_height = $params['height'];
			else
				if(isset($params['preserve_aspect_ratio']))
				{
					$factor = $params['width'] / $src_width;
					$dst_height = floor($src_height * $factor);
				}
				else
					$dst_height = $src_height;
		}
		
		return array($dst_width, $dst_height);
  }
  
  abstract public function flip($params);
  
	abstract public function cut($x, $y, $w, $h, $bg_color);

	abstract public function resize($params);

	abstract public function rotate($angle, $bg_color);

	protected function _hex_color_to_X11($color)
	{
		return preg_replace('/(\d{2})(\d{2})(\d{2})/', 'rgb:$1/$2/$3', $color);
	}
}

?>