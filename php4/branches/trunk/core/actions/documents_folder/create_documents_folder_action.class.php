<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/site_object/create_action.class.php');

class create_documents_folder_action extends create_action
{
  function _define_dataspace_name()
  {
    return 'documents_folder_controller';
  }
}
?>