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
require_once(dirname(__FILE__) . '/ShippingConfiguration.class.php');
require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');

if (!defined('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME'))
  define('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME', 60*60*24*7);

class ShippingLocator
{
  protected $cache_result = true;
  protected $cache_life_time;

  protected $cache;

  function __construct()
  {
    $this->cache_life_time = SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME;
  }

  public function getCache()
  {
    if($this->cache)
      return $this->cache;

    include_once('Cache/Lite.php');
    include_once(LIMB_DIR . '/class/lib/system/Fs.class.php');

    Fs :: mkdir(VAR_DIR . '/shipping_options');

    $options = array(
      'cacheDir' => VAR_DIR . '/shipping_options/',
      'lifeTime' => $this->cache_life_time
    );

    $this->cache = new CacheLite($options);

    return $this->cache;
  }

  public function useCache($status = true)
  {
    $this->cache_result = $status;
  }

  public function flushCache()
  {
    $this->getCache()->clean();
  }

  public function setCacheLifeTime($time)
  {
    $this->cache_life_time = $time;
  }

  public function getCacheLifeTime()
  {
    return $this->cache_life_time;
  }

  public function getShippingOptions($shipping_configuration)
  {
    if($this->cache_result)
    {
      if(($options = $this->_getCachedOptions($shipping_configuration)) !== false)
        return $options;
    }

    if(!$options = $this->_doGetShippingOptions($shipping_configuration))
      return array();

    $options = ComplexArray :: sortArray($options, array('price' => 'ASC'));

    if($this->cache_result)
      $this->_saveCachedOptions($shipping_configuration, $options);

    return $options;
  }

  protected function _saveCachedOptions($shipping_configuration, $options)
  {
    $cache = $this->getCache();

    $cache->save(serialize($options), $shipping_configuration->getHash());
  }

  protected function _getCachedOptions($shipping_configuration)
  {
    $cache = $this->getCache();

    if($result = $cache->get($shipping_configuration->getHash()))
      return unserialize($result);
    else
      return false;
  }

  protected function _doGetShippingOptions($shipping_configuration)
  {
    return array();
  }
}

?>