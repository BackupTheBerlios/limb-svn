<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');
require_once(LIMB_DIR . '/class/cache/image_cache_manager.class.php');

class image_cache_filter implements intercepting_filter 
{ 
  public function run($filter_chain, $request, $response) 
  {
    if(!$this->_is_caching_enabled())
    {
      $filter_chain->next();
      return;
    }
    
    $cache = new image_cache_manager();
    $cache->set_uri($request->get_uri());
    
    ob_start();
    
    $filter_chain->next();
    
    if(!$response->is_empty())
      return;
    debug :: add_timing_point('image cache started');
    
    if($content = ob_get_contents())
    {
      ob_end_clean();
    
      $cache->process_content($content);
    
      $response->write($content);
    }

    debug :: add_timing_point('image cache write finished');    
  }
  
  protected function _is_caching_enabled()
  {
    if(!defined('IMAGE_CACHE_ENABLED'))
      return true;
      
    return constant('IMAGE_CACHE_ENABLED');
  }
} 
?>