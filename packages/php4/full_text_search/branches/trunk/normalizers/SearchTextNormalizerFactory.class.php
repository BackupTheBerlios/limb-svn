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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

class SearchTextNormalizerFactory
{
  protected function __construct(){}

  static public function create($class_name)
  {
    self :: _includeClassFile($class_name);

    return new $class_name();
  }

  static protected function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    include_once(dirname(__FILE__) . '/' . $class_name . '.class.php');
  }
}
?>