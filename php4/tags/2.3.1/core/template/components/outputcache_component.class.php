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

require_once(LIMB_DIR . '/core/cache/partial_page_cache_manager.class.php');

class outputcache_component extends component
{
  var $cache_manager = null;

  function outputcache_component()
  {
    $this->cache_manager =& new partial_page_cache_manager();
  }

  function prepare()
  {
    $request = request :: instance();
    $this->cache_manager->set_uri($u =& $request->get_uri());
    $this->cache_manager->set_server_id($this->get_server_id());
  }

  function set_server_id($server_id)
  {
    $this->cache_manager->set_server_id($this->server_id);
  }

  function get()
  {
    return $this->cache_manager->get();
  }

  function write(&$contents)
  {
    return $this->cache_manager->write($contents);
  }
}

?>