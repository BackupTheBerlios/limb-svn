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

class StatsHitsHostsReportAction extends FormAction
{
  protected function _defineDataspaceName()
  {
    return 'hits_hosts_form';
  }

  protected function _validPerform($request, $response)
  {
    $request->import($this->dataspace->export());

    parent :: _validPerform($request, $response);
  }

}

?>