<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

abstract class ImageLibrary
{
  const FLIP_HORIZONTAL = 1;
  const FLIP_VERTICAL = 2;

  protected $input_file;
  protected $input_file_type;
  protected $output_file;
  protected $output_file_type;
  protected $read_types = array();
  protected $create_types = array();
  protected $library_installed = false;

  function isLibraryInstalled()
  {
    return $this->library_installed;
  }

  public function setInputFile($file_path, $file_type=null)
  {
    $this->input_file = $file_path;
    if(!is_null($file_type))
      $this->setInputType($file_type);
  }

  public function setInputType($type)
  {
    if (!$this->isTypeReadSupported($type))
      throw new Exception('type not supported');

    $this->input_file_type = $type;
  }

  public function setOutputFile($file_path, $file_type=null)
  {
    $this->output_file = $file_path;
    if(!is_null($file_type))
      $this->setOutputType($file_type);
  }

  public function setOutputType($type)
  {
    if (!$this->isTypeCreateSupported($type))
      throw new Exception('type not supported');

    $this->output_file_type = $type;
  }

  public function fallBackToAnySupportedType($type)
  {
    if ($this->isTypeCreateSupported($type))
      return $type;

    if ($this->isTypeCreateSupported('PNG'))
      return 'PNG';

    if ($this->isTypeCreateSupported('JPEG'))
      return 'JPEG';

    throw new Exception('no file type supported');
  }

  public function getImageType($str)
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

  public function getMimeType($str)
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

  public function isTypeReadSupported($type)
  {
    return in_array(strtoupper($type), $this->read_types);
  }

  public function isTypeCreateSupported($type)
  {
    return in_array(strtoupper($type), $this->create_types);
  }

  public function getReadSupportedTypes()
  {
    return $this->read_types;
  }

  public function getCreateSupportedTypes()
  {
    return $this->create_types;
  }

  public function getDstDimensions($src_width, $src_height, $params)
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
    elseif(isset($params['xscale']) ||  isset($params['yscale']))
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
    elseif(isset($params['width']) ||  isset($params['height']))
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

  abstract public function commit();

  protected function _hexColorToX11($color)
  {
    return preg_replace('/(\d{2})(\d{2})(\d{2})/', 'rgb:$1/$2/$3', $color);
  }
}

?>