
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
require_once(dirname(__FILE__) . '/ImageObjectsRecordSet.class.php');

class ImageObjectsDAO extends SQLBasedDAO
{
  function & fetch()
  {
    return new ImageObjectsRecordSet(parent :: fetch());
  }
}
?>

