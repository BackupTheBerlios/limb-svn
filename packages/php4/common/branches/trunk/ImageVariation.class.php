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
require_once(LIMB_DIR . '/class/core/DomainObject.class.php');

class ImageVariation extends DomainObject
{
  protected $_media_manager;
  protected $_image_library;

  protected function _getMediaManager()
  {
    if($this->_media_manager)
      return $this->_media_manager;

    include_once(dirname(__FILE__) . '/MediaManager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;
  }

  protected function _getImageLibrary()
  {
    if($this->_image_library)
      return $this->_image_library;

    include_once(LIMB_DIR . '/class/lib/image/ImageFactory.class.php');
    $this->_image_library = ImageFactory :: create();

    return $this->_image_library;
  }

  public function getMediaFile()
  {
    return $this->_getMediaManager()->getMediaFilePath($this->getMediaFileId());
  }

  public function getMediaFileType()
  {
    return $this->_getImageLibrary()->getImageType($this->getMimeType());
  }

  public function loadFromFile($file)
  {
    $media_file_id = $this->_getMediaManager()->store($file);
    $this->setMediaFileId($media_file_id);

    $this->_updateDimensionsUsingFile($file);
  }

  //for mocking, refactor and use fs?
  protected function _generateTempFile()
  {
    return tempnam(VAR_DIR, 'p');
  }

  //for mocking, refactor and use fs?
  protected function _unlinkTempFile($temp_file)
  {
    unlink($temp_file);
  }

  public function resize($max_size)
  {
    $image_library = $this->_getImageLibrary();
    $media_manager = $this->_getMediaManager();

    $media_file_id = $this->getMediaFileId();

    $input_file = $media_manager->getMediaFilePath($media_file_id);
    $output_file = $this->_generateTempFile();

    $input_file_type = $image_library->getImageType($this->getMimeType());
    $output_file_type = $image_library->fallBackToAnySupportedType($input_file_type);

    try
    {
      $image_library->setInputFile($input_file);
      $image_library->setInputType($input_file_type);

      $image_library->setOutputFile($output_file);
      $image_library->setOutputType($output_file_type);
      $image_library->resize(array('max_dimension' => $max_size));//ugly!!!
      $image_library->commit();

      $this->_updateDimensionsUsingFile($output_file);
      $media_file_id = $media_manager->store($output_file);

      $this->setMediaFileId($media_file_id);
    }
    catch(Exception $e)
    {
      if(file_exists($output_file))
        $this->_unlinkTempFile($output_file);
      throw $e;
    }

    $this->_unlinkTempFile($output_file);
  }

  protected function _updateDimensionsUsingFile($file)
  {
    $size = getimagesize($file);
    $this->setWidth($size[0]);
    $this->setHeight($size[1]);
  }

  public function getEtag()
  {
    return $this->get('etag');
  }

  public function setEtag($etag)
  {
    $this->set('etag', $etag);
  }

  public function getName()
  {
    return $this->get('name');
  }

  public function setName($name)
  {
    $this->set('name', $name);
  }

  public function getWidth()
  {
    return (int)$this->get('width');
  }

  public function setWidth($width)
  {
    $this->set('width', (int)$width);
  }

  public function getHeight()
  {
    return (int)$this->get('height');
  }

  public function setHeight($height)
  {
    $this->set('height', (int)$height);
  }

  public function getMimeType()
  {
    return $this->get('mime_type');
  }

  public function setMimeType($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  public function getFileName()
  {
    return $this->get('file_name');
  }

  public function setFileName($file_name)
  {
    $this->set('file_name', $file_name);
  }

  public function getImageId()
  {
    return (int)$this->get('image_id');
  }

  public function setImageId($image_id)
  {
    $this->set('image_id', (int)$image_id);
  }

  public function getMediaFileId()
  {
    return $this->get('media_file_id');
  }

  public function setMediaFileId($media_file_id)
  {
    $this->set('media_file_id', $media_file_id);
  }

  public function getMediaId()
  {
    return (int)$this->get('media_id');
  }

  public function setMediaId($media_id)
  {
    $this->set('media_id', (int)$media_id);
  }

  public function getSize()
  {
    return (int)$this->get('size');
  }

  public function setSize($size)
  {
    $this->set('size', (int)$size);
  }
}

?>
