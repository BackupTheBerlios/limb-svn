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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

class articles_folder_controller extends site_object_controller
{
  public function get_display_action_properties()
  {
    return array();
  }

  public function define_display($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/display.html')
                                  array(LIMB :: STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  public function get_admin_display_action_properties()
  {
    return array();
  }

  public function define_admin_display($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/admin_display.html')
                                  array(LIMB :: STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  public function get_create_article_action_properties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => strings :: get('create_article', 'article'),
                 'img_src' => '/shared/images/new.generic.gif');
  }

  public function define_create_article($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/article/create.html')
                                  array(LIMB :: STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_ARTICLES_DIR . '/commands/create_article_form_command',
                                        'article_form'),
                                  array(LIMB :: STATUS_FORM_SUBMITTED => 'process',
                                        LIMB :: STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_ARTICLES_DIR . '/commands/create_article_command',
                                  array(LIMB :: STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  public function get_create_articles_folder_action_properties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => strings :: get('create_articles_folder', 'article'),
                 'img_src' => '/shared/images/new.folder.gif');
  }

  public function define_create_articles_folder($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/create.html')
                                  array(LIMB :: STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_DIR . '/class/core/commands/create_simple_folder_form_command',
                                        'articles_folder_form'),
                                  array(LIMB :: STATUS_FORM_SUBMITTED => 'process',
                                        LIMB :: STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_DIR . '/class/core/commands/create_site_object_command',
                                  array(LIMB :: STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  public function get_edit_action_properties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => strings :: get('edit_articles_folder', 'article'),
                 'img_src' => '/shared/images/edit.gif');
  }

  public function define_edit($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/edit.html')
                                  array(LIMB :: STATUS_OK => 'form'));

    $state_machine->registerState('form',
                                  array(LIMB_DIR . '/class/core/commands/edit_simple_folder_form_command',
                                        'articles_folder_form'),
                                  array(LIMB :: STATUS_FORM_SUBMITTED => 'process',
                                        LIMB :: STATUS_FORM_DISPLAYED => 'render'));

    $state_machine->registerState('process',
                                  LIMB_DIR . '/class/core/commands/edit_site_object_command',
                                  array(LIMB :: STATUS_ERROR => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }
}

?>