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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');

class StatsRoutesReportAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'routes_form';
  }

  function _validPerform(&$request, &$response)
  {
    $request->import($this->dataspace->export());

    parent :: _validPerform(&$request, &$response);
  }

}

?>