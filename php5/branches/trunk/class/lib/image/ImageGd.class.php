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
require_once(LIMB_DIR . '/class/lib/image/image_library.class.php');

class image_gd extends image_library
{
  protected $image;
  protected $gd_version;
  protected $option_re = '/(<tr.*%s.*<\/tr>)/Ui';
  protected $create_func = '';
  protected $resize_func = '';
  protected $image_types = array(1 => 'GIF', 2 => 'JPEG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF(intel byte order)', 8 => 'TIFF(motorola byte order)', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');

  function __construct()
  {
    if (!extension_loaded('gd'))
    {
      $this->library_installed = false;
      return;
    }

    $this->library_installed = true;

    $this->_determine_gd_options();

    if ($this->gd_version >= 2)
    {
      $this->create_func = 'ImageCreateTrueColor';
      $this->resize_func = 'ImageCopyResampled';
    }
    else
    {
      $this->create_func = 'ImageCreate';
      $this->resize_func = 'ImageCopyResized';
    }
  }

  protected function _determine_gd_options()
  {
    if (function_exists('gd_info'))
      $this->_determine_gd_options_through_gd_info();
    else
      $this->_determine_gd_options_through_php_info();
  }

  protected function _determine_gd_options_through_gd_info()
  {
    $info = gd_info();
    $this->gd_version = $this->_get_numeric_gd_version($info['GD Version']);

    if ($info['GIF Read Support'])
      $this->read_types[] = 'GIF';

    if ($info['GIF Create Support'])
      $this->create_types[] = 'GIF';

    if ($info['JPG Support'])
      $this->read_types[] = $this->create_types[] = 'JPEG';

    if ($info['PNG Support'])
      $this->read_types[] = $this->create_types[] = 'PNG';
  }

  protected function _determine_gd_options_through_php_info()
  {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $this->gd_version = $this->_get_numeric_gd_version($this->_get_gd_option($phpinfo, 'GD Version'));

    if (strpos($this->_get_gd_option($phpinfo, 'GIF Read Support'), 'enabled') !== false)
      $this->read_types[] = 'GIF';

    if (strpos($this->_get_gd_option($phpinfo, 'GIF Create Support'), 'enabled') !== false)
      $this->cerate_types[] = 'GIF';

    if (strpos($this->_get_gd_option($phpinfo, 'JPG Support'), 'enabled') !== false)
      $this->read_types[] = $this->create_types[] = 'JPEG';

    if (strpos($this->_get_gd_option($phpinfo, 'PNG Support'), 'enabled') !== false)
      $this->read_types[] = $this->create_types[] = 'PNG';
  }

  protected function _get_gd_option($phpinfo, $option)
  {
    $re = sprintf($this->option_re, $option);
    preg_match($re, $phpinfo, $matches);
    return $matches[1];
  }

  protected function _get_numeric_gd_version($str)
  {
    $re = "/[^\.\d]*([\.\d]+)[^\.\d]*/";
    preg_match($re, $str, $matches);
    return (float)$matches[1];
  }

  protected function _get_image()
  {
    if($this->image)
      return $this->image;

    if (!$this->input_file_type)
    {
      $info = getimagesize($this->input_file);
      $this->input_file_type = $this->image_types[$info[2]];
    }

    $create_func = "ImageCreateFrom{$this->input_file_type}";
    $this->image = $create_func($this->input_file);

    return $this->image;
  }

  protected function _set_image($image)
  {
    $this->image = $image;
  }

  public function parse_hex_color($hex)
  {
    $length = strlen($hex);
    $color['red'] = hexdec(substr($hex, $length - 6, 2));
    $color['green'] = hexdec(substr($hex, $length - 4, 2));
    $color['blue'] = hexdec(substr($hex, $length - 2, 2));
    return $color;
  }

  public function reset()
  {
    $this->image = null;
  }

  public function commit()
  {
    $image = $this->_get_image();

    $create_func = "Image{$this->output_file_type}";
    $create_func($image, $this->output_file);

    $this->reset();
  }

  public function resize($params)
  {
    $image = $this->_get_image();

    $src_width = ImageSX($image);
    $src_height = ImageSY($image);

    list($dst_width, $dst_height) = $this->get_dst_dimensions($src_width, $src_height, $params);

    $create_func = $this->create_func;
    $resize_func = $this->resize_func;

    $dest_image = $create_func($dst_width, $dst_height);
    $resize_func($dest_image, $image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);

    ImageDestroy($image);

    $this->_set_image($dest_image);
  }

  public function rotate($angle, $bg_color)
  {
    $image = $this->_get_image();

    $color = $this->parse_hex_color($bg_color);
    $background_color = imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
    $this->_set_image(imagerotate($image, $angle, $background_color));
  }

  public function flip($params)
  {
    $image = $this->_get_image();

    $x = imagesx($image);
    $y = imagesy($image);

    $create_func = $this->create_func;
    $resize_func = $this->resize_func;

    $dest_image = $create_func($x, $y);

    if ($params == self :: FLIP_HORIZONTAL)
      $resize_func($dest_image, $image, 0, 0, $x, 0, $x, $y, -$x, $y);

    if ($params == self :: FLIP_VERTICAL)
      $resize_func($dest_image, $image, 0, 0, 0, $y, $x, $y, $x, -$y);

    ImageDestroy($image);
    $this->_set_image($dest_image);
  }

  public function cut($x, $y, $w, $h, $bg_color)
  {
    $image = $this->_get_image();

    $color = $this->parse_hex_color($bg_color);
    $background_color = imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
    imagefill($image, 0, 0, $background_color);

    $create_func = $this->create_func;
    $resize_func = $this->resize_func;

    $dest_image = $create_func($w, $h);
    $resize_func($dest_image, $image, 0, 0, $x, $y, $w, $h, $w, $h);

    ImageDestroy($image);
    $this->_set_image($dest_image);
  }
}
?>
