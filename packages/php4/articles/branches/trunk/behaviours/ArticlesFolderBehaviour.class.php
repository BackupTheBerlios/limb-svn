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
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');

class ArticlesFolderController extends SiteObjectController
{
  function getDisplayActionProperties()
  {
    return array();
  }

  function defineDisplay($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/display.html')
                                  array(LIMB :: STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  function getAdminDisplayActionProperties()
  {
    return array();
  }

  function defineAdminDisplay($state_machine)
  {
    $state_machine->registerState('init',
                                  array(LIMB_DIR . '/class/core/commands/use_view_command',
                                        '/articles_folder/admin_display.html')
                                  array(LIMB :: STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  LIMB_DIR . '/class/core/commands/display_view_command');
  }

  function getCreateArticleActionProperties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => Strings :: get('create_article', 'article'),
                 'img_src' => '/shared/images/new.generic.gif');
  }

  function defineCreateArticle($state_machine)
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

  function getCreateArticlesFolderActionProperties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => Strings :: get('create_articles_folder', 'article'),
                 'img_src' => '/shared/images/new.folder.gif');
  }

  function defineCreateArticlesFolder($state_machine)
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

  function getEditActionProperties()
  {
    return array('popup' => true,
                 'JIP' => true,
                 'action_name' => Strings :: get('edit_articles_folder', 'article'),
                 'img_src' => '/shared/images/edit.gif');
  }

  function defineEdit($state_machine)
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