<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
define('FLIP_HORIZONTAL', 1);
define('FLIP_VERTICAL', 2);

class image_library
{
  var $input_file = null;
  var $input_file_type = null;
  var $output_file = null;
  var $output_file_type = null;
  var $read_types = array();
  var $create_types = array();
  var $library_installed = false;

  function image_library()
  {
  }

  function is_library_installed()
  {
    return $this->library_installed;
  }

  function set_input_file($file_name, $type)
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

  function set_output_file($file_name, &$type)
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

  function get_image_type($str)
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

  function get_mime_type($str)
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

  function is_type_read_supported($type)
  {
    return in_array(strtoupper($type), $this->read_types);
  }

  function is_type_create_supported($type)
  {
    return in_array(strtoupper($type), $this->create_types);
  }

  function get_read_supported_types()
  {
    return $this->read_types;
  }

  function get_create_supported_types()
  {
    return $this->create_types;
  }

  function get_dst_dimensions($src_width, $src_height, $params)
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

  function flip()
  {
  }

  function cut()
  {
  }

  function resize()
  {
  }

  function rotate()
  {
  }

  function _hex_color_to_X11($color)
  {
    return preg_replace('/(\d{2})(\d{2})(\d{2})/', 'rgb:$1/$2/$3', $color);
  }
}

?>