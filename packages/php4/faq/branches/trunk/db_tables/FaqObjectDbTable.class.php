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
require_once(LIMB_DIR . '/core/db_tables/OneTableObjectDbTable.class.php');

class FaqObjectDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'question' => '',
        'question_author' => '',
        'question_author_email' => '',
        'answer' => '',
        'answer_author' => '',
        'answer_author_email' => ''
      )
    );
  }
}

?>