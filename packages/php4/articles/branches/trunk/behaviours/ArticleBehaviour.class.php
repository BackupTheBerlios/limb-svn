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
require_once(LIMB_DIR . '/core/controllers/SiteObjectController.class.php');

class ArticleController extends SiteObjectController
{
  function getDisplayActionProperties()
  {
    return array();
  }

  function defineDisplay(&$state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/core/commands/use_view_command',
                                        '/article/display.html')
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/core/commands/display_view_command');
  }

  function getPrintVersionActionProperties()
  {
    return array('action_name' => Strings :: get('print_version_action', 'document'));
  }

  function definePrintVersion(&$state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/core/commands/use_view_command',
                                        '/article/print_version.html')
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/core/commands/display_view_command');
  }

  function getEditActionProperties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => Strings :: get('edit_article', 'article'),
                 'img_src' => '/shared/images/edit.gif');
  }

  function defineEdit(&$state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/core/commands/use_view_command',
                                        '/article/edit.html')
                                  array(LIMB_STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_ARTICLES_DIR . '/commands/edit_article_form_command',
                                        'article_form'),
                                  array(LIMB_STATUS_FORM_SUBMITTED => 'process',
                                        LIMB_STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_ARTICLES_DIR . '/commands/edit_article_command',
                                  array(LIMB_STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/core/commands/display_view_command');
  }
}

?>