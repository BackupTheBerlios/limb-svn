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
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

class SiteObjectsDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    include_once(LIMB_DIR . '/core/dao/SiteObjectsRawSQL.class.php');
    return new SiteObjectsRawSQL();
  }
}

?>
