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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class stats_search_engines_report_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'search_engines_form';
  }

  function _valid_perform(&$request, &$response)
  {
    $request->merge_attributes($this->dataspace->export());

    parent :: _valid_perform($request, $response);
  }

}

?>