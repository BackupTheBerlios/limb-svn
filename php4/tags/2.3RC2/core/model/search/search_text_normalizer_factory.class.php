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
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');

class search_text_normalizer_factory
{
  function search_text_normalizer_factory()
  {
  }

  function & instance($class_name)
  {
    include_class($class_name, '/core/model/search/normalizers/');
    $obj =&	instantiate_object($class_name);
    return $obj;
  }

}
?>