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

class EditGuestbookMessageAction extends FormEditSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'guestbook_message';
  }

  function _defineDataspaceName()
  {
    return 'edit_guestbook_message';
  }

  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'message' => 'message',
          'sender' => 'sender',
          'sender_email' => 'sender_email',
          'comment' => 'comment',
          'comment_author' => 'comment_author',
          'comment_author_email' => 'comment_author_email',
        )
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'message'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'sender'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'sender_email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'comment_author_email'));
  }


  function _initDataspace($request)
  {
    parent :: _initDataspace($request);

    $data = $this->dataspace->export();

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    if (empty($data['comment_author']))
      $data['comment_author'] = $user->getLogin();

    if (empty($data['comment_author_email']))
      $data['comment_author_email'] = $user->get('email', '');

    $this->dataspace->import($data);
  }

  function _processTransferedDataspace()
  {
    $this->_htmlspecialcharsDataspaceValue('message');
    $this->_htmlspecialcharsDataspaceValue('sender_email');
    $this->_htmlspecialcharsDataspaceValue('title');
    $this->_htmlspecialcharsDataspaceValue('sender');
    $this->_htmlspecialcharsDataspaceValue('comment_author');
    $this->_htmlspecialcharsDataspaceValue('comment_author_email');
  }
}

?>