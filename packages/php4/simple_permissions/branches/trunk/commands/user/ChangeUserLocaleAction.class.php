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
require_once(LIMB_DIR . '/class/actions/FormEditSiteObjectAction.class.php');
require_once(LIMB_DIR . '/class/validators/rules/RequiredRule.class.php');

class ChangeUserLocaleAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'change_locale_form';
  }

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'locale_id'));
  }

  function _validPerform(&$request, &$response)
  {
    $locale_id = $this->dataspace->get('locale_id');

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));
    elseif(isset($_SERVER['HTTP_REFERER']))
      $response->redirect($_SERVER['HTTP_REFERER']);
    else
      $response->redirect('/');

    if (!Locale :: isValidLocaleId($locale_id))
    {
      $request->setStatus(Request :: STATUS_FAILURE);
    }

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $user->set('locale_id', $locale_id);

    $request->setStatus(Request :: STATUS_SUCCESS);
  }
}

?>