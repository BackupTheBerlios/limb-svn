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
require_once(LIMB_DIR . '/class/cache/PartialPageCacheManager.class.php');

class OutputcacheComponent extends Component
{
  var $cache_manager = null;

  function __construct()
  {
    $this->cache_manager = new PartialPageCacheManager();
  }

  function prepare()
  {
    $request = Limb :: toolkit()->getRequest();
    $this->cache_manager->setUri($request->getUri());
    $this->cache_manager->setServerId($this->getServerId());
  }

  function setServerId($server_id)
  {
    $this->cache_manager->setServerId($this->server_id);
  }

  function get()
  {
    return $this->cache_manager->get();
  }

  function write($contents)
  {
    return $this->cache_manager->write($contents);
  }
}

?>