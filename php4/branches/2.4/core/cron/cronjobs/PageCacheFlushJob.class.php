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
require_once(dirname(__FILE__) . '/CronjobCommand.class.php');

class PageCacheFlushJob extends CronjobCommand
{
  function _installManagers()
  {
    include_once(LIMB_DIR . '/core/cache/PartialPageCacheManager.class.php');
    include_once(LIMB_DIR . '/core/cache/FullPageCacheManager.class.php');
    include_once(LIMB_DIR . '/core/cache/ImageCacheManager.class.php');
  }

  function perform()
  {
    $this->_installManagers();

    $this->response->write("Flushing full page cache...");

    $full_cache_mgr = new FullPageCacheManager();
    $full_cache_mgr->flush();

    $this->response->write("done\n");

    $this->response->write("Flushing partial page cache...");

    $partial_cache_mgr = new PartialPageCacheManager();
    $partial_cache_mgr->flush();

    $this->response->write("done\n");

    $this->response->write("Flushing images cache...");

    $image_cache_mgr = new ImageCacheManager();
    $image_cache_mgr->flush();

    $this->response->write("done\n");
  }
}

?>