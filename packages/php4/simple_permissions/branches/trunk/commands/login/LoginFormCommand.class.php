<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: login_command.class.php 827 2004-10-23 15:00:44Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormCommand.class.php');

class LoginFormCommand extends FormCommand
{
  function _registerValidationRules(&$validator, &$dataspace)
  {
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'login'));
    $validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'password'));
  }

  function _mergeDataspaceWithRequest(&$dataspace, &$request)
  {
    parent :: _mergeDataspaceWithRequest($dataspace, $request);

    $this->_transferRedirectParam($dataspace, $request);
  }

  function _transferRedirectParam(&$dataspace, &$request)
  {
    if(!$redirect = $request->get('redirect'))
      return;

    $dataspace->set('redirect', urldecode($this->_getRedirectString($request)));
  }

  function _getRedirectString(&$request)
  {
    if(!$redirect = $request->get('redirect'))
      return '';

    if(!preg_match("/^([a-z0-9\.#\/\?&=\+\-_]+)/si", $redirect))
      return '';

    return $redirect;
  }
}

?>