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

class VoteAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'vote_action';
  }

  function _validPerform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject('PollContainer');
    $data = $this->dataspace->export();

    $request->setStatus(Request :: STATUS_FAILURE);

    if (!isset($data['answer']))
    {
      MessageBox :: writeNotice(Strings :: get('no_answer', 'poll'));
      return;
    }

    $object->registerAnswer($data['answer']);
    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }
}

?>