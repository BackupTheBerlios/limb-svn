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

    $full_page_cache_size = $this->_format_size($full_page_cache_manager->get_cache_size());
    $this->view->set('full_page_cache_size', $full_page_cache_size);

    $partial_page_cache_size = $this->_format_size($partial_page_cache_manager->get_cache_size());
    $this->view->set('partial_page_cache_size', $partial_page_cache_size);

    $image_cache_size = $this->_format_size($image_cache_manager->get_cache_size());
    $this->view->set('image_cache_size', $image_cache_size);

    $template_cache_size = $this->_format_size($this->_get_template_cache_size());
    $this->view->set('template_cache_size', $template_cache_size);

    $general_cache_size = $this->_format_size($this->_get_general_cache_size());
    $this->view->set('general_cache_size', $general_cache_size);

    $ini_cache_size = $this->_format_size($this->_get_ini_cache_size());
    $this->view->set('ini_cache_size', $ini_cache_size);
  }

  function _get_general_cache_size()
  {
    return $this->_get_directory_file_size(VAR_DIR . '/cache');
  }

  function _get_template_cache_size()
  {
    return $this->_get_directory_file_size(VAR_DIR . '/compiled');
  }

  function _get_ini_cache_size()
  {
    return $this->_get_directory_file_size(VAR_DIR . '/ini');
  }

  function _get_directory_file_size($dir)
  {
    $size = 0;
    $files = fs :: find($dir, 'f');
    foreach($files as $file)
      $size += filesize($file);

    return $size;
  }

  function _format_size($size)
  {
    return number_format($size / 1024) . " KB";
  }
}

?>