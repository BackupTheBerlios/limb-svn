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
@define('HTTP_SHARED_DIR', LIMB_DIR . '/shared/');
@define('MEDIA_DIR', VAR_DIR . '/media/');
@define('DAY_CACHE', 24*60*60);

class DisplayRequestedImageCommand// implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $response =& $toolkit->getResponse();
    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');

    $datasource->setRequest($request);

    if(!$object_data = $datasource->fetch())
      return LIMB_STATUS_ERROR;

    $variation = $this->_getRequestedVariation($request);

    if(!isset($object_data['variations'][$variation]))
    {
      if($variation == 'original')
        return LIMB_STATUS_ERROR;
      else
      {
        $response->header("Content-type: image/gif");
        $response->readfile(HTTP_SHARED_DIR . 'images/1x1.gif');
        $response->commit();//for speed
        return;//for tests, fix!!!
      }
    }

    $image = $object_data['variations'][$variation];

    if(!file_exists(MEDIA_DIR. $image['media_id'] . '.media'))
    {
      if($variation == 'original')
        return LIMB_STATUS_ERROR;
      else
      {
        $response->header("HTTP/1.1 404 Not found");
        $response->commit();//for speed
      }
      return;//for tests, fix!!!
    }

    $http_cache = $this->_getHttpCache();
    $http_cache->setLastModifiedTime($object_data['modified_date']);
    $http_cache->setCacheTime(DAY_CACHE);

    if(!$http_cache->checkAndWrite($response))
    {
      $response->header("Content-Disposition: filename={$image['file_name']}");
      $response->readfile(MEDIA_DIR. $image['media_id'] .'.media');
    }

    $response->header("Content-type: {$image['mime_type']}");

    if($variation == 'original')
      return LIMB_STATUS_OK;
    else
      $response->commit();//for speed
    return;//for tests, fix!!!
  }

  function _getHttpCache()
  {
    include_once(LIMB_DIR . '/class/core/request/HttpCache.class.php');
    return new HttpCache();
  }

  function _getRequestedVariation($request)
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('image_variations.ini');

    $variation = 'thumbnail';
    $image_variations = $ini->getAll();

    foreach($image_variations as $key => $value)
    {
      if ($request->hasAttribute($key))
      {
        $variation = $key;
        break;
      }
    }

    return $variation;
  }
}

?>