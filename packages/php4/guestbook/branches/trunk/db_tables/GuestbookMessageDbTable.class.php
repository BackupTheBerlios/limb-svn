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
require_once(LIMB_DIR . '/class/db_tables/OneTableObjectDbTable.class.php');

class GuestbookMessageDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'message' => '',
        'sender_email' => '',
        'sender' => '',
        'comment' => '',
        'comment_author' => '',
        'comment_author_email' => ''
      )
    );
  }
}

?>