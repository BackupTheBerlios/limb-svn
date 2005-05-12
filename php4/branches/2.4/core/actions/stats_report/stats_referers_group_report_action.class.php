<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_referers_report_action.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class stats_referers_group_report_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'referers_group_form';
  }

  function _init_dataspace(&$request)
  {
    $this->dataspace->set('group', $request->get_attribute('group'));
  }

  function _valid_perform(&$request, &$response)
  {
    $request->merge_attributes($this->dataspace->export());

    parent :: _valid_perform($request, $response);
  }

}

?>