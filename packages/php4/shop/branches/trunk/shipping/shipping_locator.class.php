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
require_once(dirname(__FILE__) . '/shipping/shipping_configuration.class.php'); 
require_once(LIMB_DIR . 'class/lib/system/fs.class.php'); 

define('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME', 60*60*24*7);

class shipping_locator
{
  var $cache_result = true;
  var $cache_life_time;
 
  var $cache; 
  
  function shipping_locator()
  {
    $this->cache_life_time = SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME;
  }
    
  function & get_cache()
  {
    if($this->cache)  
      return $this->cache;
    
    
    die('fix me: should use Pear::Lite_Cache!!!');
    include_once(LIMB_DIR . '/core/lib/cache/cache_lite.class.php');
    
    $options = array(
      'cache_dir' => VAR_DIR . '/shipping_options',
      'life_time' => $this->cache_life_time
    );
        
    $this->cache =& new cache_lite($options);      
     
    return $this->cache;    
  }
  
  function use_cache($status = true)
  {
    $this->cache_result = $status;
  }
  
  function flush_cache()
  {
    $cache =& $this->get_cache();
    $cache->clean();
  }
  
  function set_cache_life_time($time)
  {
    $this->cache_life_time = $time;
  }
  
  function get_cache_life_time()
  {
    return $this->cache_life_time;
  }
  
  function get_shipping_options($shipping_configuration)
  {
    if($this->cache_result)
    {
      if(($options = $this->_get_cached_options($shipping_configuration)) !== false)
        return $options;
    }
    
    if(!$options = $this->_do_get_shipping_options($shipping_configuration))
      return array();
    
    $options = complex_array :: sort_array($options, array('price' => 'ASC'));
    
    if($this->cache_result)
      $this->_save_cached_options($shipping_configuration, $options);
      
    return $options;
  }
      
  function _save_cached_options($shipping_configuration, $options)
  {
    $cache =& $this->get_cache();
    
    $cache->save(serialize($options), $shipping_configuration->get_hash());
  }

  function _get_cached_options($shipping_configuration)
  {
    $cache =& $this->get_cache();
    
    if($result = $cache->get($shipping_configuration->get_hash()))
      return unserialize($result);
    else
      return false;
  }
  
  function _do_get_shipping_options($shipping_configuration)
  {
    return array();
  }
}

?>