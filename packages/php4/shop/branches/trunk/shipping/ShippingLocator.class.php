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

@define('SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME', 60*60*24*7);

class ShippingLocator
{
  var $cache_result = true;
  var $cache_life_time;

  var $cache;

  function ShippingLocator()
  {
    $this->cache_life_time = SHIPPING_LOCATOR_DEFAULT_CACHE_LIFE_TIME;
  }

  function getCache()
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

  function useCache($status = true)
  {
    $this->cache_result = $status;
  }

  function flushCache()
  {
    $cache =& $this->getCache();
    $cache->clean();
  }

  function setCacheLifeTime($time)
  {
    $this->cache_life_time = $time;
  }

  function getCacheLifeTime()
  {
    return $this->cache_life_time;
  }

  function getShippingOptions($shipping_configuration)
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

  function _saveCachedOptions($shipping_configuration, $options)
  {
    $cache =& $this->getCache();

    $cache->save(serialize($options), $shipping_configuration->getHash());
  }

  function _getCachedOptions($shipping_configuration)
  {
    $cache =& $this->getCache();

    if($result = $cache->get($shipping_configuration->getHash()))
      return unserialize($result);
    else
      return false;
  }

  function _doGetShippingOptions($shipping_configuration)
  {
    return array();
  }
}

?>