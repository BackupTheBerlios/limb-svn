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
require_once(LIMB_DIR . '/core/actions/FormAction.class.php');

class StatsEventFilterAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'events_filter_form';
  }

  function _validPerform(&$request, &$response)
  {
    $request->merge($this->dataspace->export());

    parent :: _validPerform(&$request, &$response);
  }
}

?>