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
require_once(LIMB_DIR . '/core/actions/form_create_site_object_action.class.php');

class create_pricelist_object_action extends form_create_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'pricelist_object';
  }

  function _define_dataspace_name()
  {
    return 'pricelist_object_form';
  }

  function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'object_content' => 'content',
          'file_id' => 'file_id'
        )
    );
  }
}

?>