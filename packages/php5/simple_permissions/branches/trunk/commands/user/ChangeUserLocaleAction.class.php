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
require_once(LIMB_DIR . '/class/core/actions/FormEditSiteObjectAction.class.php');
require_once(LIMB_DIR . '/class/validators/rules/RequiredRule.class.php');

class ChangeUserLocaleAction extends FormAction
{
  protected function _defineDataspaceName()
  {
    return 'change_locale_form';
  }

  protected function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'locale_id'));
  }

  protected function _validPerform($request, $response)
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

    Limb :: toolkit()->getUser()->set('locale_id', $locale_id);

    $request->setStatus(Request :: STATUS_SUCCESS);
  }
}

?>