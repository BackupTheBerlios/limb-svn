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
require_once(LIMB_DIR . '/core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . '/core/fetcher.class.php');

class datasource
{
  function datasource()
  {
  }

  function & get_dataset(&$counter, $params=array())
  {
    $counter = 0;
    return new array_dataset(array());
  }
}


?>