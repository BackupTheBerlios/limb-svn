<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
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
    
    $cache = $this->_get_image_cache_manager();
    $cache->set_uri($request->get_uri());
    
    ob_start();
    
    $filter_chain->next();
    
    if($response->file_sent())
      return;

    debug :: add_timing_point('image cache started');
    
    if($content = $response->get_response_string())
    {
      //by reference
      $cache->process_content($content);
      $response->write($content);
    }  

    debug :: add_timing_point('image cache write finished');    
  }
  
  protected function _get_image_cache_manager()
  {
    return new image_cache_manager();
  }
  
  protected function _is_caching_enabled()
  {
    if(!defined('IMAGE_CACHE_ENABLED'))
      return true;
      
    return constant('IMAGE_CACHE_ENABLED');
  }
} 
?>