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
require_once(LIMB_DIR . '/class/cache/ImageCacheManager.class.php');

class ImageCacheFilter// implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    if(!$this->_isCachingEnabled())
    {
      $filter_chain->next();
      return;
    }

    $cache = $this->_getImageCacheManager();
    $cache->setUri($request->getUri());

    ob_start();

    $filter_chain->next();

    if($response->fileSent())
      return;

    Debug :: addTimingPoint('image cache started');

    if($content = $response->getResponseString())
    {
      //by reference
      $cache->processContent($content);
      $response->write($content);
    }

    Debug :: addTimingPoint('image cache write finished');
  }

  function _getImageCacheManager()
  {
    return new ImageCacheManager();
  }

  function _isCachingEnabled()
  {
    if(!defined('IMAGE_CACHE_ENABLED'))
      return true;

    return constant('IMAGE_CACHE_ENABLED');
  }
}
?>