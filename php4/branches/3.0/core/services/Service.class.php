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

class Service
{
  var $name;
  var $ini;
  var $current_action;

  function Service($name)
  {
    $this->name = $name;
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

    $toolkit =& Limb :: toolkit();
    $this->ini =& $toolkit->getIni($this->name . '.service.ini', 'service');

    if(!is_object($this->ini))
      return throw_error(new LimbException($this->name . '.service.ini not found'));//FIX

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

  function getDefaultAction()
  {
    $ini =& $this->_getIni();
    if(!$ini->hasOption('default_action'))
      return 'display';

    return $ini->getOption('default_action');
  }

  function getCurrentAction()
  {
    if($this->current_action)
      return $this->current_action;

    $this->current_action = $this->getDefaultAction();
    return $this->current_action;
  }

  function setCurrentAction($action)
  {
    $this->current_action = $action;
  }

  function getName()
  {
    return $this->name;
  }
}

?>