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
require_once(LIMB_DIR . '/core/i18n/Strings.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

class Behaviour extends Object
{
  var $ini;

  function Behaviour($name)
  {
    parent :: Object();

    $this->set('name', $name);
  }

  function & getActionCommand($action)
  {
    $ini =& $this->_getIni();

    if(!$ini->hasGroup($action) || !$ini->hasOption('command', $action))
    {
      include_once(LIMB_DIR . '/core/commands/NullCommand.class.php');
      return new NullCommand();
    }

    $command_handle = new LimbHandle($ini->getOption('command', $action));

    $command =& Handle :: resolve($command_handle);

    return $command;
  }

  function & _getIni()
  {
    if(is_object($this->ini))
      return $this->ini;

    if (!$name = $this->getName())
      $name = 'default';

    $toolkit =& Limb :: toolkit();
    $this->ini =& $toolkit->getIni($name . '.behaviour.ini', 'behaviour');
    return $this->ini;
  }

  function getActionProperties($action)
  {
    $ini =& $this->_getIni();

    if($ini->hasGroup($action))
      return $ini->getGroup($action);
    else
      return array();
  }

  function getActionsList()
  {
    $ini =& $this->_getIni();

    $groups = $ini->getAll();
    if(isset($groups['default']))
      unset($groups['default']);

    return array_keys($groups);
  }

  function actionExists($action)
  {
    $ini =& $this->_getIni();

    return $ini->hasGroup($action);
  }

  function canBeParent()
  {
    $ini =& $this->_getIni();
    if(!$ini->hasOption('can_be_parent'))
      return false;

    return (bool)$ini->getOption('can_be_parent');
  }

  function getDefaultAction()
  {
    $ini =& $this->_getIni();
    if(!$ini->hasOption('default_action'))
      return 'display';

    return $ini->getOption('default_action');
  }

  function getId()
  {
    return (int)$this->get('id');
  }

  function setId($id)
  {
    $this->set('id', (int)$id);
  }

  function getName()
  {
    return $this->get('name');
  }
}

?>