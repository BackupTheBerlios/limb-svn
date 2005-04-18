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
require_once(LIMB_DIR . '/core/filters/intercepting_filter.class.php');
require_once(LIMB_DIR . '/core/cache/full_page_cache_manager.class.php');

class full_page_cache_filter extends intercepting_filter
{
  function run(&$filter_chain, &$request, &$response)
  {
    if(!$response->is_empty() || !$this->_is_caching_enabled())
    {
      $filter_chain->next();
      return;
    }

    debug :: add_timing_point('full page cache started');

    $cache = new full_page_cache_manager();

    $cache->set_uri($request->get_uri());

    if($contents =& $cache->get())
    {
      debug :: add_timing_point('full page cache read finished');

      $response->write($contents);
      return;
    }

    $filter_chain->next();

    $cache->write($content =& $response->get_response_string());

    debug :: add_timing_point('full page cache write finished');
  }

  function _is_caching_enabled()
  {
    if(!defined('FULL_PAGE_CACHE_ENABLED'))
      return true;

    return constant('FULL_PAGE_CACHE_ENABLED');
  }
}
?>