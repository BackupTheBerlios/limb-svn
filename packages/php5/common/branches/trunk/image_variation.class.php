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
require_once(LIMB_DIR . '/class/core/domain_object.class.php');

class image_variation extends domain_object
{
  protected $_media_manager;
  protected $_image_library;

  protected function _get_media_manager()
  {
    if($this->_media_manager)
      return $this->_media_manager;

    include_once(dirname(__FILE__) . '/media_manager.class.php');
    $this->_media_manager = new media_manager();

    return $this->_media_manager;
  }

  protected function _get_image_library()
  {
    if($this->_image_library)
      return $this->_image_library;

    include_once(LIMB_DIR . '/class/lib/image/image_factory.class.php');
    $this->_image_library = image_factory :: create();

    return $this->_image_library;
  }

  public function get_media_file()
  {
    return $this->_get_media_manager()->get_media_file_path($this->get_media_file_id());
  }

  public function get_media_file_type()
  {
    return $this->_get_image_library()->get_image_type($this->get_mime_type());
  }

  public function load_from_file($file)
  {
    $media_file_id = $this->_get_media_manager()->store($file);
    $this->set_media_file_id($media_file_id);

    $this->_update_dimensions_using_file($file);
  }

  //for mocking, refactor and use fs?
  protected function _generate_temp_file()
  {
    return tempnam(VAR_DIR, 'p');
  }

  //for mocking, refactor and use fs?
  protected function _unlink_temp_file($temp_file)
  {
    unlink($temp_file);
  }

  public function resize($max_size)
  {
    $image_library = $this->_get_image_library();
    $media_manager = $this->_get_media_manager();

    $media_file_id = $this->get_media_file_id();

    $input_file = $media_manager->get_media_file_path($media_file_id);
    $output_file = $this->_generate_temp_file();

    $input_file_type = $image_library->get_image_type($this->get_mime_type());
    $output_file_type = $image_library->fall_back_to_any_supported_type($input_file_type);

    try
    {
      $image_library->set_input_file($input_file);
      $image_library->set_input_type($input_file_type);

      $image_library->set_output_file($output_file);
      $image_library->set_output_type($output_file_type);
      $image_library->resize(array('max_dimension' => $max_size));//ugly!!!
      $image_library->commit();

      $this->_update_dimensions_using_file($output_file);
      $media_file_id = $media_manager->store($output_file);

      $this->set_media_file_id($media_file_id);
    }
    catch(Exception $e)
    {
      if(file_exists($output_file))
        $this->_unlink_temp_file($output_file);
      throw $e;
    }

    $this->_unlink_temp_file($output_file);
  }

  protected function _update_dimensions_using_file($file)
  {
    $size = getimagesize($file);
    $this->set_width($size[0]);
    $this->set_height($size[1]);
  }

  public function get_etag()
  {
    return $this->get('etag');
  }

  public function set_etag($etag)
  {
    $this->set('etag', $etag);
  }

  public function get_name()
  {
    return $this->get('name');
  }

  public function set_name($name)
  {
    $this->set('name', $name);
  }

  public function get_width()
  {
    return (int)$this->get('width');
  }

  public function set_width($width)
  {
    $this->set('width', (int)$width);
  }

  public function get_height()
  {
    return (int)$this->get('height');
  }

  public function set_height($height)
  {
    $this->set('height', (int)$height);
  }

  public function get_mime_type()
  {
    return $this->get('mime_type');
  }

  public function set_mime_type($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  public function get_file_name()
  {
    return $this->get('file_name');
  }

  public function set_file_name($file_name)
  {
    $this->set('file_name', $file_name);
  }

  public function get_image_id()
  {
    return (int)$this->get('image_id');
  }

  public function set_image_id($image_id)
  {
    $this->set('image_id', (int)$image_id);
  }

  public function get_media_file_id()
  {
    return $this->get('media_file_id');
  }

  public function set_media_file_id($media_file_id)
  {
    $this->set('media_file_id', $media_file_id);
  }

  public function get_media_id()
  {
    return (int)$this->get('media_id');
  }

  public function set_media_id($media_id)
  {
    $this->set('media_id', (int)$media_id);
  }

  public function get_size()
  {
    return (int)$this->get('size');
  }

  public function set_size($size)
  {
    $this->set('size', (int)$size);
  }
}

?>
