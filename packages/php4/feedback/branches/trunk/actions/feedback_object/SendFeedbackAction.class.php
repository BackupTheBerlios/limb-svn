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
require_once(LIMB_DIR . '/class/core/SysParam.class.php');

class SendFeedbackAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'feedback_form';
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'subject'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'sender_email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'sender_email'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'body'));
  }

  function _getEmail()
  {
    $inst =& SysParam :: instance();
    if(!$email = $inst->getParam('contact_email', 'char'))
      $email = constant('ADMINISTRATOR_EMAIL');

    return $email;
  }

  function _getMailSubject()
  {
    return sprintf(Strings :: get('message_subject', 'feedback'),
                        $this->dataspace->get('subject'),
                        $_SERVER['HTTP_HOST']);
  }

  function _validPerform($request, $response)
  {
    $mail_data = $this->dataspace->export();

    if(isset($mail_data['sender_name']) )
      $sender_name = $mail_data['sender_name'];
    else
      $sender_name = $mail_data['sender_firstname'] . ' ' . $mail_data['sender_lastname'];

    $body = sprintf(Strings :: get('body_template', 'feedback'),
                    $sender_name,
                    $mail_data['sender_email'],
                    $mail_data['body']);

    $body = str_replace('<br>', "\n", $body);

    $subject = $this->_getMailSubject();

    $recipient_email = $this->_getEmail();

    $mailer = $this->_getMailer();

    $headers['From']    = $mail_data['sender_email'];
    $headers['To']      = $recipient_email;
    $headers['Subject'] = $subject;

    if(!$recipient_email ||
       !$mailer->send($recipient_email,
                    $headers,
                    $body
                    )
        )
    {
      MessageBox :: writeNotice(Strings :: get('mail_not_sent', 'feedback'));

      $request->setStatus(Request :: STATUS_FAILUER);
      return;
    }

    MessageBox :: writeNotice(Strings :: get('message_was_sent', 'feedback'));

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
    $response->redirect($_SERVER['PHP_SELF']);
  }

  function _getMailer()
  {
    include_once('Mail.php');

    return Mail :: factory('mail');
  }
}
?>