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
require_once(LIMB_DIR . '/core/actions/FormCreateSiteObjectAction.class.php');

class CreateGuestbookMessageAction extends FormCreateSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'guestbook_message';
  }

  function _defineDataspaceName()
  {
    return 'create_guestbook_message';
  }

  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'message' => 'message',
          'sender' => 'sender',
          'sender_email' => 'sender_email',
        )
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'message'));
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'sender'));
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/email_rule', 'sender_email'));
  }

  function _initDataspace(&$request)
  {
    $data['identifier'] = md5(rand());

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $data['sender'] = $user->getLogin();
    $data['sender_email'] = $user->get('email', '');

    $this->dataspace->import($data);
  }

  function _processTransferedDataspace()
  {
    $this->_htmlspecialcharsDataspaceValue('message');
    $this->_htmlspecialcharsDataspaceValue('sender_email');
    $this->_htmlspecialcharsDataspaceValue('title');
    $this->_htmlspecialcharsDataspaceValue('sender');
  }

}

?>