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
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');
require_once(dirname(__FILE__) . '/../../AccessPolicy.class.php');

class SetGroupAccessTemplateAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'set_group_access_template';
  }

  function _initDataspace($request)
  {
    if (!$class_id = $request->get('class_id'))
      throw new LimbException('class_id not defined');

    $access_policy = new AccessPolicy();
    $data['template'] = $access_policy->getAccessTemplates($class_id, ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    $this->dataspace->merge($data);
  }

  function _validPerform(&$request, &$response)
  {
    if (!$class_id = $request->get('class_id'))
      throw new LimbException('class_id not defined');

    $data = $this->dataspace->export();

    $access_policy = new AccessPolicy();
    $access_policy->saveAccessTemplates($class_id, $data['template'], ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);

    if($request->hasAttribute('popup'))
      $response->write(closePopupNoParentReloadResponse());
  }
}
?>