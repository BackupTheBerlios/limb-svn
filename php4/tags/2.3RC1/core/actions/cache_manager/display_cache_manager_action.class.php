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
require_once(LIMB_DIR . '/core/actions/action.class.php');
require_once(LIMB_DIR . '/core/cache/full_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/partial_page_cache_manager.class.php');
require_once(LIMB_DIR . '/core/cache/image_cache_manager.class.php');

class display_cache_manager_action extends action
{
  function perform(&$request, &$response)
  {
    parent :: perform($request, $response);
    
    $full_page_cache_manager = new full_page_cache_manager();
    $partial_page_cache_manager = new partial_page_cache_manager();
    $image_cache_manager = new image_cache_manager();
    
    $full_page_cache_size = number_format($full_page_cache_manager->get_cache_size()/1024)." KB";
    $this->view->set('full_page_cache_size', $full_page_cache_size);

    $partial_page_cache_size = number_format($partial_page_cache_manager->get_cache_size()/1024)." KB";
    $this->view->set('partial_page_cache_size', $partial_page_cache_size);

    $image_cache_size = number_format($image_cache_manager->get_cache_size()/1024)." KB";
    $this->view->set('image_cache_size', $image_cache_size);

    $template_cache_size = number_format($this->_get_template_cache_size()/1024)." KB";
    $this->view->set('template_cache_size', $template_cache_size);

    $ini_cache_size = number_format($this->_get_ini_cache_size()/1024)." KB";
    $this->view->set('ini_cache_size', $ini_cache_size);
  }
  
  function _get_template_cache_size()
  {
    $size = 0;
    $files = fs :: find_subitems(VAR_DIR . '/compiled', 'f');
	  foreach($files as $file)
	    $size += filesize($file);     
	  
	  return $size;  
  }

  function _get_ini_cache_size()
  {
    $size = 0;
    $files = fs :: find_subitems(CACHE_DIR, 'f');
	  foreach($files as $file)
	    $size += filesize($file);     
	  
	  return $size;  
  }
}

?>