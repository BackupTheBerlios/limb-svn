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
require_once(dirname(__FILE__) . '/cronjob_command.class.php');

class page_cache_flush_job extends cronjob_command
{
  protected function _install_managers()
  {
    include_once(LIMB_DIR . '/class/cache/partial_page_cache_manager.class.php');
    include_once(LIMB_DIR . '/class/cache/full_page_cache_manager.class.php');
    include_once(LIMB_DIR . '/class/cache/image_cache_manager.class.php');
  }

  public function perform()
  {
    $this->_install_managers();

    $this->response->write("Flushing full page cache...");

    $full_cache_mgr = new full_page_cache_manager();
    $full_cache_mgr->flush();

    $this->response->write("done\n");

    $this->response->write("Flushing partial page cache...");

    $partial_cache_mgr = new partial_page_cache_manager();
    $partial_cache_mgr->flush();

    $this->response->write("done\n");

    $this->response->write("Flushing images cache...");

    $image_cache_mgr = new image_cache_manager();
    $image_cache_mgr->flush();

    $this->response->write("done\n");
  }
}

?>