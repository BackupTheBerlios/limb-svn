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
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');
require_once(LIMB_DIR . '/class/cache/full_page_cache_manager.class.php');

class full_page_cache_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {
    if(!$this->_is_caching_enabled())
    {
      $filter_chain->next();
      return;
    }

    debug :: add_timing_point('full page cache started');

    $cache = $this->_get_full_pache_cache_manager();

    $cache->set_request($request);

    if($contents = $cache->get())
    {
      debug :: add_timing_point('full page cache read finished');

      $response->write($contents);
      return;
    }

    $filter_chain->next();

    $cache->write($response->get_response_string());

    debug :: add_timing_point('full page cache write finished');
  }

  protected function _get_full_pache_cache_manager()
  {
    return new full_page_cache_manager();
  }

  protected function _is_caching_enabled()
  {
    if(!defined('FULL_PAGE_CACHE_ENABLED'))
      return true;

    return constant('FULL_PAGE_CACHE_ENABLED');
  }
}
?>