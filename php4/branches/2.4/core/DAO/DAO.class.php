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

class DAO
{
  function & fetch()
  {
    include_once(WACT_ROOT . '/iterator/iterator.inc.php');
    return new EmptyIterator();
  }

  function fetchById($id){}
}

?>
