<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAO.class.php 1103 2005-02-14 15:16:43Z pachanga $
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
