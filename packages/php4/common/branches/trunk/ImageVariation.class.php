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
  var $_media_manager;
  var $_image_library;

  function _getMediaManager()
  {
    if($this->_media_manager)
      return $this->_media_manager;

    include_once(dirname(__FILE__) . '/MediaManager.class.php');
    $this->_media_manager = new MediaManager();

    return $this->_media_manager;
  }

  function _getImageLibrary()
  {
    if($this->_image_library)
      return $this->_image_library;

    include_once(LIMB_DIR . '/class/lib/image/ImageFactory.class.php');
    $this->_image_library = ImageFactory :: create();

    return $this->_image_library;
  }

  function getMediaFile()
  {
    $mgr =& $this->_getMediaManager();
    return $mgr->getMediaFilePath($this->getMediaFileId());
  }

  function getMediaFileType()
  {
    $lib =& $this->_getImageLibrary();
    return $lib->getImageType($this->getMimeType());
  }

  function loadFromFile($file)
  {
    $mgr =& $this->_getMediaManager();
    $media_file_id = $mgr->store($file);
    $this->setMediaFileId($media_file_id);

    $this->_updateDimensionsUsingFile($file);
  }

  //for mocking, refactor and use fs?
  function _generateTempFile()
  {
    return tempnam(VAR_DIR, 'p');
  }

  //for mocking, refactor and use fs?
  function _unlinkTempFile($temp_file)
  {
    unlink($temp_file);
  }

  function resize($max_size)
  {
    $image_library =& $this->_getImageLibrary();
    $media_manager =& $this->_getMediaManager();

    $media_file_id = $this->getMediaFileId();

    $input_file = $media_manager->getMediaFilePath($media_file_id);
    $output_file = $this->_generateTempFile();

    $input_file_type = $image_library->getImageType($this->getMimeType());
    $output_file_type = $image_library->fallBackToAnySupportedType($input_file_type);

    $image_library->setInputFile($input_file);
    $image_library->setInputType($input_file_type);

    $image_library->setOutputFile($output_file);
    $image_library->setOutputType($output_file_type);
    $image_library->resize(array('max_dimension' => $max_size));//ugly!!!

    if(Limb :: isError($e = $image_library->commit()))//even more ugly :(
    {
      if(file_exists($output_file))
        $this->_unlinkTempFile($output_file);
      return $e;
    }

    $this->_updateDimensionsUsingFile($output_file);
    $media_file_id = $media_manager->store($output_file);

    $this->setMediaFileId($media_file_id);

    $this->_unlinkTempFile($output_file);
  }

  function _updateDimensionsUsingFile($file)
  {
    $size = getimagesize($file);
    $this->setWidth($size[0]);
    $this->setHeight($size[1]);
  }

  function getEtag()
  {
    return $this->get('etag');
  }

  function setEtag($etag)
  {
    $this->set('etag', $etag);
  }

  function getName()
  {
    return $this->get('name');
  }

  function setName($name)
  {
    $this->set('name', $name);
  }

  function getWidth()
  {
    return (int)$this->get('width');
  }

  function setWidth($width)
  {
    $this->set('width', (int)$width);
  }

  function getHeight()
  {
    return (int)$this->get('height');
  }

  function setHeight($height)
  {
    $this->set('height', (int)$height);
  }

  function getMimeType()
  {
    return $this->get('mime_type');
  }

  function setMimeType($mime_type)
  {
    $this->set('mime_type', $mime_type);
  }

  function getFileName()
  {
    return $this->get('file_name');
  }

  function setFileName($file_name)
  {
    $this->set('file_name', $file_name);
  }

  function getImageId()
  {
    return (int)$this->get('image_id');
  }

  function setImageId($image_id)
  {
    $this->set('image_id', (int)$image_id);
  }

  function getMediaFileId()
  {
    return $this->get('media_file_id');
  }

  function setMediaFileId($media_file_id)
  {
    $this->set('media_file_id', $media_file_id);
  }

  function getMediaId()
  {
    return (int)$this->get('media_id');
  }

  function setMediaId($media_id)
  {
    $this->set('media_id', (int)$media_id);
  }

  function getSize()
  {
    return (int)$this->get('size');
  }

  function setSize($size)
  {
    $this->set('size', (int)$size);
  }
}

?>
