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
  protected $cache_manager = null;

  function __construct()
  {
    $this->cache_manager = new PartialPageCacheManager();
  }

  public function prepare()
  {
    $request = Limb :: toolkit()->getRequest();
    $this->cache_manager->setUri($request->getUri());
    $this->cache_manager->setServerId($this->getServerId());
  }

  public function setServerId($server_id)
  {
    $this->cache_manager->setServerId($this->server_id);
  }

  public function get()
  {
    return $this->cache_manager->get();
  }

  public function write($contents)
  {
    return $this->cache_manager->write($contents);
  }
}

?>