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
require_once(dirname(__FILE__) . '/shipping_configuration.class.php'); 
require_once(LIMB_DIR . 'class/lib/system/fs.class.php'); 

if (!defined('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME'))
	define('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME', 60*60*24*7);

class shipping_locator
{
  private $cache_result = true;
  private $cache_life_time;
 
  private $cache; 
  
  function __construct()
  {
    $this->cache_life_time = SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME;
  }
    
  public function get_cache()
  {
    if($this->cache)  
      return $this->cache;
        
    include_once('Cache/Lite.php');
    include_once(LIMB_DIR . '/class/lib/system/fs.class.php');
    
    fs :: mkdir(VAR_DIR . '/shipping_options');
    
    $options = array(
      'cacheDir' => VAR_DIR . '/shipping_options/',
      'lifeTime' => $this->cache_life_time
    );
        
    $this->cache = new Cache_Lite($options);  
     
    return $this->cache;    
  }
  
  public function use_cache($status = true)
  {
    $this->cache_result = $status;
  }
  
  public function flush_cache()
  {
    $this->get_cache()->clean();
  }
  
  public function set_cache_life_time($time)
  {
    $this->cache_life_time = $time;
  }
  
  public function get_cache_life_time()
  {
    return $this->cache_life_time;
  }
  
  public function get_shipping_options($shipping_configuration)
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
      
  protected function _save_cached_options($shipping_configuration, $options)
  {
    $cache =& $this->get_cache();
    
    $cache->save(serialize($options), $shipping_configuration->get_hash());
  }

  protected function _get_cached_options($shipping_configuration)
  {
    $cache =& $this->get_cache();
    
    if($result = $cache->get($shipping_configuration->get_hash()))
      return unserialize($result);
    else
      return false;
  }
  
  protected function _do_get_shipping_options($shipping_configuration)
  {
    return array();
  }
}

?>