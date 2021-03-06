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
class image_factory
{
  function image_factory()
  {
  }

  function & create($library = 'gd', $dir = '')
  {
    if(defined('IMAGE_LIBRARY'))
      $library = IMAGE_LIBRARY;

    $image_class_name = 'image_' . $library;

    if(isset($GLOBALS['global_' . $image_class_name]))
      $obj =& $GLOBALS['global_' . $image_class_name];
    else
      $obj = null;

    if(get_class($obj) != $image_class_name)
    {
      $dir = ($dir == '') ? LIMB_DIR . '/core/lib/image/' : $dir;

      if(!file_exists($dir . $image_class_name . '.class.php'))
          error('image library not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
                array('library' => $library, 'dir' => $dir));

      include_once($dir . $image_class_name . '.class.php');

      $obj =& new $image_class_name();
      $GLOBALS['global_' . $image_class_name] =& $obj;
    }

    return $obj;
  }

}
?>