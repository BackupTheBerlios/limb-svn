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
require_once(dirname(__FILE__). '\updateParamCommonAction.class.php');

class UpdateParamAction extends UpdateParamCommonAction
{
  function _defineDataspaceName()
  {
    return 'site_param_form';
  }
}
?>