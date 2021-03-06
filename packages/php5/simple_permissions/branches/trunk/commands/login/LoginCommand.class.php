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
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');
require_once(dirname(__FILE__) . '/../../SimpleAuthenticator.class.php');

class LoginCommand implements Command
{
  public function perform()
  {
    $toolkit = Limb :: toolkit();

    $dataspace = $toolkit->getDataspace();

    $login_params = array(
      'login' => $dataspace->get('login'),
      'password' => $dataspace->get('password'),
      'locale_id' => $dataspace->get('locale_id')
    );

    $toolkit->getAuthenticator()->login($login_params);

    if (!$toolkit->getUser()->isLoggedIn())
    {
      return LIMB :: STATUS_ERROR;
    }

    $response = $toolkit->getResponse();

    if($redirect = $dataspace->get('redirect'))
    {
      $response->redirect($redirect);
      return LIMB :: STATUS_OK;
    }

    $referer = $this->_getHttpReferer();
    if($referer &&  (strpos(strtolower($referer), '/root/login') === false))
    {
      $response->redirect($referer);
      return LIMB :: STATUS_OK;
    }

    $response->redirect('/');
    return LIMB :: STATUS_OK;
  }

  // for mocking
  protected function _getHttpReferer()
  {
    return $_SERVER['HTTP_REFERER'];
  }
}

?>