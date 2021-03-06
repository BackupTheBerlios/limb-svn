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
require_once(LIMB_DIR . '/core/cache/partial_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/full_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/image_cache_manager.class.php');

class page_cache_flush_job
{
  function perform(&$response)
  {    
    $response->write("Flushing full page cache...");
    
    $full_cache_mgr = new full_page_cache_manager();
    $full_cache_mgr->flush();
    
    $response->write("done\n");
    
    $response->write("Flushing partial page cache...");
    
    $partial_cache_mgr = new partial_page_cache_manager();
    $partial_cache_mgr->flush();
    
    $response->write("done\n");

    $response->write("Flushing images cache...");
    
    $image_cache_mgr = new image_cache_manager();
    $image_cache_mgr->flush();
    
    $response->write("done\n");
  }
}

?>