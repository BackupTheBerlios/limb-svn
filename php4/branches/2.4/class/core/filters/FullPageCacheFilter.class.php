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
require_once(LIMB_DIR . '/class/cache/FullPageCacheManager.class.php');

class FullPageCacheFilter// implements InterceptingFilter
{
  function run($filter_chain, $request, $response)
  {
    if(!$this->_isCachingEnabled())
    {
      $filter_chain->next();
      return;
    }

    Debug :: addTimingPoint('full page cache started');

    $cache = $this->_getFullPacheCacheManager();

    $cache->setRequest($request);

    if($contents = $cache->get())
    {
      Debug :: addTimingPoint('full page cache read finished');

      $response->write($contents);
      return;
    }

    $filter_chain->next();

    $cache->write($response->getResponseString());

    Debug :: addTimingPoint('full page cache write finished');
  }

  function _getFullPacheCacheManager()
  {
    return new FullPageCacheManager();
  }

  function _isCachingEnabled()
  {
    if(!defined('FULL_PAGE_CACHE_ENABLED'))
      return true;

    return constant('FULL_PAGE_CACHE_ENABLED');
  }
}
?>