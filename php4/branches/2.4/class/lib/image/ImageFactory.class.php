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
class ImageFactory
{
  function create($library = 'gd', $dir = '')
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
      $dir = ($dir == '') ? LIMB_DIR . '/class/lib/image/' : $dir;

      if(!file_exists($dir . $image_class_name . '.class.php'))
          return throw(new FileNotFoundException('image library not found', $dir . $image_class_name . '.class.php'));

      include_once($dir . $image_class_name . '.class.php');

      $obj = new $image_class_name();
      $GLOBALS['global_' . $image_class_name] =& $obj;
    }

    return $obj;
  }

}
?>